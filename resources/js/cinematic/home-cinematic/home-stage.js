/* orbital-stage.js — <orbital-stage>
   Three.js owns everything in this file : un renderer, une scène, une caméra, une
   boucle de rendu. Theatre.js n'entre jamais ici — il appelle setShot() et rien d'autre.

   Deux mondes dans la même scène, jamais visibles en même temps :

     space   Terre (R = 100) + lune + Voie lactée + rim atmosphérique
     ground  plaine herbeuse texturée, grande infrastructure, dalle de nuages,
             soleil de midi derrière la structure

   Le passage de l'un à l'autre se fait *sous le voile de nuages*, quand l'écran est
   complètement bouché : une seule caméra, aucune coupe visible, aucun second RAF.

   Seam
     stage.setShot({ dist: 3800, fov: 38, exposure: 0.4 })   // fusion partielle
     stage.shot()                                            // relecture
     stage.release()                                         // fin de cinématique → interactif
     stage.bindpages(nodeList)                               // points d'intérêt projetés

   Attributs
     drive="external"   coupe l'input interne : la timeline mène
     tex-base="/textures/"
     quality="auto|high|low"
*/

import * as THREE from 'three';
import { EffectComposer } from 'three/examples/jsm/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/examples/jsm/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/examples/jsm/postprocessing/UnrealBloomPass.js';
import { ShaderPass } from 'three/examples/jsm/postprocessing/ShaderPass.js';
import { OutputPass } from 'three/examples/jsm/postprocessing/OutputPass.js';

/* ── échelles ─────────────────────────────────────────────────────────────────
   R = 100 est l'unité du monde spatial. La lune est ramenée à 1.65 R du centre :
   la vidéo de référence triche exactement de cette façon, sinon les deux corps ne
   tiennent jamais dans le même cadre.                                          */
const R = 100, MOON_R = 27, MOON_ORBIT = 165;
const DECK_LOW = 1500, DECK_TOP = 2600;      // dalle nuageuse, monde au sol
const SITE_EXT = 26000;                      // demi-étendue de la plaine
const SITE_Z = -3800;                        // centre de l'infrastructure
const CURVE = 920000;                        // rayon de la fausse courbure terrestre

/* Générateur déterministe : la plaine et les blocs techniques doivent être les
   mêmes à chaque chargement, sinon le plan final n'est jamais celui qu'on a validé. */
function mulberry32(a) {
  return function () {
    a |= 0; a = a + 0x6D2B79F5 | 0;
    let t = Math.imul(a ^ a >>> 15, 1 | a);
    t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
    return ((t ^ t >>> 14) >>> 0) / 4294967296;
  };
}

const clamp = (v, a, b) => v < a ? a : v > b ? b : v;
const lerp = (a, b, t) => a + (b - a) * t;

/* l'état de mise en scène — setShot() écrit ici, la boucle de rendu lit ici */
const SHOT = {
  world: 0,
  /* caméra spatiale */
  dist: 10050, azim: 0, elev: 0.02, fov: 38, roll: 0, offX: -0.06, offY: 0.06,
  /* caméra au sol */
  alt: 1500, pitch: -0.34, yaw: 0, fwd: 5200, sway: 0,
  /* ciel, soleil, corps */
  sunAz: -0.87, sunEl: 0.18, atmo: 0.34, nightMix: 1, cloudSpin: 0, starFade: 1,
  moonPhase: 3.05, moonRise: 0.1, ambient: 0, haze: 0,
  /* nuages */
  deck: 0, veil: 0,
  /* infrastructure : marquages au sol, verre allumé, colonnes de brume,
     signalétique holographique, trafic aérien */
  reveal: 0, windows: 0, beams: 0, holo: 0, traffic: 0,
  /* composite */
  exposure: 0.34, bloom: 0.55, vignette: 0.86, grain: 0.022, aberr: 0.1,
  smear: 0, streaks: 0, fade: 1, flash: 0
};

/* ── shaders ──────────────────────────────────────────────────────────────── */

const VERT_WORLD = `
varying vec2 vUv; varying vec3 vN; varying vec3 vW;
void main(){
  vUv = uv;
  vN = normalize(mat3(modelMatrix) * normal);
  vec4 w = modelMatrix * vec4(position, 1.0);
  vW = w.xyz;
  gl_Position = projectionMatrix * viewMatrix * w;
}`;

/* La Terre : jour, nuit, nuages et glint océan dans une seule passe. Les nuages
   tournent indépendamment (uCloudSpin) — c'est ce décalage qui donne la sensation
   d'une planète vivante plutôt que d'une bille texturée. */
const FRAG_EARTH = `
uniform sampler2D uDay, uNight, uClouds, uNrm, uSpec;
uniform vec3 uSun, uAtmoCol, uHazeCol;
uniform float uCloudSpin, uNightMix, uAtmo, uHaze;
varying vec2 vUv; varying vec3 vN; varying vec3 vW;
void main(){
  vec3 n = normalize(vN);
  vec3 V = normalize(cameraPosition - vW);
  vec3 nm = texture2D(uNrm, vUv).xyz * 2.0 - 1.0;
  float lam = clamp(dot(n, uSun) + nm.x * 0.10, -1.0, 1.0);
  float day = smoothstep(-0.10, 0.32, lam);
  float relief = 1.0 + nm.x * 0.55 * smoothstep(0.0, 0.45, day);

  vec3 dayCol   = texture2D(uDay, vUv).rgb;
  vec3 nightCol = texture2D(uNight, vUv).rgb;
  float cl      = texture2D(uClouds, vec2(vUv.x + uCloudSpin, vUv.y)).r;
  float ocean   = texture2D(uSpec, vUv).r;

  vec3 H = normalize(uSun + V);
  float glint = pow(max(dot(n, H), 0.0), 340.0) * ocean * day * 0.9;

  vec3 col = dayCol * (0.05 + day * 1.30 * relief);
  col += nightCol * vec3(1.0, 0.80, 0.52) * (1.0 - day) * uNightMix * (1.0 - cl * 0.86);
  col = mix(col, vec3(0.90, 0.94, 1.0) * (0.09 + day * 1.22), cl * 0.94);
  col += glint;

  float fres = pow(1.0 - max(dot(n, V), 0.0), 2.6);
  col += uAtmoCol * fres * uAtmo * (0.22 + day * 1.1);
  col = mix(col, uHazeCol * (0.30 + day * 0.95), uHaze);
  gl_FragColor = vec4(col, 1.0);
}`;

/* coquille additive vue de l'intérieur : le halo au ras du limbe */
const FRAG_ATMO = `
uniform vec3 uCol, uSun; uniform float uAmt;
varying vec2 vUv; varying vec3 vN; varying vec3 vW;
void main(){
  vec3 n = normalize(vN);
  vec3 V = normalize(cameraPosition - vW);
  float rim = pow(1.0 - abs(dot(n, V)), 3.2);
  float lit = smoothstep(-0.40, 0.42, dot(n, uSun));
  float a = rim * lit * uAmt;
  gl_FragColor = vec4(uCol * a, a);
}`;

