/* anim.js — animations d'entrée de section, une fabrique par concept :
   create(id, stage, ctx) -> { play, reset, resize, destroy }.
   Aucune dépendance nouvelle : gsap vient de animation/motion.js (booté par
   la page hôte, voir labs/animation/animation-lab.js). */

let MOT = null;
export function bind(mod) { MOT = mod; return mod; }

const G = () => (MOT && MOT.gsap) || window.gsap || null;
const red = () => !!(MOT && MOT.reduced && MOT.reduced());
const rnd = (i, s = 1) => { const v = Math.sin((i + 1) * 12.9898 * s) * 43758.5453; return v - Math.floor(v); };
const ACC = 'rgba(145,132,217,';
const INK = 'rgba(226,221,209,';
const BG = '#05050a';

const mk = (tag, css, parent) => {
  const el = document.createElement(tag);
  if (css) el.style.cssText = css;
  if (parent) parent.appendChild(el);
  return el;
};
const layer = (stage, css = '') => mk('div', 'position:absolute;inset:0;pointer-events:none;' + css, stage);
const svgEl = (n, attrs, parent) => {
  const el = document.createElementNS('http://www.w3.org/2000/svg', n);
  for (const k in attrs) el.setAttribute(k, attrs[k]);
  if (parent) parent.appendChild(el);
  return el;
};
const content = stage => stage.querySelector('[data-anim-content]');
const size = stage => { const r = stage.getBoundingClientRect(); return { w: Math.max(1, r.width), h: Math.max(1, r.height) }; };
const perimeter = (w, h) => t => {
  const P = 2 * (w + h);
  let s = ((t % 1) + 1) % 1 * P;
  if (s < w) return [s, 0];
  s -= w; if (s < h) return [w, s];
  s -= h; if (s < w) return [w - s, h];
  s -= w; return [0, h - s];
};

/* ------------------------------------------------- 01 · Broken Glass --- */

export function brokenGlass(stage, ctx = {}) {
  const host = layer(stage);
  let shards = [], cracks = [];

  const build = () => {
    host.innerHTML = ''; shards = []; cracks = [];
    const { w, h } = size(stage);
    const svg = svgEl('svg', { width: w, height: h, viewBox: '0 0 ' + w + ' ' + h, style: 'position:absolute;inset:0;overflow:visible' }, host);
    const per = perimeter(w, h);
    const band = Math.min(w, h) * 0.17;
    const inward = (p, d) => {
      const vx = w / 2 - p[0], vy = h / 2 - p[1], l = Math.hypot(vx, vy) || 1;
      return [p[0] + vx / l * d, p[1] + vy / l * d];
    };
    const N = 20;
    for (let i = 0; i < N; i++) {
      const a = per(i / N), b = per((i + 1) / N);
      const ia = inward(a, band * (0.45 + rnd(i) * 0.95)), ib = inward(b, band * (0.45 + rnd(i + 4) * 0.95));
      const poly = svgEl('polygon', {
        points: [a, b, ib, ia].map(p => p[0].toFixed(1) + ',' + p[1].toFixed(1)).join(' '),
        fill: ACC + '.05)', stroke: INK + '.40)', 'stroke-width': '1', 'vector-effect': 'non-scaling-stroke'
      }, svg);
      const mx = (a[0] + b[0]) / 2 - w / 2, my = (a[1] + b[1]) / 2 - h / 2, ml = Math.hypot(mx, my) || 1;
      poly._nx = mx / ml; poly._ny = my / ml;
      shards.push(poly);
    }
    for (let k = 0; k < 3; k++) {
      const s = per(rnd(k + 21)), e = inward(per(rnd(k + 31)), band * 1.9 + rnd(k + 61) * 70);
      const mid = [(s[0] + e[0]) / 2 + (rnd(k + 41) - 0.5) * 80, (s[1] + e[1]) / 2 + (rnd(k + 51) - 0.5) * 80];
      const pl = svgEl('polyline', {
        points: [s, mid, e].map(p => p[0].toFixed(1) + ',' + p[1].toFixed(1)).join(' '),
        fill: 'none', stroke: INK + '.30)', 'stroke-width': '1', 'vector-effect': 'non-scaling-stroke'
      }, svg);
      const len = pl.getTotalLength ? pl.getTotalLength() : 400;
      pl.setAttribute('stroke-dasharray', len);
      pl._len = len;
      cracks.push(pl);
    }
  };

  const reset = () => {
    const g = G(); if (!g) return;
    g.set(content(stage), { opacity: 0, filter: 'blur(7px)', scale: 1.015 });
    shards.forEach((s, i) => g.set(s, {
      opacity: 0, x: s._nx * (55 + rnd(i) * 95), y: s._ny * (55 + rnd(i + 5) * 95),
      rotate: (rnd(i + 9) - 0.5) * 46, scale: 0.7 + rnd(i + 2) * 0.4
    }));
    cracks.forEach(c => g.set(c, { strokeDashoffset: c._len, opacity: 1 }));
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const tl = g.timeline();
    tl.to(shards, { opacity: 1, x: 0, y: 0, rotate: 0, scale: 1, duration: 1.05 * sp, ease: 'power4.out', stagger: { each: 0.03 * sp, from: 'random' } })
      .to(cracks, { strokeDashoffset: 0, duration: 0.55 * sp, ease: 'power2.out', stagger: 0.07 * sp }, 0.3 * sp)
      .to(content(stage), { opacity: 1, filter: 'blur(0px)', scale: 1, duration: 0.75 * sp, ease: 'power2.out' }, 0.42 * sp)
      .to(shards, { opacity: 0.5, duration: 0.5 * sp }, '>-0.3')
      .to(cracks, { opacity: 0.3, duration: 0.45 * sp }, '<');
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(shards.concat(cracks)); host.remove(); } };
}

