<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import SidebarLink from '@/components/SidebarLink.vue';

const activeSub = ref(null);
const logoutForm = useForm({});

const handleLogout = () => {
    logoutForm.post(route('logout'));
};

const menus = [
    { name: 'Dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'admin.dashboard' },
    { name: 'Kelola Akun', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', route: 'admin.accounts' },
    { name: 'Pengajuan Buku', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', route: 'admin.book-submissions' },
    { name: 'Permintaan Buku', icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', route: 'admin.book-requests' },
    { name: 'Tunggakan Siswa', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', route: 'admin.late-returns' },
    { name: 'Ulasan Buku', icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', route: 'admin.reviews' },
    { name: 'Keluhan & Saran', icon: 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', route: 'admin.complaints' },
];
</script>

<template>
    <aside class="fixed left-0 top-0 z-50 h-screen w-80 bg-white border-r border-slate-200 flex flex-col shadow-sm">
        <div class="flex h-full flex-col">
            <div class="flex items-center px-8 py-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-100 transition-transform hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none">SKIE-Library</h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Administrator</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
                <SidebarLink v-for="menu in menus" :key="menu.name" :href="route(menu.route)">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon"></path>
                    </svg>
                    {{ menu.name }}
                </SidebarLink>

                <div class="pt-2">
                    <button @click="activeSub = activeSub === 'log' ? null : 'log'"
                        class="w-full flex items-center justify-between px-4 py-3 text-slate-600 font-semibold text-sm rounded-xl hover:bg-slate-50 transition-all group">
                        <div class="flex items-center gap-4">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Log Aktivitas
                        </div>
                        <svg :class="{'rotate-180 text-orange-500': activeSub === 'log'}" class="w-4 h-4 transition-transform duration-300 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    <div v-show="activeSub === 'log'" class="pl-12 mt-1 space-y-1 overflow-hidden transition-all">
                        <SidebarLink :href="route('admin.activity-logs', {role: 'anggota'})" class="!py-2 !text-xs">Siswa</SidebarLink>
                        <SidebarLink :href="route('admin.activity-logs', {role: 'petugas'})" class="!py-2 !text-xs">Petugas</SidebarLink>
                        <SidebarLink :href="route('admin.activity-logs', {role: 'admin'})" class="!py-2 !text-xs">Admin</SidebarLink>
                    </div>
                </div>
            </nav>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 mb-4 p-2">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center border border-orange-200 shadow-sm">
                        <span class="text-sm font-bold text-orange-700">{{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'A' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $page.props.auth?.user?.name || 'Admin' }}</p>
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-tighter italic">Super Admin</p>
                    </div>
                </div>
                <button @click="handleLogout" class="w-full flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar Sistem
                </button>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.nav-link {
    @apply flex items-center gap-4 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-orange-600;
    background: none;
    border: none;
    width: 100%;
    cursor: pointer;
    font-family: inherit;
    text-align: left;
}

.active-link .icon-style {
    @apply text-orange-600 scale-110;
}

.icon-style {
    @apply transition-transform duration-200 text-slate-400 group-hover:text-orange-600;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-200 rounded-full;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>