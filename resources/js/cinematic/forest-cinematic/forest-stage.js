/* forest-stage.js — <forest-stage> : décor forestier (dirt_road_forest.glb) piloté
   en externe par une timeline Theatre.js, sur le modèle de <black-hole-stage>.

   Three.js possède tout ce qui touche au rendu (renderer, ciel, scène, chargement du
   modèle, lumières, ombres, bloom, RAF, teardown). La timeline externe pousse la mise
   en scène via host.setShot({…}) — pas de rendu déclenché par setShot lui-même : les
   valeurs sont lues par la boucle déjà en place. host.shot() relit l'état courant.

   Deux choses ont changé par rapport à la passe précédente.

   1. LA CAMÉRA SUIT LA ROUTE. Le chemin de terre du décor est échantillonné ci-dessous
      (ROAD, relevé sur les maillages Dirt_Road du GLB, avant → fond de vallée), monté
      en courbe Catmull-Rom et reparamétré en longueur d'arc. La timeline ne pousse plus
      six coordonnées libres mais un point kilométrique : `s` (0…1 le long de la route),
      `lift` (hauteur au-dessus du sol), `side` (déport latéral), `ahead` (distance du
      point visé, en avant sur la route), `aim` (relèvement du regard). Impossible de
      sortir du décor : la caméra est accrochée au ruban.

   2. MIDI, PAS D'AUBE. Plus de brouillard porteur d'ambiance : le ciel est un dégradé
      de plein jour (horizon pâle → zénith bleu profond), le soleil est haut (`sunEl`
      ~70°) et blanc, les ombres sont portées par une shadow map qui suit la caméra, et
      il ne reste du brouillard qu'une perspective aérienne (`air`) qui blanchit les
      collines lointaines. Le bloom est résiduel — le soleil n'est presque jamais dans
      le champ à midi.

   Pas de gestion de fondu/flash ici : ces transitions restent en DOM (mêmes crochets
   [data-wc="flash|curtain"] que la page trou noir), au-dessus du canvas. */

import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { MeshoptDecoder } from 'three/examples/jsm/libs/meshopt_decoder.module.js';
import { EffectComposer } from 'three/examples/jsm/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/examples/jsm/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/examples/jsm/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/examples/jsm/postprocessing/OutputPass.js';
import { disposeScene, disposeRenderer } from '../../three/core/dispose.js';

const DEG = Math.PI / 180;
const NOON = new THREE.Color(1.0, 0.97, 0.92);   /* lumière zénithale, à peine chaude */
const WARM = new THREE.Color(1.0, 0.87, 0.68);   /* fin d'après-midi, si sunWarmth → 1 */

/* Axe de la route : axe médian des maillages Dirt_Road du GLB, relevé hors ligne
   (rastérisation du ruban en grille de 2 unités, transformée de distance, plus court
   chemin par la crête), puis altitude reprise sur les sommets de la chaussée.
   [x, y(sol), z], du premier plan (Z ≈ +76, sol ≈ 173) vers le fond de la vallée
   (X ≈ -87, sol ≈ 133) : 361 unités de développé, 39 de dénivelé — ça descend.

   `side` déporte la caméra dans la largeur du chemin ; positif = vers la gauche. */
/*const ROAD = [
  [-26.6, 172.6, 75.8], [-16.7, 172.9, 68.3], [-6.2, 173.1, 61.6], [4.4, 173.2, 55.1],
  [15.0, 173.2, 48.5], [26.4, 173.2, 43.5], [38.6, 173.0, 41.4], [51.1, 172.3, 41.0],
  [63.5, 170.8, 40.5], [75.5, 168.8, 37.2], [83.9, 166.4, 28.4], [84.9, 164.2, 16.2],
  [80.7, 162.1, 4.5], [75.1, 159.8, -6.7], [69.3, 157.5, -17.7], [61.8, 155.0, -27.6],
  [51.1, 152.3, -33.5], [38.9, 150.1, -31.7], [28.2, 148.3, -25.4], [17.5, 146.9, -19.1],
  [5.3, 145.5, -18.2], [-5.3, 144.3, -24.5], [-13.6, 142.9, -33.8], [-21.2, 141.7, -43.7],
  [-29.4, 140.3, -53.0], [-38.8, 138.7, -61.2], [-49.7, 136.9, -67.2], [-61.8, 135.4, -70.2],
  [-74.2, 134.4, -71.0], [-86.6, 133.5, -71.5]
];*/

