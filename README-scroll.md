# Scroll Lab — mise en place et mode d'emploi

Quinze mécaniques de scroll, un seul socle : `resources/js/portfolio/motion.js`.
Ce document a deux niveaux : la **mise en route** (dix minutes, pour reprendre la main
sur le projet) puis la **référence** (une fiche par mécanique, plus la procédure pour en
ajouter une).

---

# Niveau 1 — Mise en route

## 1. Installer les deux dépendances

```bash
npm i gsap lenis
npm run dev
```

Rien d'autre : pas de plugin GSAP payant (le découpage de texte est fait maison dans
`splitChars()`), pas de bibliothèque de scroll supplémentaire.

## 2. Les fichiers

    resources/js/portfolio/motion.js   le socle : boot, reveals, parallax, les 15 systèmes
    resources/js/lab.js                entrée Vite de la page laboratoire
    resources/css/lab.css              structure des scènes du laboratoire
    resources/views/pages/lab.blade.php  la page (une section par mécanique)
    routes/web.php                     /laboratoire → pages.lab (nom de route : lab)

Le gabarit `layouts/site.blade.php` charge l'entrée Vite renvoyée par la section `entry`,
donc une page peut remplacer `site.js` par la sienne :

```blade
@section('entry', 'resources/js/lab.js')
```

`lab.js` importe déjà `portfolio.css`, `lab.css` et `portfolio/portfolio.js` : il n'y a
jamais deux entrées chargées sur la même page.

## 3. Le seul ordre à respecter

```js
import * as M from './portfolio/motion.js';

await M.boot();          // 1. GSAP + ScrollTrigger + Lenis, une fois par page
M.reveals(document);     // 2. les registres déclaratifs
M.parallax(document);
M.counters(document);
M.fisheye(section);      // 3. les systèmes, sur leur propre scène
M.ScrollTrigger.refresh();
```

`boot()` est idempotent. Tout ce qui mesure la page doit passer **après**, sinon
ScrollTrigger calcule ses bornes sur une mise en page qui n'existe pas encore.

## 4. Ajouter une mécanique à une page existante

1. Dans la vue : une section, une scène en `100vh` avec `overflow:hidden`, et le
   balisage attendu par la mécanique (colonne « Balisage » de la référence).
2. Dans l'entrée JS : une entrée dans l'objet `SYSTEMS` de `lab.js`, clé = valeur de
   `data-lab`. La fonction reçoit la section et renvoie sa fonction de nettoyage.
3. Rien à câbler de plus : la boucle de montage de `lab.js` fait le reste.

## 5. Les cinq pièges

- **Un seul propriétaire du scroll.** Lenis contrôle le scroll ; ne jamais ajouter
  `scroll-behavior: smooth`, `scroll-snap-type` ou un `scrollTo` natif animé par-dessus.
- **Toujours remonter le nettoyage.** Les systèmes qui s'abonnent (`magnet`, `panels`,
  `flux`, et tous ceux qui écoutent `resize`) renvoient une fonction : la conserver et
  l'appeler à la sortie de page, sinon un abonné fuit par visite.
- **Pas de `width`/`height`/`top`/`left` animés.** `transform`, `opacity`, `filter`,
  `clip-path`. La seule exception assumée est `fisheye`, qui anime des hauteurs — d'où
  ses hauteurs normalisées, décrites plus bas.
- **Après toute insertion de contenu, `ScrollTrigger.refresh()`.** Sinon les sections
  épinglées gardent les bornes de l'ancienne hauteur de page.
- **Mode réduit d'abord.** `prefers-reduced-motion` doit laisser une page lisible, pas
  une page vide : chaque système pose son état de repos (souvent la progression finale).

---

# Niveau 2 — Référence

## Le socle

