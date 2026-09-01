/* <carousel-field> — le fond WebGL du Carousel Lab.
   Même contrat que <black-hole-stage> / <orbital-stage> : cet élément possède
   TOUT ce qui est Three.js (un renderer, une scène, une boucle, le teardown)
   et rien du temps. La page pousse des valeurs par setField(), aussi souvent
   qu'elle veut :

     host.setField({ pos, drag, energy, focus:[x,y], warp, dim })

   Objets partiels, fusion sans allocation, aucune RAF supplémentaire — les
   valeurs sont lues par la boucle déjà en place. host.field() les relit.

   Attributs : quality (auto|high|low), motion (full|reduce)
   Événements : cf-ready, cf-error */
import * as THREE from 'three';

/* Le fond entier tient dans ce fragment : nappe, réticule à trois anneaux, tirets
   angulaires, aberration au drag, vignette, grain. Un seul shader, comme le three-lab. */
const FRAG = `
  precision highp float;
  varying vec2 vUv;
  uniform vec2  uRes;
  uniform float uTime;
  uniform float uPos;
  uniform float uDrag;
  uniform float uEnergy;
  uniform float uWarp;
  uniform float uDim;
  uniform vec2  uFocus;
  uniform vec3  uAccent;

  float ign(vec2 p){ return fract(52.9829189 * fract(0.06711056*p.x + 0.00583715*p.y)); }
  float ring(float r, float rad, float w){ return smoothstep(w, 0.0, abs(r - rad)); }

  void main(){
    vec2 asp = vec2(uRes.x / uRes.y, 1.0);
    vec2 p = (vUv - 0.5) * asp;
    vec2 f = (uFocus - 0.5) * asp;
    vec2 d = p - f;
    d.x += uDrag * 0.05 * uWarp;
    float r = length(d * vec2(1.0, 1.26));

    vec3 col = vec3(0.020, 0.020, 0.039);
    col += vec3(0.052, 0.048, 0.098) * exp(-r * 1.45);
    col += uAccent * exp(-r * 2.7) * (0.14 + 0.12 * uEnergy);

    float w = 0.005 + 0.011 * abs(uDrag);
    float rr  = ring(r, 0.29 + 0.012 * sin(uTime * 0.55) + 0.045 * uEnergy, w) * 0.85;
          rr += ring(r, 0.53 + 0.025 * uDrag, w * 0.8) * 0.42;
          rr += ring(r, 0.88 - 0.030 * uEnergy, w * 0.7) * 0.20;
    col += uAccent * rr * (0.30 + 0.45 * uEnergy);

    float ang = atan(d.y, d.x) / 6.2831853;
    float tick = step(0.88, abs(fract(ang * 24.0 + uPos * 0.5) * 2.0 - 1.0));
    col += uAccent * tick * ring(r, 0.53 + 0.025 * uDrag, 0.018) * 0.55;

    float ab = uDrag * 0.022 * uWarp;
    col.r += uAccent.r * exp(-length((d - vec2(ab, 0.0)) * vec2(1.0, 1.26)) * 2.7) * 0.11 * abs(uDrag);
    col.b += uAccent.b * exp(-length((d + vec2(ab, 0.0)) * vec2(1.0, 1.26)) * 2.7) * 0.11 * abs(uDrag);

    col *= 1.0 - uDim * 0.62;
    col *= 1.0 - 0.52 * smoothstep(0.34, 1.06, length(p));
    col += (ign(vUv * uRes) - 0.5) * 0.019;

    gl_FragColor = vec4(col, 1.0);
  }`;

const VERT = `
  varying vec2 vUv;
  void main(){ vUv = uv; gl_Position = vec4(position.xy, 0.0, 1.0); }`;

class CarouselField extends HTMLElement {
  constructor() {
    super();
    this._f = { pos: 0, drag: 0, energy: 0, warp: 1, dim: 0, focus: [0.5, 0.46] };
    this._alive = false;
  }

  /* ------------------------------------------------------------- API -- */
  setField(o) {
    if (!o) return;
    const f = this._f;
    if (o.pos !== undefined) f.pos = o.pos;
    if (o.drag !== undefined) f.drag = o.drag;
    if (o.energy !== undefined) f.energy = o.energy;
    if (o.warp !== undefined) f.warp = o.warp;
    if (o.dim !== undefined) f.dim = o.dim;
    if (o.focus) { f.focus[0] = o.focus[0]; f.focus[1] = o.focus[1]; }
  }
  field() { return this._f; }

  /* -------------------------------------------------------- montage -- */
  connectedCallback() {
    if (this._canvas) return;
    if (getComputedStyle(this).position === 'static') {
      this.style.position = 'absolute';
      this.style.inset = '0';
      this.style.display = 'block';
      this.style.overflow = 'hidden';
    }
    this._canvas = document.createElement('canvas');
    this._canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;display:block';
    this.appendChild(this._canvas);
    this._alive = true;
    try {
      this._run();
    } catch (e) {
      console.error('[carousel-field]', e);
      this.dispatchEvent(new CustomEvent('cf-error', { detail: String(e), bubbles: true }));
    }
  }

