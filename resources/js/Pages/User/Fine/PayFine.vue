<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <Link href="/my-loans" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Rak Buku
        </Link>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">💳 Pembayaran Denda Keterlambatan</h1>
        <p class="text-slate-500">Selesaikan pembayaran denda untuk pengembalian buku yang terlambat</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- Borrow Info Section -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 border-b border-orange-200/50 p-8">
          <div class="flex gap-6">
            <!-- Book Cover -->
            <div class="w-24 h-32 rounded-xl overflow-hidden bg-slate-100 border-2 border-white shadow-lg shrink-0">
              <!-- Placeholder for book cover -->
              <div class="w-full h-full bg-gradient-to-br from-orange-200 to-orange-400 flex items-center justify-center text-3xl">
                📚
              </div>
            </div>

            <!-- Book Details -->
            <div class="flex-1">
              <h2 class="text-lg font-bold text-slate-900 mb-1">{{ borrow.book?.title }}</h2>
              <p class="text-sm text-slate-600 mb-4">{{ borrow.book?.author }}</p>
              
              <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                  <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hari Terlambat</p>
                  <p class="text-2xl font-black text-orange-600 mt-1">{{ borrow.late_days }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Denda</p>
                  <p class="text-2xl font-black text-red-600 mt-1">Rp {{ formatNumber(borrow.late_fee) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Denda Calculation -->
        <div class="p-8 border-b border-slate-200/60 bg-slate-50/50">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Detail Perhitungan Denda</h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center p-4 bg-white rounded-xl border border-slate-100">
              <span class="text-sm font-medium text-slate-600">Jumlah hari terlambat</span>
              <span class="font-bold text-slate-900">{{ borrow.late_days }} hari</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-white rounded-xl border border-slate-100">
              <span class="text-sm font-medium text-slate-600">Tarif denda per hari</span>
              <span class="font-bold text-slate-900">Rp 5.000</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-orange-50 rounded-xl border-2 border-orange-200">
              <span class="text-sm font-bold text-orange-900">Total Denda Pembayaran</span>
              <span class="text-2xl font-black text-orange-600">Rp {{ formatNumber(borrow.late_fee) }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="p-8">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6">Pilih Metode Pembayaran</h3>
          
          <div class="space-y-4">
            <!-- QRIS Option -->
            <button
              @click="selectedMethod = 'qris'"
              :class="[
                'w-full p-6 rounded-2xl border-2 transition-all duration-200 flex items-start gap-4 text-left group',
                selectedMethod === 'qris' 
                  ? 'border-orange-500 bg-orange-50' 
                  : 'border-slate-200 bg-white hover:border-orange-200 hover:bg-orange-50/30'
              ]"
            >
              <div :class="[
                'w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all',
                selectedMethod === 'qris'
                  ? 'border-orange-500 bg-orange-500'
                  : 'border-slate-300 group-hover:border-orange-300'
              ]">
                <svg v-if="selectedMethod === 'qris'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-slate-900 text-base mb-1">Pembayaran QRIS</h4>
                <p class="text-sm text-slate-600">Bayar menggunakan QRIS dari aplikasi e-wallet atau mobile banking Anda</p>
                <div class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Tersedia 24/7
                </div>
              </div>
            </button>

            <!-- Cash Option -->
            <button
              @click="selectedMethod = 'cash'"
              :class="[
                'w-full p-6 rounded-2xl border-2 transition-all duration-200 flex items-start gap-4 text-left group',
                selectedMethod === 'cash' 
                  ? 'border-orange-500 bg-orange-50' 
                  : 'border-slate-200 bg-white hover:border-orange-200 hover:bg-orange-50/30'
              ]"
            >
              <div :class="[
                'w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 mt-1 transition-all',
                selectedMethod === 'cash'
                  ? 'border-orange-500 bg-orange-500'
                  : 'border-slate-300 group-hover:border-orange-300'
              ]">
                <svg v-if="selectedMethod === 'cash'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-slate-900 text-base mb-1">Pembayaran Tunai</h4>
                <p class="text-sm text-slate-600">Bayar langsung ke kasir perpustakaan dalam bentuk uang tunai</p>
                <div class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 9V7a1 1 0 011-1h8a1 1 0 011 1v2M5 9c0 1.657-4 3-4 3s4 1.343 4 3m6-6c0 1.657 4 3 4 3s-4 1.343-4 3m0 0v2a1 1 0 001 1h-2a1 1 0 001-1v-2" />
                  </svg>
                  Teller Perpustakaan
                </div>
              </div>
            </button>
          </div>

          <!-- Additional Info -->
          <div class="mt-8 p-4 bg-slate-50 rounded-xl border border-slate-200/60">
            <div class="flex gap-3">
              <svg class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              <div class="text-xs text-slate-600 leading-relaxed">
                <p class="font-semibold text-slate-900 mb-1">Catatan Penting:</p>
                <ul class="space-y-1 list-disc list-inside">
                  <li v-if="selectedMethod === 'qris'">Scan QRIS dengan aplikasi pembayaran Anda</li>
                  <li v-if="selectedMethod === 'qris'">Pembayaran akan diverifikasi secara otomatis</li>
                  <li v-if="selectedMethod === 'cash'">Serahkan uang tunai kepada kasir perpustakaan</li>
                  <li v-if="selectedMethod === 'cash'">Kasir akan konfirmasi pembayaran Anda</li>
                  <li>Simpan bukti pembayaran untuk arsip Anda</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-4">
        <Link 
          href="/my-loans"
          class="flex-1 py-4 px-6 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold text-center hover:bg-slate-50 transition-all"
        >
          Batalkan
        </Link>
        <button
          @click="processPayment"
          :disabled="isProcessing"
          class="flex-1 py-4 px-6 rounded-2xl bg-orange-600 hover:bg-orange-700 disabled:bg-orange-400 text-white font-bold text-center transition-all flex items-center justify-center gap-2"
        >
          <svg v-if="isProcessing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ selectedMethod === 'qris' ? 'Lanjut ke QRIS' : 'Konfirmasi Pembayaran Tunai' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  borrow: {
    type: Object,
    required: true
  }
});

