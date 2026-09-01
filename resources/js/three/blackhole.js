/* welcome-blackhole.js — <black-hole-stage> for the Laravel/Vite welcome page.
   Three.js + GSAP + GLSL. Camera sits at the event horizon; navigation objects are
   static anchors resolved once in world space.

   Events on the element (bubble to document):
     bh-state    {phase, progress, capture, armed, hover:{name,url,x,y}|null}
     bh-navigate {name, url}
     bh-reveal
     bh-home     {url}

   drive="external" — the element keeps everything that is Three.js (renderer, scene,
   shaders, post chain, resize, RAF, teardown) and hands the *staging* to an outside
   timeline: internal scroll/touch/key input, the progress→camera solve and the GSAP
   capture timeline are all switched off. The outside timeline calls
   host.setShot({ dist, azim, elev, fov, roll, parallax, lens, diskBright, diskThick,
   diskMul, spin, bgFade, capture, objDim, bloom, vignette, aberr, grain, exposure, fade })
   as often as it likes; host.shot() reads the current values back. Used by the
   Theatre.js-driven /welcome-cinematic page (welcome-cinematic.js). */

import * as THREE from 'three';
import { gsap } from 'gsap';

import NOISE from '../shaders/blackhole/lib/noise.glsl';
import BH_FRAG from '../shaders/blackhole/blackhole.frag';
import FS_VERT from '../shaders/blackhole/fullscreen.vert';
import BRIGHT_FRAG from '../shaders/blackhole/bright.frag';
import BLUR_FRAG from '../shaders/blackhole/blur.frag';
import COMP_FRAG from '../shaders/blackhole/composite.frag';
import OBJ_VERT from '../shaders/blackhole/object.vert';
import OBJ_FRAG from '../shaders/blackhole/object.frag';
import GLOW_VERT from '../shaders/blackhole/glow.vert';
import GLOW_FRAG from '../shaders/blackhole/glow.frag';

const TIERS = {
  high:   { steps: 190, scale: 1.0,  dpr: 2.0,  bloom: 1 },
  medium: { steps: 128, scale: 0.82, dpr: 1.6,  bloom: 1 },
  low:    { steps: 76,  scale: 0.66, dpr: 1.25, bloom: 0 }
};

const PALETTES = {
  bone:   [[1.00, 0.985, 0.96], [1.00, 0.855, 0.60], [0.86, 0.52, 0.26]],
  violet: [[0.98, 0.97, 1.00], [0.78, 0.72, 0.98], [0.40, 0.38, 0.72]],
  ember:  [[1.00, 0.98, 0.93], [1.00, 0.60, 0.24], [0.62, 0.22, 0.16]],
  steel:  [[0.98, 0.99, 1.00], [0.80, 0.88, 0.98], [0.40, 0.52, 0.70]]
};

class BlackHoleStage extends HTMLElement {
  static get observedAttributes() { return ['disk-palette', 'diskpalette', 'duration', 'quality']; }
  attributeChangedCallback(n, o, v) { if (this._apply && o !== v) this._apply(n.replace(/-/g, ''), v); }
  /* a framework-driven mount may set camelCase props as lowercased attributes,
     so the kebab spelling never arrives — read both */
  attr(name) { const v = this.getAttribute(name); return v === null ? this.getAttribute(name.replace(/-/g, '')) : v; }

  connectedCallback() {
    this._dead = false;
    if (this._booted) return;
    this._booted = true;
    Object.assign(this.style, { display: 'block', position: 'absolute', inset: '0', width: '100%', height: '100%', overflow: 'hidden', touchAction: 'none' });
    this._canvas = document.createElement('canvas');
    Object.assign(this._canvas.style, { position: 'absolute', inset: '0', width: '100%', height: '100%', display: 'block' });
    this.appendChild(this._canvas);
    try { this._run(); } catch (e) {
      console.error('[black-hole-stage]', e);
      this.dispatchEvent(new CustomEvent('bh-error', { detail: String(e), bubbles: true }));
    }
  }
  disconnectedCallback() { this._dead = true; this._dispose && this._dispose(); }

