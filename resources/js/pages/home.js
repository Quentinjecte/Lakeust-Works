/* home.js — entrée Vite de / (page d'accueil marketing du studio). Page
   autonome, pas de layout partagé : le hero pilote <black-hole-stage> en
   drive="external" (le vrai custom element, resources/js/three/blackhole.js —
   pas la copie CDN de la maquette), plus le curseur personnalisé, le rideau
   de saut interne et la piste horizontale épinglée de la méthode (GSAP/
   ScrollTrigger). [data-reveal] et [data-count] restent hors de ce fichier —
   voir la note dans initScroll() plus bas. */

import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);

const root = document.querySelector('[data-lang]');
if (root) boot();

function boot() {
  let dead = false;
  let raf = 0, craf = 0;
  const st = [];
  const tw = [];

  /* ── i18n ──────────────────────────────────────────────────────────── */
  document.querySelectorAll('[data-lw-lang]').forEach(btn => {
    btn.addEventListener('click', () => {
      const lang = btn.dataset.lwLang;
      root.dataset.lang = lang;
      document.querySelectorAll('[data-lw-lang]').forEach(b => {
        const on = b === btn;
        b.style.borderColor = on ? 'var(--accent)' : 'transparent';
        b.style.background = on ? 'rgba(145,132,217,.16)' : 'transparent';
        b.style.color = on ? 'var(--accent-200)' : 'var(--text-3)';
      });
    });
  });

  /* ── formulaire — démonstration, n'envoie rien ───────────────────────── */
  const form = document.querySelector('[data-lw-form]');
  if (form) form.addEventListener('submit', e => e.preventDefault());

  /* ── hero : le trou noir tourne en drive="external", cette page pousse le shot ── */
  function startStage() {
    const t0 = performance.now();
    const tick = () => {
      if (dead) return;
      const el = document.querySelector('black-hole-stage');
      if (el && el.setShot) {
        const t = (performance.now() - t0) / 1000;
        const h = window.innerHeight || 800;
        const p = Math.min(1, Math.max(0, (window.scrollY || 0) / h));
        const intro = Math.min(1, t / 2.2);
        el.setShot({
          dist: 92 - p * 20 + Math.sin(t * 0.11) * 1.6,
          azim: 0.42 + t * 0.014,
          elev: 0.34 - p * 0.10 + Math.sin(t * 0.17) * 0.02,
          fov: 54, roll: 0, parallax: 1,
          lens: 0.9, diskBright: 1.05, diskThick: 0.5, diskMul: 1.0,
          spin: 0.5, bgFade: 0.9, capture: 0, objDim: 1,
          bloom: 1.05, vignette: 0.96, aberr: 0.2, grain: 0.022,
          exposure: 0.58 * intro * (1 - p * 0.7),
          fade: intro * (1 - p * 0.85)
        });
      }
      raf = requestAnimationFrame(tick);
    };
    tick();
  }

  /* ── curseur personnalisé + survol magnétique ────────────────────────── */
  let cursorEls = null;
  let onMove = null;
  function startCursor() {
    if (matchMedia('(hover: none)').matches) return;
    const c = document.createElement('div');
    c.setAttribute('data-cursor', '1');
    c.style.cssText = 'position:fixed;left:0;top:0;z-index:9999;pointer-events:none;width:34px;height:34px;margin:-17px 0 0 -17px;border-radius:50%;border:1px solid rgba(145,132,217,.7);transition:width .22s ease,height .22s ease,margin .22s ease,background .22s ease;mix-blend-mode:screen';
    const dot = document.createElement('div');
    dot.style.cssText = 'position:fixed;left:0;top:0;z-index:9999;pointer-events:none;width:4px;height:4px;margin:-2px 0 0 -2px;border-radius:50%;background:#d2cefd';
    document.body.appendChild(c);
    document.body.appendChild(dot);
    cursorEls = { c, dot };

    let mx = innerWidth / 2, my = innerHeight / 2, cx = mx, cy = my;
    const magnets = new Map();
    onMove = e => {
      mx = e.clientX; my = e.clientY;
      dot.style.transform = 'translate3d(' + mx + 'px,' + my + 'px,0)';
      const hit = e.target && e.target.closest ? e.target.closest('[data-magnet]') : null;
      const big = hit || (e.target && e.target.closest && e.target.closest('a,button'));
      c.style.width = c.style.height = big ? '58px' : '34px';
      c.style.margin = big ? '-29px 0 0 -29px' : '-17px 0 0 -17px';
      c.style.background = big ? 'rgba(145,132,217,.12)' : 'transparent';
      if (hit) {
        const r = hit.getBoundingClientRect();
        const dx = (mx - (r.left + r.width / 2)) / Math.max(r.width, 1);
        const dy = (my - (r.top + r.height / 2)) / Math.max(r.height, 1);
        const k = Math.min(14, r.width * 0.05);
        hit.style.transform = 'translate3d(' + (dx * k).toFixed(2) + 'px,' + (dy * k).toFixed(2) + 'px,0)';
        hit.style.transition = 'transform .18s ease-out';
        magnets.set(hit, 1);
      }
      magnets.forEach((_, el) => {
        if (el !== hit) { el.style.transform = ''; el.style.transition = 'transform .45s cubic-bezier(.2,.8,.2,1)'; magnets.delete(el); }
      });
    };
    window.addEventListener('pointermove', onMove, { passive: true });

    const follow = () => {
      if (dead) return;
      cx += (mx - cx) * 0.16; cy += (my - cy) * 0.16;
      c.style.transform = 'translate3d(' + cx.toFixed(1) + 'px,' + cy.toFixed(1) + 'px,0)';
      craf = requestAnimationFrame(follow);
    };
    follow();
  }

  /* ── rideau à la Barba sur les ancres internes ───────────────────────── */
  let onClick = null;
  function startCurtain() {
    const curtain = document.querySelector('[data-curtain]');
    onClick = e => {
      const a = e.target && e.target.closest ? e.target.closest('[data-jump]') : null;
      if (!a || !curtain) return;
      e.preventDefault();
      const id = a.getAttribute('data-jump');
      const target = id === 'top' ? document.body : document.getElementById(id);
      if (!target) return;
      const y = id === 'top' ? 0 : target.getBoundingClientRect().top + window.scrollY - 60;
      gsap.timeline()
        .to(curtain, { opacity: 1, duration: 0.34, ease: 'power2.in' })
        .add(() => window.scrollTo({ top: y, behavior: 'instant' }))
        .to(curtain, { opacity: 0, duration: 0.5, ease: 'power2.out' }, '+=0.06');
    };
    document.addEventListener('click', onClick, true);
  }

  /* ── hero / piste horizontale épinglée ────────────────────────────────
     [data-reveal] et [data-count] ne sont PAS pilotés ici : ils le sont déjà
     site-wide par setupReveal() dans core/page-systems.js (IntersectionObserver
     + classe .is-in, voir reveals.css), appelé via initPage(root) dans
     welcome-lakeust.js sur tout le sous-arbre [data-wl-root] — qui couvre
     cette page en entier. Un second système ici (GSAP/ScrollTrigger, styles
     inline) entrait en conflit avec le premier : opacité inline à 0 jamais
     rattrapée par la classe .is-in (spécificité), sections entières restant
     invisibles, et deux compteurs concurrents sur les mêmes [data-count]. */
  function initScroll() {
    if (dead) return;
    const q = s => Array.from(document.querySelectorAll(s));
    const keep = t => { tw.push(t); if (t.scrollTrigger) st.push(t.scrollTrigger); return t; };

    keep(gsap.from(q('[data-hero]'), { y: 26, opacity: 0, duration: 1.1, ease: 'power3.out', stagger: 0.12, delay: 0.35 }));

    const track = document.querySelector('[data-track]');
    const pin = document.querySelector('[data-pin]');
    if (track && pin && window.innerWidth > 900) {
      const dist = () => Math.max(0, track.scrollWidth - window.innerWidth + 40);
      keep(gsap.to(track, {
        x: () => -dist(), ease: 'none',
        scrollTrigger: {
          trigger: pin, start: 'top top', end: () => '+=' + (dist() + window.innerHeight * 0.6),
          pin: true, scrub: 0.6, invalidateOnRefresh: true, anticipatePin: 1
        }
      }));
    }
    ScrollTrigger.refresh();
  }

  startStage();
  startCursor();
  startCurtain();
  initScroll();

  addEventListener('pagehide', () => {
    dead = true;
    cancelAnimationFrame(raf);
    cancelAnimationFrame(craf);
    st.forEach(t => { try { t.kill(true); } catch (e) { /* déjà démonté */ } });
    tw.forEach(t => { try { t.kill(); } catch (e) { /* déjà démonté */ } });
    if (cursorEls) { cursorEls.c.remove(); cursorEls.dot.remove(); }
    if (onMove) window.removeEventListener('pointermove', onMove);
    if (onClick) document.removeEventListener('click', onClick, true);
  }, { once: true });
}