/* ---------------------------------------------- 02 · Industrial Scan --- */

export function industrialScan(stage, ctx = {}) {
  const host = layer(stage);
  let edges = [], brackets = [], ticks = [], scan = null, readout = null;

  const build = () => {
    host.innerHTML = ''; edges = []; brackets = []; ticks = [];
    const pad = 14;
    const dash = deg => 'background-image:repeating-linear-gradient(' + deg + 'deg,' + INK + '.55) 0 7px,transparent 7px 14px)';
    const e = (css, origin, ax) => {
      const el = mk('span', 'position:absolute;' + css, host);
      el._o = origin; el._ax = ax; edges.push(el); return el;
    };
    e('left:' + pad + 'px;right:' + pad + 'px;top:' + pad + 'px;height:1px;' + dash(90), '0% 50%', 'x');
    e('right:' + pad + 'px;top:' + pad + 'px;bottom:' + pad + 'px;width:1px;' + dash(0), '50% 0%', 'y');
    e('left:' + pad + 'px;right:' + pad + 'px;bottom:' + pad + 'px;height:1px;' + dash(90), '100% 50%', 'x');
    e('left:' + pad + 'px;top:' + pad + 'px;bottom:' + pad + 'px;width:1px;' + dash(0), '50% 100%', 'y');

    [[0, 0], [1, 0], [1, 1], [0, 1]].forEach(([cx, cy], i) => {
      const b = mk('span', 'position:absolute;width:16px;height:16px;' +
        (cx ? 'right:' + (pad - 1) + 'px;border-right:' : 'left:' + (pad - 1) + 'px;border-left:') + '1px solid ' + ACC + '.9);' +
        (cy ? 'bottom:' + (pad - 1) + 'px;border-bottom:' : 'top:' + (pad - 1) + 'px;border-top:') + '1px solid ' + ACC + '.9);', host);
      brackets.push(b);
    });

    const rail = mk('span', 'position:absolute;left:' + (pad + 22) + 'px;top:' + (pad + 5) + 'px;display:flex;gap:9px;align-items:flex-start', host);
    for (let i = 0; i < 14; i++) ticks.push(mk('span', 'width:1px;height:' + (i % 4 === 0 ? 9 : 5) + 'px;background:' + INK + '.45)', rail));

    scan = mk('span', 'position:absolute;left:' + pad + 'px;right:' + pad + 'px;top:0;height:64px;' +
      'background:linear-gradient(180deg,transparent,' + ACC + '.14) 78%,' + ACC + '.9));' +
      'box-shadow:0 0 18px ' + ACC + '.35)', host);

    readout = mk('span', 'position:absolute;right:' + (pad + 20) + 'px;bottom:' + (pad + 10) + 'px;font-size:10px;letter-spacing:.22em;' +
      'color:' + ACC + '.9);font-variant-numeric:tabular-nums', host);
    readout.textContent = 'SCAN 000';
  };

  const reset = () => {
    const g = G(); if (!g) return;
    edges.forEach(el => g.set(el, { opacity: 1, scaleX: el._ax === 'x' ? 0 : 1, scaleY: el._ax === 'y' ? 0 : 1, transformOrigin: el._o }));
    g.set(brackets, { opacity: 0, scale: 0.5 });
    g.set(ticks, { opacity: 0, scaleY: 0, transformOrigin: '50% 0%' });
    g.set(scan, { opacity: 0, y: 0 });
    g.set(readout, { opacity: 0 });
    g.set(content(stage), { opacity: 0, x: -10, filter: 'blur(2px)' });
    readout.textContent = 'SCAN 000';
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const { h } = size(stage);
    const o = { v: 0 };
    const tl = g.timeline();
    tl.to(edges[0], { scaleX: 1, duration: 0.3 * sp, ease: 'power2.out' })
      .to(edges[1], { scaleY: 1, duration: 0.3 * sp, ease: 'power2.out' }, '>-0.09')
      .to(edges[2], { scaleX: 1, duration: 0.3 * sp, ease: 'power2.out' }, '>-0.09')
      .to(edges[3], { scaleY: 1, duration: 0.3 * sp, ease: 'power2.out' }, '>-0.09')
      .to(brackets, { opacity: 1, scale: 1, duration: 0.3 * sp, ease: 'power3.out', stagger: 0.05 * sp }, 0.16 * sp)
      .to(readout, { opacity: 1, duration: 0.2 * sp }, 0.3 * sp)
      .to(o, { v: 1, duration: 1.15 * sp, ease: 'none', onUpdate: () => { readout.textContent = 'SCAN ' + String(Math.round(o.v * 100)).padStart(3, '0'); } }, 0.3 * sp)
      .fromTo(scan, { y: -30, opacity: 0 }, { y: h - 34, opacity: 1, duration: 0.95 * sp, ease: 'power1.inOut' }, 0.42 * sp)
      .to(ticks, { opacity: 1, scaleY: 1, duration: 0.22 * sp, stagger: 0.022 * sp }, 0.6 * sp)
      .to(content(stage), { opacity: 1, x: 0, filter: 'blur(0px)', duration: 0.55 * sp, ease: 'steps(5)' }, 0.86 * sp)
      .to(scan, { opacity: 0, duration: 0.3 * sp }, '>-0.2')
      .to(edges, { opacity: 0.6, duration: 0.4 * sp }, '<');
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(edges.concat(brackets, ticks, [scan, readout])); host.remove(); } };
}

