<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ user: Object });
const activeSub = ref(null);

const menus = [
    { name: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'dashboard' },
    { name: 'Kelola Akun', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', route: '#' },
    { name: 'Pengajuan Buku', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253', route: '#' },
    { name: 'Tunggakan Siswa', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', route: '#' },
    { name: 'Ulasan Buku', icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', route: '#' },
    { name: 'Saran Siswa', icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', route: '#' },
];
</script>

<template>
    <aside class="w-72 bg-white border-r border-slate-200 h-screen fixed overflow-y-auto">
        <div class="p-8 mb-4">
            <h1 class="text-xl font-black text-indigo-600 tracking-tighter uppercase">LibAdmin <span class="text-slate-400">v1</span></h1>
        </div>

        <nav class="px-4 space-y-1">
            <Link v-for="menu in menus" :key="menu.name" :href="menu.route"
                class="flex items-center gap-3 px-4 py-3 text-slate-500 font-bold text-[11px] uppercase tracking-widest hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all group">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon" />
                </svg>
                {{ menu.name }}
            </Link>

            <div>
                <button @click="activeSub = activeSub === 'log' ? null : 'log'"
                    class="w-full flex items-center justify-between px-4 py-3 text-slate-500 font-bold text-[11px] uppercase tracking-widest hover:bg-slate-50 rounded-xl transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Log Aktivitas
                    </div>
                    <svg :class="{'rotate-180': activeSub === 'log'}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div v-show="activeSub === 'log'" class="pl-12 mt-1 space-y-1 overflow-hidden transition-all">
                    <Link href="#" class="block py-2 text-[10px] font-bold text-slate-400 hover:text-indigo-500 uppercase">Siswa</Link>
                    <Link href="#" class="block py-2 text-[10px] font-bold text-slate-400 hover:text-indigo-500 uppercase">Petugas</Link>
                    <Link href="#" class="block py-2 text-[10px] font-bold text-slate-400 hover:text-indigo-500 uppercase">Admin</Link>
                </div>
            </div>
        </nav>
    </aside>
</template>