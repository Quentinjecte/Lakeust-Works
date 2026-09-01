/* labs/chevron.js — géométrie du chevron en escalier mité.

   Deux bras partent d'un apex. Chaque bras empile des bandes jointives,
   décalées perpendiculairement. La règle mesurée sur le croquis :

     bande 0 : rien devant elle, elle traverse l'apex et continue.
     bande i : coupée le long de l'arête intérieure de la bande i-1,
               qui appartient à l'AUTRE bras.

   Donc les fins de bande descendent en escalier alterné (gauche, droite,
   gauche, droite) et deux bandes ne se recouvrent jamais : chacune butte
   contre la précédente, son arête posée sur la ligne de sa voisine.

   Les formes sont des polygones exacts posés en clipPath SVG. Le clip vit sur
   un conteneur fixe ; le contenu glisse à l'intérieur. Une bande ne peut donc
   pas empiéter sur une autre, même en mouvement. */

const NS = 'http://www.w3.org/2000/svg';
const el = (n) => document.createElementNS(NS, n);

/* p1 + s·d1 = p2 + r·d2 */
function isect(p1, d1, p2, d2) {
  const den = d1[0] * d2[1] - d1[1] * d2[0];
  if (Math.abs(den) < 1e-9) return [p1[0], p1[1]];
  const s = ((p2[0] - p1[0]) * d2[1] - (p2[1] - p1[1]) * d2[0]) / den;
  return [p1[0] + d1[0] * s, p1[1] + d1[1] * s];
}

export class Chevron {
  constructor(stage, bands, opts = {}) {
    this.stage = stage;
    this.bands = bands;
    this.o = Object.assign({
      apexX: 0.5, apexY: 0.06, arm: 18,
      thickRatio: 0.2, minThick: 92, maxThick: 560, gap: 0,
      push: 0.2, dim: 0.42, tileRatio: 0.5, labelPos: 0.34,
      edgeOff: 'rgba(145,132,217,.26)',
      edgeOn: 'rgba(182,172,234,.78)',
      onState: null
    }, opts);
    this.sel = -1;
    this.uid = 'cv' + Math.random().toString(36).slice(2, 8);
    this.geo = [];
    this._svg();
    this._ro = new ResizeObserver(() => this.measure());
    this._ro.observe(stage);
    this.measure();
  }

  destroy() {
    this._ro.disconnect();
    if (this.svg) this.svg.remove();
  }

  _svg() {
    const svg = el('svg');
    svg.setAttribute('aria-hidden', 'true');
    svg.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:20;overflow:visible';
    const defs = el('defs');
    svg.appendChild(defs);
    this.polys = []; this.edges = [];
    this.bands.forEach((b, i) => {
      const cp = el('clipPath');
      cp.setAttribute('id', this.uid + '-' + i);
      cp.setAttribute('clipPathUnits', 'userSpaceOnUse');
      const pg = el('polygon');
      cp.appendChild(pg); defs.appendChild(cp);
      b.style.clipPath = 'url(#' + this.uid + '-' + i + ')';
      this.polys.push(pg);

      const p = el('path');
      p.setAttribute('fill', 'none');
      p.setAttribute('stroke', this.o.edgeOff);
      p.setAttribute('stroke-width', '1');
      p.setAttribute('vector-effect', 'non-scaling-stroke');
      p.style.transition = 'stroke .34s ease';
      svg.appendChild(p);
      this.edges.push(p);
    });
    this.stage.appendChild(svg);
    this.svg = svg;
  }

