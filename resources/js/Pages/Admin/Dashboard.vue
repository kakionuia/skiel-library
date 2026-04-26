<template>
  <AuthenticatedLayout title="Dashboard">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Overview</h1>
          <p class="mt-2 text-sm text-gray-500">Pantau performa sistem dan kelola pengguna dalam satu dasbor terpadu.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
            <dt>
              <div class="absolute rounded-xl bg-blue-500/10 p-3">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Petugas</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
              <p class="text-3xl font-semibold text-gray-900">{{ petugasCount }}</p>
            </dd>
          </div>

          <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md">
            <dt>
              <div class="absolute rounded-xl bg-indigo-500/10 p-3">
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Pengguna</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
              <p class="text-3xl font-semibold text-gray-900">{{ userCount }}</p>
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
              <p class="text-3xl font-semibold text-amber-600">{{ pendingCount }}</p>
            </dd>
          </div>
        </div>

        <div class="mt-10">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Link 
              href="/admin/accounts" 
              class="group flex items-center space-x-4 rounded-2xl border border-gray-200 bg-white p-5 transition-all hover:border-indigo-500 hover:ring-1 hover:ring-indigo-500 shadow-sm"
            >
              <div class="flex-shrink-0 rounded-lg bg-gray-50 p-3 group-hover:bg-indigo-50">
                <svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <p class="text-base font-semibold text-gray-900">Kelola Akun</p>
                <p class="text-sm text-gray-500">Konfigurasi hak akses petugas dan user.</p>
              </div>
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Pastikan path benar
import { Link } from '@inertiajs/vue3';

defineProps({
  petugasCount: { type: Number, default: 0 },
  userCount: { type: Number, default: 0 },
  pendingCount: { type: Number, default: 0 },
});
</script>