/* ------------------------------------------------ 03 · Organic Reveal --- */

export function organicReveal(stage, ctx = {}) {
  const host = layer(stage, 'overflow:hidden');
  const cv = mk('canvas', 'position:absolute;inset:0;width:100%;height:100%', host);
  const lobes = Array.from({ length: 30 }, (_, i) => ({ a: i / 30 * Math.PI * 2, r: 0.6 + rnd(i) * 0.85, ph: rnd(i + 7) * 6.283, sp: 0.7 + rnd(i + 13) * 1.4 }));
  let W = 1, H = 1, dpr = 1, cx = null;

  const build = () => {
    const { w, h } = size(stage);
    dpr = Math.min(2, window.devicePixelRatio || 1);
    W = Math.round(w * dpr); H = Math.round(h * dpr);
    cv.width = W; cv.height = H;
    cx = cv.getContext('2d');
    draw(0);
  };

  const draw = t => {
    if (!cx) return;
    cx.setTransform(1, 0, 0, 1, 0, 0);
    cx.clearRect(0, 0, W, H);
    cx.fillStyle = BG;
    cx.fillRect(0, 0, W, H);
    if (t <= 0.001) return;
    const ox = W * 0.24, oy = H * 0.74;
    const R = Math.hypot(W, H) * 1.12 * t;
    const p = new Path2D();
    lobes.forEach((l, i) => {
      const rr = R * (0.6 + 0.4 * Math.sin(l.ph + t * l.sp * 3.4)) * (0.62 + 0.5 * l.r);
      const x = ox + Math.cos(l.a) * rr, y = oy + Math.sin(l.a) * rr;
      if (i === 0) p.moveTo(x, y); else p.lineTo(x, y);
    });
    p.closePath();
    cx.save();
    cx.globalCompositeOperation = 'destination-out';
    cx.filter = 'blur(' + (9 * dpr).toFixed(1) + 'px)';
    cx.fill(p);
    cx.restore();
    cx.save();
    cx.strokeStyle = ACC + (0.15 + 0.5 * (1 - t)).toFixed(3) + ')';
    cx.lineWidth = 1.4 * dpr;
    cx.filter = 'blur(' + (2 * dpr).toFixed(1) + 'px)';
    cx.stroke(p);
    cx.restore();
  };

  const reset = () => {
    const g = G(); if (!g) return;
    draw(0);
    g.set(content(stage), { opacity: 0, y: 14, filter: 'blur(9px)' });
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const o = { t: 0 };
    const tl = g.timeline();
    tl.to(o, { t: 1, duration: 1.55 * sp, ease: 'power2.inOut', onUpdate: () => draw(o.t) })
      .to(content(stage), { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.85 * sp, ease: 'power2.out' }, 0.5 * sp);
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(cv); host.remove(); } };
}

