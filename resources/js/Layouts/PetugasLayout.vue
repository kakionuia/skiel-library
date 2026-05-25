<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
</script>

<template>
    <div class="flex min-h-screen bg-[#f8fafc] dark:bg-slate-950">
        <PetugasSidebar />
        <div class="flex-1 ml-80 flex flex-col">
            <header class="h-16 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-700/80 sticky top-0 z-[90] px-8"></header>
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
