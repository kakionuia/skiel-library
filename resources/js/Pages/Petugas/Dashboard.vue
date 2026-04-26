<template>
  <PetugasLayout title="Dashboard Petugas">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>
          <p class="mt-2 text-sm text-gray-500">Selamat datang di panel petugas perpustakaan.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Total Buku</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.totalBooks }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.borrowedBooks }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Terlambat</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.lateReturns }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Total Anggota</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.totalMembers }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- My Book Requests -->
          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Pengajuan Buku Saya</h3>
              <Link :href="route('petugas.book-requests')" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat semua →</Link>
            </div>
            <div class="space-y-3">
              <div v-for="request in myBookRequests.data" :key="request.id" class="flex items-center justify-between p-3 border border-gray-100 rounded-lg">
                <div>
                  <p class="font-medium text-gray-900">{{ request.title }}</p>
                  <p class="text-sm text-gray-500">{{ request.author }}</p>
                </div>
                <span :class="{
                  'bg-amber-100 text-amber-700': request.status === 'pending',
                  'bg-green-100 text-green-700': request.status === 'approved',
                  'bg-red-100 text-red-700': request.status === 'rejected',
                }" class="px-2 py-1 rounded text-xs font-medium">
                  {{ request.status }}
                </span>
              </div>
              <div v-if="!myBookRequests.data?.length" class="text-center py-4 text-gray-500">
                Belum ada pengajuan.
              </div>
            </div>
          </div>

          <!-- Recent Activities -->
          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
              <Link :href="route('petugas.activity-logs')" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat semua →</Link>
            </div>
            <div class="space-y-3">
              <div v-for="activity in recentActivities.data" :key="activity.id" class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                <div class="flex-1">
                  <p class="text-sm text-gray-900">{{ activity.description }}</p>
                  <p class="text-xs text-gray-500">{{ activity.created_at }}</p>
                </div>
              </div>
              <div v-if="!recentActivities.data?.length" class="text-center py-4 text-gray-500">
                Belum ada aktivitas.
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </PetugasLayout>
</template>

<script setup>
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  stats: { type: Object, default: () => ({}) },
  myBookRequests: { type: Object, default: () => ({ data: [] }) },
  recentActivities: { type: Object, default: () => ({ data: [] }) },
});
</script>