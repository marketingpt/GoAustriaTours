import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/admin.css', 'resources/js/admin.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Keep the public bundle small: no polyfill preload, modern browsers only.
        modulePreload: { polyfill: false },
        target: 'es2020',
    },
});
