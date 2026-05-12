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
          <h1 class="text-2xl font-bold text-gray-900">Detail Petugas Perpustakaan</h1>
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
                    <span class="text-sm font-medium">{{ petugas.role }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">Status</span>
                    <span :class="{
                      'bg-green-100 text-green-800': petugas.status === 'approved',
                      'bg-yellow-100 text-yellow-800': petugas.status === 'pending',
                      'bg-red-100 text-red-800': petugas.status === 'rejected',
                    }" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ petugas.status }}
                    </span>
                  </div>
                  <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-500">Dibuat</span>
                    <span class="text-sm font-medium">{{ formatDate(petugas.created_at) }}</span>
                  </div>
                </div>

                <div class="mt-6 w-full flex flex-col gap-2">
                  <button @click="deleteUser" class="w-full py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                    Hapus Akun
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity History -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden flex flex-col h-[600px]">
              <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-transparent">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Aktivitas</h3>
                <p class="text-sm text-gray-500">100 aktivitas terbaru</p>
              </div>
              
              <!-- Scrollable Activity List -->
              <div class="flex-1 overflow-y-auto">
                <div v-if="activities.length === 0" class="flex items-center justify-center h-full text-gray-400">
                  <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>Belum ada aktivitas</p>
                  </div>
                </div>
                
                <div v-else class="divide-y divide-gray-100">
                  <div 
                    v-for="(activity, index) in activities" 
                    :key="index"
                    class="px-6 py-4 hover:bg-gray-50 transition-colors border-l-4"
                    :class="{
                      'border-l-blue-500': activity.type === 'login',
                      'border-l-green-500': activity.type === 'create',
                      'border-l-orange-500': activity.type === 'update',
                      'border-l-red-500': activity.type === 'delete',
                      'border-l-purple-500': activity.type === 'approve',
                      'border-l-indigo-500': activity.type === 'reject',
                      'border-l-gray-300': !['login', 'create', 'update', 'delete', 'approve', 'reject'].includes(activity.type),
                    }"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <div class="flex items-center gap-3 mb-1">
                          <span :class="{
                            'bg-blue-100 text-blue-700': activity.type === 'login',
                            'bg-green-100 text-green-700': activity.type === 'create',
                            'bg-orange-100 text-orange-700': activity.type === 'update',
                            'bg-red-100 text-red-700': activity.type === 'delete',
                            'bg-purple-100 text-purple-700': activity.type === 'approve',
                            'bg-indigo-100 text-indigo-700': activity.type === 'reject',
                            'bg-gray-100 text-gray-700': !['login', 'create', 'update', 'delete', 'approve', 'reject'].includes(activity.type),
                          }" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase">
                            {{ activity.type }}
                          </span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ formatDateTime(activity.created_at) }}</p>
                      </div>
                    </div>
                    
                    <!-- Metadata if available -->
                    <div v-if="activity.metadata" class="mt-2 pt-2 border-t border-gray-100">
                      <details class="text-xs text-gray-600 cursor-pointer hover:text-gray-800">
                        <summary class="font-medium">Detail Metadata</summary>
                        <pre class="mt-2 bg-gray-50 p-2 rounded text-[10px] overflow-auto max-h-24">{{ JSON.stringify(activity.metadata, null, 2) }}</pre>
                      </details>
                    </div>
                  </div>
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
import { Link, router } from '@inertiajs/vue3';
import { dangerConfirm } from '@/Utils/sweetAlert';

const props = defineProps({
  petugas: { type: Object, default: () => ({}) },
  activities: { type: Array, default: () => [] },
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatDateTime = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  });
};

const deleteUser = async () => {
  if (await dangerConfirm({
    title: 'Hapus akun ini?',
    text: `Akun ${props.petugas.name} akan dihapus secara permanen.`,
    confirmButtonText: 'Ya, hapus',
  })) {
    router.delete(`/admin/accounts/${props.petugas.id}`);
  }
};
</script>

