<template>
  <AuthenticatedLayout title="Dashboard">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Admin</h1>
          <p class="mt-2 text-sm text-gray-500">Pantau performa sistem dan kelola pengguna dalam satu dasbor terpadu.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
            <dt>
              <div class="absolute rounded-xl bg-blue-500/10 p-3">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Buku</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
              <p class="text-3xl font-semibold text-gray-900">{{ stats.totalBooks }}</p>
            </dd>
          </div>

          <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
            <dt>
              <div class="absolute rounded-xl bg-green-500/10 p-3">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Anggota</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
              <p class="text-3xl font-semibold text-gray-900">{{ stats.totalMembers }}</p>
            </dd>
          </div>

          <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
            <dt>
              <div class="absolute rounded-xl bg-purple-500/10 p-3">
                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Petugas</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
              <p class="text-3xl font-semibold text-gray-900">{{ stats.totalPetugas }}</p>
            </dd>
          </div>

          <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
            <dt>
              <div class="absolute rounded-xl bg-amber-500/10 p-3">
                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="ml-16 truncate text-sm font-medium text-gray-500">Menunggu Persetujuan</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
              <p class="text-3xl font-semibold text-amber-600">{{ stats.pendingMembers }}</p>
            </dd>
          </div>
        </div>

        <!-- Charts and Alerts Row -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
          <!-- Borrow Chart -->
          <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Peminjaman Buku (6 Bulan Terakhir)</h2>
            <div class="mt-6 h-64">
              <div class="flex h-full items-end justify-between gap-2">
                <div v-for="(item, index) in monthlyBorrows" :key="index" class="flex-1 flex flex-col items-center">
                  <div class="w-full relative flex-1 flex items-end">
                    <div 
                      class="w-full bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-lg transition-all duration-500"
                      :style="{ height: `${(item.count / maxBorrow) * 100}%` }"
                    ></div>
                  </div>
                  <p class="mt-2 text-xs text-gray-500">{{ item.month }}</p>
                  <p class="text-sm font-medium text-gray-900">{{ item.count }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Late Fees & Alerts -->
          <div class="space-y-6">
            <!-- Late Returns Alert -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">Buku Terlambat</p>
                  <p class="mt-2 text-3xl font-bold text-red-600">{{ stats.lateReturnsCount }}</p>
                </div>
                <Link href="/admin/late-returns" class="text-sm text-blue-600 hover:text-blue-800">
                  Lihat Semua →
                </Link>
              </div>
            </div>

            <!-- Total Late Fee -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">Total Denda Keterlambatan</p>
                  <p class="mt-2 text-3xl font-bold text-orange-600">Rp {{ formatNumber(stats.totalLateFee) }}</p>
                </div>
              </div>
            </div>

            <!-- Pending Book Requests -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-500">Permintaan Buku</p>
                  <p class="mt-2 text-3xl font-bold text-indigo-600">{{ stats.pendingBookRequests }}</p>
                </div>
                <Link href="/admin/book-requests" class="text-sm text-blue-600 hover:text-blue-800">
                  Lihat Semua →
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Menu -->
        <div class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Menu Cepat</h2>
          <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Link href="/admin/accounts" class="group flex items-center space-x-4 rounded-xl border border-gray-200 bg-white p-4 transition-all hover:border-blue-500 hover:ring-1 hover:ring-blue-500">
              <div class="flex-shrink-0 rounded-lg bg-blue-50 p-3 group-hover:bg-blue-100">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Kelola Akun</p>
                <p class="text-sm text-gray-500">Kelola anggota & petugas</p>
              </div>
            </Link>

            <Link href="/admin/book-requests" class="group flex items-center space-x-4 rounded-xl border border-gray-200 bg-white p-4 transition-all hover:border-green-500 hover:ring-1 hover:ring-green-500">
              <div class="flex-shrink-0 rounded-lg bg-green-50 p-3 group-hover:bg-green-100">
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Permintaan Buku</p>
                <p class="text-sm text-gray-500">Lihat permintaan petugas</p>
              </div>
            </Link>

            <Link href="/admin/late-returns" class="group flex items-center space-x-4 rounded-xl border border-gray-200 bg-white p-4 transition-all hover:border-red-500 hover:ring-1 hover:ring-red-500">
              <div class="flex-shrink-0 rounded-lg bg-red-50 p-3 group-hover:bg-red-100">
                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Penunggakan</p>
                <p class="text-sm text-gray-500">Lihat buku terlambat</p>
              </div>
            </Link>

            <Link href="/admin/complaints" class="group flex items-center space-x-4 rounded-xl border border-gray-200 bg-white p-4 transition-all hover:border-yellow-500 hover:ring-1 hover:ring-yellow-500">
              <div class="flex-shrink-0 rounded-lg bg-yellow-50 p-3 group-hover:bg-yellow-100">
                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-900">Keluhan & Saran</p>
                <p class="text-sm text-gray-500">{{ stats.pendingComplaints }} belum ditangani</p>
              </div>
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  monthlyBorrows: { type: Array, default: () => [] },
});

const maxBorrow = computed(() => {
  const max = Math.max(...props.monthlyBorrows.map(b => b.count));
  return max > 0 ? max : 1;
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};
</script>