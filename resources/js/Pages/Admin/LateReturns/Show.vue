<template>
  <AuthenticatedLayout title="Detail Penunggakan">
    <div class="py-12 px-24 mx-12 bg-slate-50 min-h-screen">
      <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4">
          <Link href="/admin/late-returns" class="text-slate-500 hover:text-slate-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-slate-900">Detail Keterlambatan</h1>
            <p class="text-sm text-slate-500">{{ user.name }} - {{ user.email }}</p>
          </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-slate-200">
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">NISN</p>
              <p class="mt-1 font-semibold text-slate-900">{{ user.nisn || '-' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kelas</p>
              <p class="mt-1 font-semibold text-slate-900">{{ user.kelas || '-' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Denda</p>
              <p class="mt-1 font-bold text-orange-600">Rp {{ formatNumber(totalFine) }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Terlambat</p>
              <p class="mt-1 font-bold text-slate-900">{{ totalLateDays }} hari</p>
            </div>
          </div>
        </div>

        <!-- Looping Daftar Buku Berasalah -->
        <div class="space-y-5">
          <div v-for="borrow in lateBorrows" :key="borrow.id" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 overflow-hidden">
            
            <!-- Book Header Info -->
            <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-bold text-slate-900">{{ borrow.book?.title }}</h2>
                <p class="text-sm text-slate-500">{{ borrow.book?.author }}</p>
                
                <div class="mt-4 grid grid-cols-3 gap-5 text-sm">
                  <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jatuh Tempo</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ formatDate(borrow.due_date) }}</p>
                  </div>
                  <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terlambat</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ borrow.late_days }} hari</p>
                  </div>
                  <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                    <p class="mt-1 font-bold text-orange-600">Rp {{ formatNumber((borrow.late_fee || 0) + (borrow.additional_fine || 0)) }}</p>
                  </div>
                </div>
              </div>
              
              <div class="text-right space-y-2">
                <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium border border-slate-200 bg-slate-50 text-slate-700">
                  {{ getPaymentStatusLabel(borrow.payment_status) }}
                </span>
                <p v-if="borrow.payment_method && borrow.payment_method !== 'none'" class="text-xs text-slate-500">
                  {{ borrow.payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                </p>
              </div>
            </div>

            <!-- Bagian Laporan Buku Rusak -->
            <div v-if="borrow.is_damaged" class="p-6 bg-slate-50/50 border-b border-slate-100 space-y-4">
              <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Laporan Kerusakan</h3>
              
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="space-y-2 text-slate-700">
                  <p class="text-sm"><span class="font-medium text-slate-500">Status:</span> {{ getDamageStatusLabel(borrow.damage_status) }}</p>
                  <p class="text-sm"><span class="font-medium text-slate-500">Jenis:</span> {{ getDamageTypeLabel(borrow.damage_type) }}</p>
                  <p class="text-sm bg-white p-3 rounded-xl border border-slate-200 text-slate-600">{{ borrow.damage_description }}</p>
                </div>
                
                <div v-if="borrow.damage_images?.length" class="grid grid-cols-2 gap-3">
                  <img v-for="(image, index) in borrow.damage_images" :key="index" :src="image" class="w-full h-32 object-cover rounded-xl border border-slate-200" />
                </div>
              </div>

              <!-- Form Input Denda Tambahan -->
              <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 pt-4 border-t border-slate-200/60">
                <input 
                  v-model.number="damageForm[borrow.id].additionalFine" 
                  type="number" 
                  min="0" 
                  class="rounded-xl border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" 
                  placeholder="Denda tambahan" 
                />
                <input 
                  v-model="damageForm[borrow.id].staffComment" 
                  type="text" 
                  class="rounded-xl border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" 
                  placeholder="Komentar admin/petugas" 
                />
                <button 
                  @click="submitAdditionalFine(borrow.id)" 
                  class="px-5 py-3 bg-slate-900 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition-colors"
                >
                  Kirim Tanggapan
                </button>
              </div>
            </div>

            <!-- Kondisi Buku Normal -->
            <div v-else class="p-6 bg-white border-b border-slate-100">
              <p class="text-sm font-medium text-slate-600">Buku dilaporkan dalam kondisi baik. Tidak ada biaya kerusakan tambahan.</p>
            </div>

            <!-- Action Selesai -->
            <div class="p-6 flex justify-end">
              <button 
                @click="markAsResolved(borrow.id)" 
                class="px-6 py-3 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700 transition-colors shadow-lg shadow-orange-500/10"
              >
                Tandai Masalah Selesai
              </button>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="lateBorrows.length === 0" class="bg-white rounded-2xl p-10 text-center text-slate-500 shadow-sm ring-1 ring-slate-200">
            Tidak ada penunggakan.
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  lateBorrows: { type: Array, default: () => [] },
});

const damageForm = reactive({});

props.lateBorrows.forEach((borrow) => {
  if (borrow.is_damaged) {
    damageForm[borrow.id] = {
      additionalFine: borrow.additional_fine || 0,
      staffComment: borrow.staff_comment || '',
    };
  }
});

const totalFine = props.lateBorrows.reduce((sum, b) => sum + (b.late_fee || 0) + (b.additional_fine || 0), 0);
const totalLateDays = props.lateBorrows.reduce((sum, b) => sum + (b.late_days || 0), 0);

const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';

const getDamageTypeLabel = (type) => ({
  lost: 'Hilang',
  wet: 'Basah',
  torn: 'Sobek',
  stained: 'Tertimpa Noda',
  other: 'Lainnya',
}[type] || '-');

const getDamageStatusLabel = (status) => ({
  pending: 'Menunggu tanggapan',
  reviewed: 'Sudah ditinjau',
  responded: 'Tanggapan dikirim',
  resolved: 'Selesai',
}[status] || '-');

const getPaymentStatusLabel = (status) => {
  if (status === 'confirmed') return 'Lunas';
  if (status === 'pending') return 'Menunggu pembayaran';
  return 'Belum pilih bayar';
};

function submitAdditionalFine(borrowId) {
  router.post(`/admin/late-returns/${borrowId}/additional-fine`, {
    additional_fine: damageForm[borrowId].additionalFine,
    staff_comment: damageForm[borrowId].staffComment,
  }, {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil',
        text: 'Tanggapan denda tambahan dikirim ke siswa.',
        icon: 'success',
        confirmButtonColor: '#f97316'
      });
    },
  });
}

function markAsResolved(borrowId) {
  router.post(`/admin/late-returns/${borrowId}/resolve`, {}, {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil',
        text: 'Masalah keterlambatan selesai.',
        icon: 'success',
        confirmButtonColor: '#f97316'
      });
    },
  });
}
</script>