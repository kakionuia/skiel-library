<template>
  <AuthenticatedLayout title="Laporan Kerusakan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">📋 Laporan Kerusakan Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola laporan kerusakan buku yang dilaporkan oleh pengguna. Tanggapi dan tentukan denda tambahan jika diperlukan.</p>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div>
              <h3 class="font-bold text-blue-900">Cara Merespons Laporan</h3>
              <p class="text-sm text-blue-800 mt-1">
                1. Klik laporan untuk melihat detail kerusakan dan foto
                2. Tentukan nominal denda tambahan (selain denda keterlambatan)
                3. Beri komentar untuk penjelasan kepada pengguna
                4. Setelah pengguna membayar, tandai masalah sebagai selesai
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Pengguna</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jenis Kerusakan</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Denda Keterlambatan</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal Laporan</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="report in reports.data" :key="report.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-gray-900">{{ report.user?.name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ report.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ report.book?.title || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ report.book?.author }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span 
                      class="px-3 py-1 rounded-full text-xs font-bold"
                      :class="getDamageBadgeClass(report.damage_type)"
                    >
                      {{ getDamageTypeLabel(report.damage_type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-orange-600 font-medium">Rp {{ formatNumber(report.late_fee) }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-sm text-gray-600">{{ formatDate(report.created_at) }}</span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link 
                      :href="`/petugas/damage-reports/${report.id}`" 
                      class="inline-flex items-center gap-2 px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold hover:bg-orange-200 transition-all"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                      Respons
                    </Link>
                  </td>
                </tr>
                <tr v-if="!reports.data?.length">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    Tidak ada laporan kerusakan yang menunggu respons.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="reports.links" class="mt-6 flex items-center justify-between">
          <p class="text-sm text-gray-600">
            Menampilkan {{ reports.from }} hingga {{ reports.to }} dari {{ reports.total }} hasil
          </p>
          <div class="flex gap-2">
            <Link
              v-for="link in reports.links"
              :key="link.label"
              v-html="link.label"
              :href="link.url || '#'"
              :class="{
                'px-3 py-1 rounded border': true,
                'bg-orange-600 text-white border-orange-600': link.active,
                'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
              }"
            />
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
  reports: { type: Object, default: () => ({ data: [] }) },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const getDamageTypeLabel = (type) => {
  const labels = {
    lost: '🗑️ Hilang',
    wet: '💧 Basah',
    torn: '✂️ Sobek',
    stained: '🔴 Ternoda',
    other: '❓ Lainnya'
  };
  return labels[type] || type;
};

const getDamageBadgeClass = (type) => {
  const classes = {
    lost: 'bg-red-100 text-red-700',
    wet: 'bg-blue-100 text-blue-700',
    torn: 'bg-yellow-100 text-yellow-700',
    stained: 'bg-purple-100 text-purple-700',
    other: 'bg-gray-100 text-gray-700'
  };
  return classes[type] || 'bg-gray-100 text-gray-700';
};
</script>