  _run() {
    const host = this;
    const canvas = this._canvas;

    let pages = [];
    try { pages = JSON.parse(this.attr('pages') || '[]'); } catch (e) { pages = []; }
    const homeUrl = this.attr('home-url') || '';
    const travelSeconds = parseFloat(this.attr('duration')) || 15;

    /* drive="external": keep every bit of Three.js here (renderer, scene, shaders,
       post chain, resize, RAF, teardown) and hand the staging to an outside timeline.
       Scroll/touch/key input, the progress→camera solve and the GSAP capture timeline
       switch off; the timeline writes through host.setShot(). */
    const external = (this.attr('drive') || 'internal') === 'external';
    /* the staging values an outside timeline writes. The defaults are a DARK frame,
       not the internal journey's opening shot: several seconds pass between this
       element booting and the timeline seeding position 0, and whatever sits in
       these fields is on screen for all of it. fade:0 keeps the canvas black until
       the timeline fades it in. */
    const SHOT = {
      dist: 320, azim: 0.30, elev: 0.34, fov: 54, roll: 0, parallax: 1,
      lens: 0.82, diskBright: 0.10, diskThick: 0.55, diskMul: 1.0, spin: 0.9,
      bgFade: 0.10, capture: 0, objDim: 1,
      bloom: 1.1, vignette: 0.92, aberr: 0.25, grain: 0.020, exposure: 0.06, fade: 0
    };
    host.setShot = o => { if (o) Object.assign(SHOT, o); };
    host.shot = () => Object.assign({}, SHOT);

    const touch = matchMedia('(hover: none)').matches;
    let tier = this.attr('quality') || 'auto';
    if (tier === 'auto') {
      const cores = navigator.hardwareConcurrency || 4;
      const small = Math.min(screen.width, screen.height) < 760 || touch;
      tier = (small || cores <= 4) ? 'low' : (cores <= 8 ? 'medium' : 'high');
    }
    const Q = Object.assign({}, TIERS[tier] || TIERS.high);

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: false, alpha: false, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(devicePixelRatio || 1, Q.dpr));
    renderer.outputColorSpace = THREE.LinearSRGBColorSpace;
    renderer.autoClear = false;

    const fsGeo = new THREE.PlaneGeometry(2, 2);
    const fsCam = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    const fsScene = new THREE.Scene();
    const fsQuad = new THREE.Mesh(fsGeo, null);
    fsQuad.frustumCulled = false;
    fsScene.add(fsQuad);
    const blit = (mat, target) => { fsQuad.material = mat; renderer.setRenderTarget(target || null); renderer.clear(); renderer.render(fsScene, fsCam); };

    const rtOpts = { type: THREE.HalfFloatType, depthBuffer: true, colorSpace: THREE.LinearSRGBColorSpace, minFilter: THREE.LinearFilter, magFilter: THREE.LinearFilter };
    const rtScene = new THREE.WebGLRenderTarget(2, 2, rtOpts);
    const rtA = new THREE.WebGLRenderTarget(2, 2, { ...rtOpts, depthBuffer: false });
    const rtB = new THREE.WebGLRenderTarget(2, 2, { ...rtOpts, depthBuffer: false });
    const rtC = new THREE.WebGLRenderTarget(2, 2, { ...rtOpts, depthBuffer: false });
    const rtD = new THREE.WebGLRenderTarget(2, 2, { ...rtOpts, depthBuffer: false });

    const bhUni = {
      uRes: { value: new THREE.Vector2(2, 2) },
      uTime: { value: 0 },
      uCamPos: { value: new THREE.Vector3(0, 0, 13) },
      uCamBasis: { value: new THREE.Matrix3() },
      uTanHalfFov: { value: Math.tan(THREE.MathUtils.degToRad(62) * 0.5) },
      uDiskBright: { value: 0.62 },
      uDiskMul: { value: 1.0 },
      uDiskThick: { value: 0.62 },
      uLens: { value: 0.82 },
      uBgFade: { value: 1.0 },
      uCapture: { value: 0 },
      uSpin: { value: 1.0 },
      uEscape: { value: 140 },
      uHot: { value: new THREE.Color(0.98, 0.955, 0.90) },
      uMid: { value: new THREE.Color(1.0, 0.80, 0.53) },
      uCool: { value: new THREE.Color(0.58, 0.53, 0.79) }
    };
    const bhSource = steps => '#define STEPS ' + steps + '\n' + NOISE + '\n' + BH_FRAG;
    const bhMat = new THREE.ShaderMaterial({
      vertexShader: FS_VERT, fragmentShader: bhSource(Q.steps),
      uniforms: bhUni, depthTest: false, depthWrite: false
    });

