<template>
  <AuthenticatedLayout title="Detail Petugas">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex items-center gap-4">
          <Link href="/admin/accounts" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Detail Petugas</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Profile Info -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-2xl">
                  {{ petugas.name.charAt(0) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ petugas.name }}</h2>
                <p class="text-sm text-gray-500">{{ petugas.email }}</p>
                
                <div class="mt-4 w-full space-y-3">
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">Role</span>
                    <span class="text-sm font-medium">Petugas</span>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">Status Akun</span>
                    <span class="text-sm font-medium text-green-600">Aktif</span>
                  </div>
                  <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-500">Terdaftar</span>
                    <span class="text-sm font-medium">{{ petugas.created_at }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Logs -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
              <div class="space-y-3">
                <div v-for="activity in activities" :key="activity.id" class="flex items-start gap-3 p-3 border rounded-lg">
                  <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                    <p class="text-xs text-gray-500">{{ activity.created_at }}</p>
                  </div>
                </div>
                <div v-if="activities.length === 0" class="text-center py-8 text-gray-500">
                  Belum ada aktivitas.
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  petugas: { type: Object, default: () => ({}) },
  activities: { type: Array, default: () => [] },
});
</script>