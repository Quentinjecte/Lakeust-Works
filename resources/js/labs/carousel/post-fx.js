/* labs/post-fx.js — transitions par post-traitement pour le Carousel Lab.

   Le carrousel est du DOM : impossible de le rendre dans une texture sans le
   recopier. On applique donc le shader là où le navigateur en accepte un sur
   du DOM vivant — un graphe de filtres SVG, exécuté par le compositeur GPU.
   Le graphe est monté une fois ; chaque image ne réécrit qu'un ou deux
   attributs numériques (le seuil, l'amplitude), jamais la structure.

   Deux passes :
     dissolve — seuil sur un champ de bruit. Le plan sortant est mangé pixel
                par pixel, l'entrant se reforme dans un bruit de graine
                différente, et la frontière entre les deux brûle en accent.
     fracture — displacement map + séparation RVB. Le plan se disloque, les
                trois couches se décalent, la coupe a lieu au pic.

   L'appelant ne connaît que show(i). */

const NS = 'http://www.w3.org/2000/svg';
let uid = 0;

const el = (t, a, p) => {
  const n = document.createElementNS(NS, t);
  for (const k in a) n.setAttribute(k, a[k]);
  if (p) p.appendChild(n);
  return n;
};

const ACC = '#b3a9e6';
const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ------------------------------------------------------------ dissolve --- */
/* alpha = step(seuil, bruit) : le corps garde ce qui passe le seuil, une
   seconde coupe 0.10 plus haut donne la bande de front, teintée en accent. */
function dissolveFilter(defs, id, seed) {
  const S = 26;
  const f = el('filter', { id, x: '-10%', y: '-10%', width: '120%', height: '120%', 'color-interpolation-filters': 'sRGB' }, defs);
  el('feTurbulence', { type: 'fractalNoise', baseFrequency: '0.021 0.027', numOctaves: '5', seed, stitchTiles: 'stitch', result: 'n' }, f);
  el('feColorMatrix', { in: 'n', type: 'matrix', values: '0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1 0 0 0 0', result: 'lum' }, f);
  const keep = el('feComponentTransfer', { in: 'lum', result: 'keep' }, f);
  const fa = el('feFuncA', { type: 'linear', slope: S, intercept: '0' }, keep);
  const core = el('feComponentTransfer', { in: 'lum', result: 'core' }, f);
  const fb = el('feFuncA', { type: 'linear', slope: S, intercept: '0' }, core);
  el('feComposite', { in: 'keep', in2: 'core', operator: 'out', result: 'band' }, f);
  el('feComposite', { in: 'band', in2: 'SourceAlpha', operator: 'in', result: 'bandc' }, f);
  el('feFlood', { 'flood-color': ACC, result: 'acc' }, f);
  el('feComposite', { in: 'acc', in2: 'bandc', operator: 'in', result: 'edge' }, f);
  el('feGaussianBlur', { in: 'edge', stdDeviation: '1.2', result: 'edgeb' }, f);
  el('feComposite', { in: 'SourceGraphic', in2: 'keep', operator: 'in', result: 'body' }, f);
  const m = el('feMerge', {}, f);
  el('feMergeNode', { in: 'body' }, m);
  el('feMergeNode', { in: 'edgeb' }, m);
  /* t : 0 = plan intact, 1 = plan entièrement consommé */
  return t => {
    const T = t * 1.08 - 0.04;
    fa.setAttribute('intercept', (-S * T).toFixed(3));
    fb.setAttribute('intercept', (-S * (T + 0.10)).toFixed(3));
  };
}

/* ------------------------------------------------------------ fracture --- */
function fractureFilter(defs, id, seed) {
  const f = el('filter', { id, x: '-14%', y: '-14%', width: '128%', height: '128%', 'color-interpolation-filters': 'sRGB' }, defs);
  const turb = el('feTurbulence', { type: 'turbulence', baseFrequency: '0.004 0.13', numOctaves: '2', seed, result: 't' }, f);
  const disp = el('feDisplacementMap', { in: 'SourceGraphic', in2: 't', scale: '0', xChannelSelector: 'R', yChannelSelector: 'G', result: 'd' }, f);
  el('feColorMatrix', { in: 'd', type: 'matrix', values: '1 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1 0', result: 'rc' }, f);
  const ro = el('feOffset', { in: 'rc', dx: '0', dy: '0', result: 'r' }, f);
  el('feColorMatrix', { in: 'd', type: 'matrix', values: '0 0 0 0 0 0 1 0 0 0 0 0 0 0 0 0 0 0 1 0', result: 'gc' }, f);
  el('feColorMatrix', { in: 'd', type: 'matrix', values: '0 0 0 0 0 0 0 0 0 0 0 0 1 0 0 0 0 0 1 0', result: 'bc' }, f);
  const bo = el('feOffset', { in: 'bc', dx: '0', dy: '0', result: 'b' }, f);
  el('feBlend', { in: 'r', in2: 'gc', mode: 'screen', result: 'rg' }, f);
  el('feBlend', { in: 'rg', in2: 'b', mode: 'screen' }, f);
  return (a, dir) => {
    disp.setAttribute('scale', (a * 52).toFixed(2));
    ro.setAttribute('dx', (a * 14 * dir).toFixed(2));
    bo.setAttribute('dx', (-a * 14 * dir).toFixed(2));
    turb.setAttribute('seed', String(seed + Math.floor(a * 26)));
  };
}

