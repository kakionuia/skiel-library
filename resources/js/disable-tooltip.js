// disable-tooltip.js - Hilangkan semua tooltip URL di seluruh aplikasi

(function() {
    // Intercept semua event mouse
    const blockAllTooltips = (e) => {
        // Block status bar update
        if (window.status !== undefined) {
            window.status = '';
        }
        if (window.defaultStatus !== undefined) {
            window.defaultStatus = '';
        }
        
        // Hentikan event bubbling
        e.stopPropagation();
        e.preventDefault();
        return false;
    };

    // Fix untuk semua element yang akan ditambahkan nanti
    const fixAllElements = () => {
        // Tangkap semua link, button, div yang bisa muncul tooltip
        const allInteractive = document.querySelectorAll('a, button, div, span, li, .nav-link, [href], [title]');
        
        allInteractive.forEach(el => {
            // Hapus atribut title
            el.removeAttribute('title');
            el.removeAttribute('data-title');
            el.removeAttribute('tooltip');
            
            // Block semua event mouse yang bisa memicu tooltip
            ['mouseenter', 'mouseover', 'mousemove', 'mouseleave', 'hover'].forEach(eventType => {
                el.addEventListener(eventType, blockAllTooltips, true);
            });
        });
    };

    // Jalankan pertama kali
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixAllElements);
    } else {
        fixAllElements();
    }

    // Observer untuk element baru
    const observer = new MutationObserver(fixAllElements);
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['title', 'href']
    });

    // Override global
    Object.defineProperty(window, 'status', {
        get: () => '',
        set: () => {}
    });
    
    Object.defineProperty(window, 'defaultStatus', {
        get: () => '',
        set: () => {}
    });
})();