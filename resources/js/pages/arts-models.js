/* arts-models.js — comportements de la branche « Lakeust Arts » (pages/arts/*),
   chargé par pages/arts/subsite.blade.php (section entry). Importer
   ui/model-viewer.js suffit à enregistrer l'élément <lw-model-viewer>.

     [data-ar-filter]    liste des modèles : filtre par outil (data-tool sur les cartes)
     [data-ar-view]      fiche d'un modèle : onglets Rendu / Maillage / Rotation auto
                         (pilotent le <lw-model-viewer data-ar-viewer>)
     [data-ar-build]     page À propos : le modèle du hero arrive en Maillage puis se
                         « construit » en plein, une seule fois */

import '../ui/model-viewer.js';

/* ------------------------------------------------------- filtre par outil --- */
const chips = [...document.querySelectorAll('[data-ar-filter]')];
if (chips.length) {
    const cards = [...document.querySelectorAll('[data-ar-card]')];
    const count = document.querySelector('[data-ar-count]');
    chips.forEach(chip => chip.addEventListener('click', () => {
        const f = chip.dataset.arFilter;
        chips.forEach(c => c.classList.toggle('is-on', c === chip));
        let shown = 0;
        cards.forEach(card => {
            const ok = f === 'all' || card.dataset.tool.split(' ').includes(f);
            card.hidden = !ok;
            if (ok) shown++;
        });
        if (count) count.textContent = String(shown).padStart(2, '0');
    }));
}

/* ------------------------------------------------------ onglets de la fiche --- */
const views = [...document.querySelectorAll('[data-ar-view]')];
const viewer = document.querySelector('[data-ar-viewer]');
if (views.length && viewer) {
    views.forEach(btn => btn.addEventListener('click', () => {
        const v = btn.dataset.arView;
        views.forEach(b => b.classList.toggle('is-on', b === btn));
        viewer.toggleAttribute('wireframe', v === 'wire');
        viewer.toggleAttribute('autorotate', v === 'spin');
    }));
}

/* ------------------------------------------- À propos : le hero se construit --- */
/* Le visualiseur du hero démarre avec l'attribut wireframe ; WIRE ms après la fin du
   premier chargement il passe en plein. Une seule fois : rien n'est rejoué quand le
   visualiseur est démonté / remonté au défilement de la page. */
const WIRE = 1100;
document.querySelectorAll('lw-model-viewer[data-ar-build]').forEach(v => {
    v.addEventListener('lw-model-load', () => setTimeout(() => v.removeAttribute('wireframe'), WIRE), { once: true });
});