const ROAD = [
    [-86.6, 133.5, -71.5],
    [-74.2, 134.4, -71.0],
    [-61.8, 135.4, -70.2],
    [-49.7, 136.9, -67.2],
    [-38.8, 138.7, -61.2],
    [-29.4, 140.3, -53.0],
    [-21.2, 141.7, -43.7],
    [-13.6, 142.9, -33.8],
    [-5.3, 144.3, -24.5],
    [5.3, 145.5, -18.2],
    [17.5, 146.9, -19.1],
    [28.2, 148.3, -25.4],
    [38.9, 150.1, -31.7],
    [51.1, 152.3, -33.5],
    [61.8, 155.0, -27.6],
    [69.3, 157.5, -17.7],
    [75.1, 159.8, -6.7],
    [80.7, 162.1, 4.5],
    [84.9, 164.2, 16.2],
    [83.9, 166.4, 28.4],
    [75.5, 168.8, 37.2],
    [63.5, 170.8, 40.5],
    [51.1, 172.3, 41.0],
    [38.6, 173.0, 41.4],
    [26.4, 173.2, 43.5],
    [15.0, 173.2, 48.5],
    [4.4, 173.2, 55.1],
    [-6.2, 173.1, 61.6],
    [-16.7, 172.9, 68.3],
    [-26.6, 172.6, 75.8]
];

/* garde-fous : au-delà, le diorama se voit comme un objet posé sur rien */
const BOUNDS = { x: [-190, 330], y: [130, 250], z: [-320, 190] };
const clamp = (v, [lo, hi]) => v < lo ? lo : v > hi ? hi : v;
const clamp01 = v => v < 0 ? 0 : v > 1 ? 1 : v;

const SKY_VERT = `
  varying vec3 vDir;
  void main() {
    vDir = (modelMatrix * vec4(position, 1.0)).xyz - cameraPosition;
    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
  }`;

/* Ciel de midi : bleu profond au zénith, pâle sur l'horizon (uHaze règle ce
   blanchiment), et un soleil réduit à un disque net plus deux lobes très serrés —
   à midi la couronne est petite, c'est le ciel entier qui est lumineux. */
const SKY_FRAG = `
  precision highp float;
  varying vec3 vDir;
  uniform vec3 uHorizon, uZenith, uSun, uSunDir;
  uniform float uGlow, uHaze;
  void main() {
    vec3 d = normalize(vDir);
    float up = clamp(d.y, 0.0, 1.0);
    vec3 col = mix(uHorizon, uZenith, pow(up, 0.42));
    col = mix(col, uHorizon, uHaze * pow(1.0 - up, 6.0));
    float ca = max(dot(d, uSunDir), 0.0);
    float lobes = pow(ca, 7000.0) * 9.0 + pow(ca, 320.0) * 0.34 + pow(ca, 26.0) * 0.05;
    col += uSun * lobes * uGlow;
    gl_FragColor = vec4(col, 1.0);
  }`;

class ForestStage extends HTMLElement {
  connectedCallback() {
    this._dead = false;
    if (this._booted) return;
    this._booted = true;
    Object.assign(this.style, { display: 'block', position: 'absolute', inset: '0', width: '100%', height: '100%', overflow: 'hidden' });
    this._canvas = document.createElement('canvas');
    Object.assign(this._canvas.style, { position: 'absolute', inset: '0', width: '100%', height: '100%', display: 'block' });
    this.appendChild(this._canvas);
    try { this._run(); } catch (e) {
      console.error('[forest-stage]', e);
      this.dispatchEvent(new CustomEvent('fs-error', { detail: String(e), bubbles: true }));
    }
  }
  disconnectedCallback() { this._dead = true; this._dispose && this._dispose(); }

