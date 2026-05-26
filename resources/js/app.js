import './disable-tooltip';  // Tambahkan ini PERTAMA
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { useAppSettings } from './Utils/useAppSettings';
import AppSettings from './Components/AppSettings.vue';

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// Import ikon Solid (Pesan & Telepon)
import { faEnvelope, faPhone } from '@fortawesome/free-solid-svg-icons'

// Import ikon Brands (YouTube, Instagram, TikTok)
import { faYoutube, faInstagram, faTiktok } from '@fortawesome/free-brands-svg-icons'

// Masukkan semua ikon ke library
library.add(faEnvelope, faPhone, faYoutube, faInstagram, faTiktok)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Initialize app settings
const { initSettings } = useAppSettings();
initSettings();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({
            render: () => h('div', [
                h(App, props),
                h(AppSettings),
            ]),
        })
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
