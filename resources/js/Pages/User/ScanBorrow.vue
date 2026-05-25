<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 px-4 py-4 backdrop-blur sm:px-8">
      <div class="mx-auto flex max-w-6xl items-center justify-between gap-4">
        <Link href="/dashboard" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-orange-600">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7" />
          </svg>
          Dashboard
        </Link>

        <Link href="/my-loans" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-black uppercase tracking-widest text-slate-600 hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600">
          Rak Buku
        </Link>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-8 sm:py-10">
      <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="min-h-[520px] overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 shadow-sm">
          <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <div>
              <p class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-300">Pemindai Barcode Buku</p>
              <h1 class="mt-1 text-lg font-black text-white">Arahkan kamera ke barcode</h1>
            </div>
            <span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white">
              {{ scannerStatus }}
            </span>
          </div>

          <div class="relative flex min-h-[460px] items-center justify-center p-4">
            <div id="barcode-reader" class="h-full min-h-[420px] w-full overflow-hidden rounded-xl bg-black"></div>

            <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-10">
              <div class="h-48 w-full max-w-xl rounded-2xl border-2 border-orange-400/90 shadow-[0_0_0_999px_rgba(2,6,23,0.32)]"></div>
            </div>
          </div>
        </div>

        <aside class="space-y-4">
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Kode Terbaca</p>
            <div class="mt-3 min-h-12 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-mono text-sm font-bold text-slate-800 break-all">
              {{ scannedCode || 'Belum ada barcode' }}
            </div>

            <div class="mt-4 flex gap-2">
              <input
                v-model="manualCode"
                type="text"
                placeholder="Ketik kode manual"
                class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 font-mono text-xs font-bold text-slate-700 outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                @keyup.enter="lookupManualCode"
              >
              <button
                type="button"
                class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-black uppercase tracking-widest text-white hover:bg-orange-600"
                @click="lookupManualCode"
              >
                Cek
              </button>
            </div>

            <p v-if="message" class="mt-3 text-xs font-semibold" :class="isValid ? 'text-emerald-600' : 'text-rose-600'">
              {{ message }}
            </p>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-400">Buku Terdeteksi</p>

            <div v-if="book" class="mt-4 flex gap-4">
              <div class="h-28 w-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                <img :src="bookCover" :alt="book.title" class="h-full w-full object-cover" @error="coverFailed = true">
              </div>
              <div class="min-w-0">
                <h2 class="line-clamp-2 text-sm font-black leading-snug text-slate-900">{{ book.title }}</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ book.author || 'Penulis tidak tersedia' }}</p>
                <p class="mt-3 text-[11px] font-bold text-slate-400">Stok tersedia: {{ book.available_quantity }}</p>
              </div>
            </div>

            <div v-else class="mt-4 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-xs font-semibold text-slate-400">
              Data buku akan muncul setelah barcode valid.
            </div>
          </div>

          <button
            type="button"
            :disabled="!isValid || isBorrowing"
            @click="borrowScannedBook"
            class="w-full rounded-xl bg-orange-600 px-5 py-4 text-sm font-black uppercase tracking-widest text-white shadow-sm shadow-orange-500/20 transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none"
          >
            {{ isBorrowing ? 'Memproses...' : 'Pinjam Buku' }}
          </button>

          <button
            type="button"
            @click="restartScanner"
            class="w-full rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-black uppercase tracking-widest text-slate-600 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600"
          >
            Scan Ulang
          </button>
        </aside>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

const scanner = ref(null);
const scannedCode = ref('');
const manualCode = ref('');
const lastLookupCode = ref('');
const scannerStatus = ref('Siap');
const message = ref('');
const book = ref(null);
const isValid = ref(false);
const isBorrowing = ref(false);
const coverFailed = ref(false);

const bookCover = computed(() => {
  if (!book.value || coverFailed.value || !book.value.cover_image) {
    const title = book.value?.title || 'Buku';
    return `https://placehold.co/240x336/f1f5f9/64748b?text=${encodeURIComponent(title)}`;
  }

  return book.value.cover_image;
});

onMounted(() => {
  startScanner();
});

onBeforeUnmount(() => {
  stopScanner();
});

