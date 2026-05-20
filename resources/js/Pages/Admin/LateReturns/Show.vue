<template>
  <AuthenticatedLayout title="Detail Penunggakan">
    <div class="py-10 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
          <Link href="/admin/late-returns" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Keterlambatan</h1>
            <p class="text-sm text-gray-500">{{ user.name }} - {{ user.email }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase">NISN</p>
              <p class="mt-1 font-semibold text-gray-900">{{ user.nisn || '-' }}</p>
            </div>
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase">Kelas</p>
              <p class="mt-1 font-semibold text-gray-900">{{ user.kelas || '-' }}</p>
            </div>
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase">Total Denda</p>
              <p class="mt-1 font-black text-red-600">Rp {{ formatNumber(totalFine) }}</p>
            </div>
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase">Total Terlambat</p>
              <p class="mt-1 font-black text-orange-600">{{ totalLateDays }} hari</p>
            </div>
          </div>
        </div>

        <div class="space-y-5">
          <div v-for="borrow in lateBorrows" :key="borrow.id" class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-bold text-gray-900">{{ borrow.book?.title }}</h2>
                <p class="text-sm text-gray-500">{{ borrow.book?.author }}</p>
                <div class="mt-4 grid grid-cols-3 gap-5 text-sm">
                  <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Jatuh Tempo</p>
                    <p class="mt-1 font-semibold text-red-600">{{ formatDate(borrow.due_date) }}</p>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Terlambat</p>
                    <p class="mt-1 font-semibold text-orange-600">{{ borrow.late_days }} hari</p>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total</p>
                    <p class="mt-1 font-black text-red-600">Rp {{ formatNumber((borrow.late_fee || 0) + (borrow.additional_fine || 0)) }}</p>
                  </div>
                </div>
              </div>
              <div class="text-right space-y-2">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold" :class="getPaymentStatusClass(borrow.payment_status)">
                  {{ getPaymentStatusLabel(borrow.payment_status) }}
                </span>
                <p v-if="borrow.payment_method && borrow.payment_method !== 'none'" class="text-xs text-gray-500">{{ borrow.payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</p>
              </div>
            </div>

            <div v-if="borrow.is_damaged" class="p-6 bg-red-50 border-b border-red-100 space-y-4">
              <h3 class="text-sm font-black uppercase tracking-wide text-red-900">Laporan Kerusakan</h3>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="space-y-3">
                  <p class="text-sm"><span class="font-bold">Status:</span> {{ getDamageStatusLabel(borrow.damage_status) }}</p>
                  <p class="text-sm"><span class="font-bold">Jenis:</span> {{ getDamageTypeLabel(borrow.damage_type) }}</p>
                  <p class="text-sm text-gray-700">{{ borrow.damage_description }}</p>
                </div>
                <div v-if="borrow.damage_images?.length" class="grid grid-cols-2 gap-3">
                  <img v-for="(image, index) in borrow.damage_images" :key="index" :src="image" class="w-full h-32 object-cover rounded-xl border border-red-200" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 pt-4 border-t border-red-200">
                <input v-model.number="damageForm[borrow.id].additionalFine" type="number" min="0" class="rounded-xl border-gray-300" placeholder="Denda tambahan" />
                <input v-model="damageForm[borrow.id].staffComment" type="text" class="rounded-xl border-gray-300" placeholder="Komentar admin/petugas" />
                <button @click="submitAdditionalFine(borrow.id)" class="px-5 py-3 bg-orange-600 text-white rounded-xl font-bold hover:bg-orange-700">
                  Kirim Tanggapan
                </button>
              </div>
            </div>

            <div v-else class="p-6 bg-green-50 border-b border-green-100">
              <p class="text-sm font-semibold text-green-800">Buku dilaporkan dalam kondisi baik. Tidak ada biaya kerusakan tambahan.</p>
            </div>

            <div class="p-6 flex justify-end">
              <button @click="markAsResolved(borrow.id)" class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
                Tandai Masalah Selesai
              </button>
            </div>
          </div>

          <div v-if="lateBorrows.length === 0" class="bg-white rounded-2xl p-10 text-center text-gray-500 shadow-sm ring-1 ring-gray-200">
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

const getPaymentStatusClass = (status) => {
  if (status === 'confirmed') return 'bg-green-100 text-green-700';
  if (status === 'pending') return 'bg-yellow-100 text-yellow-700';
  return 'bg-gray-100 text-gray-600';
};

function submitAdditionalFine(borrowId) {
  router.post(`/admin/late-returns/${borrowId}/additional-fine`, {
    additional_fine: damageForm[borrowId].additionalFine,
    staff_comment: damageForm[borrowId].staffComment,
  }, {
    onSuccess: () => Swal.fire('Berhasil', 'Tanggapan denda tambahan dikirim ke siswa.', 'success'),
  });
}

function markAsResolved(borrowId) {
  router.post(`/admin/late-returns/${borrowId}/resolve`, {}, {
    onSuccess: () => Swal.fire('Berhasil', 'Masalah keterlambatan selesai.', 'success'),
  });
}
</script>
