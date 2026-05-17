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
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">⏳ Menunggu Pembayaran</h1>
        <p class="text-slate-500">Selesaikan pembayaran denda sebelum waktu habis</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden mb-8">
        <!-- Countdown Timer -->
        <div class="bg-gradient-to-r from-red-500 to-orange-500 p-8 text-white">
          <div class="text-center">
            <p class="text-sm font-semibold opacity-90 mb-2">Sisa Waktu Pembayaran</p>
            <div class="text-5xl font-black mb-4">{{ formattedTime }}</div>
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
          </div>
        </div>

        <!-- Payment Info -->
        <div class="p-8 space-y-6">
          <div class="bg-orange-50 rounded-2xl p-6 border border-orange-200">
            <h3 class="text-lg font-bold text-orange-900 mb-4">Detail Pembayaran</h3>
            <div class="space-y-3">
              <div class="flex justify-between py-2 border-b border-orange-100">
                <span class="text-sm text-slate-600">Buku</span>
                <span class="text-sm font-bold text-slate-900">{{ borrow.book?.title }}</span>
              </div>
              <div class="flex justify-between py-2 border-b border-orange-100">
                <span class="text-sm text-slate-600">Metode Pembayaran</span>
                <span class="text-sm font-bold text-slate-900">
                  {{ borrow.payment_method === 'qris' ? '📱 QRIS' : '💵 Tunai' }}
                </span>
              </div>
              <div class="flex justify-between py-2 border-b border-orange-100">
                <span class="text-sm text-slate-600">Total Denda</span>
                <span class="text-lg font-black text-red-600">Rp {{ formatNumber(borrow.total_fine || borrow.late_fee) }}</span>
              </div>
              <div class="flex justify-between py-2">
                <span class="text-sm text-slate-600">Status</span>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">
                  ⏳ Menunggu Konfirmasi
                </span>
              </div>
            </div>
          </div>

          <!-- WhatsApp Contact Button -->
          <div class="text-center">
            <a 
              :href="whatsappLink"
              target="_blank"
              class="inline-flex items-center gap-3 px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-2xl font-bold transition-all shadow-lg shadow-green-500/20"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
              </svg>
              Hubungi Petugas via WhatsApp
            </a>
            <p class="text-xs text-slate-500 mt-3">Klik untuk menghubungi petugas perpustakaan jika ada kendala</p>
          </div>
        </div>
      </div>

      <!-- Auto Refresh Info -->
      <div class="text-center text-xs text-slate-400">
        Halaman ini akan otomatis refresh untuk mengecek status pembayaran
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

const timeLeft = ref(7200); // 2 jam dalam detik
let timerInterval = null;

const hours = computed(() => Math.floor(timeLeft.value / 3600));
const minutes = computed(() => Math.floor((timeLeft.value % 3600) / 60));
const seconds = computed(() => timeLeft.value % 60);
const formattedTime = computed(() => {
  return `${String(hours.value).padStart(2, '0')}:${String(minutes.value).padStart(2, '0')}:${String(seconds.value).padStart(2, '0')}`;
});

const whatsappLink = computed(() => {
  const phone = '6281234567890'; // Ganti dengan nomor WhatsApp perpustakaan
  const message = `Halo petugas, saya ingin mengkonfirmasi pembayaran denda untuk buku "${props.borrow.book?.title}". ID Peminjaman: #${props.borrow.id}`;
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

// Auto refresh setiap 30 detik
let refreshInterval = null;
onMounted(() => {
  if (props.borrow.payment_due_date) {
    const due = new Date(props.borrow.payment_due_date).getTime();
    timeLeft.value = Math.max(0, Math.floor((due - Date.now()) / 1000));
  }
  startTimer();
  refreshInterval = setInterval(() => {
    router.reload();
  }, 30000);
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
  if (refreshInterval) clearInterval(refreshInterval);
});
</script>