/* -------------------------------------------------- 04 · Glitch Frame --- */

export function glitchFrame(stage, ctx = {}) {
  const host = layer(stage);
  let slices = [], ghosts = [];

  const build = () => {
    host.innerHTML = ''; slices = []; ghosts = [];
    const N = 7;
    for (let i = 0; i < N; i++) {
      const y0 = i / N * 100, y1 = (i + 1) / N * 100;
      const s = mk('span', 'position:absolute;inset:12px;border:1px solid ' + INK + '.55);' +
        'clip-path:inset(' + y0.toFixed(2) + '% 0 ' + (100 - y1).toFixed(2) + '% 0)', host);
      slices.push(s);
    }
    [[ACC + '.85)', 1], [INK + '.5)', -1]].forEach(([col, dir]) => {
      const gh = mk('span', 'position:absolute;inset:12px;border:1px solid ' + col + ';mix-blend-mode:screen', host);
      gh._dir = dir; ghosts.push(gh);
    });
  };

  const reset = () => {
    const g = G(); if (!g) return;
    g.set(slices, { opacity: 0, x: (i) => (rnd(i) - 0.5) * 70 });
    g.set(ghosts, { opacity: 0, x: (i, t) => t._dir * 16 });
    g.set(content(stage), { opacity: 0, x: 12, skewX: -6 });
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const tl = g.timeline();
    tl.set(slices, { opacity: 1 });
    for (let k = 0; k < 5; k++) {
      tl.set(slices, { x: (i) => (rnd(i + k * 13) - 0.5) * (58 - k * 9), opacity: (i) => rnd(i + k * 7) > 0.18 ? 1 : 0.15 })
        .to({}, { duration: 0.055 * sp });
    }
    tl.set(ghosts, { opacity: 0.8 }, 0.1 * sp)
      .to(slices, { x: 0, opacity: 1, duration: 0.4 * sp, ease: 'power4.out' })
      .to(ghosts, { x: 0, opacity: 0, duration: 0.45 * sp, ease: 'power3.out' }, '<')
      .to(content(stage), { opacity: 1, x: 0, skewX: 0, duration: 0.42 * sp, ease: 'power3.out' }, '<+0.04')
      .to(slices, { opacity: 0.35, duration: 0.4 * sp }, '>-0.1');
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(slices.concat(ghosts)); host.remove(); } };
}

/* ----------------------------------------------------- 05 · Wireframe --- */

