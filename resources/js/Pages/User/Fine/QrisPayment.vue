<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  borrow: Object,
  qrisCode: String
});

const isVerifying = ref(false);

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function verifyPayment() {
  isVerifying.value = true;
  // Simulate verification (in real implementation, this would call backend API)
  setTimeout(() => {
    Swal.fire({
      title: 'Pembayaran Berhasil!',
      text: 'Pembayaran QRIS Anda telah dikonfirmasi. Terima kasih!',
      icon: 'success',
      confirmButtonColor: '#ea580c'
    }).then(() => {
      router.visit('/my-loans');
    });
  }, 2000);
}

function goBack() {
  window.history.back();
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <button @click="goBack" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali
        </button>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">📱 Pembayaran QRIS</h1>
        <p class="text-slate-500">Scan kode QR di bawah dengan aplikasi pembayaran Anda</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- QRIS Code Section -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border-b border-blue-200/50 p-8 flex flex-col items-center">
          <div class="mb-4">
            <img :src="qrisCode" alt="QRIS Code" class="w-48 h-48 rounded-xl border-4 border-white shadow-lg">
          </div>
          
          <div class="text-center">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Scan Kode QR</h3>
            <p class="text-sm text-slate-600 mb-4">Gunakan aplikasi pembayaran untuk scan kode di atas</p>
            
            <!-- Amount -->
            <div class="bg-white rounded-2xl px-6 py-4 inline-block mb-4">
              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nominal Pembayaran</p>
              <p class="text-3xl font-black text-orange-600 mt-1">Rp {{ formatNumber(borrow.late_fee) }}</p>
            </div>

            <!-- Supported Apps -->
            <div class="mt-4 flex justify-center gap-2">
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 GCash</div>
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 ShopeePay</div>
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 OVO</div>
            </div>
          </div>
        </div>

        <!-- Book & Details Section -->
        <div class="p-8">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Detail Pembayaran</h3>
          
          <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
              <span class="text-sm font-medium text-slate-600">Buku</span>
              <span class="text-sm font-bold text-slate-900">{{ borrow.book.title }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
              <span class="text-sm font-medium text-slate-600">Hari Terlambat</span>
              <span class="text-sm font-bold text-slate-900">{{ borrow.late_days }} hari</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-orange-50 rounded-xl border-2 border-orange-200">
              <span class="text-sm font-bold text-orange-900">Total Denda</span>
              <span class="text-xl font-black text-orange-600">Rp {{ formatNumber(borrow.late_fee) }}</span>
            </div>
          </div>

          <!-- Instructions -->
          <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6">
            <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              Langkah Pembayaran:
            </h4>
            <ol class="space-y-2 text-sm text-blue-800 ml-6 list-decimal">
              <li>Buka aplikasi pembayaran favorit Anda</li>
              <li>Pilih menu "Scan QRIS" atau "Bayar"</li>
              <li>Arahkan kamera ke kode QR di atas</li>
              <li>Verifikasi nominal dan lakukan pembayaran</li>
              <li>Setelah berhasil, klik tombol "Verifikasi Pembayaran" di bawah</li>
            </ol>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-4">
        <button
          @click="goBack"
          class="flex-1 py-4 px-6 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold text-center hover:bg-slate-50 transition-all"
        >
          Batal
        </button>
        <button
          @click="verifyPayment"
          :disabled="isVerifying"
          class="flex-1 py-4 px-6 rounded-2xl bg-orange-600 hover:bg-orange-700 disabled:bg-orange-400 text-white font-bold text-center transition-all flex items-center justify-center gap-2"
        >
          <svg v-if="isVerifying" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ isVerifying ? 'Memverifikasi...' : 'Verifikasi Pembayaran ✓' }}
        </button>
      </div>

      <!-- Support -->
      <div class="mt-8 text-center text-sm text-slate-600">
        <p>Ada masalah dengan pembayaran?</p>
        <Link href="/contact-support" class="text-orange-600 hover:text-orange-700 font-semibold">Hubungi dukungan kami</Link>
      </div>
    </div>
  </div>
</template>