const FRAG_SKY = `
uniform vec3 uZen, uHor, uSunCol, uSun;
varying vec2 vUv; varying vec3 vN; varying vec3 vW;
void main(){
  vec3 d = normalize(vW);
  float h = clamp(d.y * 1.35, 0.0, 1.0);
  vec3 c = mix(uHor, uZen, pow(h, 0.62));
  float sd = max(dot(d, uSun), 0.0);
  /* le disque, puis deux halos : c'est le halo large qui fait le contre-jour de
     midi derrière la structure, pas le disque lui-même */
  float disk = smoothstep(0.99955, 0.99988, sd);
  c += uSunCol * (disk * 9.0 + pow(sd, 1400.0) * 3.0
                + pow(sd, 90.0) * 0.55 + pow(sd, 9.0) * 0.10);
  gl_FragColor = vec4(c, 1.0);
}`;

/* Le sol n'a plus de shader à lui : c'est un MeshStandardMaterial dont on
   injecte le mélange herbe/terre, la courbure et l'ondulation — de cette façon il
   reçoit les ombres portées, le brouillard et l'éclairage sans les réécrire. */

/* Nuages : quads instanciés billboardés en espace vue. vFade évite que les quads
   ne se coupent net sur le plan proche quand la caméra traverse la dalle. */
const VERT_CLOUD = `
attribute float aSeed;
uniform float uNear;
varying vec2 vUv; varying float vSeed; varying float vFade; varying float vWy;
void main(){
  vUv = uv; vSeed = aSeed;
  vec4 c = modelViewMatrix * instanceMatrix * vec4(0.0, 0.0, 0.0, 1.0);
  float sc = length(instanceMatrix[0].xyz);
  vFade = smoothstep(uNear, uNear + 620.0, -c.z);
  vWy = instanceMatrix[3].y + position.y * sc;
  gl_Position = projectionMatrix * (c + vec4(position.x * sc, position.y * sc, 0.0, 0.0));
}`;

const FRAG_CLOUD = `
uniform float uAmt, uTime, uLight, uFloor; uniform vec3 uCol, uLitCol;
varying vec2 vUv; varying float vSeed; varying float vFade; varying float vWy;
float h21(vec2 p){ return fract(sin(dot(p, vec2(41.3, 289.1))) * 43758.5453); }
float vn(vec2 p){
  vec2 i = floor(p), f = fract(p); f = f * f * (3.0 - 2.0 * f);
  return mix(mix(h21(i), h21(i + vec2(1.0, 0.0)), f.x),
             mix(h21(i + vec2(0.0, 1.0)), h21(i + vec2(1.0, 1.0)), f.x), f.y);
}
void main(){
  float r = length(vUv - 0.5) * 2.0;
  float blob = smoothstep(1.0, 0.10, r);
  float n = vn(vUv * 3.1 + vec2(uTime * 0.015 + vSeed * 7.0, vSeed * 3.0)) * 0.62
          + vn(vUv * 7.4 + vec2(vSeed * 11.0, -uTime * 0.024)) * 0.38;
  /* la dalle a une base : sans ça les quads pendent jusqu'au sol et lavent la ville */
  float a = blob * (0.30 + n * 0.95) * uAmt * vFade * smoothstep(uFloor - 120.0, uFloor + 520.0, vWy);
  vec3 c = mix(uCol, uLitCol, clamp(n * uLight, 0.0, 1.0));
  gl_FragColor = vec4(c, clamp(a, 0.0, 1.0));
}`;

const VERT_INST_UV = `
attribute float aSeed;
varying vec2 vUv; varying float vSeed; varying vec3 vN; varying vec3 vW;
void main(){
  vUv = uv; vSeed = aSeed;
  vec4 w = modelMatrix * instanceMatrix * vec4(position, 1.0);
  vW = w.xyz;
  vN = normalize(mat3(modelMatrix) * mat3(instanceMatrix) * normal);
  gl_Position = projectionMatrix * viewMatrix * w;
}`;

/* faisceaux verticaux : le rim fait le volume, sans volumétrique réel */
const FRAG_BEAM = `
uniform vec3 uCol; uniform float uAmt;
varying vec2 vUv; varying float vSeed; varying vec3 vN; varying vec3 vW;
void main(){
  vec3 V = normalize(cameraPosition - vW);
  /* coeur plus lumineux que le bord : un rim franc dessinerait le contour du
     cylindre au lieu de le cacher. Fondu aux deux extrémités, sinon la
     découpe ouverte se lit comme une ellipse nette. */
  float core = pow(abs(dot(normalize(vN), V)), 0.9);
  float ends = smoothstep(0.0, 0.20, vUv.y) * smoothstep(1.0, 0.68, vUv.y);
  float up = pow(1.0 - vUv.y, 1.4);
  float a = uAmt * core * ends * up * (0.55 + 0.35 * sin(vSeed * 9.0));
  gl_FragColor = vec4(uCol * a, a);
}`;

const FRAG_HOLO = `
uniform vec3 uCol; uniform float uAmt, uTime;
varying vec2 vUv; varying float vSeed; varying vec3 vN; varying vec3 vW;
void main(){
  float scan = 0.5 + 0.5 * sin(vUv.y * 86.0 - uTime * 2.1 + vSeed * 10.0);
  float band = 0.5 + 0.5 * sin(vUv.y * 5.0 + uTime * 0.5 + vSeed * 4.0);
  float edge = smoothstep(0.0, 0.14, vUv.x) * smoothstep(1.0, 0.86, vUv.x)
             * smoothstep(0.0, 0.05, vUv.y) * smoothstep(1.0, 0.62, vUv.y);
  float a = uAmt * edge * (0.06 + scan * 0.16 + band * 0.10);
  gl_FragColor = vec4(uCol * a, a);
}`;

/* trafic aérien : la position est calculée dans le shader, le CPU ne touche à rien */
const VERT_TRAFFIC = `
attribute float aLane, aPhase;
uniform float uTime, uAmt, uPix, uZOff;
varying float vA;
void main(){
  float t = fract(aPhase + uTime * (0.0055 + 0.0042 * fract(aLane * 0.37)));
  float dir = mod(aLane, 2.0) < 1.0 ? 1.0 : -1.0;
  vec3 p;
  p.x = mix(-17000.0, 17000.0, t) * dir;
  p.z = (fract(aLane * 0.61) - 0.5) * 19000.0 + uZOff;
  p.y = 520.0 + fract(aLane * 0.29) * 1150.0;
  vec4 mv = modelViewMatrix * vec4(p, 1.0);
  gl_PointSize = min((3.0 + 5.4 * fract(aLane * 0.13)) * uPix * (3600.0 / max(-mv.z, 1.0)), 11.0 * uPix);
  vA = uAmt * smoothstep(0.0, 0.06, t) * smoothstep(1.0, 0.94, t);
  gl_Position = projectionMatrix * mv;
}`;

/* traînées de vitesse : enfant de la caméra, donc modelViewMatrix = identité */
const VERT_STREAK = `
uniform float uTime, uAmt, uSpeed, uPix;
varying float vA;
void main(){
  vec3 p = position;
  p.z = mod(p.z + uTime * uSpeed, 1800.0) - 1780.0;
  gl_PointSize = min(1.8 * uPix * (320.0 / max(-p.z, 1.0)), 9.0 * uPix);
  vA = uAmt * smoothstep(-1780.0, -1000.0, p.z) * smoothstep(-40.0, -260.0, p.z);
  gl_Position = projectionMatrix * vec4(p, 1.0);
}`;