    const brightMat = new THREE.ShaderMaterial({ vertexShader: FS_VERT, fragmentShader: BRIGHT_FRAG, uniforms: { tSrc: { value: null }, uThresh: { value: 0.55 } }, depthTest: false });
    const blurMat = new THREE.ShaderMaterial({ vertexShader: FS_VERT, fragmentShader: BLUR_FRAG, uniforms: { tSrc: { value: null }, uDir: { value: new THREE.Vector2() } }, depthTest: false });
    const compUni = {
      tScene: { value: rtScene.texture }, tBloomA: { value: rtB.texture }, tBloomB: { value: rtD.texture },
      uBloom: { value: Q.bloom ? 1.85 : 0.85 }, uVignette: { value: 0.74 }, uAberr: { value: 0.55 },
      uGrain: { value: 0.010 }, uTime: { value: 0 }, uFade: { value: 0 }, uExposure: { value: 0.80 },
      uRes: { value: new THREE.Vector2(2, 2) }
    };
    const compMat = new THREE.ShaderMaterial({ vertexShader: FS_VERT, fragmentShader: COMP_FRAG, uniforms: compUni, depthTest: false });

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(62, 1, 0.02, 900);
    const refCam = new THREE.PerspectiveCamera(62, 1, 0.02, 900);

    /* base viewpoint: just outside the photon sphere, horizon filling the frame */
    const D0 = 13.0, AZ0 = 0.30, EL0 = 0.26, D_NEAR = 8.4;

    const PALETTE = [0xb9b2a2, 0x8a90a6, 0xa79375, 0x76809c, 0x9b8f7c];
    const bodies = [];
    const geoSphere = new THREE.IcosahedronGeometry(1, 3);
    const glowGeo = new THREE.PlaneGeometry(1, 1);

    function makeBody(cfg) {
      const mat = new THREE.ShaderMaterial({
        vertexShader: OBJ_VERT, fragmentShader: OBJ_FRAG,
        uniforms: { uColor: { value: new THREE.Color(cfg.color) }, uHi: { value: 0 }, uOccl: { value: 1 }, uDim: { value: 1 }, uTime: { value: 0 } }
      });
      const mesh = new THREE.Mesh(geoSphere, mat);
      mesh.scale.setScalar(cfg.size);
      const glowMat = new THREE.ShaderMaterial({
        vertexShader: GLOW_VERT, fragmentShader: GLOW_FRAG,
        uniforms: { uColor: { value: new THREE.Color(cfg.color) }, uHi: { value: 0 }, uOccl: { value: 1 }, uDim: { value: 1 } },
        transparent: true, blending: THREE.AdditiveBlending, depthWrite: false
      });
      const glow = new THREE.Mesh(glowGeo, glowMat);
      const group = new THREE.Group();
      group.add(mesh); group.add(glow);
      scene.add(group);
      const b = { ...cfg, group, mesh, glow, mat, glowMat, hi: 0, target: 0, pos: new THREE.Vector3(), baseScale: mesh.scale.clone(), baseGlow: 1 };
      bodies.push(b);
      return b;
    }

    /* navigation points — screen anchors resolved once, then frozen in world space.
       depths sit beyond the hole so they barely drift while the camera falls inward. */
    const ANCHORS = [
      { nx: -0.66, ny: 0.44, depth: 19.0 },
      { nx: 0.72, ny: -0.46, depth: 17.6 },
      { nx: 0.64, ny: 0.70, depth: 20.4 }
    ];
    pages.forEach((p, i) => {
      const a = ANCHORS[i % ANCHORS.length];
      const ring = Math.floor(i / ANCHORS.length);
      makeBody({
        page: true, name: p.name, url: p.url,
        nx: p.nx ?? a.nx * (ring ? 0.84 : 1),
        ny: p.ny ?? a.ny * (ring ? 0.58 : 1),
        depth: p.depth ?? (a.depth + ring * 2.4),
        size: p.size ?? 0.3,
        color: p.color ?? PALETTE[i % PALETTE.length]
      });
    });

    function layoutBodies() {
      const ce = Math.cos(EL0);
      refCam.position.set(Math.sin(AZ0) * ce * D0, Math.sin(EL0) * D0, Math.cos(AZ0) * ce * D0);
      refCam.lookAt(0, 0, 0);
      refCam.updateMatrixWorld();
      const fwd = new THREE.Vector3(0, 0, -1).applyQuaternion(refCam.quaternion);
      const right = new THREE.Vector3(1, 0, 0).applyQuaternion(refCam.quaternion);
      const up = new THREE.Vector3(0, 1, 0).applyQuaternion(refCam.quaternion);
      const th = bhUni.uTanHalfFov.value;
      const asp = Math.max(W / H, 0.62);
      for (const b of bodies) {
        const dep = b.depth;
        b.pos.copy(refCam.position)
          .addScaledVector(fwd, dep)
          .addScaledVector(right, b.nx * th * dep * asp)
          .addScaledVector(up, b.ny * th * dep);
        b.group.position.copy(b.pos);
        const s = dep * th * 0.088;
        b.baseScale.setScalar(s);
        b.mesh.scale.copy(b.baseScale);
        b.baseGlow = s * 5.2;
        b.glow.scale.setScalar(b.baseGlow);
      }
    }

