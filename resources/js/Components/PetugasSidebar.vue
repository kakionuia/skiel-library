
<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const logoutForm = useForm({});
const handleLogout = () => {
    logoutForm.post(route('logout'));
};
</script>

<template>
    <aside class="fixed left-0 top-0 z-50 h-screen w-80 bg-white border-r border-slate-200 flex flex-col shadow-sm">
        <div class="flex h-full flex-col">
            <!-- Logo -->
            <div class="flex items-center px-8 py-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100 transition-transform hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none">Libra<span class="text-indigo-600">Sync</span></h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Petugas Perpustakaan</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
                <Link :href="route('petugas.dashboard')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                    Dashboard
                </Link>

                <Link :href="route('petugas.borrows')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Kelola Peminjaman
                </Link>

                <Link :href="route('petugas.books')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Daftar Buku
                </Link>

                <Link :href="route('petugas.reports')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Laporan Harian
                </Link>

                <Link :href="route('petugas.late-returns')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Anggota Terlambat
                </Link>

                <Link :href="route('petugas.book-requests')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Permintaan Buku
                </Link>

                <Link :href="route('petugas.activity-logs')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Log Aktivitas
                </Link>

                <Link :href="route('petugas.settings')" class="nav-link group">
                    <svg class="w-5 h-5 icon-style" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Pengaturan
                </Link>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 mb-4 p-2">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center border border-indigo-200 shadow-sm">
                        <span class="text-sm font-bold text-indigo-700">{{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $page.props.auth?.user?.name || 'User' }}</p>
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-tighter italic">Petugas</p>
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
/* Utility untuk link navigasi agar kode template tidak kotor */
.nav-link {
    @apply flex items-center gap-4 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-indigo-600;
}

/* State link aktif dari inertiajs */
.router-link-active,
[aria-current="page"] {
    @apply bg-indigo-50 text-indigo-700 font-bold shadow-sm shadow-indigo-100/50;
}

[aria-current="page"] .icon-style {
    @apply text-indigo-600 scale-110;
}

.icon-style {
    @apply transition-transform duration-200 text-slate-400 group-hover:text-indigo-600;
}
</style>