async function startScanner() {
  if (scanner.value) return;

  scannerStatus.value = 'Membuka Kamera';
  scanner.value = new Html5Qrcode('barcode-reader');

  try {
    const cameras = await Html5Qrcode.getCameras();
    
    if (!cameras || cameras.length === 0) {
      throw new Error('Kamera tidak ditemukan');
    }

    // Cari kamera belakang
    const backCamera = cameras.find(cam => 
      cam.id.toLowerCase().includes('back') || 
      cam.label.toLowerCase().includes('belakang')
    ) || cameras[cameras.length - 1];

    const config = {
      fps: 30, // Maksimal fps
      qrbox: { width: 300, height: 200 }, // Area scan lebih besar
      aspectRatio: 1.333,
      disableFlip: true,
      // Hapus formatsToSupport - biarkan auto-detect
      experimentalFeatures: {
        useBarCodeDetectorIfSupported: true // Gunakan BarcodeDetector API browser kalau support
      }
    };

    await scanner.value.start(
      { deviceId: backCamera.id }, // Paksa pakai kamera belakang
      config,
      handleScanSuccess,
      (errorMessage) => {
        // Log error untuk debugging
        console.log('Scan error:', errorMessage);
      }
    );
    
    scannerStatus.value = 'Scanning - Arahkan lebih dekat';
  } catch (error) {
    scannerStatus.value = 'Gagal';
    console.error('Scanner error:', error);
    message.value = 'Kamera gagal dibuka. Coba: refresh halaman, cek izin kamera, atau gunakan Chrome.';
  }
}

function handleScanSuccess(decodedText) {
  console.log('✅ SCAN BERHASIL:', decodedText); // Debug
  const code = String(decodedText || '').trim();
  if (!code || code === lastLookupCode.value) {
    console.log('⏭️ Skip: sama dengan kode sebelumnya');
    return;
  }

  // Beri feedback getar kalau di mobile
  if (navigator.vibrate) {
    navigator.vibrate(100);
  }

  scannedCode.value = code;
  manualCode.value = code;
  lastLookupCode.value = code;
  
  // Stop scan sementara untuk cegah duplicate
  scannerStatus.value = 'Terbaca!';
  
  lookupBarcode(code);
}

function scannerBox(viewfinderWidth, viewfinderHeight) {
  const width = Math.floor(viewfinderWidth * 0.82);
  const height = Math.min(220, Math.floor(viewfinderHeight * 0.45));

  return {
    width: Math.min(viewfinderWidth - 24, Math.max(240, width)),
    height: Math.min(viewfinderHeight - 24, Math.max(120, height)),
  };
}

function lookupManualCode() {
  const code = manualCode.value.trim();
  if (!code) {
    message.value = 'Masukkan kode barcode dulu.';
    return;
  }

  scannedCode.value = code;
  lastLookupCode.value = code;
  lookupBarcode(code);
}

async function lookupBarcode(code) {
  scannerStatus.value = 'Mengecek';
  isValid.value = false;
  book.value = null;
  coverFailed.value = false;
  message.value = 'Mengecek barcode...';

  try {
    const response = await axios.post(route('scan-borrow.lookup'), {
      barcode_code: code,
    });

    book.value = response.data.book;
    isValid.value = Boolean(response.data.valid);
    message.value = 'Barcode valid. Buku siap diajukan.';
    scannerStatus.value = 'Valid';
  } catch (error) {
    message.value = error.response?.data?.message || 'Barcode tidak valid.';
    scannerStatus.value = 'Tidak Valid';
  }
}

async function borrowScannedBook() {
  if (!isValid.value || !scannedCode.value) return;

  isBorrowing.value = true;
  message.value = 'Mengirim pengajuan peminjaman...';

  try {
    const response = await axios.post(route('scan-borrow.store'), {
      barcode_code: scannedCode.value,
    });

    await stopScanner();
    router.visit(response.data.redirect || route('borrow.my-loans'));
  } catch (error) {
    message.value = error.response?.data?.message || 'Gagal mengajukan peminjaman.';
    isBorrowing.value = false;
  }
}

async function restartScanner() {
  scannedCode.value = '';
  manualCode.value = '';
  lastLookupCode.value = '';
  message.value = '';
  book.value = null;
  isValid.value = false;
  coverFailed.value = false;
  await stopScanner();
  await startScanner();
}
</script>

<style scoped>
#barcode-reader :deep(video) {
  min-height: 420px;
  object-fit: cover;
}
</style>
