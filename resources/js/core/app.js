/* app.js — entrée Vite principale : boot des systèmes de page (voir
   core/page-systems.js), de la navigation Barba (core/barba-transitions.js)
   et de la bascule de langue (core/i18n.js). Contrôle : le nettoyage de la
   classe .page-in au tout premier chargement (les transitions Barba la
   retirent elles-mêmes ensuite) et l'appel à initBarba(). Dépendances
   principales : ./bootstrap (axios), core/page-systems.js, core/
   barba-transitions.js, core/i18n.js. */

import './bootstrap';
import './page-systems.js';
import { initBarba } from './barba-transitions.js';
import { bootI18n } from './i18n.js';

bootI18n();

/* .page-in reste sur <main> tant que l'animation d'entrée tourne. Une fois
   finie, on retire la classe : sinon la valeur "translate" qu'elle maintient
   (même égale à "none" en fin de keyframe) continue d'établir un containing
   block pour les descendants position:fixed, ce qui casse les sections
   épinglées de Scroll Lab imbriquées dans <main> (corridor, entrelacs...).
   Ne s'applique qu'au tout premier chargement réel : les transitions Barba
   retirent déjà la classe elles-mêmes avant de jouer leur propre entrée
   (voir core/barba-transitions.js, hooks.beforeEnter). */
document.querySelectorAll('.page-in').forEach(el => {
    el.addEventListener('animationend', () => el.classList.remove('page-in'), { once: true });
});

/* initBarba() se charge elle-même de monter page-systems.js/project.js sur
   le contenu déjà présent au premier chargement (voir bootPageSystems) :
   pas besoin d'un initPage(document) séparé ici. */
initBarba();

document.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        // La page revient depuis le BFCache → on force un reload
        window.location.reload();
    }
});
