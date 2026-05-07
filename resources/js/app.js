import './disable-tooltip';  // Tambahkan ini PERTAMA
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import { library } from '@fortawesome/fontawesome-svg-core';
import { faYoutube } from '@fortawesome/free-brands-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faYoutube);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Hilangkan semua tooltip URL di seluruh aplikasi
document.addEventListener('DOMContentLoaded', function() {
    // Hilangkan tooltip dari semua element
    const removeTooltips = () => {
        document.querySelectorAll('[title]').forEach(el => {
            el.removeAttribute('title');
        });
        
        document.querySelectorAll('a, button, div').forEach(el => {
            el.addEventListener('mouseenter', (e) => {
                e.preventDefault();
                e.stopPropagation();
                window.status = '';
                return false;
            });
        });
    };
    
    removeTooltips();
    
    // Observer untuk element yang baru ditambahkan
    const observer = new MutationObserver(removeTooltips);
    observer.observe(document.body, { childList: true, subtree: true });
});

// Override default browser behavior
window.defaultStatus = '';
window.status = '';

