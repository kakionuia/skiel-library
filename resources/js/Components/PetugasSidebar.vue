<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeSub = ref(null);

const menus = [
    { name: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'petugas.dashboard' },
    { name: 'Kelola Buku', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', route: 'petugas.books' },
    { name: 'Pengajuan Buku', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253', route: 'petugas.book-requests' },
    { name: 'Tunggakan Siswa', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', route: 'petugas.late-returns' },
    { name: 'Log Aktivitas', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', route: 'petugas.activity-logs' },
];
</script>

<template>
    <aside class="w-72 bg-white border-r border-slate-200 h-screen fixed overflow-y-auto z-30 shadow-sm">
        <div class="p-8 mb-4">
            <h1 class="text-xl font-black text-indigo-600 tracking-tighter uppercase italic">
                LibPetugas <span class="text-slate-400 not-italic font-medium">v1</span>
            </h1>
        </div>

        <nav class="px-4 space-y-1">
            <Link v-for="menu in menus" :key="menu.name" :href="route(menu.route)"
                :class="route().current(menu.route) 
                    ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100' 
                    : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'"
                class="flex items-center gap-3 px-4 py-3 font-bold text-[11px] uppercase tracking-widest rounded-xl transition-all group">
                <svg class="w-5 h-5 transition-colors" 
                    :class="route().current(menu.route) ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500'" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon" />
                </svg>
                {{ menu.name }}
            </Link>
        </nav>
    </aside>
</template>