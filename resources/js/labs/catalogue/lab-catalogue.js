/* lab-catalogue.js — entrée Vite de /lab, le catalogue qui liste les six
   labs. Contrairement aux labs eux-mêmes, cette page n'a qu'un seul écran :
   pas de rail de navigation, pas de mécaniques à monter/démonter. Se
   contente de démarrer motion.js et d'y monter les reveals/chars des
   fiches, exactement ce que labs/scroll/scroll-lab.js fait pour son propre
   écran Catalogue (data-lab="catalogue") — mais sans charger les quinze
   autres mécaniques de Scroll Lab dont cette page n'a aucun usage. */

import * as M from '../../animation/motion.js';

const sec = document.querySelector('.lab-sec[data-lab="catalogue"]');
if (sec) {
  sec.classList.add('is-active'); // .lab-sec est display:none tant que ça n'est pas posé (voir css/labs/lab-shared.css)
  M.boot().then(() => {
    M.mountScreen(sec, [
      r => M.reveals(r),
      r => M.converge(r),
      r => r.querySelectorAll('[data-chars]').forEach(el => M.charsIn(el, { delay: 0.1 }))
    ]);
  });
}
