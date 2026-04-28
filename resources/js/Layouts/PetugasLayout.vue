<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
</script>

<template>
    <div class="flex min-h-screen bg-[#f8fafc]">
        <PetugasSidebar />
        <main class="flex-1 ml-80">
            <slot />
        </main>
    </div>
</template>