import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts', 'resources/css/app.css'], // CSS + JS
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    optimizeDeps: {
        include: [
            '@inertiajs/vue3',
            '@vueuse/core',
            'ziggy-js',
            'laravel-echo',
            'pusher-js',
            'laravel-vite-plugin/inertia-helpers',
        ],
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
