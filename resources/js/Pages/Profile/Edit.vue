<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    recentBorrows: {
        type: Array,
        default: () => [],
    },
    activeBorrows: {
        type: Array,
        default: () => [],
    },
    totalBorrows: {
        type: Number,
        default: 0,
    },
    overdueCount: {
        type: Number,
        default: 0,
    },
    totalFines: {
        type: Number,
        default: 0,
    },
});

const user = usePage().props.auth.user;
const isAnggota = user.role === 'anggota';

const profilePhotoUrl = computed(() => {
    return user.profile_photo_url || null;
});

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout :showSidebar="false">
        <template #header>
            <div class="flex items-center gap-3">
                <div class="h-7 w-1.5 bg-gradient-to-b from-orange-500 to-amber-500 rounded-full"></div>
                <h2 class="text-xl font-bold tracking-tight text-gray-900">
                    User Profile
                </h2>
            </div>
        </template>

        <div class="py-10 bg-gradient-to-b from-gray-50/50 to-white min-h-screen">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                
                <!-- Profile Summary Card with Photo -->
                <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-zinc-900 overflow-hidden shadow-2xl sm:rounded-3xl border border-slate-700/50">
                    <!-- Decorative Background Glow -->
                    <div class="absolute top-0 right-0 -mt-12 -mr-12 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-orange-400/5 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="p-6 sm:p-10 relative z-10">
                        <div class="flex flex-col sm:flex-row items-center gap-8">
                            <!-- Avatar with Photo -->
                            <div class="shrink-0 relative group">
                                <div class="absolute -inset-1.5 bg-gradient-to-r from-orange-500 to-amber-600 rounded-full blur-lg opacity-50 group-hover:opacity-100 transition duration-500 group-hover:blur-xl"></div>
                                <div class="relative h-28 w-28 sm:h-32 sm:w-32 rounded-full border-[3px] border-orange-500/80 overflow-hidden bg-gradient-to-br from-slate-700 to-slate-800 shadow-2xl">
                                    <!-- Show Profile Photo -->
                                    <img 
                                        v-if="profilePhotoUrl" 
                                        :src="profilePhotoUrl" 
                                        alt="Profile Photo" 
                                        class="h-full w-full object-cover"
                                    >
                                    <!-- Fallback Initial -->
                                    <div v-else class="h-full w-full flex items-center justify-center bg-gradient-to-br from-orange-500 to-amber-500">
                                        <span class="text-4xl sm:text-5xl font-black text-white drop-shadow-lg">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Online indicator -->
                                <div class="absolute bottom-2 right-2 w-4 h-4 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                            </div>

                            <!-- User Identity -->
                            <div class="flex-1 text-center sm:text-left space-y-4">
                                <div>
                                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                                        {{ user.name }}
                                    </h1>
                                    <div class="flex items-center justify-center sm:justify-start gap-3 mt-2">
                                        <p class="text-orange-400 font-medium text-sm tracking-wider uppercase bg-orange-500/10 px-3 py-1 rounded-full border border-orange-500/20">
                                            ID: #{{ user.id || 'MEMBER' }}
                                        </p>
                                        <span v-if="user.nisn" class="text-slate-400 text-xs font-mono">
                                            NISN: {{ user.nisn }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-2 text-sm text-slate-300">
                                    <div v-if="user.kelas" class="flex items-center gap-2 bg-slate-800/80 px-3.5 py-2 rounded-xl border border-slate-700/50 hover:border-orange-500/30 transition-colors">
                                        <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="font-medium">Kelas {{ user.kelas }}</span>
                                    </div>
                                    <div v-if="user.jurusan" class="flex items-center gap-2 bg-slate-800/80 px-3.5 py-2 rounded-xl border border-slate-700/50 hover:border-orange-500/30 transition-colors">
                                        <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                        <span class="font-medium">{{ user.jurusan }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-slate-800/80 px-3.5 py-2 rounded-xl border border-slate-700/50 hover:border-orange-500/30 transition-colors">
                                        <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="truncate max-w-[200px] font-medium">{{ user.email }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Role badge -->
                            <div class="shrink-0">
                                <span class="inline-flex items-center rounded-2xl px-5 py-2.5 text-sm font-bold uppercase tracking-widest border-2 shadow-lg"
                                    :class="{
                                        'bg-orange-500/10 text-orange-400 border-orange-500/30 shadow-orange-500/5': isAnggota,
                                        'bg-amber-500/10 text-amber-400 border-amber-500/30 shadow-amber-500/5': !isAnggota
                                    }"
                                >
                                    <span class="h-2 w-2 rounded-full mr-2.5 animate-pulse" :class="isAnggota ? 'bg-orange-400' : 'bg-amber-400'"></span>
                                    {{ user.role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards (Untuk Anggota) -->
                <div v-if="isAnggota" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Total Peminjaman -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pinjam</p>
                                <p class="text-2xl font-bold text-gray-900">{{ totalBorrows }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aktif Dipinjam -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ activeBorrows.length }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Terlambat -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-gradient-to-br from-red-50 to-red-100 rounded-xl">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Terlambat</p>
                                <p class="text-2xl font-bold text-gray-900">{{ overdueCount }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Denda -->
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Denda</p>
                                <p class="text-lg font-bold text-gray-900">{{ formatCurrency(totalFines) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Books Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Recent Borrows -->
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 p-6 sm:p-8">
                        <div class="border-b border-gray-100 pb-5 mb-5">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2.5">
                                <span class="p-2 bg-orange-50 text-orange-600 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                Peminjaman Terbaru
                            </h3>
                        </div>
                        
                        <div v-if="recentBorrows.length === 0" class="text-center py-12 text-sm text-gray-400">
                            <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="font-medium">Belum ada peminjaman</p>
                        </div>
                        <ul v-else class="space-y-5 relative pl-4 before:absolute before:top-2 before:bottom-2 before:left-[7px] before:w-0.5 before:bg-gradient-to-b before:from-orange-200 before:to-transparent">
                            <li v-for="borrow in recentBorrows" :key="borrow.id" class="relative flex gap-4 items-start">
                                <div class="absolute -left-[22px] mt-1.5 h-3 w-3 rounded-full bg-orange-500 ring-4 ring-white shadow-sm"></div>
                                <div class="flex-1 min-w-0 bg-gray-50 rounded-xl p-4 hover:bg-orange-50/30 transition-colors">
                                    <p class="text-sm font-bold text-gray-800 leading-snug">
                                        {{ borrow.book?.title || 'Buku' }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        <span class="text-xs text-gray-500">
                                            {{ new Date(borrow.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                        </span>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-700': borrow.status === 'returned',
                                                'bg-orange-100 text-orange-700': borrow.status === 'approved' || borrow.status === 'borrowed',
                                                'bg-yellow-100 text-yellow-700': borrow.status === 'pending',
                                                'bg-red-100 text-red-700': borrow.status === 'rejected',
                                            }"
                                        >
                                            {{ borrow.status }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Active Borrows (Only for Anggota) -->
                    <div v-if="isAnggota" class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 p-6 sm:p-8">
                        <div class="border-b border-gray-100 pb-5 mb-5">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2.5">
                                <span class="p-2 bg-orange-50 text-orange-600 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </span>
                                Peminjaman Aktif
                            </h3>
                        </div>

                        <div v-if="activeBorrows.length === 0" class="text-center py-12 text-sm text-gray-400">
                            <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="font-medium">Tidak ada peminjaman aktif</p>
                        </div>
                        <div v-else class="space-y-3.5">
                            <div v-for="borrow in activeBorrows" :key="borrow.id" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-orange-50/30 hover:border-orange-200 transition-all duration-200 group">
                                <div class="h-16 w-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center text-xs font-black text-white shadow-md group-hover:scale-105 transition-transform">
                                    {{ borrow.book?.title?.charAt(0) || '📖' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:text-orange-600 transition-colors">
                                        {{ borrow.book?.title }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Batas: 
                                        <span class="font-semibold" :class="{ 'text-red-600': new Date(borrow.due_date) < new Date() }">
                                            {{ new Date(borrow.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    <span v-if="new Date(borrow.due_date) < new Date()" class="text-xs font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-lg">
                                        Terlambat
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin / Staff Visual State -->
                    <div v-else class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 p-6 sm:p-8 flex flex-col items-center justify-center min-h-[300px]">
                        <div class="text-center max-w-sm space-y-4">
                            <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center text-orange-500 border border-slate-700 shadow-xl">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="2" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-800">Panel Manajemen Aktif</h4>
                                <p class="text-sm text-gray-500 mt-1">Akun Anda dikonfigurasi sebagai administrator sistem dengan hak akses penuh ke dasbor kontrol.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Information Form Component -->
                <div class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 p-6 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <!-- Settings & Security Group (Password & Delete) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-gray-100 p-6 sm:p-8">
                        <UpdatePasswordForm />
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-red-100 p-6 sm:p-8 bg-gradient-to-b from-white to-red-50/10">
                        <DeleteUserForm />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>