const FRAG_POINT = `
uniform vec3 uCol;
varying float vA;
void main(){
  float d = length(gl_PointCoord - 0.5) * 2.0;
  float a = vA * smoothstep(1.0, 0.15, d);
  if (a <= 0.002) discard;
  gl_FragColor = vec4(uCol * a, a);
}`;

const VERT_STAR = `
attribute float aMag;
uniform float uPix, uFade;
varying float vA;
void main(){
  vec4 mv = modelViewMatrix * vec4(position, 1.0);
  gl_PointSize = (0.9 + aMag * 2.4) * uPix;
  vA = uFade * (0.18 + aMag * 0.9);
  gl_Position = projectionMatrix * mv;
}`;

/* composite final : exposition, écrasement des noirs, smear radial, aberration,
   vignettage, grain, voile, flash, fondu. Une seule passe. */
const FRAG_FINAL = `
uniform sampler2D tDiffuse;
uniform vec3 uVeilCol, uFlashCol;
uniform float uExposure, uVignette, uGrain, uAberr, uSmear, uVeil, uFlash, uFade, uTime;
varying vec2 vUv;
void main(){
  vec2 uv = vUv, c = uv - 0.5;
  float r2 = dot(c, c);
  vec3 col;
  if (uAberr > 0.002) {
    vec2 d = c * uAberr * 0.0018;
    col = vec3(texture2D(tDiffuse, uv + d).r, texture2D(tDiffuse, uv).g, texture2D(tDiffuse, uv - d).b);
  } else {
    col = texture2D(tDiffuse, uv).rgb;
  }
  if (uSmear > 0.002) {
    vec3 s = col;
    for (int i = 1; i < 5; i++) {
      s += texture2D(tDiffuse, uv - c * (float(i) / 4.0) * uSmear * 0.09).rgb;
    }
    col = s * 0.2;
  }
  col *= uExposure;
  col = max(col - 0.012, 0.0) / 0.988;
  col *= 1.0 - uVignette * smoothstep(0.09, 0.60, r2);
  col = mix(col, uVeilCol, uVeil);
  col += uFlashCol * uFlash;
  float g = fract(sin(dot(uv + fract(uTime), vec2(12.9898, 78.233))) * 43758.5453);
  col += (g - 0.5) * uGrain;
  col *= 1.0 - uFade;
  gl_FragColor = vec4(col, 1.0);
}`;

const VERT_QUAD = `
varying vec2 vUv;
void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }`;

/* ── l'élément ────────────────────────────────────────────────────────────── */

class OrbitalStage extends HTMLElement {
  constructor() {
    super();
    this._s = Object.assign({}, SHOT);
    this._free = false;          // true = Three.js a reprisla main
    this._dead = false;
    this._pages = [];
    this._ptr = { x: 0, y: 0, tx: 0, ty: 0 };
    this._disposables = [];
  }

  /* — seam — */
  setShot(partial) {
    const s = this._s;
    for (const k in partial) if (k in s) s[k] = partial[k];
  }
  shot() { return this._s; }
  release() { this._free = true; this.dispatchEvent(new CustomEvent('orbital:free')); }
  /* rendre la main à la timeline : un rejeu ne doit pas garder le mode interactif */
  hold() { this._free = false; }
  bindpages(nodes) { this._pages = Array.from(nodes || []); }

  connectedCallback() {
    if (this._booted) return;
    this._booted = true;
    this.style.cssText = 'position:absolute;inset:0;display:block';
    this._canvas = document.createElement('canvas');
    this._canvas.style.cssText = 'display:block;width:100%;height:100%';
    this.appendChild(this._canvas);
    this._onPointer = e => {
      const r = this.getBoundingClientRect();
      this._ptr.tx = ((e.clientX - r.left) / r.width - 0.5) * 2;
      this._ptr.ty = ((e.clientY - r.top) / r.height - 0.5) * 2;
    };
    addEventListener('pointermove', this._onPointer, { passive: true });
    this._onVis = () => { this._hidden = document.hidden; };
    document.addEventListener('visibilitychange', this._onVis);
    this._boot();
  }

  disconnectedCallback() { this._dead = true; this._dispose(); }

  _tier() {
    const q = this.getAttribute('quality') || 'auto';
    if (q === 'low' || q === 'high') return q;
    const cores = navigator.hardwareConcurrency || 8;
    return (innerWidth < 760 || cores <= 4) ? 'low' : 'high';
  }

