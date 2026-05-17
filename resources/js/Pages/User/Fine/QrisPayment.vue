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
  const phone = '6281234567890'; // Ganti dengan nomor WhatsApp perpustakaan
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

      <!-- Countdown Timer -->
      <div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl p-6 mb-6 text-white shadow-lg">
        <div class="text-center">
          <p class="text-sm font-semibold opacity-90 mb-2">⏰ Sisa Waktu Pembayaran</p>
          <div class="text-5xl font-black mb-3">{{ formattedTime }}</div>
          <div class="flex justify-center gap-4 text-sm">
            <div class="bg-white/20 rounded-lg px-4 py-2">
              <span class="block text-2xl font-bold">{{ hours }}</span>
              <span class="text-xs opacity-75">Jam</span>
            </div>
            <div class="bg-white/20 rounded-lg px-4 py-2">
              <span class="block text-2xl font-bold">{{ minutes }}</span>
              <span class="text-xs opacity-75">Menit</span>
            </div>
            <div class="bg-white/20 rounded-lg px-4 py-2">
              <span class="block text-2xl font-bold">{{ seconds }}</span>
              <span class="text-xs opacity-75">Detik</span>
            </div>
          </div>
          <p v-if="isTimeUp" class="mt-3 text-sm font-bold text-yellow-200">
            Waktu habis! Silakan hubungi petugas untuk perpanjangan.
          </p>
        </div>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- QRIS Code Section -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border-b border-blue-200/50 p-8 flex flex-col items-center">
          <div class="mb-4 bg-white p-4 rounded-2xl shadow-inner">
            <img 
              :src="qrisCode" 
              alt="QRIS Code" 
              class="w-56 h-56 object-contain"
            />
          </div>
          
          <div class="text-center">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Scan Kode QR</h3>
            <p class="text-sm text-slate-600 mb-4">Gunakan aplikasi pembayaran untuk scan kode di atas</p>
            
            <!-- Amount -->
            <div class="bg-white rounded-2xl px-6 py-4 inline-block mb-4 shadow-sm border border-slate-100">
              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nominal Pembayaran</p>
              <p class="text-3xl font-black text-orange-600 mt-1">Rp {{ formatNumber(borrow.total_fine) }}</p>
            </div>

            <!-- Supported Apps -->
            <div class="mt-4 flex justify-center gap-2 flex-wrap">
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 GoPay</div>
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 OVO</div>
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 Dana</div>
              <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-full">📱 ShopeePay</div>
            </div>
          </div>
        </div>

        <!-- WhatsApp Contact -->
        <div class="p-8 bg-green-50 border-t border-green-100">
          <div class="text-center">
            <p class="text-sm font-bold text-green-800 mb-3">Butuh bantuan? Hubungi petugas via WhatsApp</p>
            <a 
              :href="whatsappLink"
              target="_blank"
              class="inline-flex items-center gap-3 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-2xl font-bold transition-all shadow-lg shadow-green-500/20"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
              </svg>
              Hubungi Petugas via WhatsApp
            </a>
          </div>
        </div>

        <!-- Book & Details Section -->
        <div class="p-8">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Detail Pembayaran</h3>
          
          <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
              <span class="text-sm font-medium text-slate-600">Buku</span>
              <span class="text-sm font-bold text-slate-900">{{ borrow.book?.title }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
              <span class="text-sm font-medium text-slate-600">Hari Terlambat</span>
              <span class="text-sm font-bold text-slate-900">{{ borrow.late_days }} hari</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-orange-50 rounded-xl border-2 border-orange-200">
              <span class="text-sm font-bold text-orange-900">Total Denda</span>
              <span class="text-xl font-black text-orange-600">Rp {{ formatNumber(borrow.total_fine) }}</span>
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
              <li>Hubungi petugas jika pembayaran sudah dilakukan</li>
              <li>Tunggu petugas mengkonfirmasi pembayaran Anda</li>
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
          @click="router.visit('/my-loans')"
          class="flex-1 py-4 px-6 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-center transition-all"
        >
          Kembali ke Rak Buku
        </button>
      </div>
    </div>
  </div>
</template>
