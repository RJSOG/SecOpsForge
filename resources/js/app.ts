import { createInertiaApp } from '@inertiajs/vue3';
import { configureEcho } from '@laravel/echo-vue';
import axios from 'axios';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, type DefineComponent } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import '../css/app.css';

configureEcho({
    broadcaster: 'pusher',
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const initializeFrontAuth = async () => {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
};

initializeFrontAuth().then(() => {
    createInertiaApp({
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: async (name) => {
            const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');

            const pageModule = await resolvePageComponent(
                `./Pages/${name}.vue`,
                pages,
            );
            const page = (pageModule as unknown as { default: DefineComponent })
                .default;

            // Assure que le layout existe
            page.layout =
                page.layout ??
                (await import('./layouts/AppLayout.vue')).default;

            return page;
        },
        setup({ el, App, props, plugin }) {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    }).then(() => {});
});