const selectedMethod = ref('qris');
const isProcessing = ref(false);

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
}

function processPayment() {
  if (!selectedMethod.value) {
    Swal.fire({
      title: 'Peringatan',
      text: 'Silakan pilih metode pembayaran terlebih dahulu.',
      icon: 'warning',
      confirmButtonColor: '#ea580c'
    });
    return;
  }

  isProcessing.value = true;

  let confirmText = '';
  if (selectedMethod.value === 'qris') {
    confirmText = `Anda akan melanjutkan ke pembayaran QRIS sebesar Rp ${formatNumber(props.borrow.late_fee)}`;
  } else {
    confirmText = `Anda akan membayar denda tunai sebesar Rp ${formatNumber(props.borrow.late_fee)} ke kasir perpustakaan`;
  }

  Swal.fire({
    title: 'Konfirmasi Pembayaran',
    text: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#6b7280',
    confirmButtonText: selectedMethod.value === 'qris' ? 'Lanjut ke QRIS' : 'Konfirmasi',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/borrow/${props.borrow.id}/fine/process`, {
        payment_method: selectedMethod.value
      }, {
        onSuccess: () => {
          isProcessing.value = false;
          if (selectedMethod.value === 'qris') {
            Swal.fire('Berhasil', 'Silakan lakukan pembayaran melalui QRIS.', 'success');
          } else {
            Swal.fire({
              title: 'Pembayaran Diajukan',
              text: 'Pembayaran tunai Anda telah diajukan. Silakan bayar ke kasir perpustakaan dengan nominal Rp ' + formatNumber(props.borrow.late_fee),
              icon: 'success',
              confirmButtonColor: '#ea580c'
            }).then(() => {
              router.visit('/my-loans');
            });
          }
        },
        onError: (error) => {
          isProcessing.value = false;
          Swal.fire('Gagal', 'Terjadi kesalahan saat memproses pembayaran.', 'error');
        }
      });
    } else {
      isProcessing.value = false;
    }
  });
}
</script>
