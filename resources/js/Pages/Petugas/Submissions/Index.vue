<template>
  <AuthenticatedLayout title="Pengajuan Buku Saya">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengajuan Buku Saya</h1>
          <p class="mt-2 text-sm text-gray-500">Pantau status pengajuan buku yang telah Anda buat.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-bold uppercase text-gray-500 tracking-wide">Total Pengajuan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.total }}</p>
              </div>
              <div class="text-4xl text-gray-200">📚</div>
            </div>
          </div>
          
          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 border-t-4 border-amber-400">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-bold uppercase text-amber-600 tracking-wide">Menunggu</p>
                <p class="text-3xl font-bold text-amber-600 mt-2">{{ stats.pending }}</p>
              </div>
              <div class="text-4xl">⏳</div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 border-t-4 border-emerald-400">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-bold uppercase text-emerald-600 tracking-wide">Disetujui</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2">{{ stats.approved }}</p>
              </div>
              <div class="text-4xl">✓</div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 border-t-4 border-rose-400">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-bold uppercase text-rose-600 tracking-wide">Ditolak</p>
                <p class="text-3xl font-bold text-rose-600 mt-2">{{ stats.rejected }}</p>
              </div>
              <div class="text-4xl">✗</div>
            </div>
          </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Daftar Pengajuan</h2>
            <Link :href="route('petugas.books.create')" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              + Ajukan Buku Baru
            </Link>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Judul Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Penulis</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">ISBN</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jumlah</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="sub in submissions" :key="sub.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <img v-if="sub.cover" :src="sub.cover" :alt="sub.title" class="w-10 h-14 object-cover rounded">
                      <div v-else class="w-10 h-14 bg-gray-200 rounded flex items-center justify-center text-xs">
                        📕
                      </div>
                      <div>
                        <div class="font-semibold text-gray-900">{{ sub.title }}</div>
                        <div class="text-xs text-gray-500">{{ sub.category || 'Umum' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ sub.author }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ sub.isbn || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ sub.quantity }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700 ring-1 ring-amber-200': sub.status === 'pending',
                      'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200': sub.status === 'approved',
                      'bg-rose-100 text-rose-700 ring-1 ring-rose-200': sub.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tight">
                      {{ getStatusLabel(sub.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ formatDate(sub.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button v-if="sub.status === 'pending'" @click="showDetails(sub)" class="text-orange-600 hover:text-orange-700 font-medium text-sm">
                        Lihat
                      </button>
                      <span v-else class="text-xs text-gray-500">
                        {{ sub.status === 'approved' ? '✓ Ditambahkan' : '✗ Ditolak' }}
                      </span>
                    </div>
                  </td>
                </tr>
                <tr v-if="submissions.length === 0">
                  <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-2">
                      <div class="text-4xl">📭</div>
                      <p>Belum ada pengajuan buku.</p>
                      <Link :href="route('petugas.books.create')" class="text-orange-600 hover:text-orange-700 font-medium mt-2">
                        Buat pengajuan baru →
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { infoAlert } from '@/Utils/sweetAlert';

defineProps({
  submissions: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({ total: 0, pending: 0, approved: 0, rejected: 0 }) },
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const getStatusLabel = (status) => {
  const labels = {
    'pending': 'Menunggu',
    'approved': 'Disetujui',
    'rejected': 'Ditolak',
  };
  return labels[status] || status;
};

const showDetails = (sub) => {
  infoAlert({
    title: 'Detail Pengajuan',
    html: `
      <div class="space-y-2 text-left">
        <p><span class="font-bold text-slate-700">Status:</span> ${getStatusLabel(sub.status)}</p>
        <p><span class="font-bold text-slate-700">Diajukan:</span> ${formatDate(sub.created_at)}</p>
      </div>
    `,
  });
};
</script>
