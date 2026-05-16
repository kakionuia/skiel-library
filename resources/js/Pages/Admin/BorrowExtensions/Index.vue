<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  extensions: Object,
  filters: Object
});

const page = usePage();

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

function getDaysLeft(dueDate) {
  if (!dueDate) return '-';
  const diffDays = Math.ceil((new Date(dueDate) - new Date()) / (1000 * 60 * 60 * 24));
  if (diffDays < 0) return `${Math.abs(diffDays)} hari terlambat`;
  if (diffDays === 0) return 'Hari ini';
  return `${diffDays} hari lagi`;
}

function getStatusBadge(status) {
  const badges = {
    pending: { class: 'bg-amber-100 text-amber-800', text: 'Menunggu' },
    approved: { class: 'bg-green-100 text-green-800', text: 'Disetujui' },
    rejected: { class: 'bg-red-100 text-red-800', text: 'Ditolak' }
  };
  return badges[status] || { class: 'bg-gray-100 text-gray-800', text: status };
}

function approveExtension(extension) {
  Swal.fire({
    title: 'Setujui Perpanjangan?',
    html: `
      <div class="text-left space-y-2">
        <p><strong>${extension.borrow.user.name}</strong> mengajukan perpanjangan <strong>${extension.borrow.book.title}</strong></p>
        <p>Durasi: <strong>${extension.requested_days} hari</strong></p>
        ${extension.reason ? `<p>Alasan: <em>${extension.reason}</em></p>` : ''}
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Setujui',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/borrow-extensions/${extension.id}/approve`, {}, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Perpanjangan berhasil disetujui.', 'success');
        },
        onError: () => {
          Swal.fire('Gagal!', 'Terjadi kesalahan saat menyetujui perpanjangan.', 'error');
        }
      });
    }
  });
}

function rejectExtension(extension) {
  Swal.fire({
    title: 'Tolak Perpanjangan?',
    html: '<p class="text-sm">Masukkan alasan penolakan (opsional):</p>',
    input: 'textarea',
    inputAttributes: { class: 'w-full px-3 py-2 border border-gray-300 rounded-lg' },
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Tolak',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/borrow-extensions/${extension.id}/reject`, {
        reason: result.value || ''
      }, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Perpanjangan berhasil ditolak.', 'success');
        },
        onError: () => {
          Swal.fire('Gagal!', 'Terjadi kesalahan saat menolak perpanjangan.', 'error');
        }
      });
    }
  });
}
</script>

<template>
  <AdminLayout>
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">Permintaan Perpanjangan Peminjaman</h1>
        <p class="text-gray-600 text-sm mt-1">Kelola permintaan perpanjangan dari pengguna</p>
      </div>

      <div class="px-6 py-4">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Permintaan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="extensions.data.length === 0">
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada permintaan perpanjangan</td>
              </tr>
              <tr v-for="extension in extensions.data" :key="extension.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ extension.borrow.user.name }}</div>
                  <div class="text-xs text-gray-500">{{ extension.borrow.user.email }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ extension.borrow.book.title }}</div>
                  <div class="text-xs text-gray-500">{{ extension.borrow.book.author }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-medium text-gray-900">{{ extension.requested_days }} hari</span>
                </td>
                <td class="px-6 py-4">
                  <p class="text-sm text-gray-600">{{ extension.reason || '-' }}</p>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(extension.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="px-3 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusBadge(extension.status).class"
                  >
                    {{ getStatusBadge(extension.status).text }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <div v-if="extension.status === 'pending'" class="flex gap-2">
                    <button
                      @click="approveExtension(extension)"
                      class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium"
                    >
                      Setujui
                    </button>
                    <button
                      @click="rejectExtension(extension)"
                      class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium"
                    >
                      Tolak
                    </button>
                  </div>
                  <span v-else class="text-gray-500 text-xs">Selesai</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="extensions.links" class="mt-6 flex items-center justify-between">
          <p class="text-sm text-gray-600">
            Menampilkan {{ extensions.from }} hingga {{ extensions.to }} dari {{ extensions.total }} hasil
          </p>
          <div class="flex gap-2">
            <Link
              v-for="link in extensions.links"
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
  </AdminLayout>
</template>