export function wireframe(stage, ctx = {}) {
  const host = layer(stage, 'perspective:1000px');
  const inner = mk('div', 'position:absolute;inset:0;transform-style:preserve-3d', host);
  let edges = [];

  const build = () => {
    const g = G(); if (!g) return;
    inner.innerHTML = ''; edges = [];
    const { w, h } = size(stage);
    const pad = 20, d = Math.min(240, w * 0.3);
    const W = w - pad * 2, H = h - pad * 2;
    const line = (x, y, z, len, axis, grp) => {
      const el = mk('span', 'position:absolute;left:0;top:0;background:' + INK + '.5)', inner);
      if (axis === 'y') { el.style.width = '1px'; el.style.height = len + 'px'; g.set(el, { x, y, z, transformOrigin: '50% 0%' }); el._ax = 'scaleY'; }
      else if (axis === 'z') { el.style.width = len + 'px'; el.style.height = '1px'; g.set(el, { x, y, z, rotationY: 90, transformOrigin: '50% 50%' }); el._ax = 'scaleX'; }
      else { el.style.width = len + 'px'; el.style.height = '1px'; g.set(el, { x, y, z, transformOrigin: '0% 50%' }); el._ax = 'scaleX'; }
      el._grp = grp; el._z = z;
      edges.push(el);
    };
    [[d / 2, 'front'], [-d / 2, 'back']].forEach(([z, grp]) => {
      line(pad, pad, z, W, 'x', grp);
      line(pad, h - pad, z, W, 'x', grp);
      line(pad, pad, z, H, 'y', grp);
      line(w - pad, pad, z, H, 'y', grp);
    });
    [[pad, pad], [w - pad, pad], [pad, h - pad], [w - pad, h - pad]].forEach(([x, y]) => line(x, y, 0, d, 'z', 'conn'));
    edges.filter(e => e._grp === 'back').forEach(e => { e.style.background = ACC + '.45)'; });
    edges.filter(e => e._grp === 'conn').forEach(e => { e.style.background = INK + '.22)'; });
  };

  const reset = () => {
    const g = G(); if (!g) return;
    g.set(inner, { rotationY: -30, rotationX: 15, scale: 0.92 });
    g.set(edges, { opacity: 0, scaleX: (i, t) => t._ax === 'scaleX' ? 0 : 1, scaleY: (i, t) => t._ax === 'scaleY' ? 0 : 1, z: (i, t) => t._z });
    g.set(content(stage), { opacity: 0, scale: 0.985 });
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const back = edges.filter(e => e._grp === 'back'), conn = edges.filter(e => e._grp === 'conn'), front = edges.filter(e => e._grp === 'front');
    const tl = g.timeline();
    tl.to(edges, { opacity: 1, scaleX: 1, scaleY: 1, duration: 0.5 * sp, ease: 'power2.out', stagger: 0.06 * sp })
      .to(inner, { rotationY: 0, rotationX: 0, scale: 1, duration: 1.35 * sp, ease: 'power3.inOut' }, 0.25 * sp)
      .to(back, { z: 0, opacity: 0, duration: 0.8 * sp, ease: 'power2.inOut' }, 0.95 * sp)
      .to(conn, { opacity: 0, duration: 0.5 * sp }, 0.95 * sp)
      .to(front, { z: 0, duration: 0.85 * sp, ease: 'power2.inOut' }, 0.95 * sp)
      .to(front, { opacity: 0.55, duration: 0.5 * sp }, '>-0.3')
      .to(content(stage), { opacity: 1, scale: 1, duration: 0.7 * sp, ease: 'power2.out' }, 1.15 * sp);
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(edges); host.remove(); } };
}

/* ---------------------------------------------- 06 · Particle Assembly --- */

