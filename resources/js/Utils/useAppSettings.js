import { ref, watch } from 'vue';

const STORAGE_KEY_THEME = 'app_theme';
const STORAGE_KEY_FONT_SIZE = 'app_font_size';

const theme = ref('light');
const fontSize = ref(1); // 0.85, 1, 1.15, 1.3

const fontSizeNameMap = {
    0.85: 'compact',
    1: 'normal',
    1.15: 'large',
    1.3: 'xlarge',
};

export const useAppSettings = () => {
    // Initialize from localStorage
    const initSettings = () => {
        const savedTheme = localStorage.getItem(STORAGE_KEY_THEME);
        const savedFontSize = localStorage.getItem(STORAGE_KEY_FONT_SIZE);
        
        if (savedTheme) {
            theme.value = savedTheme;
        } else {
            // Auto-detect system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                theme.value = 'dark';
            }
        }
        
        if (savedFontSize) {
            fontSize.value = parseFloat(savedFontSize);
        }
        
        applySettings();
    };

    const applySettings = () => {
        // Apply theme
        const html = document.documentElement;
        if (theme.value === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        // Apply font sizing to text classes only; keep layout spacing stable.
        html.dataset.fontSize = fontSizeNameMap[fontSize.value] || 'normal';
    };

    const setTheme = (newTheme) => {
        theme.value = newTheme;
        localStorage.setItem(STORAGE_KEY_THEME, newTheme);
        applySettings();
    };

    const setFontSize = (size) => {
        fontSize.value = size;
        localStorage.setItem(STORAGE_KEY_FONT_SIZE, size);
        applySettings();
    };

    const toggleTheme = () => {
        const newTheme = theme.value === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
    };

    const increaseFontSize = () => {
        const sizes = [0.85, 1, 1.15, 1.3];
        const currentIndex = sizes.indexOf(fontSize.value);
        if (currentIndex < sizes.length - 1) {
            setFontSize(sizes[currentIndex + 1]);
        }
    };

    const decreaseFontSize = () => {
        const sizes = [0.85, 1, 1.15, 1.3];
        const currentIndex = sizes.indexOf(fontSize.value);
        if (currentIndex > 0) {
            setFontSize(sizes[currentIndex - 1]);
        }
    };

    const resetSettings = () => {
        setTheme('light');
        setFontSize(1);
    };

    return {
        theme,
        fontSize,
        initSettings,
        setTheme,
        setFontSize,
        toggleTheme,
        increaseFontSize,
        decreaseFontSize,
        resetSettings,
    };
};