/* -------------------------------------------------------------- moteur --- */
export function createFX(kind, panels, opt = {}) {
  const g = opt.gsap || window.gsap;
  const sp = 1 / (opt.speed || 1);
  const id = 'clfx' + (++uid);

  const svg = el('svg', { 'aria-hidden': 'true', style: 'position:absolute;width:0;height:0;overflow:hidden;pointer-events:none' });
  const defs = el('defs', {}, svg);
  document.body.appendChild(svg);

  const mk = kind === 'fracture' ? fractureFilter : dissolveFilter;
  const setA = mk(defs, id + 'a', 3);
  const setB = mk(defs, id + 'b', 17);

  /* cur = plan effectivement en place, pending = plan visé. La hystérésis de
     l'appelant se compare à la destination : tant qu'une passe court vers i,
     un nouvel appel vers i est ignoré au lieu de la relancer à zéro. */
  let cur = 0, pending = 0, tl = null;
  const api = { energy: 0, get index() { return pending; } };

  const hard = i => {
    panels.forEach((p, k) => {
      const on = k === i;
      p.style.filter = '';
      p.style.willChange = '';
      p.style.transform = '';
      p.style.opacity = on ? '1' : '0';
      p.style.visibility = on ? 'visible' : 'hidden';
      p.style.pointerEvents = on ? 'auto' : 'none';
      p.style.zIndex = on ? '2' : '1';
    });
  };

  api.show = (i, immediate) => {
    i = Math.max(0, Math.min(panels.length - 1, i));
    if (tl && i === pending && !immediate) return;
    if (tl) { tl.kill(); tl = null; }
    pending = i;
    if (i === cur || immediate || reduced() || !g) { cur = i; api.energy = 0; hard(i); return; }

    const dir = i > cur ? 1 : -1;
    const from = panels[cur], to = panels[i];
    hard(cur);
    [from, to].forEach(p => {
      p.style.visibility = 'visible';
      p.style.opacity = '1';
      p.style.willChange = 'filter,transform';
      p.style.pointerEvents = 'none';
    });
    from.style.zIndex = '3';
    to.style.zIndex = '2';
    from.style.filter = 'url(#' + id + 'a)';
    to.style.filter = 'url(#' + id + 'b)';

    const done = () => { cur = pending = i; tl = null; api.energy = 0; hard(i); };
    const s = { a: 0, b: 1 };

    if (kind === 'fracture') {
      setA(0, dir); setB(0, -dir);
      to.style.opacity = '0';
      tl = g.timeline({
        onUpdate: () => { setA(s.a, dir); setB(s.a, -dir); api.energy = s.a; },
        onComplete: done
      })
        .to(s, { a: 1, duration: 0.24 * sp, ease: 'power2.in' }, 0)
        .to(s, { a: 0, duration: 0.46 * sp, ease: 'power2.out' }, 0.24 * sp)
        .to(from, { opacity: 0, duration: 0.10 * sp, ease: 'none' }, 0.22 * sp)
        .to(to, { opacity: 1, duration: 0.12 * sp, ease: 'none' }, 0.24 * sp)
        .fromTo(to, { scale: 1.04 }, { scale: 1, duration: 0.6 * sp, ease: 'power3.out' }, 0.24 * sp);
    } else {
      setA(0); setB(1);
      tl = g.timeline({
        onUpdate: () => { setA(s.a); setB(s.b); api.energy = Math.min(1, Math.sin(tl.progress() * Math.PI) * 1.15); },
        onComplete: done
      })
        .to(s, { a: 1, duration: 0.62 * sp, ease: 'power1.in' }, 0)
        .to(s, { b: 0, duration: 0.70 * sp, ease: 'power1.out' }, 0.20 * sp)
        .fromTo(to, { scale: 1.035 }, { scale: 1, duration: 0.9 * sp, ease: 'power3.out' }, 0.20 * sp)
        .to(from, { scale: 0.985, duration: 0.62 * sp, ease: 'power1.in' }, 0);
    }
  };

  api.destroy = () => {
    if (tl) tl.kill();
    tl = null;
    pending = cur;
    if (g) g.killTweensOf(panels);
    panels.forEach(p => { p.style.filter = ''; p.style.willChange = ''; });
    svg.remove();
  };

  hard(0);
  return api;
}
