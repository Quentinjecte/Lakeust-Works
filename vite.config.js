import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import glsl from 'vite-plugin-glsl';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS — chaque fichier ci-dessous est un point d'entrée fin qui
                // réexporte (@import) le détail depuis resources/css/{core,components,
                // animations,labs,pages}/ — voir leurs propres en-têtes.
                'resources/css/app.css',
                'resources/css/web.css',
                'resources/css/lab.css',
                'resources/css/labs/animation-lab.css',

                // core — bootstrap, page-systems (reveals/scroll/parallax), Barba
                'resources/js/core/app.js',

                // pages — entrées JS propres à une page "Lakeust Web" autonome
                'resources/js/pages/home.js',
                'resources/js/pages/welcome-lakeust.js',

                // ui — composants réutilisés en @vite direct sur une page (fiches
                // projet studio + web), pas juste des imports d'une autre entrée
                'resources/js/ui/carousel.js',

                // labs — un dossier par lab, engine + entrée de page
                'resources/js/labs/catalogue/lab-catalogue.js',
                'resources/js/labs/scroll/scroll-lab.js',
                'resources/js/labs/barba/barba-lab.js',
                'resources/js/labs/three/three-lab.js',
                'resources/js/labs/animation/animation-lab.js',
                'resources/js/labs/carousel/carousel-lab.js',
                'resources/js/labs/chevron/chevron-lab.js',

                // three — scènes WebGL réutilisées telles quelles (welcome hero,
                // Three Lab, Carousel Lab) + la page de test scratch
                'resources/js/three/three-test.js',
                'resources/js/three/blackhole.js',

                // cinematic — Three.js (rendu) + Theatre.js (timeline), une entrée
                // par cinématique ; le détail partagé vit dans cinematic/shared/
                'resources/js/cinematic/blackhole-cinematic/blackhole-cinematic-entry.js',
                'resources/js/cinematic/forest-cinematic/forest-cinematic-entry.js',
                'resources/js/cinematic/home-cinematic/home-cinematic-entry.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        glsl(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**', '**/.vs/**'],
        },
    },
});
