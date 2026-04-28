import { ref, watch } from 'vue';

const isDark = ref(true);

function init() {
    const stored = localStorage.getItem('theme');
    if (stored) {
        isDark.value = stored === 'dark';
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    apply();
}

function apply() {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

function toggle() {
    isDark.value = !isDark.value;
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
    apply();
}

// Initialize on first import
if (typeof window !== 'undefined') {
    init();
}

export function useTheme() {
    return { isDark, toggle };
}
