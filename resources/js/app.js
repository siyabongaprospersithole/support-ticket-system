import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import vSelect from 'vue-select';

// Disable hot module reload during setup if the env var is set
if (import.meta.env.VITE_DISABLE_HMR === 'true') {
    // Prevent HMR by disabling the accept handler
    if (import.meta.hot) {
        import.meta.hot.accept = () => {};
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .component('v-select', vSelect)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
