import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'; // <-- 1. Importe le plugin ici

export default defineConfig({
    plugins: [
        tailwindcss(), // <-- 2. Ajoute-le juste AVANT le plugin Laravel
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});