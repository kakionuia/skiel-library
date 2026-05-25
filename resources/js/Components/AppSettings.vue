<script setup>
import { useAppSettings } from '@/Utils/useAppSettings';

const { theme, fontSize, toggleTheme, increaseFontSize, decreaseFontSize, resetSettings } = useAppSettings();

const fontSizePercent = () => {
    return Math.round(fontSize.value * 100);
};
</script>

<template>
    <div class="fixed bottom-5 right-5 z-[1000] flex items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/95 px-3 py-2 text-slate-700 shadow-xl shadow-slate-900/10 backdrop-blur-md dark:border-slate-700/80 dark:bg-slate-900/95 dark:text-slate-200">
        <span class="hidden text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 sm:inline">
            Tampilan
        </span>

        <div class="hidden h-6 w-px bg-slate-200 dark:bg-slate-700 sm:block"></div>

        <button
            @click="toggleTheme"
            class="flex h-8 w-8 items-center justify-center rounded-xl hover:bg-slate-100 transition-colors duration-200 dark:hover:bg-slate-800"
            :aria-label="`Ganti ke ${theme === 'light' ? 'dark' : 'light'} mode`"
        >
            <svg v-if="theme === 'light'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l-2.12-2.12a4 4 0 00 5.656-5.656l2.12 2.12a6 6 0 01-5.656 5.656z" clip-rule="evenodd"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
            </svg>
        </button>

        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

        <div class="flex items-center gap-1">
            <button
                @click="decreaseFontSize"
                class="flex h-8 w-8 items-center justify-center rounded-xl hover:bg-slate-100 transition-colors duration-200 disabled:cursor-not-allowed dark:hover:bg-slate-800"
                aria-label="Kecilkan tulisan"
                :disabled="fontSize === 0.85"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/>
                </svg>
            </button>

            <span class="min-w-[2.4rem] px-1 text-center text-xs font-black tabular-nums text-slate-600 dark:text-slate-300">
                {{ fontSizePercent() }}%
            </span>

            <button
                @click="increaseFontSize"
                class="flex h-8 w-8 items-center justify-center rounded-xl hover:bg-slate-100 transition-colors duration-200 disabled:cursor-not-allowed dark:hover:bg-slate-800"
                aria-label="Besarkan tulisan"
                :disabled="fontSize === 1.3"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>

        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

        <button
            @click="resetSettings"
            class="flex h-8 w-8 items-center justify-center rounded-xl hover:bg-slate-100 transition-colors duration-200 dark:hover:bg-slate-800"
            aria-label="Reset tampilan"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>
</template>

<style scoped>
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