    const S = { progress: 0, target: 0, capture: 0, phase: 'travel', azim: AZ0, mx: 0, my: 0, tmx: 0, tmy: 0, diskBoost: 0, dim: 1 };
    const LOCK = 0.78;
    let WHEEL_TOTAL = travelSeconds * 300;
    let wheelAcc = 0, armed = null, hovered = null;

    const dist = () => {
      const d = D0 * Math.pow(D_NEAR / D0, S.progress);
      return THREE.MathUtils.lerp(d, 1.06, Math.pow(S.capture, 2.6));
    };

    const emit = () => host.dispatchEvent(new CustomEvent('bh-state', {
      bubbles: true, detail: {
        phase: S.phase, progress: S.progress, capture: S.capture,
        armed: armed ? armed.name : null,
        hover: hovered ? { name: hovered.name, url: hovered.url, x: hovered.sx, y: hovered.sy } : null
      }
    }));

    if (external) S.phase = 'external';

    const setTarget = v => { S.target = Math.max(S.phase === 'travel' ? 0 : S.progress, Math.min(1, v)); };
    const onWheel = e => {
      if (S.phase !== 'travel') return;
      e.preventDefault();
      wheelAcc += e.deltaY;
      setTarget(wheelAcc / WHEEL_TOTAL);
      wheelAcc = S.target * WHEEL_TOTAL;
    };

    let touchY = null;
    const onTouchStart = e => { touchY = e.touches[0].clientY; };
    const onTouchMove = e => {
      if (touchY === null || S.phase !== 'travel') return;
      const dy = touchY - e.touches[0].clientY;
      touchY = e.touches[0].clientY;
      wheelAcc += dy * 2.4;
      setTarget(wheelAcc / WHEEL_TOTAL);
      wheelAcc = S.target * WHEEL_TOTAL;
    };
    const onKey = e => {
      if (S.phase !== 'travel') return;
      const step = WHEEL_TOTAL * 0.06;
      if (e.key === 'ArrowDown' || e.key === 'PageDown' || e.key === ' ') { wheelAcc += step; setTarget(wheelAcc / WHEEL_TOTAL); wheelAcc = S.target * WHEEL_TOTAL; }
      if (e.key === 'ArrowUp' || e.key === 'PageUp') { wheelAcc -= step; setTarget(wheelAcc / WHEEL_TOTAL); wheelAcc = S.target * WHEEL_TOTAL; }
    };
    if (!external) {
      host.addEventListener('wheel', onWheel, { passive: false });
      host.addEventListener('touchstart', onTouchStart, { passive: true });
      host.addEventListener('touchmove', onTouchMove, { passive: true });
      addEventListener('keydown', onKey);
    }

    const ray = new THREE.Raycaster();
    const ptr = new THREE.Vector2(-9, -9);
    let hasPointer = false;
    host.addEventListener('pointermove', e => {
      const r = host.getBoundingClientRect();
      ptr.set(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1);
      S.tmx = ((e.clientX - r.left) / r.width - 0.5) * 2;
      S.tmy = ((e.clientY - r.top) / r.height - 0.5) * 2;
      hasPointer = true;
    });
    host.addEventListener('pointerleave', () => { hasPointer = false; ptr.set(-9, -9); S.tmx = 0; S.tmy = 0; });
    host.addEventListener('pointerdown', e => {
      const r = host.getBoundingClientRect();
      ptr.set(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1);
      hasPointer = true;
      pick();
      if (!hovered) { if (touch) { armed = null; emit(); } return; }
      if (touch && armed !== hovered) {
        armed = hovered; emit();
        gsap.fromTo(hovered.group.scale, { x: 1, y: 1, z: 1 }, { x: 1.18, y: 1.18, z: 1.18, duration: 0.4, yoyo: true, repeat: 1, ease: 'power2.out' });
        return;
      }
      go(hovered);
    });

    function go(b) {
      host.dispatchEvent(new CustomEvent('bh-navigate', { bubbles: true, detail: { name: b.name, url: b.url } }));
      gsap.to(compUni.uFade, { value: 0, duration: 0.65, ease: 'power2.in', onComplete: () => { if (b.url) location.assign(b.url); } });
    }

