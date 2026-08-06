import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/guru-dashboard-charts.js',
                'resources/js/admin-dashboard-charts.js',
                'resources/js/admin-teachers-table.js',
            ],
            publicDirectory: '../public_html',
            buildDirectory: 'build',
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        outDir: '../public_html/build',
        emptyOutDir: true,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