| Fonction | Rôle |
| --- | --- |
| `boot()` | instancie GSAP, ScrollTrigger, Lenis ; renvoie `{ gsap, ScrollTrigger, lenis }` |
| `setMotion('auto' \| 'full' \| 'reduce')` | force le mode de mouvement ; `auto` suit le système |
| `reduced()` | l'état courant, à tester avant tout calcul coûteux |
| `getScroll()` / `getVelocity()` | position et vitesse signée (px/frame à 60 Hz), mises en cache |
| `watchScroll(cb)` | s'abonne au flux de scroll ; **renvoie son désabonnement** |
| `scrollTo(y, opts)` | passe par Lenis s'il est actif |
| `pinned(el, build, opts)` | section épinglée, timeline scrubbée ; `opts.length`, `scrub`, `onUpdate` |
| `driven(el, build, opts)` | identique, sans épinglage |
| `splitChars(el)` / `charsIn(el)` | découpage par caractère (remplace SplitText) |
| `reveals` · `converge` · `parallax` · `counters` | registres déclaratifs, une passe par page |
| `geoTransition(swap)` | transition géométrique entre deux contenus |

Options communes des systèmes épinglés : `length` (longueur de scroll consommée, en px),
`scrub` (retard de suivi, en secondes), `onUpdate` (rappel par frame).

## Les registres déclaratifs

```html
<div data-reveal="hex" data-reveal-delay="180">…</div>
<div data-reveal-stagger="90">…</div>          <!-- décale les enfants directs -->
<div data-speed="0.55" data-scrub-scale="1.06">…</div>
<span data-count="15" data-count-suffix=" k">0</span>
```

`data-reveal` accepte : `up down left right fade blur persp zoom iris hex wipe wipeUp box
line lineY`. Chaque élément ne joue qu'une fois.

## Les quinze mécaniques

| Nº | Fonction | Ce que le scroll pilote | Balisage attendu | Coût |
| --- | --- | --- | --- | --- |
| 01 | `snapPanels(c, {onChange})` | le panneau actif, avec aimantation après le geste | `[data-panel]`, `[data-panel-speed]` | faible |
| 02 | `parallax(root)` | le décalage de chaque plan | `[data-speed]` dans `[data-parallax-scope]` | faible |
| 03 | `pinned(el, …, {onUpdate})` | une valeur 0 → 1 lue par le reste | la scène seule | nul |
| 04 | `pinned` + timeline | le curseur d'une timeline de chapitres | `[data-chapter]` | faible |
| 05 | `infinite(root, {render})` | la fenêtre de rendu d'une liste sans fin | un conteneur vide | faible |
| 06 | `reveals` + `charsIn` | le déclenchement à l'entrée dans l'écran | `[data-reveal]`, `[data-chars]` | faible |
| 07 | `orbit(stage, {spins})` | l'angle, l'inclinaison, puis l'effondrement en grille | `[data-orb]` | moyen |
| 08 | `magnet(stage, {cols, rows, radius})` | **la vitesse et le sens**, pas la position | `[data-field]`, `[data-attractor]` | élevé |
| 09 | `corridor(stage, {travel})` | l'avancée de la caméra en Z | `[data-corridor]`, `[data-plane]` (+ `-x`, `-y`, `-rot`) | élevé |
| 10 | `deconstruct(stage)` | le démontage puis le remontage | `[data-decon-title]`, `[data-part="x,y,rot"]`, `[data-build="x,y,rot"]` | moyen |
| 11 | `fracture(stage)` | quatre lois de mouvement dans une même section | `[data-zone]` (+ `-scale`, `-rot`), `[data-zone-inner]` | faible |
| 12 | `morph(stage, {shapes})` | l'interpolation entre deux formes | `[data-morph]`, `[data-morph-at]` (+ `-win`) | faible |
| 13 | `braid(stage, {turns})` | un angle de phase unique | `[data-strand]` | faible |
| 14 | `foldback(stage)` | une onde triangulaire : la scène se rejoue à l'envers | `[data-fold="0…1"]` (+ `-win`), `[data-fold-ground]`, `[data-fold-ghost]` | faible |
| 15 | `fisheye(stage, {min, sigma})` | le point de lecture, pas le défilement | `[data-bands]`, `[data-band]`, `[data-band-head]`, `[data-band-body]` | moyen |

### Notes par mécanique

**01 · Panneaux.** L'aimantation est calculée 150 ms après l'arrêt du geste et refusée
au-delà d'un demi-écran d'écart : le scroll n'est jamais confisqué en pleine course.

**07 · Orbitale.** Les positions sont recalculées à chaque frame depuis la progression,
sans tween intermédiaire ; l'état final (la grille) est mesuré sur la première fiche, donc
la grille suit la taille réelle du contenu.