export function particleAssembly(stage, ctx = {}) {
  const host = layer(stage, 'overflow:hidden');
  const cv = mk('canvas', 'position:absolute;inset:0;width:100%;height:100%', host);
  let W = 1, H = 1, dpr = 1, cx = null, parts = [], sampled = false;

  const build = () => {
    const { w, h } = size(stage);
    dpr = Math.min(2, window.devicePixelRatio || 1);
    W = Math.round(w * dpr); H = Math.round(h * dpr);
    cv.width = W; cv.height = H;
    cx = cv.getContext('2d');
    sampled = false;
    if (cx) cx.clearRect(0, 0, W, H);
  };

  const targets = () => {
    const pts = [];
    const pad = 18 * dpr, step = 11 * dpr;
    for (let x = pad; x <= W - pad; x += step) { pts.push([x, pad, 0]); pts.push([x, H - pad, 0]); }
    for (let y = pad; y <= H - pad; y += step) { pts.push([pad, y, 0]); pts.push([W - pad, y, 0]); }
    const t = stage.querySelector('[data-anim-title]');
    if (t) {
      const r = t.getBoundingClientRect(), sr = stage.getBoundingClientRect();
      const cs = getComputedStyle(t);
      const off = document.createElement('canvas');
      off.width = W; off.height = H;
      const c2 = off.getContext('2d');
      c2.fillStyle = '#fff';
      c2.textBaseline = 'top';
      if ('letterSpacing' in c2) c2.letterSpacing = (parseFloat(cs.letterSpacing) || 0) * dpr + 'px';
      c2.font = cs.fontWeight + ' ' + (parseFloat(cs.fontSize) * dpr) + 'px ' + cs.fontFamily;
      c2.fillText((t.textContent || '').trim(), (r.left - sr.left) * dpr, (r.top - sr.top) * dpr);
      const data = c2.getImageData(0, 0, W, H).data;
      const gs = Math.max(3, Math.round(5 * dpr));
      for (let y = 0; y < H; y += gs) for (let x = 0; x < W; x += gs) if (data[(y * W + x) * 4 + 3] > 140) pts.push([x, y, 1]);
    }
    return pts;
  };

  const seed = () => {
    parts = targets().map((p, i) => {
      const a = rnd(i) * Math.PI * 2, rad = Math.hypot(W, H) * (0.55 + rnd(i + 5) * 0.6);
      return { tx: p[0], ty: p[1], glyph: p[2], sx: W / 2 + Math.cos(a) * rad, sy: H / 2 + Math.sin(a) * rad, d: rnd(i + 11) * 0.34 };
    });
    sampled = true;
  };

  const draw = t => {
    if (!cx) return;
    cx.clearRect(0, 0, W, H);
    const fade = t > 0.86 ? 1 - (t - 0.86) / 0.14 : 1;
    parts.forEach(p => {
      const k = Math.min(1, Math.max(0, (t - p.d) / (1 - p.d)));
      const e = 1 - Math.pow(1 - k, 3);
      const x = p.sx + (p.tx - p.sx) * e, y = p.sy + (p.ty - p.sy) * e;
      cx.fillStyle = p.glyph ? INK + (0.35 + 0.6 * k) * fade + ')' : ACC + (0.3 + 0.6 * k) * fade + ')';
      const s = (p.glyph ? 1.5 : 1.2) * dpr * (1.7 - 0.7 * e);
      cx.fillRect(x - s / 2, y - s / 2, s, s);
    });
  };

  const reset = () => {
    const g = G(); if (!g) return;
    if (!sampled) seed();
    draw(0);
    g.set(content(stage), { opacity: 0 });
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    if (!sampled) seed();
    reset();
    const sp = 1 / (ctx.speed || 1);
    const o = { t: 0 };
    const tl = g.timeline();
    tl.to(o, { t: 1, duration: 1.7 * sp, ease: 'none', onUpdate: () => draw(o.t) })
      .to(content(stage), { opacity: 1, duration: 0.5 * sp, ease: 'power2.out' }, 1.25 * sp);
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(() => { sampled = false; });
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(cv); host.remove(); } };
}

/* --------------------------------------------- 07 · Shattered Reveal --- */

export function shatteredReveal(stage, ctx = {}) {
  const host = layer(stage, 'overflow:hidden');
  let pieces = [];

  const build = () => {
    host.innerHTML = ''; pieces = [];
    const src = content(stage);
    if (!src) return;
    const N = 8, j = 5;
    for (let i = 0; i < N; i++) {
      const y0 = i / N * 100, y1 = (i + 1) / N * 100;
      const w = mk('div', 'position:absolute;inset:0;will-change:transform;clip-path:polygon(' +
        '-6% ' + (y0 - rnd(i) * j).toFixed(2) + '%,106% ' + (y0 - rnd(i + 3) * j).toFixed(2) + '%,' +
        '106% ' + (y1 + rnd(i + 7) * j).toFixed(2) + '%,-6% ' + (y1 + rnd(i + 11) * j).toFixed(2) + '%)', host);
      const c = src.cloneNode(true);
      c.removeAttribute('data-anim-content');
      c.removeAttribute('data-anim-title');
      c.querySelectorAll('[data-anim-title]').forEach(n => n.removeAttribute('data-anim-title'));
      w.appendChild(c);
      pieces.push(w);
    }
  };

  const reset = () => {
    const g = G(); if (!g) return;
    g.set(host, { opacity: 1 });
    g.set(content(stage), { opacity: 0 });
    g.set(pieces, {
      opacity: 0, x: (i) => (rnd(i) - 0.5) * 220, y: (i) => (rnd(i + 5) - 0.5) * 110,
      rotate: (i) => (rnd(i + 9) - 0.5) * 7, filter: 'blur(10px)'
    });
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const tl = g.timeline();
    tl.to(pieces, { opacity: 1, duration: 0.35 * sp, stagger: { each: 0.04 * sp, from: 'center' } })
      .to(pieces, { x: 0, y: 0, rotate: 0, filter: 'blur(0px)', duration: 1.25 * sp, ease: 'power4.out', stagger: { each: 0.05 * sp, from: 'center' } }, 0.1 * sp)
      .set(content(stage), { opacity: 1 })
      .set(host, { opacity: 0 });
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(pieces); host.remove(); } };
}

