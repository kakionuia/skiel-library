<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
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
    totalPoints: {
        type: Number,
        default: 0,
    },
    monthlyPoints: {
        type: Number,
        default: 0,
    },
    pointPerBook: {
        type: Number,
        default: 10,
    },
    pointMonthLabel: {
        type: String,
        default: '',
    },
});

const user = usePage().props.auth.user;
const isAnggota = user.role === 'anggota';
const managementRoute = computed(() => {
    if (user.role === 'admin') return route('admin.dashboard');
    if (user.role === 'petugas') return route('petugas.dashboard');
    return route('dashboard');
});

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
                <div class="h-6 w-1 bg-orange-500 rounded-full"></div>
                <h2 class="text-lg font-bold tracking-tight text-gray-900">
                    Profil Pengguna
                </h2>
            </div>
        </template>

        <div class="py-10 bg-gray-50 min-h-screen font-sans">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                
                <!-- Profile Header Card -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6">
                        
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                            <!-- Avatar -->
                            <div class="relative shrink-0">
                                <div class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm">
                                    <img 
                                        v-if="profilePhotoUrl" 
                                        :src="profilePhotoUrl" 
                                        alt="Profile Photo" 
                                        class="h-full w-full object-cover"
                                    >
                                    <div v-else class="h-full w-full flex items-center justify-center bg-orange-500">
                                        <span class="text-3xl font-bold text-white tracking-wider">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>

                            <!-- User Meta -->
                            <div class="text-center sm:text-left space-y-2">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ user.name }}</h1>
                                    <p class="text-xs font-medium text-gray-400 mt-0.5 uppercase tracking-wider">ID: #{{ user.id || 'MEMBER' }}</p>
                                </div>
                                
                                <div class="flex flex-wrap justify-center sm:justify-start gap-2 text-xs font-medium text-gray-600">
                                    <span v-if="user.kelas" class="bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200/60">Kelas {{ user.kelas }}</span>
                                    <span v-if="user.jurusan" class="bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200/60">{{ user.jurusan }}</span>
                                    <span v-if="user.nisn" class="bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200/60 font-mono">NISN: {{ user.nisn }}</span>
                                </div>

                                <div class="pt-1 flex flex-col sm:flex-row sm:items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                    <span class="flex items-center justify-center sm:justify-start gap-1.5">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ user.email }}
                                    </span>
                                    <span v-if="user.phone" class="flex items-center justify-center sm:justify-start gap-1.5">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.2a1 1 0 01.98.8l.55 2.76a1 1 0 01-.27.9L7.1 8.82a16 16 0 007.08 7.08l1.36-1.36a1 1 0 01.9-.27l2.76.55a1 1 0 01.8.98V18a2 2 0 01-2 2h-1C9.27 20 3 13.73 3 6V5z"/></svg>
                                        {{ user.phone }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Role -->
                        <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-200">
                            {{ user.role }}
                        </span>
                    </div>
                </div>

                <!-- Stats Grid (Anggota Only) -->
                <div v-if="isAnggota" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Pinjam</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalBorrows }} <span class="text-xs font-normal text-gray-400">buku</span></p>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Aktif Dipinjam</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ activeBorrows.length }} <span class="text-xs font-normal text-gray-400">buku</span></p>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Terlambat</p>
                        <p class="text-2xl font-bold mt-1" :class="overdueCount > 0 ? 'text-red-600' : 'text-gray-900'">{{ overdueCount }}</p>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200/80 p-5 shadow-sm">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Denda</p>
                        <p class="text-xl font-bold text-gray-900 mt-1.5 tracking-tight">{{ formatCurrency(totalFines) }}</p>
                    </div>
                </div>

                <!-- Point System (Anggota Only) -->
                <div v-if="isAnggota" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-gray-900 to-slate-800 rounded-xl p-6 text-white border border-slate-800 shadow-sm">
                        <p class="text-xs font-semibold text-orange-400 uppercase tracking-wider">Total Poin Literasi</p>
                        <p class="text-3xl font-bold mt-2 tracking-tight">{{ props.totalPoints }}</p>
                        <p class="text-xs text-gray-400 mt-3 leading-relaxed">Akumulasi validasi seluruh peminjaman buku. Tiap 1 buku bernilai {{ props.pointPerBook }} poin.</p>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200/80 p-6 shadow-sm flex justify-between items-start">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-orange-500 uppercase tracking-wider">Poin Bulan Ini</p>
                            <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ props.monthlyPoints }}</p>
                            <p class="text-xs text-gray-400 pt-2">{{ props.pointMonthLabel }} · Reset otomatis di awal bulan.</p>
                        </div>
                        <div class="p-2.5 bg-orange-50 rounded-lg text-orange-500 border border-orange-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Book Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Recent Borrows -->
                    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6">
                        <div class="border-b border-gray-100 pb-4 mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Peminjaman Terbaru</h3>
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        
                        <div v-if="recentBorrows.length === 0" class="text-center py-12 text-xs text-gray-400">
                            <p>Belum ada riwayat aktivitas peminjaman.</p>
                        </div>
                        
                        <div v-else class="divide-y divide-gray-100">
                            <div v-for="borrow in recentBorrows" :key="borrow.id" class="py-3.5 first:pt-0 last:pb-0 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ borrow.book?.title || 'Buku' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ new Date(borrow.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                    </p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border"
                                    :class="{
                                        'bg-gray-50 text-gray-600 border-gray-200': borrow.status === 'returned',
                                        'bg-orange-50 text-orange-600 border-orange-200': borrow.status === 'approved' || borrow.status === 'borrowed',
                                        'bg-amber-50 text-amber-600 border-amber-200': borrow.status === 'pending',
                                        'bg-red-50 text-red-600 border-red-200': borrow.status === 'rejected',
                                    }"
                                >
                                    {{ borrow.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Borrows (Ke Rak Buku Saat Di-klik) -->
                    <div v-if="isAnggota" class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6">
                        <div class="border-b border-gray-100 pb-4 mb-4 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Peminjaman Aktif</h3>
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>

                        <div v-if="activeBorrows.length === 0" class="text-center py-12 text-xs text-gray-400">
                            <p>Tidak ada peminjaman aktif saat ini.</p>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <!-- Link Diarahkan ke Halaman Rak Buku -->
                            <Link 
                                v-for="borrow in activeBorrows" 
                                :key="borrow.id" 
                                :href="route('borrow.my-loans')"
                                class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200/70 bg-gray-50/40 hover:bg-orange-50/30 hover:border-orange-200/80 transition group"
                            >
                                <div class="min-w-0 flex items-center gap-3">
                                    <div class="h-8 w-6 bg-gray-100 border border-gray-200 rounded text-[10px] flex items-center justify-center font-bold text-gray-400 group-hover:bg-white group-hover:border-orange-200 transition">
                                        BK
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate group-hover:text-orange-600 transition">
                                            {{ borrow.book?.title }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Batas: 
                                            <span :class="{ 'text-red-600 font-medium': new Date(borrow.due_date) < new Date() }">
                                                {{ new Date(borrow.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <span v-if="new Date(borrow.due_date) < new Date()" class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded">
                                        Terlambat
                                    </span>
                                    <svg class="h-4 w-4 text-gray-300 group-hover:text-orange-400 transform group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Panel Manajemen (Admin / Petugas) -->
                    <Link v-else :href="managementRoute" class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6 flex flex-col items-center justify-center text-center min-h-[220px] hover:border-orange-200 transition group">
                        <div class="h-10 w-10 rounded-xl bg-orange-50 text-orange-500 border border-orange-100 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-800">Panel Manajemen Aktif</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-xs">Akses penuh ke sistem kontrol dashboard.</p>
                        <span class="mt-4 text-xs font-semibold text-orange-500 bg-orange-50 border border-orange-100 px-3 py-1 rounded-lg group-hover:bg-orange-500 group-hover:text-white transition">
                            Masuk Dashboard &rarr;
                        </span>
                    </Link>
                </div>

                <!-- Update Profile Section -->
                <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <!-- Security Management -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
                        <UpdatePasswordForm />
                    </div>
                    
                    <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6 sm:p-8 bg-gradient-to-b from-white to-red-50/5">
                        <DeleteUserForm />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
