<template>
  <AuthenticatedLayout title="Penunggakan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Penunggakan Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola anggota dengan buku yang terlambat dikembalikan.</p>
        </div>

        <!-- Notification Button -->
        <div class="mb-6 flex justify-end">
          <button 
            @click="sendBulkNotification"
            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-orange-500/20"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Kirim Notifikasi ke Semua
          </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jatuh Tempo</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Hari Terlambat</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Denda</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="borrow in lateBorrows.data" :key="borrow.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-gray-900">{{ borrow.user?.name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ borrow.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ borrow.book?.title || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ formatDate(borrow.due_date) }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-red-600 font-medium">{{ borrow.late_days || 0 }} hari</span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-orange-600 font-medium">Rp {{ formatNumber(borrow.late_fee) }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <span 
                      class="px-3 py-1 rounded-full text-xs font-bold"
                      :class="borrow.is_damaged ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                    >
                      {{ borrow.is_damaged ? '⚠ Rusak' : '✓ Normal' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button 
                        @click="sendNotification(borrow.user_id)"
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold hover:bg-blue-200 transition-all"
                      >
                        🔔 Notif
                      </button>
                      <Link 
                        :href="`/admin/late-returns/${borrow.user_id}`" 
                        class="text-orange-600 hover:text-orange-800 text-sm font-bold"
                      >
                        Detail →
                      </Link>
                    </div>
                  </td>
                </tr>
                <tr v-if="!lateBorrows.data?.length">
                  <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada penunggakan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="lateBorrows.links" class="mt-6 flex items-center justify-between">
          <p class="text-sm text-gray-600">
            Menampilkan {{ lateBorrows.from }} hingga {{ lateBorrows.to }} dari {{ lateBorrows.total }} hasil
          </p>
          <div class="flex gap-2">
            <Link
              v-for="link in lateBorrows.links"
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
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
  lateBorrows: { type: Object, default: () => ({ data: [] }) },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const sendNotification = (userId) => {
  Swal.fire({
    title: 'Kirim Notifikasi?',
    text: 'Notifikasi keterlambatan akan dikirim ke anggota ini.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Kirim',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/notifications/send-late-reminder/${userId}`, {}, {
        onSuccess: () => {
          Swal.fire('Terkirim!', 'Notifikasi berhasil dikirim.', 'success');
        }
      });
    }
  });
};

const sendBulkNotification = () => {
  Swal.fire({
    title: 'Kirim Notifikasi Massal?',
    text: 'Semua anggota dengan keterlambatan akan menerima notifikasi.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Kirim Semua',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post('/admin/notifications/send-bulk-late-reminder', {}, {
        onSuccess: () => {
          Swal.fire('Terkirim!', 'Notifikasi massal berhasil dikirim.', 'success');
        }
      });
    }
  });
};
</script>