    function pick() {
      if (S.phase !== 'travel' || !hasPointer) { if (hovered) { hovered.target = 0; hovered = null; emit(); } return; }
      ray.setFromCamera(ptr, camera);
      const targets = bodies.filter(b => b.page).map(b => b.mesh);
      const hit = ray.intersectObjects(targets, false)[0];
      let b = hit ? bodies.find(x => x.mesh === hit.object) : null;

      if (!b) {
        const px = (ptr.x * 0.5 + 0.5) * W, py = (-ptr.y * 0.5 + 0.5) * H;
        const grab = touch ? 30 : 24;
        let best = 1e9;
        for (const c of bodies) {
          if (!c.page || !c.onScreen) continue;
          const dd = Math.hypot(c.sx - px, c.sy - py);
          if (dd < grab && dd < best) { best = dd; b = c; }
        }
      }

      if (b !== hovered) {
        if (hovered) { hovered.target = 0; gsap.to(hovered.group.scale, { x: 1, y: 1, z: 1, duration: 0.4, ease: 'power3.out' }); }
        hovered = b;
        if (b) { b.target = 1; gsap.to(b.group.scale, { x: 1.22, y: 1.22, z: 1.22, duration: 0.5, ease: 'back.out(2.2)' }); }
        host.style.cursor = b ? 'pointer' : '';
        emit();
      }
    }

    function beginCapture() {
      if (S.phase !== 'travel') return;
      S.phase = 'capture';
      if (hovered) { hovered.target = 0; hovered = null; }
      host.style.cursor = '';
      emit();
      gsap.timeline({ defaults: { ease: 'none' } })
        .to(S, { capture: 1, duration: 7.4, ease: 'power3.in' }, 0)
        .to(S, { diskBoost: 0.95, duration: 6.0, ease: 'power2.in' }, 0)
        .to(bhUni.uLens, { value: 1.65, duration: 6.6, ease: 'power2.in' }, 0)
        .to(bhUni.uSpin, { value: 2.6, duration: 7.0, ease: 'power2.in' }, 0)
        .to(bhUni.uBgFade, { value: 0.05, duration: 5.4, ease: 'power2.in' }, 1.2)
        .to(compUni.uAberr, { value: 3.2, duration: 6.0, ease: 'power2.in' }, 0.6)
        .to(compUni.uVignette, { value: 0.92, duration: 6.0 }, 0.6)
        .to(S, { dim: 0, duration: 3.4, ease: 'power2.in' }, 2.8)
        .to(compUni.uExposure, { value: 2.6, duration: 1.1, ease: 'power2.in' }, 6.3)
        .to(compUni.uExposure, { value: 0.0, duration: 1.5, ease: 'power2.inOut' }, 7.4)
        .add(() => { S.phase = 'reveal'; emit(); host.dispatchEvent(new CustomEvent('bh-reveal', { bubbles: true })); }, 8.2)
        .to(compUni.uExposure, { value: 0.42, duration: 3.0, ease: 'power2.out' }, 8.6)
        .to(bhUni.uDiskMul, { value: 0.16, duration: 3.0 }, 8.6)
        .add(() => {
          host.dispatchEvent(new CustomEvent('bh-home', { bubbles: true, detail: { url: homeUrl } }));
          if (homeUrl) gsap.to(compUni.uFade, { value: 0, duration: 1.0, delay: 2.2, onComplete: () => location.assign(homeUrl) });
        }, 11.4);
    }

    const setPalette = key => {
      const p = PALETTES[key] || PALETTES.bone;
      bhUni.uHot.value.setRGB(p[0][0], p[0][1], p[0][2]);
      bhUni.uMid.value.setRGB(p[1][0], p[1][1], p[1][2]);
      bhUni.uCool.value.setRGB(p[2][0], p[2][1], p[2][2]);
    };
    setPalette(this.attr('disk-palette') || 'bone');

    this._apply = (name, value) => {
      if (name === 'diskpalette') setPalette(value);
      if (name === 'duration') { const s = parseFloat(value) || 15; WHEEL_TOTAL = s * 300; wheelAcc = S.target * WHEEL_TOTAL; }
      if (name === 'quality' && value && value !== 'auto') {
        const n = TIERS[value];
        if (!n) return;
        Object.assign(Q, n);
        renderer.setPixelRatio(Math.min(devicePixelRatio || 1, Q.dpr));
        bhMat.fragmentShader = bhSource(Q.steps);
        bhMat.needsUpdate = true;
        compUni.uBloom.value = Q.bloom ? 1.85 : 0.0;
        resize();
      }
    };

