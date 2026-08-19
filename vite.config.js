import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import glsl from 'vite-plugin-glsl';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/project.js',
                'resources/css/lab.css',
                'resources/js/lab.js',
                'resources/js/three-test.js',
                'resources/js/welcome-blackhole.js',
                'resources/js/welcome-cinematic-entry.js',
                'resources/js/forest-cinematic-entry.js',
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