/* -------------------------------------------------- 08 · Liquid Glass --- */

export function liquidGlass(stage, ctx = {}) {
  const host = layer(stage, 'overflow:hidden');
  const band = mk('div', 'position:absolute;top:-12%;height:124%;width:34%;left:0;' +
    'backdrop-filter:blur(15px) saturate(1.5) brightness(1.14);-webkit-backdrop-filter:blur(15px) saturate(1.5) brightness(1.14);' +
    'background:linear-gradient(100deg,' + ACC + '.10),' + INK + '.05) 45%,' + ACC + '.16));' +
    'border-left:1px solid ' + INK + '.40);border-right:1px solid ' + ACC + '.85);' +
    'box-shadow:0 0 40px ' + ACC + '.18);transform:skewX(-9deg)', host);
  mk('span', 'position:absolute;inset:0;background:radial-gradient(55% 40% at 30% 30%,' + INK + '.10),transparent 70%)', band);

  const reset = () => {
    const g = G(); if (!g) return;
    const { w } = size(stage);
    g.set(band, { x: -w * 0.5, opacity: 0 });
    g.set(content(stage), { clipPath: 'inset(0% 100% 0% 0%)', filter: 'blur(7px)' });
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const { w } = size(stage);
    const tl = g.timeline();
    tl.to(band, { opacity: 1, duration: 0.25 * sp })
      .to(band, { x: w * 1.15, duration: 1.5 * sp, ease: 'power2.inOut' }, 0)
      .to(content(stage), { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.45 * sp, ease: 'power2.inOut' }, 0.1 * sp)
      .to(content(stage), { filter: 'blur(0px)', duration: 0.9 * sp, ease: 'power2.out' }, 0.5 * sp)
      .to(band, { opacity: 0, duration: 0.35 * sp }, '>-0.35');
    if (instant || red()) tl.progress(1);
    return tl;
  };

  reset();
  return { play, reset, resize: reset, destroy: () => { const g = G(); if (g) g.killTweensOf([band, content(stage)]); host.remove(); } };
}

/* ------------------------------------------------ 09 · Chevron Mosaic --- */

