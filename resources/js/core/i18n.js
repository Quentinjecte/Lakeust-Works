/* i18n.js — bascule de langue FR/EN partagée par tout le site sauf
   home.blade.php (exclu du périmètre, garde son propre mécanisme).
   Contrôle : l'attribut [data-lang] sur <html> (lu par core/i18n.css pour
   les paires .i18n-fr/.i18n-en déjà écrites à la main — welcome.blade.php,
   about.blade.php), et le remplacement de texte pour tout élément portant
   data-i18n="clé" (nouveau contenu, voir resources/js/i18n/dictionary.json).
   Dépendances : dictionary.json (import statique, bundlé — pas de requête
   réseau séparée). Paramètre modifiable : SUPPORTED (langues proposées
   dans le sélecteur) et STORAGE_KEY. Précaution : refreshI18n() doit être
   rappelé après chaque swap Barba (voir core/barba-transitions.js), sinon
   le contenu qui vient d'arriver dans <main> reste dans sa langue par
   défaut (le HTML de la page, toujours écrit en français). */

import dict from '../i18n/dictionary.json';

const STORAGE_KEY = 'lakeust-lang';
const SUPPORTED = ['fr', 'en'];
const LABELS = { fr: 'FR', en: 'EN' };

export function getLang() {
  const saved = localStorage.getItem(STORAGE_KEY);
  return SUPPORTED.includes(saved) ? saved : 'fr';
}

/* Remplace le texte de tout [data-i18n] trouvé dans `scope` (document par
   défaut, ou juste le conteneur Barba qui vient d'être injecté). Repli sur
   le français si la clé n'a pas de traduction dans la langue demandée —
   jamais un texte vide. data-i18n-html bascule sur innerHTML (pour les
   rares chaînes qui ont besoin d'une balise, ex. <br>). */
function applyDict(lang, scope = document) {
  scope.querySelectorAll('[data-i18n]').forEach(el => {
    const entry = dict[el.dataset.i18n];
    if (!entry) return;
    const text = entry[lang] || entry.fr || '';
    if (el.dataset.i18nHtml !== undefined) el.innerHTML = text;
    else el.textContent = text;
  });
  /* data-i18n-attr="placeholder:form.name.placeholder;title:autre.cle" —
     pour traduire un attribut plutôt que le contenu d'un élément. */
  scope.querySelectorAll('[data-i18n-attr]').forEach(el => {
    el.dataset.i18nAttr.split(';').forEach(pair => {
      const [attr, key] = pair.split(':').map(s => s.trim());
      const entry = key && dict[key];
      if (attr && entry) el.setAttribute(attr, entry[lang] || entry.fr || '');
    });
  });
}

export function refreshI18n(scope) {
  applyDict(getLang(), scope);
}

export function setLang(lang) {
  if (!SUPPORTED.includes(lang)) lang = 'fr';
  localStorage.setItem(STORAGE_KEY, lang);
  document.documentElement.lang = lang;
  document.documentElement.dataset.lang = lang;
  applyDict(lang);
  document.querySelectorAll('[data-lang-select]').forEach(el => { el.value = lang; });
}

/* Construit et câble un <select> dans chaque [data-lang-switch] trouvé sur
   la page (nav, footer...). Idempotent : sans effet si déjà monté (utile
   après un swap Barba, le mount du nav persiste et n'a pas besoin d'être
   reconstruit). */
export function initLangSwitch(scope = document) {
  scope.querySelectorAll('[data-lang-switch]').forEach(mount => {
    if (mount.dataset.langReady) return;
    mount.dataset.langReady = '1';
    const select = document.createElement('select');
    select.className = 'lang-select';
    select.setAttribute('data-lang-select', '');
    const ariaEntry = dict['lang.aria'];
    select.setAttribute('aria-label', (ariaEntry && ariaEntry[getLang()]) || 'Langue / Language');
    SUPPORTED.forEach(code => {
      const opt = document.createElement('option');
      opt.value = code;
      opt.textContent = LABELS[code];
      select.appendChild(opt);
    });
    select.value = getLang();
    select.addEventListener('change', () => setLang(select.value));
    mount.appendChild(select);
  });
}

export function bootI18n() {
  initLangSwitch();
  setLang(getLang());
}
