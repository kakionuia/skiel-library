<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100/50 py-12 px-4 antialiased">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <Link href="/my-loans" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-500 hover:text-orange-600 transition-colors duration-200 mb-4 group">
          <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Rak Buku
        </Link>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">Menunggu Pembayaran</h1>
        <p class="text-slate-500 mt-2 text-sm sm:text-base">Selesaikan administrasi denda Anda sebelum batas masa penangguhan kedaluwarsa.</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl shadow-slate-100 border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- Premium Countdown Timer Display -->
        <div class="bg-orange-500 p-8 text-white relative overflow-hidden">

          <div class="text-center relative z-10">
            <p class="text-xs font-bold text-orange-100 uppercase tracking-widest mb-3">Sisa Waktu Penangguhan</p>
            <div class="text-5xl sm:text-6xl font-black tracking-tight font-mono text-white mb-6 tabular-nums drop-shadow-sm">
              {{ formattedTime }}
            </div>
            
            <!-- Segmented Time Units -->
            <div class="flex justify-center gap-3 max-w-xs mx-auto">
              <div class="flex-1 bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/10">
                <span class="block text-xl font-bold font-mono text-white tabular-nums">{{ String(hours).padStart(2, '0') }}</span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-100">Jam</span>
              </div>
              <div class="flex-1 bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/10">
                <span class="block text-xl font-bold font-mono text-white tabular-nums">{{ String(minutes).padStart(2, '0') }}</span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-100">Menit</span>
              </div>
              <div class="flex-1 bg-white/5 backdrop-blur-md rounded-xl p-2.5 border border-white/10">
                <span class="block text-xl font-bold font-mono text-white tabular-nums">{{ String(seconds).padStart(2, '0') }}</span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-100">Detik</span>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
          <div class="bg-slate-50 rounded-2xl p-5 sm:p-6 border border-slate-200/50">
            <div class="flex items-center gap-2 pb-4 border-b border-slate-200">
              <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
              <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detail Invoice Retribusi</h3>
            </div>
            
            <div class="divide-y divide-slate-200/60 text-sm">
              <!-- Book Title -->
              <div class="flex flex-col sm:flex-row sm:justify-between py-3.5 gap-1">
                <span class="text-slate-500 font-medium">Judul Koleksi Buku</span>
                <span class="font-semibold text-slate-800 sm:text-right max-w-sm">{{ borrow.book?.title || 'Judul Buku' }}</span>
              </div>
              
              <!-- Payment Method -->
              <div class="flex justify-between py-3.5 items-center">
                <span class="text-slate-500 font-medium">Kanal Pembayaran</span>
                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-white px-3 py-1 rounded-xl border border-slate-200 shadow-sm text-xs">
                  {{ borrow.payment_method === 'qris' ? 'QRIS Auto-Clearing' : 'Tunai di Loket' }}
                </span>
              </div>
              
              <!-- Total Fine Amount -->
              <div class="flex justify-between py-4 items-center">
                <span class="text-slate-500 font-medium">Akumulasi Total Denda</span>
                <span class="text-xl font-black text-orange-500 tracking-tight">
                  <span class="text-xs font-bold text-orange-400">Rp</span> {{ formatNumber(borrow.total_fine || borrow.late_fee) }}
                </span>
              </div>
              
              <!-- Verification Status -->
              <div class="flex justify-between py-3.5 items-center pt-3">
                <span class="text-slate-500 font-medium">Status Verifikasi</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-bold">
                  <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                  Menunggu Konfirmasi Pustakawan
                </span>
              </div>
            </div>
          </div>

          <!-- Technical Support / WhatsApp Integration -->
          <div class="pt-2 text-center">
            <a 
              :href="whatsappLink"
              target="_blank"
              class="inline-flex items-center justify-center gap-2.5 w-full sm:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-2xl active:scale-[0.99] transition-all duration-150 shadow-md shadow-emerald-600/10"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
              </svg>
              Hubungi Petugas via WhatsApp
            </a>
            <p class="text-xs text-slate-400 mt-3 leading-relaxed">Apabila Anda mengalami kendala pada proses transfer dana atau validasi nota denda, silakan ajukan laporan tiket ke staf administrasi kami.</p>
          </div>
        </div>
      </div>

      <!-- Live Auto Refresh Telemetry Info -->
      <div class="flex items-center justify-center gap-2 text-xs text-slate-400 font-medium bg-slate-200/40 w-max mx-auto px-4 py-2 rounded-full border border-slate-200/20">
        <span class="flex h-2 w-2 relative">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
        </span>
        Halaman melakukan sinkronisasi berkala secara otomatis
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  borrow: { type: Object, required: true }
});

const timeLeft = ref(7200); // Default fallback 2 jam
let timerInterval = null;

const hours = computed(() => Math.floor(timeLeft.value / 3600));
const minutes = computed(() => Math.floor((timeLeft.value % 3600) / 60));
const seconds = computed(() => timeLeft.value % 60);

const formattedTime = computed(() => {
  return `${String(hours.value).padStart(2, '0')}:${String(minutes.value).padStart(2, '0')}:${String(seconds.value).padStart(2, '0')}`;
});

const whatsappLink = computed(() => {
  const phone = '6281234567890'; // Menggunakan nomor dummy perpustakaan resmi Anda
  const message = `Halo rekan Petugas Perpustakaan, saya ingin melakukan konfirmasi pelunasan tagihan denda keterlambatan untuk koleksi buku "${props.borrow.book?.title || 'Koleksi Buku'}".\n\nDetail Transaksi:\n- ID Peminjaman: #${props.borrow.id}\n- Kanal Bayar: ${props.borrow.payment_method === 'qris' ? 'QRIS' : 'Loket Tunai'}`;
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
});

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
}

function startTimer() {
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--;
    } else {
      clearInterval(timerInterval);
      router.visit('/my-loans');
    }
  }, 1000);
}

let refreshInterval = null;

onMounted(() => {
  if (props.borrow.payment_due_date) {
    const due = new Date(props.borrow.payment_due_date).getTime();
    timeLeft.value = Math.max(0, Math.floor((due - Date.now()) / 1000));
  }
  startTimer();
  
  // Sinkronisasi status mutasi mutakhir secara senyap (Inertia Reload) setiap 30 detik
  refreshInterval = setInterval(() => {
    router.reload({ preserveScroll: true });
  }, 30000);
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
  if (refreshInterval) clearInterval(refreshInterval);
});


</script>