  disconnectedCallback() {
    this._alive = false;
    if (this._ro) this._ro.disconnect();
    if (this._raf) cancelAnimationFrame(this._raf);
    if (this._dust) { this._dust.geometry.dispose(); this._dust.material.dispose(); }
    if (this._quad) { this._quad.geometry.dispose(); this._quad.material.dispose(); }
    if (this._renderer) { this._renderer.dispose(); this._renderer.forceContextLoss?.(); }
    this._renderer = this._scene = this._quad = this._dust = null;
    if (this._canvas) { this._canvas.remove(); this._canvas = null; }
  }

  _run() {
    if (!this._alive) return;

    const low = this.getAttribute('quality') === 'low' ||
      (this.getAttribute('quality') !== 'high' && (matchMedia('(max-width:760px)').matches || navigator.hardwareConcurrency < 5));
    const reduce = this.getAttribute('motion') === 'reduce';

    const renderer = new THREE.WebGLRenderer({ canvas: this._canvas, antialias: false, alpha: false, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(devicePixelRatio || 1, low ? 1.25 : 2));
    this._renderer = renderer;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(52, 1, 0.1, 120);
    camera.position.set(0, 0, 6);
    this._scene = scene;

    /* fond : un quad en espace d'écran, jamais testé en profondeur */
    const uni = {
      uRes: { value: new THREE.Vector2(1, 1) },
      uTime: { value: 0 },
      uPos: { value: 0 },
      uDrag: { value: 0 },
      uEnergy: { value: 0 },
      uWarp: { value: 1 },
      uDim: { value: 0 },
      uFocus: { value: new THREE.Vector2(0.5, 0.46) },
      uAccent: { value: new THREE.Color(0x9184d9) }
    };
    const quad = new THREE.Mesh(
      new THREE.PlaneGeometry(2, 2),
      new THREE.ShaderMaterial({ vertexShader: VERT, fragmentShader: FRAG, uniforms: uni, depthTest: false, depthWrite: false })
    );
    quad.frustumCulled = false;
    quad.renderOrder = -1;
    scene.add(quad);
    this._quad = quad;
    this._uni = uni;

    /* poussière : un seul Points, parallaxé par pos et par drag */
    const N = low ? 700 : 1700;
    const pos = new Float32Array(N * 3);
    const seed = new Float32Array(N);
    for (let i = 0; i < N; i++) {
      pos[i * 3] = (Math.random() - 0.5) * 34;
      pos[i * 3 + 1] = (Math.random() - 0.5) * 17;
      pos[i * 3 + 2] = -Math.random() * 26 + 3;
      seed[i] = Math.random();
    }
    const g = new THREE.BufferGeometry();
    g.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const dust = new THREE.Points(g, new THREE.PointsMaterial({
      color: 0xb3a9e6, size: low ? 0.055 : 0.042, sizeAttenuation: true,
      transparent: true, opacity: 0.55, blending: THREE.AdditiveBlending, depthWrite: false
    }));
    scene.add(dust);
    this._dust = dust;
    this._seed = seed;

    const resize = () => {
      const w = this.clientWidth, h = this.clientHeight;
      if (!w || !h) return;                 // layout transitoire : ne pas figer le buffer
      renderer.setSize(w, h, false);
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      uni.uRes.value.set(w * renderer.getPixelRatio(), h * renderer.getPixelRatio());
    };
    resize();
    this._ro = new ResizeObserver(resize);
    this._ro.observe(this);

    const t0 = performance.now();
    let sDrag = 0, sEnergy = 0;
    const frame = () => {
      if (!this._alive) return;
      this._raf = requestAnimationFrame(frame);
      const f = this._f;
      const t = (performance.now() - t0) / 1000;

      sDrag += (f.drag - sDrag) * 0.14;
      sEnergy += (f.energy - sEnergy) * 0.09;

      uni.uTime.value = t;
      uni.uPos.value = f.pos;
      uni.uDrag.value = sDrag;
      uni.uEnergy.value = sEnergy;
      uni.uWarp.value = f.warp;
      uni.uDim.value = f.dim;
      uni.uFocus.value.set(f.focus[0], f.focus[1]);

      dust.position.x = -f.pos * 0.62 - sDrag * 0.9;
      dust.position.y = Math.sin(t * 0.13) * 0.28;
      dust.rotation.y = t * 0.006 + sDrag * 0.035;
      dust.rotation.z = sDrag * 0.012;

      camera.position.x = sDrag * 0.30;
      camera.position.y = -sEnergy * 0.06;
      camera.rotation.z = -sDrag * 0.018;
      camera.lookAt(sDrag * 0.5, 0, -4);

      renderer.render(scene, camera);
      if (reduce) { cancelAnimationFrame(this._raf); this._raf = null; }
    };
    frame();
    this.dispatchEvent(new CustomEvent('cf-ready', { bubbles: true }));
  }
}

if (!customElements.get('carousel-field')) customElements.define('carousel-field', CarouselField);