    let W = 2, H = 2, fovScale = 1;
    function resize() {
      const r = host.getBoundingClientRect();
      W = Math.max(2, r.width | 0); H = Math.max(2, r.height | 0);
      renderer.setSize(W, H, false);
      const px = renderer.getPixelRatio();
      const sw = Math.max(2, (W * px * Q.scale) | 0), sh = Math.max(2, (H * px * Q.scale) | 0);
      rtScene.setSize(sw, sh);
      rtA.setSize(Math.max(2, sw >> 2), Math.max(2, sh >> 2));
      rtB.setSize(Math.max(2, sw >> 2), Math.max(2, sh >> 2));
      rtC.setSize(Math.max(2, sw >> 3), Math.max(2, sh >> 3));
      rtD.setSize(Math.max(2, sw >> 3), Math.max(2, sh >> 3));
      bhUni.uRes.value.set(sw, sh);
      compUni.uRes.value.set(sw, sh);
      camera.aspect = W / H;
      const fov = W / H < 0.85 ? 78 : 62;
      fovScale = fov / 62;                  // portrait widening, reapplied over SHOT.fov
      camera.fov = fov;
      refCam.fov = fov; refCam.aspect = camera.aspect; refCam.updateProjectionMatrix();
      bhUni.uTanHalfFov.value = Math.tan(THREE.MathUtils.degToRad(fov) * 0.5);
      camera.updateProjectionMatrix();
      layoutBodies();
    }
    const ro = new ResizeObserver(resize); ro.observe(host); resize();

    const clock = new THREE.Clock();
    const basis = new THREE.Matrix3();
    const tmp = new THREE.Vector3();
    let raf = 0, visible = true;
    const onVis = () => { visible = !document.hidden; };
    document.addEventListener('visibilitychange', onVis);

    /* the opening fade is staging: in external mode the timeline owns uFade */
    if (!external) gsap.to(compUni.uFade, { value: 1, duration: 2.4, ease: 'power2.out', delay: 0.15 });

