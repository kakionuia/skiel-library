<template>
  <AuthenticatedLayout title="Penunggakan Buku">
    <div class="py-12 px-24 bg-slate-50 min-h-screen">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Penunggakan Buku</h1>
          <p class="mt-2 text-sm text-slate-500">Kelola anggota dengan buku yang terlambat dikembalikan.</p>
        </div>

        <!-- Notification Button -->
        <div class="mb-6 flex justify-end">
          <button 
            @click="sendBulkNotification"
            class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-orange-500/10"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Kirim Notifikasi ke Semua
          </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500">Anggota</th>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500">Buku</th>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500">Jatuh Tempo</th>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500">Hari Terlambat</th>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500">Denda</th>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500">Status Buku</th>
                  <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="borrow in lateBorrows.data" :key="borrow.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-slate-900">{{ borrow.user?.name || '-' }}</div>
                    <div class="text-xs text-slate-500">{{ borrow.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-slate-900">{{ borrow.book?.title || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-slate-900">{{ formatDate(borrow.due_date) }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-slate-900 font-medium">{{ borrow.late_days || 0 }} hari</span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-orange-600 font-semibold">Rp {{ formatNumber(borrow.late_fee) }}</span>
                  </td>
                  <td class="px-6 py-4">
                    <span 
                      class="px-2.5 py-1 rounded-md text-xs font-medium border"
                      :class="borrow.is_damaged 
                        ? 'bg-slate-100 text-slate-900 border-slate-300' 
                        : 'bg-white text-slate-600 border-slate-200'"
                    >
                      {{ borrow.is_damaged ? 'Rusak' : 'Normal' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                      <button 
                        @click="sendNotification(borrow.user_id)"
                        class="px-3 py-1.5 bg-slate-100 text-slate-900 rounded-lg text-xs font-semibold hover:bg-slate-200 transition-all"
                      >
                        Notif
                      </button>
                      <Link 
                        :href="`/admin/late-returns/${borrow.user_id}`" 
                        class="text-orange-600 hover:text-orange-700 text-xs font-bold uppercase tracking-wider"
                      >
                        Detail
                      </Link>
                    </div>
                  </td>
                </tr>
                <tr v-if="!lateBorrows.data?.length">
                  <td colspan="7" class="px-6 py-8 text-center text-slate-500">Tidak ada penunggakan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="lateBorrows.links" class="mt-6 flex items-center justify-between">
          <p class="text-sm text-slate-600">
            Menampilkan {{ lateBorrows.from }} hingga {{ lateBorrows.to }} dari {{ lateBorrows.total }} hasil
          </p>
          <div class="flex gap-1.5">
            <Link
              v-for="link in lateBorrows.links"
              :key="link.label"
              v-html="link.label"
              :href="link.url || '#'"
              :class="{
                'px-3 py-1.5 rounded-lg text-xs font-medium border transition-all': true,
                'bg-orange-600 text-white border-orange-600': link.active,
                'bg-white text-slate-700 border-slate-200 hover:bg-slate-50': !link.active,
                'opacity-40 cursor-not-allowed': !link.url
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
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Kirim',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/notifications/send-late-reminder/${userId}`, {}, {
        onSuccess: () => {
          Swal.fire({
            title: 'Terkirim!',
            text: 'Notifikasi berhasil dikirim.',
            icon: 'success',
            confirmButtonColor: '#f97316'
          });
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
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Kirim Semua',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post('/admin/notifications/send-bulk-late-reminder', {}, {
        onSuccess: () => {
          Swal.fire({
            title: 'Terkirim!',
            text: 'Notifikasi massal berhasil dikirim.',
            icon: 'success',
            confirmButtonColor: '#f97316'
          });
        }
      });
    }
  });
};
</script>