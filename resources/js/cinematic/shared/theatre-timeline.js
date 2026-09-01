/* theatre-timeline.js — mécanique partagée par les trois cinématiques Theatre.js
   (welcome-cinematic.js, forest-cinematic.js, orbital-cinematic.js) : le
   compilateur qui transforme une table SHOTS en fichier de sauvegarde Theatre,
   l'échantillonneur de secours (mêmes nombres, mêmes easings, sans Theatre.js),
   et l'attente d'un <custom-element> de scène prêt.

   Ce qui NE se factorise PAS ici : la table SHOTS de chaque cinématique (la
   chorégraphie elle-même — caméra, ciel, cues UI) et createCinematic() dans
   chaque fichier -cinematic.js. Les trois créateurs diffèrent réellement : le
   nombre de groupes Theatre (4 pour welcome/forest, 7 pour orbital), la
   profondeur de repli si Theatre.js est indisponible (orbital retombe sur une
   horloge interne complète ; welcome/forest ne retombent que si l'état compilé
   est rejeté), et la boucle d'amorçage (orbital s'auto-démarre, les deux
   autres sont pilotés par leur -entry.js). Les forcer dans une seule fonction
   ajouterait une couche d'indirection sans réduire la complexité réelle. */

/* Points de contrôle des easings, convention CSS cubic-bezier. */
export const EASE = {
  linear: [0, 0, 1, 1],
  in: [0.42, 0, 1, 1],
  out: [0, 0, 0.58, 1],
  inOut: [0.42, 0, 0.58, 1],
  expoIn: [0.7, 0, 0.84, 0],
  expoOut: [0.16, 1, 0.3, 1],
  hold: [1, 0, 1, 0]
};

/* Générateur d'identifiants courts, préfixé par cinématique (évite les
   collisions si jamais deux séquences partagent un jour le même document). */
export function createIdFactory(prefix) {
  let uid = 0;
  return () => prefix + (uid++).toString(36) + Math.floor(Math.random() * 1e6).toString(36);
}

/* Résolution d'une courbe de Bézier cubique par Newton-Raphson (convention
   cubic-bezier CSS : x = temps normalisé, y = valeur interpolée). */
export function bezier(t, p) {
  const [x1, y1, x2, y2] = p;
  let a = t;
  for (let i = 0; i < 6; i++) {
    const s = 1 - a;
    const x = 3 * s * s * a * x1 + 3 * s * a * a * x2 + a * a * a;
    const dx = 3 * s * s * x1 + 6 * s * a * (x2 - x1) + 3 * a * a * (1 - x2);
    if (Math.abs(dx) < 1e-5) break;
    a = Math.min(1, Math.max(0, a - (x - t) / dx));
  }
  const s = 1 - a;
  return 3 * s * s * a * y1 + 3 * s * a * a * y2 + a * a * a;
}

/* Échantillonnage direct de la table SHOTS à un instant donné — le filet de
   sécurité si Theatre.js (ou l'état compilé) n'est pas disponible : mêmes
   nombres, mêmes easings, aucune dépendance externe. `out`, s'il est fourni,
   est réutilisé plutôt que réalloué (utile dans une boucle rAF). */
export function sampleTable(SHOTS, pos, out) {
  out = out || {};
  for (const objKey in SHOTS) {
    const vals = out[objKey] || (out[objKey] = {});
    for (const prop in SHOTS[objKey]) {
      const rows = SHOTS[objKey][prop];
      if (pos <= rows[0][0]) { vals[prop] = rows[0][1]; continue; }
      const last = rows[rows.length - 1];
      if (pos >= last[0]) { vals[prop] = last[1]; continue; }
      let i = 0;
      while (i < rows.length - 1 && rows[i + 1][0] <= pos) i++;
      const [t0, v0, ease] = rows[i], [t1, v1] = rows[i + 1];
      const u = (pos - t0) / Math.max(t1 - t0, 1e-6);
      vals[prop] = v0 + (v1 - v0) * bezier(u, EASE[ease || 'inOut'] || EASE.inOut);
    }
  }
  return out;
}

/* Compile une table SHOTS en vrai fichier de sauvegarde Theatre : une piste
   keyframée par prop, avec les poignées de Bézier dérivées de EASE. C'est ce
   qui rend chaque timing déplaçable dans l'éditeur (?studio) plutôt que codé
   en dur dans des tweens. */
export function compileState({ SHOTS, LENGTH, sheetName, nid }) {
  const tracksByObject = {};
  for (const objKey in SHOTS) {
    const trackData = {}, trackIdByPropPath = {};
    for (const prop in SHOTS[objKey]) {
      const rows = SHOTS[objKey][prop];
      const id = nid();
      const kfs = rows.map(([pos, value]) => ({
        id: nid(), position: pos, connectedRight: true,
        handles: [0.5, 1, 0.5, 0], type: 'bezier', value
      }));
      rows.forEach((row, i) => {
        if (i >= kfs.length - 1) return;
        const [x1, y1, x2, y2] = EASE[row[2] || 'inOut'] || EASE.inOut;
        kfs[i].handles[2] = x1; kfs[i].handles[3] = y1;
        kfs[i + 1].handles[0] = x2; kfs[i + 1].handles[1] = y2;
      });
      trackData[id] = { type: 'BasicKeyframedTrack', __debugName: objKey + ':["' + prop + '"]', keyframes: kfs };
      trackIdByPropPath['["' + prop + '"]'] = id;
    }
    tracksByObject[objKey] = { trackData, trackIdByPropPath };
  }
  return {
    sheetsById: {
      [sheetName]: {
        staticOverrides: { byObject: {} },
        sequence: { subUnitsPerUnit: 30, length: LENGTH, type: 'PositionalSequence', tracksByObject }
      }
    },
    definitionVersion: '0.4.0',
    revisionHistory: [nid()]
  };
}

/* Attend qu'un <custom-element> de scène soit prêt. Les trois stages
   (black-hole-stage, forest-stage, orbital-stage) déclarent setShot de façon
   asynchrone (import dynamique de Three.js dans connectedCallback) : le
   sélecteur seul ne suffit pas, il faut sonder la méthode. */
export function waitForStage(selector, timeoutMs = 15000) {
  return new Promise((resolve, reject) => {
    const t0 = performance.now();
    const look = () => {
      const el = document.querySelector(selector);
      if (el && typeof el.setShot === 'function') return resolve(el);
      if (performance.now() - t0 > timeoutMs) return reject(new Error('stage timeout'));
      requestAnimationFrame(look);
    };
    look();
  });
}
