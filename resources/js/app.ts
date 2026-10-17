import { createInertiaApp, router } from '@inertiajs/vue3';
import axios from 'axios';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, type DefineComponent } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'SecOpsForge';
const initializeFrontAuth = async () => {
    axios.defaults.withCredentials = true;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
};

initializeFrontAuth().then(() => {
    createInertiaApp({
        // Pages pass the full title (built server-side in App\Support\Seo).
        title: (title) => title || appName,
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

    // Meta tags are rendered server-side (app.blade.php) for the first load;
    // keep the description and canonical in sync on client-side navigation
    // (the tab title is handled by <SeoHead>).
    router.on('navigate', (event) => {
        const seo = event.detail.page.props.seo as { title?: string; description?: string; url?: string } | undefined;
        if (!seo?.title) return;

        document.querySelector('meta[name="description"]')?.setAttribute('content', seo.description ?? '');
        document.querySelector('link[rel="canonical"]')?.setAttribute('href', seo.url ?? '');
    });
});