    function frame() {
      raf = requestAnimationFrame(frame);
      if (!visible) return;
      const dt = Math.min(clock.getDelta(), 0.05);
      const t = clock.getElapsedTime();

      if (S.phase === 'travel') {
        S.progress += (S.target - S.progress) * Math.min(1, dt * 2.6);
        if (S.progress > LOCK) beginCapture();
      }
      S.mx += (S.tmx - S.mx) * Math.min(1, dt * 2.2);
      S.my += (S.tmy - S.my) * Math.min(1, dt * 2.2);

      let d, az, el;
      if (external) {
        /* staging comes from the timeline; the pointer keeps a little parallax alive
           so a paused frame is never dead still */
        d = Math.max(SHOT.dist, 1.02);
        az = SHOT.azim + S.mx * 0.055 * SHOT.parallax;
        el = SHOT.elev + S.my * 0.032 * SHOT.parallax;
        const fov = SHOT.fov * fovScale;
        if (Math.abs(camera.fov - fov) > 1e-4) {
          camera.fov = fov;
          camera.updateProjectionMatrix();
          bhUni.uTanHalfFov.value = Math.tan(THREE.MathUtils.degToRad(fov) * 0.5);
        }
        camera.up.set(Math.sin(SHOT.roll), Math.cos(SHOT.roll), 0);
      } else {
        /* camera sways at the horizon — never a full orbit, so the static
           navigation points keep their place in frame */
        d = dist();
        const sway = Math.sin(t * 0.085) * 0.052 + Math.sin(t * 0.031) * 0.026;
        S.azim = AZ0 + sway + S.capture * 1.15;
        const elev = THREE.MathUtils.lerp(EL0, 0.150, Math.min(1, S.progress * 1.15)) * (1 - S.capture * 0.55)
          + Math.sin(t * 0.062) * 0.015;
        az = S.azim + S.mx * 0.055 * (1 - S.capture);
        el = elev + S.my * 0.032 * (1 - S.capture);
      }
      camera.position.set(
        Math.sin(az) * Math.cos(el) * d,
        Math.sin(el) * d,
        Math.cos(az) * Math.cos(el) * d
      );
      camera.lookAt(0, 0, 0);
      camera.updateMatrixWorld();

      bhUni.uTime.value = t;
      bhUni.uCamPos.value.copy(camera.position);
      basis.setFromMatrix4(camera.matrixWorld);
      bhUni.uCamBasis.value.copy(basis);
      if (external) {
        bhUni.uDiskBright.value = SHOT.diskBright;
        bhUni.uLens.value = SHOT.lens;        // assigned, not clamped: the timeline scrubs both ways
        bhUni.uDiskThick.value = SHOT.diskThick;
        bhUni.uDiskMul.value = SHOT.diskMul;
        bhUni.uSpin.value = SHOT.spin;
        bhUni.uBgFade.value = SHOT.bgFade;
        bhUni.uCapture.value = SHOT.capture;
        compUni.uBloom.value = Q.bloom ? SHOT.bloom : SHOT.bloom * 0.46;
        compUni.uVignette.value = SHOT.vignette;
        compUni.uAberr.value = SHOT.aberr;
        compUni.uGrain.value = SHOT.grain;
        compUni.uExposure.value = SHOT.exposure;
        compUni.uFade.value = SHOT.fade;
      } else {
        bhUni.uDiskBright.value = 0.46 + S.progress * 0.48 + S.diskBoost;
        bhUni.uLens.value = Math.max(bhUni.uLens.value, 0.82 + S.progress * 0.30);
        bhUni.uDiskThick.value = 0.62 + S.progress * 0.40;
        bhUni.uCapture.value = S.capture;
      }
      bhUni.uEscape.value = d * 2.0 + 62;
      compUni.uTime.value = t;

      for (const b of bodies) {
        b.glow.quaternion.copy(camera.quaternion);
        const sp = tmp.copy(b.pos).project(camera);
        const camD = camera.position.length();
        const shadowNdc = (2.598 * bhUni.uLens.value / Math.max(camD, 1.2)) / bhUni.uTanHalfFov.value;
        const ndcR = Math.hypot(sp.x * (W / H), sp.y);
        const farther = b.pos.distanceTo(camera.position) > camD;
        const occl = farther ? THREE.MathUtils.smoothstep(ndcR, shadowNdc * 0.55, shadowNdc * 1.9) : 1.0;
        b.hi += (b.target - b.hi) * Math.min(1, dt * 8);
        b.mat.uniforms.uHi.value = b.hi;
        b.mat.uniforms.uOccl.value = occl;
        b.mat.uniforms.uDim.value = external ? SHOT.objDim : S.dim;
        b.glowMat.uniforms.uHi.value = b.hi;
        b.glowMat.uniforms.uOccl.value = occl;
        b.glowMat.uniforms.uDim.value = (external ? SHOT.objDim : S.dim) * (0.52 + 0.26 * Math.sin(t * 1.15 + b.depth * 2.1));
        b.sx = (sp.x * 0.5 + 0.5) * W;
        b.sy = (-sp.y * 0.5 + 0.5) * H;
        b.onScreen = sp.z < 1 && Math.abs(sp.x) < 1.25 && Math.abs(sp.y) < 1.25;
      }

      if (!touch) pick();
      if (hovered) emit();

      blit(bhMat, rtScene);
      renderer.setRenderTarget(rtScene);
      renderer.render(scene, camera);

      if (Q.bloom) {
        brightMat.uniforms.tSrc.value = rtScene.texture; blit(brightMat, rtA);
        blurMat.uniforms.tSrc.value = rtA.texture; blurMat.uniforms.uDir.value.set(1 / rtA.width, 0); blit(blurMat, rtB);
        blurMat.uniforms.tSrc.value = rtB.texture; blurMat.uniforms.uDir.value.set(0, 1 / rtA.height); blit(blurMat, rtA);
        blurMat.uniforms.tSrc.value = rtA.texture; blurMat.uniforms.uDir.value.set(1.6 / rtA.width, 0); blit(blurMat, rtB);
        blurMat.uniforms.tSrc.value = rtB.texture; blurMat.uniforms.uDir.value.set(0, 1.6 / rtA.height); blit(blurMat, rtA);
        compUni.tBloomA.value = rtA.texture;

        blurMat.uniforms.tSrc.value = rtA.texture; blurMat.uniforms.uDir.value.set(2.2 / rtC.width, 0); blit(blurMat, rtC);
        blurMat.uniforms.tSrc.value = rtC.texture; blurMat.uniforms.uDir.value.set(0, 2.2 / rtC.height); blit(blurMat, rtD);
        blurMat.uniforms.tSrc.value = rtD.texture; blurMat.uniforms.uDir.value.set(4.6 / rtC.width, 0); blit(blurMat, rtC);
        blurMat.uniforms.tSrc.value = rtC.texture; blurMat.uniforms.uDir.value.set(0, 4.6 / rtC.height); blit(blurMat, rtD);
        blurMat.uniforms.tSrc.value = rtD.texture; blurMat.uniforms.uDir.value.set(4.8 / rtC.width, 0); blit(blurMat, rtC);
        blurMat.uniforms.tSrc.value = rtC.texture; blurMat.uniforms.uDir.value.set(0, 4.8 / rtC.height); blit(blurMat, rtD);
        compUni.tBloomB.value = rtD.texture;
      } else {
        brightMat.uniforms.tSrc.value = rtScene.texture; blit(brightMat, rtA);
        blurMat.uniforms.tSrc.value = rtA.texture; blurMat.uniforms.uDir.value.set(2.0 / rtA.width, 0); blit(blurMat, rtB);
        blurMat.uniforms.tSrc.value = rtB.texture; blurMat.uniforms.uDir.value.set(0, 2.0 / rtA.height); blit(blurMat, rtA);
        compUni.tBloomA.value = rtA.texture;
        compUni.tBloomB.value = rtA.texture;
      }
      compUni.tScene.value = rtScene.texture;
      blit(compMat, null);
    }
    frame();
    emit();

