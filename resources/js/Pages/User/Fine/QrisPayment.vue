<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  borrow: Object,
  qrisCode: String
});

const timeLeft = ref(7200); // 2 jam dalam detik
let timerInterval = null;

const hours = computed(() => Math.floor(timeLeft.value / 3600));
const minutes = computed(() => Math.floor((timeLeft.value % 3600) / 60));
const seconds = computed(() => timeLeft.value % 60);

const formattedTime = computed(() => {
  return `${String(hours.value).padStart(2, '0')}:${String(minutes.value).padStart(2, '0')}:${String(seconds.value).padStart(2, '0')}`;
});

const isTimeUp = computed(() => timeLeft.value <= 0);

const whatsappLink = computed(() => {
  const phone = '6282113149076'; // Ganti dengan nomor WhatsApp perpustakaan
  const message = `Halo petugas, saya ingin konfirmasi pembayaran QRIS untuk buku "${props.borrow.book?.title}".\n\nID Peminjaman: #${props.borrow.id}\nNominal: Rp ${formatNumber(props.borrow.total_fine || props.borrow.late_fee)}`;
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
});

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

function startTimer() {
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--;
    } else {
      clearInterval(timerInterval);
    }
  }, 1000);
}

function goBack() {
  window.history.back();
}

onMounted(() => {
  if (props.borrow.payment_due_date) {
    const due = new Date(props.borrow.payment_due_date).getTime();
    timeLeft.value = Math.max(0, Math.floor((due - Date.now()) / 1000));
  }
  startTimer();
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100/50 py-12 px-4 antialiased">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <button @click="goBack" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-500 transition-colors duration-200 mb-4 group">
          <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali
        </button>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">Pembayaran QRIS</h1>
        <p class="text-slate-500 mt-2 text-sm sm:text-base">Selesaikan transaksi digital Anda dengan memindai kode QRIS di bawah ini.</p>
      </div>

      <!-- Countdown Timer (Premium Dark Dashboard Concept) -->
      <div class="bg-white rounded-3xl p-6 sm:p-8 mb-6 text-white shadow-xl shadow-slate-900/10 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-500/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6">
          <div class="text-center sm:text-left">
            <p class="text-xs font-bold uppercase tracking-widest text-orange-500 mb-1">Batas Waktu Transaksi</p>
            <h3 class="text-sm text-slate-900">Segera bayar sebelum invoice kedaluwarsa</h3>
          </div>
          
          <div class="flex items-center gap-2">
            <!-- Hours -->
            <div class="bg-orange-500 backdrop-blur-sm border border-orange-500 rounded-2xl p-3 min-w-[64px] text-center">
              <span class="block text-2xl font-black text-white leading-tight">{{ String(hours).padStart(2, '0') }}</span>
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-100">Jam</span>
            </div>
            <span class="text-xl font-bold text-slate-600 animate-pulse">:</span>
            <!-- Minutes -->
            <div class="bg-orange-500 backdrop-blur-sm border border-orange-500 rounded-2xl p-3 min-w-[64px] text-center">
              <span class="block text-2xl font-black text-white leading-tight">{{ String(minutes).padStart(2, '0') }}</span>
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-100">Menit</span>
            </div>
            <span class="text-xl font-bold text-slate-600 animate-pulse">:</span>
            <!-- Seconds -->
            <div class="bg-orange-500 backdrop-blur-sm border border-orange-500 rounded-2xl p-3 min-w-[64px] text-center">
              <span class="block text-2xl font-black text-white leading-tight">{{ String(seconds).padStart(2, '0') }}</span>
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-100">Detik</span>
            </div>
          </div>
        </div>
        
        <!-- Time Up Alert -->
        <div v-if="isTimeUp" class="mt-4 pt-4 border-t border-slate-800 text-center text-sm font-bold text-red-400">
          ⚠️ Sesi pembayaran telah berakhir. Silakan minta kode QR baru kepada petugas.
        </div>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl shadow-slate-100 border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- QRIS Code Section -->
        <div class="bg-gradient-to-b from-slate-50/80 to-white border-b border-slate-100 p-8 flex flex-col items-center">
          <!-- QR Wrapper -->
          <div class="mb-6 bg-white p-5 rounded-3xl shadow-md border border-slate-100/80 transition-transform duration-300 hover:scale-[1.02]">
            <img 
              :src="qrisCode" 
              alt="QRIS Code Perpustakaan" 
              class="w-52 h-52 sm:w-60 sm:h-60 object-contain mx-auto"
            />
          </div>
          
          <div class="text-center max-w-sm">
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Pindai QRIS Dinamis</h3>
            <p class="text-sm text-slate-500 mt-1">Kompatibel dengan seluruh aplikasi e-wallet dan perbankan Indonesia.</p>
            
            <!-- Amount Card -->
            <div class="bg-orange-50/60 border border-orange-100/80 rounded-2xl px-8 py-4 inline-block mt-5 shadow-sm">
              <p class="text-[11px] font-bold text-orange-800 uppercase tracking-widest">Total Tagihan Denda</p>
              <p class="text-3xl font-black text-orange-500 mt-1">Rp {{ formatNumber(borrow.total_fine) }}</p>
            </div>

            <!-- Supported Apps Branding (Minimalist Modern) -->
            <div class="mt-6 flex justify-center items-center gap-3 flex-wrap opacity-60 grayscale hover:opacity-100 hover:grayscale-0 transition-all duration-300">
              <span class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200/60 px-3 py-1.5 rounded-xl">GOPAY</span>
              <span class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200/60 px-3 py-1.5 rounded-xl">OVO</span>
              <span class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200/60 px-3 py-1.5 rounded-xl">DANA</span>
              <span class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200/60 px-3 py-1.5 rounded-xl">SHOPEEPAY</span>
              <span class="text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200/60 px-3 py-1.5 rounded-xl">LINKAJA</span>
            </div>
          </div>
        </div>

        <!-- Meta Bill & Instruction Details -->
        <div class="p-6 sm:p-8 space-y-6">
          <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Detail Verifikasi</h3>
            <div class="space-y-2.5">
              <div class="flex justify-between items-center p-4 bg-slate-50/60 border border-slate-100 rounded-2xl text-sm">
                <span class="font-medium text-slate-500">Judul Buku Peminjaman</span>
                <span class="font-bold text-slate-800 text-right max-w-[240px] truncate">{{ borrow.book?.title || 'Judul Buku' }}</span>
              </div>
              <div class="flex justify-between items-center p-4 bg-slate-50/60 border border-slate-100 rounded-2xl text-sm">
                <span class="font-medium text-slate-500">Total Akumulasi Keterlambatan</span>
                <span class="font-bold text-slate-800">{{ borrow.late_days }} hari</span>
              </div>
            </div>
          </div>

          <!-- Instructions Steps -->
          <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Langkah Penyelesaian</h3>
            <div class="bg-slate-50 border border-slate-200/50 rounded-2xl p-5">
              <ul class="space-y-3.5 text-sm text-slate-600">
                <li class="flex gap-3">
                  <span class="w-5 h-5 rounded-full bg-orange-100 text-orange-600 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                  <span>Buka e-wallet atau aplikasi mobile banking pilihan Anda.</span>
                </li>
                <li class="flex gap-3">
                  <span class="w-5 h-5 rounded-full bg-orange-100 text-orange-600 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                  <span>Pilih opsi pembayaran **Scan** atau **Pindai QR**.</span>
                </li>
                <li class="flex gap-3">
                  <span class="w-5 h-5 rounded-full bg-orange-100 text-orange-600 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                  <span>Arahkan pemindai kamera tepat pada barcode QRIS di atas.</span>
                </li>
                <li class="flex gap-3">
                  <span class="w-5 h-5 rounded-full bg-orange-100 text-orange-600 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">4</span>
                  <span>Periksa kembali nama merchant perpustakaan dan nominal tagihan denda Anda, lalu masukkan PIN pembayaran.</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- WhatsApp Soft Help Section -->
        <div class="px-6 py-5 sm:px-8 bg-white border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-center sm:text-left">
            <h4 class="text-sm font-bold text-slate-900">Kendala pada pemindaian barcode?</h4>
            <p class="text-xs text-slate-800 mt-0.5">Kirim bukti atau hubungi langsung meja administrasi perpustakaan.</p>
          </div>
          <a 
            :href="whatsappLink"
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all duration-150 shadow-md shadow-emerald-600/10 active:scale-[0.98]"
          >
            Hubungi via WhatsApp
          </a>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
        <button
          @click="goBack"
          class="order-2 sm:order-1 flex-1 py-4 px-6 rounded-2xl border border-slate-200 text-slate-600 font-bold text-center bg-white hover:bg-slate-50 hover:text-slate-800 active:scale-[0.99] transition-all duration-150 shadow-sm"
        >
          Kembali
        </button>
        <button
          @click="router.visit('/my-loans')"
          class="order-1 sm:order-2 flex-1 py-4 px-6 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-center shadow-md shadow-orange-500/10 active:scale-[0.99] transition-all duration-150"
        >
          Selesai & Ke Rak Buku
        </button>
      </div>
    </div>
  </div>
</template>