export function chevronMosaic(stage, ctx = {}) {
  const host = layer(stage, 'overflow:hidden');
  let tris = [];

  const build = () => {
    host.innerHTML = ''; tris = [];
    const { w, h } = size(stage);
    const svg = svgEl('svg', { width: w, height: h, viewBox: '0 0 ' + w + ' ' + h, style: 'position:absolute;inset:0' }, host);
    const cols = w < 620 ? 14 : w < 1100 ? 20 : 26;
    const cw = w / cols, rh = cw * 0.88;
    const deep = h * (ctx.mosaicDepth || 0.52);
    const half = w / 2;
    const limit = cx => deep * Math.pow(Math.min(1, Math.abs(cx - half) / half), 0.92);
    let i = 0;
    for (let r = 0; r * rh < deep; r++) {
      const y0 = r * rh, y1 = y0 + rh;
      for (let j = -1; j <= cols * 2 + 1; j++) {
        const x0 = Math.floor(j / 2) * cw + (j % 2 ? cw / 2 : 0);
        const pts = (j % 2 === 0)
          ? [[x0, y1], [x0 + cw, y1], [x0 + cw / 2, y0]]
          : [[x0, y0], [x0 + cw, y0], [x0 + cw / 2, y1]];
        const cx = (pts[0][0] + pts[1][0] + pts[2][0]) / 3;
        const cy = (pts[0][1] + pts[1][1] + pts[2][1]) / 3;
        i++;
        const lim = limit(cx);
        if (cy > lim || lim < rh * 0.4) continue;
        const rel = cy / lim;
        if (rnd(i, 1.7) > 1 - Math.pow(rel, 1.5) * 0.96) continue;
        const v = rnd(i + 13, 2.3), a = 1 - rel * 0.55;
        const fill = v < 0.26 ? ACC + (0.62 * a).toFixed(3) + ')'
          : v < 0.48 ? INK + (0.16 * a).toFixed(3) + ')'
            : v < 0.72 ? 'rgba(88,76,150,' + (0.45 * a).toFixed(3) + ')'
              : v < 0.88 ? INK + (0.05 * a).toFixed(3) + ')'
                : 'none';
        const p = svgEl('polygon', {
          points: pts.map(q => q[0].toFixed(1) + ',' + q[1].toFixed(1)).join(' '),
          fill: fill,
          stroke: v > 0.88 ? ACC + (0.40 * a).toFixed(3) + ')' : INK + (0.06 * a).toFixed(3) + ')',
          'stroke-width': '1', 'vector-effect': 'non-scaling-stroke'
        }, svg);
        p._o = cy * 1.05 + Math.abs(cx - half) * 0.5;
        p._i = i;
        tris.push(p);
      }
    }
    tris.sort((a, b) => a._o - b._o);
  };

  const reset = () => {
    const g = G(); if (!g) return;
    g.set(content(stage), { opacity: 0, y: 16 });
    tris.forEach(t => g.set(t, {
      transformOrigin: '50% 50%', opacity: 0, scale: 0.3,
      y: -16 - rnd(t._i + 3) * 26, rotate: (rnd(t._i + 8) - 0.5) * 40
    }));
  };

  const play = instant => {
    const g = G(); if (!g) return null;
    reset();
    const sp = 1 / (ctx.speed || 1);
    const tl = g.timeline();
    tl.to(tris, {
      opacity: 1, scale: 1, y: 0, rotate: 0, duration: 0.72 * sp, ease: 'power3.out',
      stagger: { each: 0.006 * sp }
    })
      .to(content(stage), { opacity: 1, y: 0, duration: 0.8 * sp, ease: 'power2.out' }, 0.34 * sp);
    if (instant || red()) tl.progress(1);
    return tl;
  };

  build();
  return { play, reset, resize: build, destroy: () => { const g = G(); if (g) g.killTweensOf(tris); host.remove(); } };
}

/* ---------------------------------------------------------- registre --- */

export const ENTRANCES = {
  glass: brokenGlass,
  scan: industrialScan,
  ink: organicReveal,
  glitch: glitchFrame,
  wire: wireframe,
  particles: particleAssembly,
  shatter: shatteredReveal,
  liquid: liquidGlass,
  mosaic: chevronMosaic
};

export const CONCEPTS = [
  { id: 'glass', n: '01', name: 'Broken Glass', tech: 'SVG · 20 fragments · stagger aléatoire' },
  { id: 'scan', n: '02', name: 'Industrial Scan', tech: 'DOM · arêtes tiretées · balayage' },
  { id: 'ink', n: '03', name: 'Organic Reveal', tech: 'Canvas · destination-out · blur' },
  { id: 'glitch', n: '04', name: 'Glitch Frame', tech: 'DOM · 7 tranches · dédoublement' },
  { id: 'wire', n: '05', name: 'Wireframe', tech: 'CSS 3D · 12 arêtes · aplatissement' },
  { id: 'particles', n: '06', name: 'Particle Assembly', tech: 'Canvas · glyphes échantillonnés' },
  { id: 'shatter', n: '07', name: 'Shattered Reveal', tech: 'DOM · clones clippés · réalignement' },
  { id: 'liquid', n: '08', name: 'Liquid Glass', tech: 'backdrop-filter · clip-path' },
  { id: 'mosaic', n: '09', name: 'Chevron Mosaic', tech: 'SVG · maille triangulaire · masque chevron' }
];

export function create(id, stage, ctx) {
  const f = ENTRANCES[id];
  return f ? f(stage, ctx || {}) : null;
}

export default { create, ENTRANCES, CONCEPTS };