  measure() {
    const w = this.stage.clientWidth, h = this.stage.clientHeight;
    if (!w || !h) return;
    const o = this.o;
    const A = o.arm * Math.PI / 180, ca = Math.cos(A), sa = Math.sin(A);
    const U = [[-ca, sa], [ca, sa]];   // direction sortante de chaque bras
    const N = [[sa, ca], [-sa, ca]];   // normale vers l'intérieur du V
    const P = [w * o.apexX, h * o.apexY];
    const t = Math.max(o.minThick, Math.min(o.maxThick, h * o.thickRatio));
    const len = Math.hypot(w, h) * 2.4;
    const back = len * 0.5;

    this.geo = this.bands.map((band, i) => {
      const a = i % 2, b = 1 - a, k = (i - a) / 2;
      const u = U[a], n = N[a];
      const off = k * (t + o.gap), offIn = off + t;
      const pOut = [P[0] + n[0] * off, P[1] + n[1] * off];
      const pIn = [P[0] + n[0] * offIn, P[1] + n[1] * offIn];

      let cA, cB;
      if (i === 0) {
        cA = [pOut[0] - u[0] * back, pOut[1] - u[1] * back];
        cB = [pIn[0] - u[0] * back, pIn[1] - u[1] * back];
      } else {
        /* arête intérieure de la bande i-1, sur l'autre bras */
        const m = Math.ceil(i / 2);
        const cOff = m * t + (m - 1) * o.gap;
        const pc = [P[0] + N[b][0] * cOff, P[1] + N[b][1] * cOff];
        cA = isect(pOut, u, pc, U[b]);
        cB = isect(pIn, u, pc, U[b]);
      }
      const fA = [cA[0] + u[0] * len, cA[1] + u[1] * len];
      const fB = [cB[0] + u[0] * len, cB[1] + u[1] * len];

      const f = (p) => p[0].toFixed(1) + ',' + p[1].toFixed(1);
      this.polys[i].setAttribute('points', [f(cA), f(fA), f(fB), f(cB)].join(' '));
      /* le trait suit la coupe puis l'arête haute : il épouse la voisine */
      this.edges[i].setAttribute('d', 'M' + f(cB) + 'L' + f(cA) + 'L' + f(fA));

      const rot = a === 0 ? -o.arm : o.arm;
      const org = a === 0 ? fA : cA;    // coin local (0,0) : +x vers l'apex à gauche
      const inner = band.querySelector('[data-inner]');
      if (inner) {
        inner.style.left = org[0].toFixed(1) + 'px';
        inner.style.top = org[1].toFixed(1) + 'px';
        inner.style.width = len.toFixed(1) + 'px';
        inner.style.height = t.toFixed(1) + 'px';
      }

      /* Le libellé est posé sur la portion VISIBLE de sa propre bande : on
         part de la coupe, on cherche où l'axe quitte le cadre, et on se place
         à une fraction du trajet. Deux bandes voisines ne peuvent donc plus
         se retrouver de part et d'autre du même joint. */
      const mid = [(cA[0] + cB[0]) / 2, (cA[1] + cB[1]) / 2];
      const s0 = (mid[0] - P[0]) * u[0] + (mid[1] - P[1]) * u[1];
      const cl = [P[0] + n[0] * (off + t / 2), P[1] + n[1] * (off + t / 2)];
      let sMax = Infinity;
      if (u[0] > 1e-6) sMax = Math.min(sMax, (w - cl[0]) / u[0]);
      if (u[0] < -1e-6) sMax = Math.min(sMax, -cl[0] / u[0]);
      if (u[1] > 1e-6) sMax = Math.min(sMax, (h - cl[1]) / u[1]);
      if (u[1] < -1e-6) sMax = Math.min(sMax, -cl[1] / u[1]);
      if (!isFinite(sMax)) sMax = len;
      const sStart = Math.max(s0 + t * 0.8, 0);
      const ls = sStart + Math.max(0, sMax - sStart) * o.labelPos;
      const label = band.querySelector('[data-label]');
      if (label) {
        label.style.left = (cl[0] + u[0] * ls).toFixed(1) + 'px';
        label.style.top = (cl[1] + u[1] * ls).toFixed(1) + 'px';
        label.style.transform = 'translate(-50%,-50%) rotate(' + rot + 'deg)';
      }

      const g = { arm: a, rank: k, rot, len, t, inner, dir: a === 0 ? 1 : -1, push: len * 0 };
      g.push = t * o.push * 4;
      if (band.dataset.tone) this._mosaic(band, g);
      return g;
    });
    this.apply();
  }

  /* Maille triangulaire : index 0 au bord extérieur pour le bras gauche
     (dont +x local pointe vers l'apex), d'où le délai calculé depuis l'apex. */
  _mosaic(band, g) {
    const host = band.querySelector('[data-tiles]');
    if (!host) return;
    const tw = Math.max(30, g.t * this.o.tileRatio);
    const n = Math.min(70, Math.ceil(g.len / tw) + 1);
    host.textContent = '';
    for (let i = 0; i < n; i++) {
      const d = document.createElement('div');
      const fromApex = g.arm === 0 ? (n - 1 - i) : i;
      d.style.cssText = 'flex:none;width:' + tw.toFixed(1) + 'px;height:100%;background:' + band.dataset.tone +
        ';clip-path:polygon(' + (i % 2 ? '50% 0,100% 100%,0 100%' : '0 0,100% 0,50% 100%') +
        ');transition:opacity .52s cubic-bezier(.22,1,.36,1),transform .62s cubic-bezier(.22,1,.36,1)' +
        ';transition-delay:' + Math.min(420, fromApex * 18) + 'ms';
      host.appendChild(d);
    }
    band._tiles = Array.from(host.children);
    band._apexSign = g.dir;
  }

  focus(i) { if (i === this.sel) return; this.sel = i; this.apply(); }
  blur() { if (this.sel < 0) return; this.sel = -1; this.apply(); }

  apply() {
    this.bands.forEach((band, i) => {
      const g = this.geo[i];
      if (!g) return;
      const on = i === this.sel, any = this.sel >= 0;
      if (g.inner) g.inner.style.transform = 'rotate(' + g.rot + 'deg) translateX(' + (on ? g.dir * g.push : 0).toFixed(1) + 'px)';
      band.style.opacity = (on || !any) ? '1' : String(this.o.dim);
      this.edges[i].setAttribute('stroke', on ? this.o.edgeOn : this.o.edgeOff);
      if (this.o.onState) this.o.onState(band, i, { on, any, geo: g });
    });
  }
}