**08 · Magnétique.** La seule mécanique pilotée par `getVelocity()` : descendre attire,
remonter repousse, s'arrêter détend le champ. 135 nœuds par défaut — à baisser sous
`(max-width: 900px)` (`cols: 9, rows: 6`) avant tout port mobile.

**09 · Corridor.** Perspective CSS pure. Les plans hors de la plage `[-6200, 220]` passent
en `visibility: hidden` : sans cela le navigateur compose des surfaces invisibles.

**10 · Déconstruction.** Les trajectoires sont **déclarées dans le balisage**
(`data-part="-420,-180,-24"`), donc réglables sans toucher au JS ; celles de sortie servent
d'entrée à la section suivante.

**12 · Morphogenèse.** Chaque forme est échantillonnée sur 48 points angulaires dans le même
ordre : n'importe quel couple de formes s'interpole, sans keyframe pré-calculée. Formes
disponibles : `rect`, `hex`, `tri`, `diamond`, `circle`.

**13 · Entrelacs.** Position latérale = sinus de la phase, profondeur = cosinus. Le
`z-index`, le flou et l'échelle dérivent du **même** cosinus : l'ordre de profondeur ne
peut pas se désynchroniser du mouvement.

**14 · Repli.** Une conversion de progression suffit ; les états sont déclarés une fois et
joués deux fois. Signaler le retour visuellement (sol, reflet), sinon le lecteur croit
avoir remonté par erreur.

**15 · Focale.** Les hauteurs sont réparties en proportion d'une gaussienne puis
normalisées sur la hauteur du cadre — jamais mesurées sur le rendu obtenu, ce qui
interdit toute boucle de rétroaction. Le plancher est mesuré sur le plus grand en-tête,
donc un titre reste toujours lisible dans une bande comprimée.

## Ajouter une seizième mécanique

```js
/* motion.js */
export function maMecanique(stage, { length = 3600, onUpdate } = {}) {
  const items = [...stage.querySelectorAll('[data-truc]')];
  if (!items.length) return;
  let last = 0;

  const layout = p => {            // tout dérive de p : aucun état conservé
    last = p;
    items.forEach((el, i) => { el.style.transform = `translate3d(0,${p * i * 20}px,0)`; });
    onUpdate && onUpdate(p);
  };

  pinned(stage, () => {}, { length, scrub: 0.35, onUpdate: st => layout(st.progress) });
  layout(reduced() ? 1 : 0);       // état de repos en mode réduit
  const onResize = () => layout(last);
  addEventListener('resize', onResize);
  return () => removeEventListener('resize', onResize);   // le contrat
}
```

Le contrat, en trois règles : **tout dérive de la progression** (aucun état accumulé, donc
le scroll inverse est exact), **le mode réduit pose un état de repos lisible**, **le montage
renvoie son nettoyage**.

## Budget de performance

Une mécanique lourde par écran, jamais deux. `magnet` et `corridor` ne doivent pas
cohabiter avec une autre scène active dans la même vue. Sur la page laboratoire elles sont
séparées par des sections neutres, et chaque scène est bornée par `overflow: hidden` pour
qu'un plan qui sort ne provoque pas de repeint plein écran.

### Palier mobile

`lite()` (dans `motion.js`) renvoie vrai sous 900 px. Deux scènes le lisent :

- `magnet` : 135 nœuds → 54 (9 × 6), points à 4 px, et une frame sur deux (le lissage
  à 0.10 absorbe l'écart). La grille est reconstruite si l'on franchit le palier —
  rotation d'écran comprise.
- `corridor` : le roulis du volume est abandonné (c'est lui qui recompose tout le
  sous-arbre 3D à chaque frame) ; l'axe Z reste, la course passe à 70 % pour compenser
  la perspective raccourcie à 620 px par `lab.css`.

Une mécanique qui n'a pas besoin du palier ne l'appelle pas. Pour en allèger une autre,
lire `lite()` au montage, garder la valeur dans une variable et la rafraîchir dans le
`resize` déjà présent — jamais un `matchMedia` par frame.

## Réutiliser hors du laboratoire

`motion.js` ne connaît que du balisage : le fichier peut être importé par n'importe quelle
entrée Vite, y compris sans la page laboratoire. Pour monter une mécanique sur une page
projet, il suffit de la scène et du balisage — `await boot()`, l'appel, puis
`ScrollTrigger.refresh()`.
