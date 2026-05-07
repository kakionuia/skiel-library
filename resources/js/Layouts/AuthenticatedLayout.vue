<script setup>
import { ref, computed } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    showSidebar: { type: Boolean, default: true },
});

const showingNavigationDropdown = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user);

const dashboardRoute = computed(() => {
    const role = user.value.role;
    if (role === 'admin') return 'admin.dashboard';
    if (role === 'petugas') return 'petugas.dashboard';
    return 'dashboard'; // default/anggota
});

const isOnDashboard = computed(() => {
    // Cek apakah route saat ini adalah dashboard route yang sesuai
    return route().current(dashboardRoute.value);
});

const isOnProfile = computed(() => {
    return route().current('profile.edit');
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <Sidebar v-if="showSidebar" />
        <div :class="['flex-1', 'flex', 'flex-col', showSidebar ? 'ml-72' : 'ml-0']">
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-20 px-8 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-800 tracking-tight">
                        <slot name="header" />
                    </h2>
                </div>
                <div class="flex items-center gap-4">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-500 hover:text-orange-600 transition">
                                {{ user.name }}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </template>
                        <template #content>
                            <!-- Jika bukan di dashboard, tampilkan link Dashboard -->
                            <DropdownLink v-if="!isOnDashboard" :href="route(dashboardRoute)">Dashboard</DropdownLink>
                            <!-- Jika bukan di profile, tampilkan link Profil -->
                            <DropdownLink v-if="!isOnProfile" :href="route('profile.edit')">Profil</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Keluar</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>
            <main class="p-8">
                <slot />
            </main>
        </div>
    </div>
</template>