  attr(name) { return this.getAttribute(name); }

  _run() {
    const host = this;
    const canvas = this._canvas;
    const src = this.attr('src') || '/GLB/dirt_road_forest.glb';
    const wantShadows = this.attr('shadows') !== 'off';

    /* ── la route, reparamétrée en longueur d'arc ─────────────────────────── */
    const curve = new THREE.CatmullRomCurve3(ROAD.map(p => new THREE.Vector3(p[0], p[1], p[2])), false, 'catmullrom', 0.5);
    const LUT = curve.getSpacedPoints(720);
    let ROAD_LEN = 0;
    for (let i = 1; i < LUT.length; i++) ROAD_LEN += LUT[i].distanceTo(LUT[i - 1]);
    const SEG = ROAD_LEN / (LUT.length - 1);

    /* position à l'abscisse u (0…1). Au-delà des extrémités on prolonge la tangente
       plutôt que de coller au dernier point : le point visé peut dépasser la fin. */
    const roadAt = (u, out) => {
      const f = u * (LUT.length - 1);
      if (f <= 0) return out.copy(LUT[0]).addScaledVector(_t0.subVectors(LUT[1], LUT[0]).normalize(), f * SEG);
      if (f >= LUT.length - 1) {
        const n = LUT.length - 1;
        return out.copy(LUT[n]).addScaledVector(_t0.subVectors(LUT[n], LUT[n - 1]).normalize(), (f - n) * SEG);
      }
      const i = Math.floor(f);
      return out.lerpVectors(LUT[i], LUT[i + 1], f - i);
    };
    const roadDir = (u, out) => {
      const i = Math.max(0, Math.min(LUT.length - 2, Math.floor(clamp01(u) * (LUT.length - 1))));
      return out.subVectors(LUT[i + 1], LUT[i]).setY(0).normalize();
    };
    const _t0 = new THREE.Vector3();

    /* Défauts = la frame 0 de la timeline : le temps que le modèle charge, l'écran
       doit déjà être un plein midi, pas un état neutre qui « saute ». */
    const SHOT = {
      s: 0.02, lift: 3.4, side: 0, ahead: 26, aim: 0.5, fov: 46, roll: 0, drift: 0.4,
      sunAz: 300, sunEl: 70, sunIntensity: 4.4, sunWarmth: 0.12,
      hemiIntensity: 1.85, exposure: 1.02,
      skyDepth: 1, haze: 0.26, glow: 1, air: 0.22, bloom: 0.16
    };
    host.setShot = o => { if (o) Object.assign(SHOT, o); };
    host.shot = () => Object.assign({}, SHOT);
    host.road = { length: ROAD_LEN, at: u => roadAt(u, new THREE.Vector3()) };
    host.ready = false;

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(devicePixelRatio || 1, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = SHOT.exposure;
    if (wantShadows) { renderer.shadowMap.enabled = true; renderer.shadowMap.type = THREE.PCFSoftShadowMap; }

    const scene = new THREE.Scene();
    /* il reste du brouillard, mais c'est de la perspective aérienne : sa couleur est
       celle de l'horizon, et `air` le pousse loin — pas de « fog » de vallée. */
    const fog = new THREE.Fog(0xc6d6e6, 700, 2700);
    scene.fog = fog;

    const camera = new THREE.PerspectiveCamera(SHOT.fov, 2, 0.5, 4000);

    const sky = new THREE.Mesh(
      new THREE.SphereGeometry(2600, 48, 24),
      new THREE.ShaderMaterial({
        vertexShader: SKY_VERT, fragmentShader: SKY_FRAG, side: THREE.BackSide,
        depthWrite: false, fog: false,
        uniforms: {
          uHorizon: { value: new THREE.Color(0.62, 0.76, 0.93) },
          uZenith: { value: new THREE.Color(0.10, 0.30, 0.76) },
          uSun: { value: new THREE.Color(1, 1, 1) },
          uSunDir: { value: new THREE.Vector3(0, 1, 0) },
          uGlow: { value: SHOT.glow }, uHaze: { value: SHOT.haze }
        }
      })
    );
    sky.frustumCulled = false;
    scene.add(sky);

    const hemi = new THREE.HemisphereLight(0xa8c8ff, 0x6b5a3e, SHOT.hemiIntensity);
    scene.add(hemi);
    const sun = new THREE.DirectionalLight(0xffffff, SHOT.sunIntensity);
    scene.add(sun);
    scene.add(sun.target);
    if (wantShadows) {
      sun.castShadow = true;
      sun.shadow.mapSize.set(2048, 2048);
      sun.shadow.camera.near = 20; sun.shadow.camera.far = 620;
      sun.shadow.camera.left = -105; sun.shadow.camera.right = 105;
      sun.shadow.camera.top = 105; sun.shadow.camera.bottom = -105;
      sun.shadow.bias = -0.0004; sun.shadow.normalBias = 1.1;
    }
    /* rebond du sol : à midi la lumière du sol remonte dans les sous-bois. */
    const bounce = new THREE.DirectionalLight(0xd9c9a6, 0.52);
    bounce.position.set(-60, -100, 40);
    scene.add(bounce);

    const loader = new GLTFLoader();
    loader.setMeshoptDecoder(MeshoptDecoder);
    loader.load(src, gltf => {
      if (host._dead) return;
      const seen = new Set();
      gltf.scene.traverse(o => {
        if (!o.isMesh) return;
        if (wantShadows) { o.castShadow = true; o.receiveShadow = true; }
        const mats = Array.isArray(o.material) ? o.material : [o.material];
        for (const m of mats) {
          if (!m || seen.has(m.uuid)) continue;
          seen.add(m.uuid);
          /* le feuillage est en plans découpés : sans alphaTest l'ombre portée est
             un rectangle noir, et le tri alpha bave sur toute la canopée. */
          if (m.transparent || m.alphaMap) { m.transparent = false; m.alphaTest = 0.5; m.depthWrite = true; }
          else if (m.map && m.alphaTest === 0) m.alphaTest = 0.35;
          m.shadowSide = THREE.DoubleSide;
          m.needsUpdate = true;
        }
      });
      scene.add(gltf.scene);
      host.ready = true;
      host.dispatchEvent(new CustomEvent('fs-ready', { bubbles: true }));
    }, undefined, err => {
      console.error('[forest-stage] load failed', err);
      host.dispatchEvent(new CustomEvent('fs-error', { detail: String(err), bubbles: true }));
    });

    const composer = new EffectComposer(renderer);
    composer.addPass(new RenderPass(scene, camera));
    const bloom = new UnrealBloomPass(new THREE.Vector2(2, 2), SHOT.bloom, 0.7, 0.86);
    composer.addPass(bloom);
    composer.addPass(new OutputPass());

    let W = 2, H = 2;
    function resize() {
      const r = host.getBoundingClientRect();
      W = Math.max(2, r.width | 0); H = Math.max(2, r.height | 0);
      renderer.setSize(W, H, false);
      composer.setSize(W, H);
      bloom.setSize(W, H);
      camera.aspect = W / H;
      camera.updateProjectionMatrix();
    }
    const ro = new ResizeObserver(resize); ro.observe(host); resize();

    const here = new THREE.Vector3(), fwd = new THREE.Vector3(), side = new THREE.Vector3();
    const lookTarget = new THREE.Vector3(), sunDir = new THREE.Vector3();
    const sunColor = new THREE.Color(), horizon = new THREE.Color(), zenith = new THREE.Color();
    const SKY_LOW = new THREE.Color(0.62, 0.76, 0.93), SKY_HIGH = new THREE.Color(0.10, 0.30, 0.76);
    const u = sky.material.uniforms;
    let raf = 0, visible = true, t0 = performance.now();
    const onVis = () => { visible = !document.hidden; };
    document.addEventListener('visibilitychange', onVis);

    function frame() {
      raf = requestAnimationFrame(frame);
      if (!visible) return;
      const t = (performance.now() - t0) / 1000;
      const d = SHOT.drift;

      /* ── caméra sur le ruban ───────────────────────────────────────────── */
      const uu = clamp01(SHOT.s);
      roadAt(uu, here);
      roadDir(uu, fwd);
      side.set(fwd.z, 0, -fwd.x);
      camera.position.set(
        clamp(here.x + side.x * SHOT.side + Math.sin(t * 0.9) * 0.5 * d, BOUNDS.x),
        clamp(here.y + SHOT.lift + Math.sin(t * 0.62 + 1.7) * 0.4 * d, BOUNDS.y),
        clamp(here.z + side.z * SHOT.side + Math.cos(t * 0.75 + 0.6) * 0.5 * d, BOUNDS.z)
      );
      roadAt(uu + SHOT.ahead / ROAD_LEN, lookTarget);
      lookTarget.y += SHOT.aim;
      lookTarget.x += Math.cos(t * 0.41) * 1.3 * d;
      lookTarget.y += Math.sin(t * 0.33 + 2.1) * 0.9 * d;
      lookTarget.z += Math.sin(t * 0.47 + 1.1) * 1.3 * d;
      const roll = SHOT.roll + Math.sin(t * 0.29) * 0.004 * d;
      camera.up.set(Math.sin(roll), Math.cos(roll), 0);
      camera.lookAt(lookTarget);
      if (Math.abs(camera.fov - SHOT.fov) > 1e-3) { camera.fov = SHOT.fov; camera.updateProjectionMatrix(); }
      sky.position.copy(camera.position);

      /* ── midi ──────────────────────────────────────────────────────────── */
      const el = SHOT.sunEl * DEG, az = SHOT.sunAz * DEG;
      sunDir.set(Math.cos(el) * Math.sin(az), Math.sin(el), Math.cos(el) * Math.cos(az));
      sunColor.lerpColors(NOON, WARM, clamp01(SHOT.sunWarmth));

      sun.target.position.copy(lookTarget);
      sun.target.updateMatrixWorld();
      sun.position.copy(sunDir).multiplyScalar(300).add(lookTarget);
      sun.intensity = SHOT.sunIntensity;
      sun.color.copy(sunColor);
      hemi.intensity = SHOT.hemiIntensity;

      const air = clamp01(SHOT.air);
      horizon.copy(SKY_LOW).lerp(sunColor, 0.18 * clamp01(SHOT.haze));
      zenith.lerpColors(horizon, SKY_HIGH, clamp01(SHOT.skyDepth));
      fog.color.copy(horizon);
      /* `air` n'est pas un brouillard : à 0,25 la portée dépasse le décor entier et
         il ne reste rien ; il faut le pousser haut pour voiler les collines du fond. */
      fog.near = 150 + (1 - air) * 700;
      fog.far = 700 + (1 - air) * 2600;

      u.uHorizon.value.copy(horizon);
      u.uZenith.value.copy(zenith);
      u.uSun.value.copy(sunColor);
      u.uSunDir.value.copy(sunDir);
      u.uGlow.value = SHOT.glow;
      u.uHaze.value = clamp01(SHOT.haze);

      bloom.strength = SHOT.bloom;
      renderer.toneMappingExposure = SHOT.exposure;

      composer.render();
    }
    frame();

    this._dispose = () => {
      cancelAnimationFrame(raf); raf = 0;
      ro.disconnect();
      document.removeEventListener('visibilitychange', onVis);
      composer.dispose && composer.dispose();
      /* le ciel (sphère + ShaderMaterial) et le GLB chargé (Dirt_Road, arbres,
         etc.) vivent tous les deux dans `scene` : un seul traverse suffit à
         libérer leurs géométries/matériaux — auparavant jamais disposés ici,
         contrairement à home-stage.js qui fait déjà ce nettoyage. */
      disposeScene(scene);
      disposeRenderer(renderer);
      host.setShot = host.shot = null;
    };
  }
}

if (!customElements.get('forest-stage')) customElements.define('forest-stage', ForestStage);

export { ForestStage, ROAD };