  async _boot() {
    const low = this._tier() === 'low';
    const sd = parseInt(this.getAttribute('seed') || '', 10);
    const rnd = this._rnd = mulberry32(Number.isFinite(sd) ? sd : 20260820);
    const base = (this.getAttribute('tex-base') || '/textures/').replace(/\/?$/, '/');

    const renderer = this._renderer = new THREE.WebGLRenderer({
      canvas: this._canvas, antialias: !low, powerPreference: 'high-performance', stencil: false
    });
    renderer.setPixelRatio(Math.min(devicePixelRatio || 1, low ? 1 : 1.75));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1;
    /* ombres portées : à midi c'est ce qui donne l'échelle de l'infrastructure.
       Rien ne les projette dans l'espace (les corps ont castShadow = false), donc
       la passe est vide pendant toute la première moitié. */
    renderer.shadowMap.enabled = !low;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    const scene = this._scene = new THREE.Scene();
    /* un seul objet Fog pour les deux mondes : on repousse near/far dans l'espace
       plutôt que de le décrocher, ce qui éviterait une recompilation de shaders */
    const fog = this._fog = new THREE.Fog(0x0a0d14, 1e6, 2e6);
    scene.fog = fog;

    const cam = this._cam = new THREE.PerspectiveCamera(38, this._aspect(), 1, 60000);
    scene.add(cam);

    /* — textures — */
    const load = new THREE.TextureLoader();
    const tex = (file, srgb) => {
      const t = load.load(base + file);
      t.colorSpace = srgb ? THREE.SRGBColorSpace : THREE.NoColorSpace;
      t.anisotropy = low ? 2 : 8;
      this._disposables.push(t);
      return t;
    };
    const dayT = tex('earth_day.jpg', true), nightT = tex('earth_night.jpg', true);
    const cloudT = tex('earth_clouds.jpg', false), nrmT = tex('earth_normal.jpg', false);
    const specT = tex('earth_spec.jpg', false), moonT = tex('moon.jpg', true);
    const starT = tex('milkyway.jpg', true);
    starT.mapping = THREE.EquirectangularReflectionMapping;
    this._starBg = starT;
    if (this._dead) return;

    /* — monde spatial — */
    const space = this._space = new THREE.Group();
    scene.add(space);
    scene.background = starT;
    scene.backgroundIntensity = 1;
    if (scene.backgroundRotation) scene.backgroundRotation.set(0, 1.15, -0.06);

    const sun = this._sun = new THREE.DirectionalLight(0xfff2e0, 3.4);
    const sunTarget = this._sunTarget = new THREE.Object3D();
    sunTarget.position.set(0, 0, SITE_Z + 400);
    scene.add(sunTarget);
    sun.target = sunTarget;
    sun.castShadow = !low;
    sun.shadow.mapSize.set(2048, 2048);
    const sc0 = sun.shadow.camera;
    sc0.left = -4800; sc0.right = 4800; sc0.top = 4800; sc0.bottom = -4800;
    sc0.near = 200; sc0.far = 30000;
    sun.shadow.bias = -0.0004;
    sun.shadow.normalBias = 26;
    scene.add(sun);
    scene.add(new THREE.AmbientLight(0x232b3d, 0.16));
    /* ciel/sol : à midi l'indirect vient du ciel et rebondit sur l'herbe */
    const hemi = this._hemi = new THREE.HemisphereLight(0xa9c6e8, 0x4c4636, 0);
    scene.add(hemi);

    const earthU = this._earthU = {
      uDay: { value: dayT }, uNight: { value: nightT }, uClouds: { value: cloudT },
      uNrm: { value: nrmT }, uSpec: { value: specT },
      uSun: { value: new THREE.Vector3(0, 0, 1) },
      uAtmoCol: { value: new THREE.Color(0x5c8fd6) },
      uHazeCol: { value: new THREE.Color(0x9fb0c4) },
      uCloudSpin: { value: 0 }, uNightMix: { value: 1 }, uAtmo: { value: 0.34 }, uHaze: { value: 0 }
    };
    const earth = new THREE.Mesh(
      new THREE.SphereGeometry(R, low ? 72 : 128, low ? 36 : 64),
      new THREE.ShaderMaterial({ uniforms: earthU, vertexShader: VERT_WORLD, fragmentShader: FRAG_EARTH })
    );
    space.add(earth);
    this._earth = earth;

    const atmoU = this._atmoU = {
      uCol: { value: new THREE.Color(0x6ea8ff) }, uSun: earthU.uSun, uAmt: { value: 0.34 }
    };
    space.add(new THREE.Mesh(
      new THREE.SphereGeometry(R * 1.055, 64, 32),
      new THREE.ShaderMaterial({
        uniforms: atmoU, vertexShader: VERT_WORLD, fragmentShader: FRAG_ATMO,
        transparent: true, blending: THREE.AdditiveBlending, side: THREE.BackSide, depthWrite: false
      })
    ));

    const moon = this._moon = new THREE.Mesh(
      new THREE.SphereGeometry(MOON_R, low ? 40 : 72, low ? 20 : 36),
      new THREE.MeshStandardMaterial({ map: moonT, roughness: 0.96, metalness: 0 })
    );
    space.add(moon);

    /* étoiles ponctuelles : la jpg 4k n'en tient pas les pointes */
    const nStars = low ? 900 : 2400;
    const sp = new Float32Array(nStars * 3), sm = new Float32Array(nStars);
    for (let i = 0; i < nStars; i++) {
      const u = rnd() * 2 - 1, th = rnd() * Math.PI * 2, r2 = Math.sqrt(1 - u * u);
      sp[i * 3] = Math.cos(th) * r2 * 40000; sp[i * 3 + 1] = u * 40000; sp[i * 3 + 2] = Math.sin(th) * r2 * 40000;
      sm[i] = Math.pow(rnd(), 2.6);
    }
    const starGeo = new THREE.BufferGeometry();
    starGeo.setAttribute('position', new THREE.BufferAttribute(sp, 3));
    starGeo.setAttribute('aMag', new THREE.BufferAttribute(sm, 1));
    this._starU = { uPix: { value: renderer.getPixelRatio() }, uFade: { value: 1 }, uCol: { value: new THREE.Color(0xdfe7ff) } };
    space.add(new THREE.Points(starGeo, new THREE.ShaderMaterial({
      uniforms: this._starU, vertexShader: VERT_STAR, fragmentShader: FRAG_POINT,
      transparent: true, blending: THREE.AdditiveBlending, depthWrite: false
    })));

    /* — monde au sol — */
    const ground = this._ground = new THREE.Group();
    ground.visible = false;
    scene.add(ground);

    this._skyU = {
      uZen: { value: new THREE.Color(0x3d6ea9) }, uHor: { value: new THREE.Color(0xc2cdd7) },
      uSunCol: { value: new THREE.Color(0xfff3de) }, uSun: earthU.uSun
    };
    ground.add(new THREE.Mesh(
      new THREE.SphereGeometry(46000, 32, 16),
      new THREE.ShaderMaterial({
        uniforms: this._skyU, vertexShader: VERT_WORLD, fragmentShader: FRAG_SKY,
        side: THREE.BackSide, depthWrite: false
      })
    ));

    /* — la plaine — deux matières fournies, mélangées par bruit ; la terre gagne
         sur l'esplanade du site et le long de la route d'accès. */
    const grassC = tex('grass_color.jpg', true), grassR = tex('grass_rough.jpg', false);
    const soilC = tex('soil_color.jpg', true), soilR = tex('soil_rough.jpg', false);
    const dispT = tex('terrain_disp.jpg', false);
    [grassC, grassR, soilC, soilR, dispT].forEach(t => {
      t.wrapS = t.wrapT = THREE.RepeatWrapping;
    });

    this._grdU = {
      uGrassC: { value: grassC }, uGrassR: { value: grassR },
      uSoilC: { value: soilC }, uSoilR: { value: soilR }, uDisp: { value: dispT },
      uCurve: { value: CURVE }, uHills: { value: 70 }, uTile: { value: 1 / 17 },
      uReveal: { value: 0 }, uSiteZ: { value: SITE_Z },
      uSiteZFlat: { value: SITE_Z }
    };
    const terrainMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.95, metalness: 0 });
    terrainMat.onBeforeCompile = sh => {
      Object.assign(sh.uniforms, this._grdU);
      sh.vertexShader = 'uniform float uCurve, uHills, uSiteZFlat;\nuniform sampler2D uDisp;\n'
        + 'varying vec3 vWp;\nvarying float vRad;\n'
        + sh.vertexShader.replace('#include <begin_vertex>', `#include <begin_vertex>
        /* le plan est couché : local xy = plan au sol, local z = altitude */
        vRad = length(transformed.xy);
        float away = smoothstep(2600.0, 6400.0, length(vec2(transformed.x, transformed.y + uSiteZFlat)));
        transformed.z += (texture2D(uDisp, transformed.xy * 0.000035).r - 0.5) * uHills * away;
        transformed.z -= (vRad * vRad) / uCurve;
        vWp = (modelMatrix * vec4(transformed, 1.0)).xyz;`);
      sh.fragmentShader = 'uniform sampler2D uGrassC, uGrassR, uSoilC, uSoilR;\n'
        + 'uniform float uTile, uReveal, uSiteZ;\nvarying vec3 vWp;\nvarying float vRad;\n'
        + 'float h21(vec2 p){ return fract(sin(dot(p, vec2(41.3, 289.1))) * 43758.5453); }\n'
        + 'float vn(vec2 p){ vec2 i = floor(p), f = fract(p); f = f * f * (3.0 - 2.0 * f);\n'
        + '  return mix(mix(h21(i), h21(i + vec2(1.0, 0.0)), f.x),\n'
        + '             mix(h21(i + vec2(0.0, 1.0)), h21(i + vec2(1.0, 1.0)), f.x), f.y); }\n'
        + sh.fragmentShader
          .replace('#include <map_fragment>', `
        vec2 tA = vWp.xz * uTile, tB = vWp.xz * (uTile * 0.17);
        float macro = vn(vWp.xz * 0.00035) * 0.62 + vn(vWp.xz * 0.0012) * 0.38;
        float dSite = length(vec2(vWp.x, vWp.z - uSiteZ));
        float apron = 1.0 - smoothstep(1900.0, 3100.0, dSite);
        float road = (1.0 - smoothstep(150.0, 250.0, abs(vWp.x)))
                   * step(uSiteZ + 300.0, vWp.z) * step(vWp.z, 1200.0);
        float soil = clamp(smoothstep(0.44, 0.74, macro) + apron * 0.92 + road, 0.0, 1.0);
        /* deux fréquences de la même matière : la basse casse la répétition du tuilage */
        vec3 gCol = texture2D(uGrassC, tA).rgb * (0.72 + texture2D(uGrassC, tB).g * 1.05);
        vec3 sCol = texture2D(uSoilC, tA).rgb * (0.95 + texture2D(uSoilC, tB).g * 1.05);
        vec3 albedo = mix(gCol, sCol, soil) * 1.30 + 0.018;
        float mark = (1.0 - smoothstep(5.0, 14.0, abs(abs(vWp.x) - 198.0)))
                   * step(uSiteZ + 400.0, vWp.z) * step(vWp.z, 900.0);
        albedo = mix(albedo, vec3(0.74, 0.75, 0.72), mark * 0.85 * uReveal);
        diffuseColor.rgb *= albedo;`)
          .replace('#include <roughnessmap_fragment>', `
        float roughnessFactor = clamp(roughness * (0.62
          + mix(texture2D(uGrassR, tA).g, texture2D(uSoilR, tA).g, soil) * 0.72), 0.74, 1.0);`);
    };
    terrainMat.customProgramCacheKey = () => 'orbital-terrain';
    const plate = new THREE.Mesh(
      new THREE.PlaneGeometry(SITE_EXT * 2, SITE_EXT * 2, low ? 96 : 180, low ? 96 : 180),
      terrainMat
    );
    plate.rotation.x = -Math.PI / 2;
    plate.receiveShadow = true;
    ground.add(plate);

    this._buildSite(ground, low);
    this._buildDeck(ground, low);

    /* traînées de vitesse, enfant de la caméra */
    const nStr = low ? 380 : 900;
    const stp = new Float32Array(nStr * 3);
    for (let i = 0; i < nStr; i++) {
      const a = rnd() * Math.PI * 2, r2 = 60 + Math.pow(rnd(), 0.6) * 620;
      stp[i * 3] = Math.cos(a) * r2; stp[i * 3 + 1] = Math.sin(a) * r2; stp[i * 3 + 2] = -rnd() * 1800;
    }
    const strGeo = new THREE.BufferGeometry();
    strGeo.setAttribute('position', new THREE.BufferAttribute(stp, 3));
    this._strU = {
      uTime: { value: 0 }, uAmt: { value: 0 }, uSpeed: { value: 900 },
      uPix: { value: renderer.getPixelRatio() }, uCol: { value: new THREE.Color(0xbcd2f2) }
    };
    const streaks = this._streaks = new THREE.Points(strGeo, new THREE.ShaderMaterial({
      uniforms: this._strU, vertexShader: VERT_STREAK, fragmentShader: FRAG_POINT,
      transparent: true, blending: THREE.AdditiveBlending, depthWrite: false
    }));
    streaks.frustumCulled = false;
    streaks.visible = false;
    cam.add(streaks);

    /* — post — */
    const size = this._size();
    const composer = this._composer = new EffectComposer(renderer);
    composer.setSize(size.w, size.h);
    composer.addPass(new RenderPass(scene, cam));
    const bloom = this._bloom = new UnrealBloomPass(
      new THREE.Vector2(Math.max(size.w * 0.5, 128), Math.max(size.h * 0.5, 128)), 0.55, 0.85, 0.72
    );
    if (!low) composer.addPass(bloom);
    this._finalU = {
      tDiffuse: { value: null },
      uVeilCol: { value: new THREE.Color(0xe7ecf1) }, uFlashCol: { value: new THREE.Color(0xdfe9ff) },
      uExposure: { value: 0.34 }, uVignette: { value: 0.86 }, uGrain: { value: 0.022 },
      uAberr: { value: 0.1 }, uSmear: { value: 0 }, uVeil: { value: 0 }, uFlash: { value: 0 },
      uFade: { value: 1 }, uTime: { value: 0 }
    };
    /* ShaderPass clone les uniforms qu'on lui passe : on récupère la référence
       *du pass*, sinon la boucle écrit dans un objet que personne ne lit. */
    const finalPass = new ShaderPass({
      uniforms: this._finalU, vertexShader: VERT_QUAD, fragmentShader: FRAG_FINAL
    });
    this._finalU = finalPass.uniforms;
    composer.addPass(finalPass);
    composer.addPass(new OutputPass());

    /* — resize — */
    this._ro = new ResizeObserver(() => this._resize());
    this._ro.observe(this);
    this._resize();

    /* — la boucle : une seule, pour la cinématique comme pour l'interactif — */
    this._clock = new THREE.Clock();
    const tick = () => {
      if (this._dead) return;
      this._raf = requestAnimationFrame(tick);
      const dt = Math.min(this._clock.getDelta(), 0.1);
      this._t = (this._t || 0) + dt;
      if (this._hidden) return;
      this._apply(dt);
      composer.render();
      this._project();
    };
    tick();
    this.dispatchEvent(new CustomEvent('orbital:ready'));
  }

  /* ── l'infrastructure ─────────────────────────────────────────────────────
     Une seule grande installation posée dans la plaine : socle, atrium de verre,
     deux ailes en oblique, tour d'antenne, anneau de service, coupole radar.
     Une vingtaine de meshes — le sujet est unique, l'instancing ne sert plus qu'aux
     blocs techniques et aux mâts de la couronne.

     Le soleil est derrière : les faces tournées vers la caméra sont à l'ombre, et
     c'est le halo du ciel qui détache la silhouette. Sans les ombres portées sur
     l'herbe, la structure flotterait.                                          */
  _buildSite(ground, low) {
    const rnd = this._rnd;
    const Z = SITE_Z, BASE = 110;
    const site = this._site = new THREE.Group();
    ground.add(site);

    const concrete = new THREE.MeshStandardMaterial({ color: 0x9b9d9f, roughness: 0.93, metalness: 0.04 });
    const shell = new THREE.MeshStandardMaterial({ color: 0x73777c, roughness: 0.70, metalness: 0.24 });
    const metal = new THREE.MeshStandardMaterial({ color: 0xb4b9bf, roughness: 0.36, metalness: 0.80 });
    const glass = new THREE.MeshStandardMaterial({ color: 0x22303e, roughness: 0.10, metalness: 0.90 });

    /* bandes d'étage et meneaux tracés en espace monde : la même trame court sur
       toutes les surfaces vitrées quelle que soit leur taille. */
    this._winU = { value: 0 };
    glass.onBeforeCompile = sh => {
      sh.uniforms.uWin = this._winU;
      sh.uniforms.uWinCol = { value: new THREE.Color(0xcfe6ff) };
      sh.vertexShader = 'varying vec3 vGp;\n' + sh.vertexShader.replace('#include <begin_vertex>',
        '#include <begin_vertex>\n vGp = (modelMatrix * vec4(transformed, 1.0)).xyz;');
      sh.fragmentShader = 'uniform float uWin;\nuniform vec3 uWinCol;\nvarying vec3 vGp;\n'
        + sh.fragmentShader.replace('#include <dithering_fragment>', `
        float floors = 1.0 - smoothstep(0.05, 0.15, abs(fract(vGp.y / 44.0) - 0.5));
        float mull = 1.0 - smoothstep(0.04, 0.13, abs(fract((vGp.x + vGp.z) / 68.0) - 0.5));
        float sharp = 1.0 - smoothstep(0.18, 0.75, fwidth(vGp.y / 44.0));
        gl_FragColor.rgb += uWinCol * (floors * 0.55 + mull * 0.22) * sharp * uWin;
        #include <dithering_fragment>`);
    };
    glass.customProgramCacheKey = () => 'orbital-glass';

    const box = (w, h, d, x, y, z, mat, ry) => {
      const me = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), mat);
      me.position.set(x, y, z);
      if (ry) me.rotation.y = ry;
      me.castShadow = true;
      me.receiveShadow = true;
      site.add(me);
      return me;
    };

    /* socle */
    box(6600, BASE, 2800, 0, BASE * 0.5, Z + 200, concrete);
    box(7400, 26, 3400, 0, 13, Z + 200, concrete);

    /* atrium de verre, plein cadre face à la caméra */
    box(1760, 580, 840, 0, BASE + 290, Z + 540, glass);
    box(1920, 74, 980, 0, BASE + 615, Z + 540, metal);
    box(120, 640, 120, -820, BASE + 320, Z + 940, shell);
    box(120, 640, 120, 820, BASE + 320, Z + 940, shell);

    /* corps principal, derrière l'atrium */
    box(3100, 360, 640, 0, BASE + 180, Z - 140, shell);
    box(2500, 96, 720, 0, BASE + 408, Z - 140, concrete);

    /* deux ailes en oblique + hélisurface au bout */
    [-1, 1].forEach(sgn => {
      box(2300, 310, 540, sgn * 1780, BASE + 155, Z + 900, shell, sgn * 0.40);
      box(2080, 130, 430, sgn * 1820, BASE + 345, Z + 930, glass, sgn * 0.40);
      const pad = new THREE.Mesh(new THREE.CylinderGeometry(215, 215, 18, 28), concrete);
      pad.position.set(sgn * 2520, BASE + 420, Z + 1320);
      pad.castShadow = true;
      pad.receiveShadow = true;
      site.add(pad);
      const ringPad = new THREE.Mesh(new THREE.TorusGeometry(150, 9, 6, 40), metal);
      ringPad.rotation.x = Math.PI / 2;
      ringPad.position.set(sgn * 2520, BASE + 432, Z + 1320);
      site.add(ringPad);
    });

    /* tour d'antenne, légèrement décalée : le soleil passe à côté, pas derrière */
    box(340, 1580, 340, -300, BASE + 790, Z - 1040, shell);
    box(190, 1420, 190, -300, BASE + 710, Z - 1040, glass);
    box(560, 96, 560, -300, BASE + 1628, Z - 1040, metal);
    box(28, 460, 28, -300, BASE + 1900, Z - 1040, metal);

    /* anneau de service au-dessus du socle */
    const ring = new THREE.Mesh(new THREE.TorusGeometry(1560, 24, 8, 104), metal);
    ring.rotation.x = Math.PI / 2;
    ring.position.set(0, BASE + 740, Z + 260);
    ring.castShadow = true;
    site.add(ring);
    [-1, 1].forEach(sgn => box(46, 740, 46, sgn * 1560, BASE + 370, Z + 260, metal));

    /* coupole radar sur son fût */
    const dish = new THREE.Mesh(
      new THREE.SphereGeometry(270, 28, 14, 0, Math.PI * 2, 0, Math.PI * 0.34), metal);
    dish.position.set(2150, BASE + 470, Z - 700);
    dish.rotation.set(-0.72, 0.42, 0);
    dish.castShadow = true;
    site.add(dish);
    box(96, 470, 96, 2150, BASE + 235, Z - 700, shell);
    box(34, 1400, 34, 2620, BASE + 700, Z - 700, metal);
    box(150, 40, 150, 2620, BASE + 1410, Z - 700, metal);

    /* portique d'accès, à l'entrée de la route */
    box(760, 130, 96, 0, BASE + 300, Z + 1500, metal);
    box(80, 300, 80, -340, BASE + 150, Z + 1500, shell);
    box(80, 300, 80, 340, BASE + 150, Z + 1500, shell);

    /* blocs techniques : couronne instanciée, corridor de caméra dégagé */
    const bN = low ? 30 : 64;
    const bGeo = new THREE.BoxGeometry(1, 1, 1);
    const blocks = new THREE.InstancedMesh(bGeo, concrete, bN);
    const m = new THREE.Matrix4(), q = new THREE.Quaternion();
    const pos = new THREE.Vector3(), sc = new THREE.Vector3(), ax = new THREE.Vector3(0, 1, 0);
    let i = 0, guard = 0;
    while (i < bN && guard++ < bN * 30) {
      const ang = rnd() * Math.PI * 2, rad = 3600 + Math.pow(rnd(), 0.7) * 5400;
      const x = Math.cos(ang) * rad, z = Z + Math.sin(ang) * rad * 0.72;
      if (Math.abs(x) < 1200 && z > Z + 900) continue;      // ne pas boucher la route
      const w = 130 + rnd() * 430, h = 70 + Math.pow(rnd(), 2.0) * 290;
      q.setFromAxisAngle(ax, rnd() * 1.2);
      blocks.setMatrixAt(i, m.compose(pos.set(x, h * 0.5, z), q, sc.set(w, h, w * lerp(0.6, 1.5, rnd()))));
      i++;
    }
    blocks.count = i;
    blocks.castShadow = true;
    blocks.receiveShadow = true;
    blocks.frustumCulled = false;
    site.add(blocks);

    /* mâts : hauts, fins, alignés le long de la route d'accès */
    const mN = low ? 8 : 14;
    const mGeo = new THREE.BoxGeometry(1, 1, 1);
    const masts = new THREE.InstancedMesh(mGeo, metal, mN);
    for (let k = 0; k < mN; k++) {
      const sgn = k % 2 ? 1 : -1, step = Math.floor(k / 2);
      const h = 560 - step * 30;
      masts.setMatrixAt(k, m.compose(
        pos.set(sgn * 800, h * 0.5, Z + 1500 + step * 900), q.identity(), sc.set(20, h, 20)));
    }
    masts.castShadow = true;
    masts.frustumCulled = false;
    site.add(masts);

    /* colonnes de brume au ras du socle : le volume sans volumétrique */
    const beamAt = [[-2600, Z + 700], [2600, Z + 700], [-1500, Z - 1500], [1500, Z - 1500],
                    [-3400, Z - 400], [3400, Z - 400], [0, Z - 2100], [-4400, Z + 900],
                    [4400, Z + 900], [700, Z - 2600]].slice(0, low ? 6 : 10);
    const bmGeo = new THREE.CylinderGeometry(150, 340, 1700, 14, 1, true);
    this._beamU = { uCol: { value: new THREE.Color(0xdce9ff) }, uAmt: { value: 0 } };
    const beams = new THREE.InstancedMesh(bmGeo, new THREE.ShaderMaterial({
      uniforms: this._beamU, vertexShader: VERT_INST_UV, fragmentShader: FRAG_BEAM,
      transparent: true, blending: THREE.AdditiveBlending, depthWrite: false, side: THREE.DoubleSide
    }), beamAt.length);
    const bs = new Float32Array(beamAt.length);
    beamAt.forEach(([x, z], k) => {
      beams.setMatrixAt(k, m.compose(pos.set(x, 700, z), q.identity(), sc.set(1, 1, 1)));
      bs[k] = rnd();
    });
    bmGeo.setAttribute('aSeed', new THREE.InstancedBufferAttribute(bs, 1));
    beams.frustumCulled = false;
    site.add(beams);

    /* signalétique holographique, dressée sur l'esplanade face à la caméra */
    const holoN = low ? 5 : 8;
    const hGeo = new THREE.PlaneGeometry(1, 1);
    this._holoU = { uCol: { value: new THREE.Color(0x9184d9) }, uAmt: { value: 0 }, uTime: { value: 0 } };
    const holos = new THREE.InstancedMesh(hGeo, new THREE.ShaderMaterial({
      uniforms: this._holoU, vertexShader: VERT_INST_UV, fragmentShader: FRAG_HOLO,
      transparent: true, blending: THREE.AdditiveBlending, depthWrite: false, side: THREE.DoubleSide
    }), holoN);
    const hs = new Float32Array(holoN);
    for (let k = 0; k < holoN; k++) {
      const sgn = k % 2 ? 1 : -1, step = Math.floor(k / 2);
      const w = 260 + rnd() * 200, h = w * lerp(1.2, 1.8, rnd());
      holos.setMatrixAt(k, m.compose(
        pos.set(sgn * (760 + step * 620), h * 0.5 + 60, Z + 1750 + step * 240),
        q.setFromAxisAngle(ax, sgn * 0.22), sc.set(w, h, 1)));
      hs[k] = rnd();
    }
    hGeo.setAttribute('aSeed', new THREE.InstancedBufferAttribute(hs, 1));
    holos.frustumCulled = false;
    site.add(holos);

    /* trafic aérien — position calculée dans le vertex shader */
    const tN = low ? 110 : 220;
    const tp = new Float32Array(tN * 3), lane = new Float32Array(tN), ph = new Float32Array(tN);
    for (let k = 0; k < tN; k++) { lane[k] = Math.floor(rnd() * 14); ph[k] = rnd(); }
    const tGeo = new THREE.BufferGeometry();
    tGeo.setAttribute('position', new THREE.BufferAttribute(tp, 3));
    tGeo.setAttribute('aLane', new THREE.BufferAttribute(lane, 1));
    tGeo.setAttribute('aPhase', new THREE.BufferAttribute(ph, 1));
    this._trafU = {
      uTime: { value: 0 }, uAmt: { value: 0 }, uZOff: { value: Z + 600 },
      uPix: { value: this._renderer.getPixelRatio() }, uCol: { value: new THREE.Color(0xf0f6ff) }
    };
    const traffic = new THREE.Points(tGeo, new THREE.ShaderMaterial({
      uniforms: this._trafU, vertexShader: VERT_TRAFFIC, fragmentShader: FRAG_POINT,
      transparent: true, blending: THREE.AdditiveBlending, depthWrite: false
    }));
    traffic.frustumCulled = false;
    site.add(traffic);

    /* ancres des points d'intérêt : quatre repères de la structure, écartés du
       centre du cadre pour ne pas se poser sur le titre — sommet de tour, les deux
       accès au ras du socle, coupole. */
    this._anchors = [
        new THREE.Vector3(-50, -125, Z - 1040),
        new THREE.Vector3(-1500, BASE + 500, Z + 1500),
        new THREE.Vector3(1500, BASE + 500, Z + 1500),
        new THREE.Vector3(-325, BASE + 1850, Z - 700)
    ];
  }

  _buildDeck(ground, low) {
    const rnd = this._rnd;
    const N = low ? 110 : 250;
    const geo = new THREE.PlaneGeometry(1, 1);
    this._deckU = {
      uAmt: { value: 0 }, uTime: { value: 0 }, uLight: { value: 2.4 }, uNear: { value: 4 },
      uFloor: { value: DECK_LOW },
      uCol: { value: new THREE.Color(0xb6c1ce) }, uLitCol: { value: new THREE.Color(0xfbfdff) }
    };
    const deck = new THREE.InstancedMesh(geo, new THREE.ShaderMaterial({
      uniforms: this._deckU, vertexShader: VERT_CLOUD, fragmentShader: FRAG_CLOUD,
      transparent: true, depthWrite: false
    }), N);
    const m = new THREE.Matrix4(), q = new THREE.Quaternion(), p = new THREE.Vector3(), s = new THREE.Vector3();
    const seeds = new Float32Array(N);
    for (let i = 0; i < N; i++) {
      const a = rnd() * Math.PI * 2, rad = Math.pow(rnd(), 0.6) * 16000;
      const sc = 1100 + Math.pow(rnd(), 1.6) * 2400;
      p.set(Math.cos(a) * rad, lerp(DECK_LOW, DECK_TOP, rnd()), Math.sin(a) * rad);
      deck.setMatrixAt(i, m.compose(p, q, s.set(sc, sc, sc)));
      seeds[i] = rnd();
    }
    geo.setAttribute('aSeed', new THREE.InstancedBufferAttribute(seeds, 1));
    deck.frustumCulled = false;
    ground.add(deck);
    this._deck = deck;
  }

  /* ── résolution de l'état → scène ─────────────────────────────────────────
     Aucune allocation ici : la boucle tourne sur des vecteurs réutilisés.      */
  _apply(dt) {
    const s = this._s, cam = this._cam, u = this._finalU;
    const inGround = s.world >= 0.5;

    if (this._ground.visible !== inGround) {
      this._ground.visible = inGround;
      this._space.visible = !inGround;
      this._scene.background = inGround ? null : this._starBg;
      if (inGround) { this._fog.near = 5200; this._fog.far = 34000; this._fog.color.copy(this._skyU.uHor.value); }
      else { this._fog.near = 1e6; this._fog.far = 2e6; }
    }

    /* soleil : une seule direction pour l'ombrage, le rim, le ciel et le glint.
       La cible bascule avec le monde — à l'origine dans l'espace (la Terre y est),
       sur le site au sol (c'est ce que la caméra d'ombre doit cadrer). */
    const ce = Math.cos(s.sunEl), se = Math.sin(s.sunEl);
    const sd = this._earthU.uSun.value.set(ce * Math.sin(s.sunAz), se, ce * Math.cos(s.sunAz)).normalize();
    const tz = inGround ? SITE_Z + 400 : 0;
    if (this._sunTarget.position.z !== tz) {
      this._sunTarget.position.z = tz;
      this._sunTarget.updateMatrixWorld();
    }
    this._sun.position.set(sd.x * 12000, sd.y * 12000, tz + sd.z * 12000);
    this._sun.intensity = inGround ? 3.1 : 3.4;
    this._sun.color.setHex(inGround ? 0xfff6ea : 0xfff2e0);
    this._hemi.intensity = s.ambient;

    if (!inGround) {
      const d = Math.max(s.dist, R * 1.005);
      const cel = Math.cos(s.elev), sel = Math.sin(s.elev);
      cam.position.set(d * cel * Math.sin(s.azim), d * sel, d * cel * Math.cos(s.azim));
      cam.up.set(0, 1, 0);
      cam.lookAt(0, 0, 0);
      /* recadrage en espace écran : indépendant de la distance, donc le sujet
         garde sa place dans le cadre pendant tout le rapprochement */
      const hy = THREE.MathUtils.degToRad(s.fov) * 0.5;
      cam.rotateY(s.offX * hy * cam.aspect);
      cam.rotateX(-s.offY * hy);
      if (s.roll) cam.rotateZ(s.roll);
      cam.near = Math.max(0.5, d * 0.008);
      cam.far = Math.max(d * 2.6, 56000);
      this._moon.position.set(
        Math.sin(s.moonPhase) * MOON_ORBIT,
        s.moonRise * MOON_ORBIT,
        Math.cos(s.moonPhase) * MOON_ORBIT
      );
      this._moon.rotation.y = s.moonPhase - 1.2;
      this._earth.rotation.y = 2.35 + this._t * 0.004;
      this._earthU.uCloudSpin.value = s.cloudSpin;
      this._earthU.uNightMix.value = s.nightMix;
      this._earthU.uAtmo.value = s.atmo;
      this._earthU.uHaze.value = s.haze;
      this._atmoU.uAmt.value = s.atmo;
      this._starU.uFade.value = s.starFade;
      this._scene.backgroundIntensity = s.starFade * 4.5;
    } else {
      let yaw = s.yaw, pitch = s.pitch, alt = s.alt, fwd = s.fwd, sway = s.sway;
      if (this._free) {
        /* interactif : le cadrage final tient, la souris ne fait que respirer */
        const p = this._ptr;
        p.x += (p.tx - p.x) * Math.min(1, dt * 3.2);
        p.y += (p.ty - p.y) * Math.min(1, dt * 3.2);
        yaw += -p.x * 0.055 + Math.sin(this._t * 0.07) * 0.006;
        pitch += -p.y * 0.028 + Math.sin(this._t * 0.05 + 1.4) * 0.004;
        sway += p.x * 26;
        alt += p.y * -14;
      }
      cam.rotation.order = 'YXZ';
      cam.position.set(sway, alt, fwd);
      cam.rotation.set(pitch, yaw, s.roll);
      cam.near = 4;
      cam.far = 90000;
      this._grdU.uReveal.value = s.reveal;
      this._winU.value = s.windows;
      this._beamU.uAmt.value = s.beams;
      this._holoU.uAmt.value = s.holo;
      this._holoU.uTime.value = this._t;
      this._trafU.uAmt.value = s.traffic;
      this._trafU.uTime.value = this._t;
      this._deckU.uNear.value = cam.near;
    }

    this._deckU.uAmt.value = s.deck;
    this._deckU.uTime.value = this._t;
    if (cam.fov !== s.fov) cam.fov = s.fov;
    cam.updateProjectionMatrix();

    const st = this._streaks;
    st.visible = s.streaks > 0.004;
    if (st.visible) { this._strU.uAmt.value = s.streaks; this._strU.uTime.value = this._t; }

    if (this._bloom) this._bloom.strength = s.bloom;
    u.uExposure.value = s.exposure;
    u.uVignette.value = s.vignette;
    u.uGrain.value = s.grain;
    u.uAberr.value = s.aberr;
    u.uSmear.value = s.smear;
    u.uVeil.value = s.veil;
    u.uFlash.value = s.flash;
    u.uFade.value = s.fade;
    u.uTime.value = this._t;
  }

  /* projection des points d'intérêt — quatre projections par frame */
  _project() {
    const pages = this._pages;
    if (!pages.length || !this._anchors) return;
    const cam = this._cam, v = this._pv || (this._pv = new THREE.Vector3());
    const size = this._size();
    for (let i = 0; i < pages.length; i++) {
      const el = pages[i], a = this._anchors[i % this._anchors.length];
      if (!el || !a) continue;
      v.copy(a).project(cam);
      const on = this._free && v.z > -1 && v.z < 1 && Math.abs(v.x) < 1.05 && Math.abs(v.y) < 1.05;
      if (!on) { if (el.style.opacity !== '0') { el.style.opacity = '0'; el.style.pointerEvents = 'none'; } continue; }
      el.style.transform = 'translate3d(' + ((v.x * 0.5 + 0.5) * size.w).toFixed(1) + 'px,'
        + ((-v.y * 0.5 + 0.5) * size.h).toFixed(1) + 'px,0) translate(-50%,-50%)';
      if (el.style.opacity !== '1') { el.style.opacity = '1'; el.style.pointerEvents = 'auto'; }
    }
  }

  _size() {
    const r = this.getBoundingClientRect();
    return { w: Math.max(2, r.width | 0), h: Math.max(2, r.height | 0) };
  }
  _aspect() { const s = this._size(); return s.w / s.h; }

  _resize() {
    if (!this._renderer) return;
    const { w, h } = this._size();
    this._cam.aspect = w / h;
    this._cam.updateProjectionMatrix();
    this._renderer.setSize(w, h, false);
    this._composer.setSize(w, h);
    if (this._bloom) this._bloom.resolution.set(Math.max(w * 0.5, 128), Math.max(h * 0.5, 128));
    const pr = this._renderer.getPixelRatio();
    this._starU.uPix.value = pr; this._strU.uPix.value = pr; this._trafU.uPix.value = pr;
  }

  _dispose() {
    cancelAnimationFrame(this._raf);
    removeEventListener('pointermove', this._onPointer);
    document.removeEventListener('visibilitychange', this._onVis);
    if (this._ro) this._ro.disconnect();
    if (this._scene) {
      this._scene.traverse(o => {
        if (o.geometry) o.geometry.dispose();
        const m = o.material;
        if (m) (Array.isArray(m) ? m : [m]).forEach(x => x.dispose());
      });
      this._scene.background = null;
    }
    this._disposables.forEach(t => t.dispose());
    this._disposables.length = 0;
    if (this._composer) this._composer.dispose();
    if (this._renderer) { this._renderer.dispose(); this._renderer.forceContextLoss(); }
    this._renderer = null;
  }
}

if (!customElements.get('orbital-stage')) customElements.define('orbital-stage', OrbitalStage);
export { OrbitalStage, SHOT };
