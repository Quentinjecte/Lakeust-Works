# Lakeust Works

Portfolio de **Quentin Renaud** : [lakeust.works](https://lakeust.works).

Le site réunit trois branches, chacune avec sa propre identité visuelle :

| Branche | Contenu | Pages |
|---|---|---|
| **Studio** | Jeux vidéo et expériences (direction artistique « présentation de jeu ») | `/studio-a-propos`, `/studio-travaux`, `/studio-lab` |
| **Web** | Sites, animations et expériences WebGL | `/web-a-propos`, `/web-travaux`, `/web-projet`, `/web-lab` |
| **Arts** | Modèles 3D (Blender, Blockbench), visualisables en direct | `/arts-a-propos`, `/arts-modeles`, `/arts-modele/{slug}` |

L'accueil (`/`) est une cinématique d'entrée qui mène à chaque branche. Les mentions légales sont sur `/mentions-legales`.

## Technologies

- **Laravel 13** (PHP 8.4+) pour les routes et les vues Blade
- **Vite 8** et **Tailwind CSS 4**
- **Three.js** (WebGL, visualiseur de modèles), **GSAP**, **Lenis**, **Barba.js** (transitions de pages), **Theatre.js** (outil de mise en scène, uniquement en développement)

Le site publié est **entièrement statique** : Laravel sert à le fabriquer, pas à l'héberger.

## Installation

Prérequis : PHP 8.4+, Composer, Node.js 20+.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

## Développement

Dans deux terminaux, à la racine du projet :

```bash
php artisan serve
```

```bash
npm run dev
```

Le site est alors sur <http://127.0.0.1:8000>.

## Publication

Le déploiement est automatique : chaque push sur `master` lance le workflow [`deploy-pages.yml`](.github/workflows/deploy-pages.yml), qui construit le site et le publie sur GitHub Pages.

Pour que ça fonctionne, la source de Pages doit être **GitHub Actions** (Settings > Pages > Build and deployment > Source). Avec « Deploy from a branch », GitHub affiche à la place ce README.

Pour fabriquer le site statique en local (dans `dist/`) :

```bash
npm run build
```

```bash
php artisan export:static
```

Lancer la seconde commande avec `public/hot` absent (c'est-à-dire Vite arrêté), sinon les pages pointent vers le serveur de développement.

## Modèles 3D

Les modèles de la branche Arts sont affichés dans le navigateur, mais ne sont **pas téléchargeables** : ils sont servis chiffrés (`.lwm`, AES-256-GCM) et déchiffrés en mémoire par le visualiseur. Les fichiers en clair ne sont jamais publiés.

Pour ajouter un modèle :

1. Exporter le modèle en glTF (Blockbench : *File > Export > Export as glTF* ; Blender : *File > Export > glTF 2.0*).
2. Le déposer dans `resources/models/` (dossier ignoré par git).
3. Lancer, **depuis la racine du projet** :
   ```bash
   php artisan models:protect
   ```
   La commande écrit `public/models/<nom>.lwm`, avec un nom en minuscules et tirets.
4. Ajouter une ligne pour ce modèle dans [`app/Support/ModelCatalog.php`](app/Support/ModelCatalog.php).
5. Commiter les `.lwm` : le déploiement n'a pas accès aux sources.

La clé de chiffrement est dans `resources/js/ui/model-key.js`. Elle sert aussi au déchiffrement côté navigateur : un site statique n'a pas de serveur pour la garder. C'est un frein contre le téléchargement, pas un coffre-fort.

## Structure

```
app/Support/ModelCatalog.php   catalogue des modèles 3D
resources/views/pages/         pages Studio, Web et Arts (Blade)
resources/views/layouts/       gabarit commun (navigation, pied de page)
resources/css/                 styles par branche (web.css, arts.css, pages/*-theme.css)
resources/js/ui/               visualiseur 3D (model-viewer.js) et déchiffrement
resources/js/pages/            scripts propres à chaque page
resources/models/              sources 3D en clair (non versionnées)
public/models/                 modèles chiffrés (.lwm)
routes/web.php                 routes du site
routes/console.php             commandes models:protect et export:static
```

## Droits

© Quentin Renaud. Tous droits réservés : textes, graphismes, vidéos, code des pages et modèles 3D ne peuvent être copiés, extraits ni réutilisés sans accord écrit. Certains modèles sont proposés à l'achat sur des plateformes tierces. Voir les [mentions légales](https://lakeust.works/mentions-legales).