    this._dispose = () => {
      cancelAnimationFrame(raf); raf = 0;
      ro.disconnect();
      removeEventListener('keydown', onKey);
      document.removeEventListener('visibilitychange', onVis);
      gsap.killTweensOf([S, bhUni.uLens, bhUni.uSpin, bhUni.uBgFade, bhUni.uDiskMul,
        compUni.uAberr, compUni.uVignette, compUni.uExposure, compUni.uFade]);
      [rtScene, rtA, rtB, rtC, rtD].forEach(r => r.dispose());
      [fsGeo, geoSphere, glowGeo].forEach(g => g.dispose());
      [bhMat, brightMat, blurMat, compMat].forEach(m => m.dispose());
      bodies.forEach(b => { b.mat.dispose(); b.glowMat.dispose(); scene.remove(b.group); });
      bodies.length = 0;
      renderer.dispose();
      host.setShot = host.shot = null;
    };
  }
}

if (!customElements.get('black-hole-stage')) customElements.define('black-hole-stage', BlackHoleStage);

/* ── overlay wiring: the Blade markup carries [data-bh] hooks ─────────────── */
function initOverlay() {
  const q = name => document.querySelector('[data-bh="' + name + '"]');
  let lastPct = -1, warned = false, revealDone = false;

  document.addEventListener('bh-state', e => {
    const d = e.detail;
    const lab = q('label'), nm = q('labelName');
    if (lab) {
      if (d.hover && d.phase === 'travel') {
        lab.style.transform = 'translate(' + d.hover.x + 'px,' + d.hover.y + 'px) translate(-50%,-50%)';
        lab.style.opacity = '1';
        if (nm && nm.textContent !== d.hover.name) nm.textContent = d.hover.name;
      } else lab.style.opacity = '0';
    }
    const pct = Math.round(Math.min(1, d.progress + d.capture * 0.22) * 100);
    if (pct !== lastPct) {
      lastPct = pct;
      const tk = q('tick'), pc = q('pct');
      if (tk) tk.style.transform = 'translateY(' + (pct * 1.32 - 3) + 'px)';
      if (pc) pc.textContent = pct + '%';
    }
    const hint = q('hint'), rail = q('rail');
    if (hint) hint.style.opacity = (d.progress > 0.06 || d.phase !== 'travel') ? '0' : '1';
    if (rail) rail.style.opacity = d.phase === 'reveal' ? '0' : '1';
    if (d.phase !== 'travel' && !warned) {
      warned = true;
      const w = q('warn');
      if (w) { w.style.opacity = '1'; setTimeout(() => { w.style.opacity = '0'; }, 2800); }
    }
  });

  document.addEventListener('bh-reveal', () => {
    if (revealDone) return;
    revealDone = true;
    const wrap = q('reveal');
    if (!wrap) return;
    wrap.style.display = 'flex';
    const letters = wrap.querySelectorAll('[data-letter]');
    const sub = q('sub'), outro = q('outro');
    gsap.set(letters, { opacity: 0, y: 30, filter: 'blur(18px)', scale: 1.6 });
    gsap.set(sub, { opacity: 0, scaleX: 0.55 });
    gsap.set(outro, { opacity: 0, y: 14 });
    gsap.to(letters, { opacity: 1, y: 0, filter: 'blur(0px)', scale: 1, duration: 1.5, ease: 'power3.out', stagger: 0.13 });
    gsap.to(sub, { opacity: 1, scaleX: 1, duration: 1.5, ease: 'power3.out', delay: 1.4 });
    gsap.to(outro, { opacity: 1, y: 0, duration: 1.0, ease: 'power2.out', delay: 2.6 });
  });

  document.addEventListener('click', e => {
    const again = e.target.closest && e.target.closest('[data-bh="again"]');
    if (again) location.reload();
  });
}

if (document.readyState === 'loading') addEventListener('DOMContentLoaded', initOverlay);
else initOverlay();

export { BlackHoleStage };
