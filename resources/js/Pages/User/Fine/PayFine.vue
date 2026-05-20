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
        <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">Pembayaran Denda</h1>
        <p class="text-slate-500 mt-2 text-sm sm:text-base">Selesaikan administrasi keterlambatan atau kerusakan pengembalian buku Anda.</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl shadow-slate-100 border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- Borrow Info Section -->
        <div class="bg-white border-b border-slate-100 p-6 sm:p-8">
          <div class="flex flex-col sm:flex-row gap-6">
            <!-- Book Cover Placeholder Icon -->
            <div class="w-24 h-32 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-500 shadow-md shadow-orange-500/20 flex items-center justify-center text-white shrink-0 mx-auto sm:mx-0">
              <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
              </svg>
            </div>

            <!-- Book Details -->
            <div class="flex-1 text-center sm:text-left flex flex-col justify-center">
              <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 w-max mx-auto sm:mx-0 mb-2">Informasi Peminjaman</span>
              <h2 class="text-xl font-bold text-slate-900 leading-snug mb-1">{{ borrow.book?.title || 'Judul Buku' }}</h2>
              <p class="text-sm text-slate-500 font-medium">{{ borrow.book?.author || 'Penulis' }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-200/60">
            <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl border border-slate-100 text-center">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hari Terlambat</p>
              <p class="text-3xl font-black text-orange-500 mt-1">{{ borrow.late_days }} <span class="text-sm font-normal text-slate-400">hari</span></p>
            </div>
            <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl border border-slate-100 text-center">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Denda Dasar</p>
              <p class="text-3xl font-black text-slate-800 mt-1"><span class="text-sm font-bold text-slate-400">Rp</span> {{ formatNumber(borrow.late_fee) }}</p>
            </div>
          </div>
        </div>

        <!-- Denda Calculation -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/30">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Rincian Kalkulasi</h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center p-4 bg-white rounded-2xl border border-slate-100">
              <span class="text-sm font-medium text-slate-600">Durasi Keterlambatan</span>
              <span class="font-semibold text-slate-800">{{ borrow.late_days }} hari</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-white rounded-2xl border border-slate-100">
              <span class="text-sm font-medium text-slate-600">Tarif / Hari</span>
              <span class="font-semibold text-slate-800">Rp 5.000</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-orange-50/50 rounded-2xl border border-orange-100">
              <span class="text-sm font-semibold text-orange-800">Subtotal Denda Waktu</span>
              <span class="font-bold text-orange-600">Rp {{ formatNumber(borrow.late_fee) }}</span>
            </div>

            <!-- Additional Fine from Damage (if any) - Refactored to Consistent Amber/Orange Theme -->
            <div v-if="borrow.is_damaged && borrow.additional_fine > 0" class="space-y-3 p-4 bg-amber-50/60 rounded-2xl border border-amber-200/70">
              <div class="flex items-start gap-3">
                <div class="p-1.5 bg-amber-100 text-amber-700 rounded-lg shrink-0 mt-0.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-xs font-bold text-amber-800 uppercase tracking-wider">Denda Kondisi Fisik</p>
                  <p class="text-sm font-semibold text-amber-900 mt-0.5">{{ getDamageTypeLabel(borrow.damage_type) }}</p>
                  <p class="text-xs text-amber-700/90 mt-1 italic">"{{ borrow.staff_comment || 'Tidak ada catatan staf' }}"</p>
                </div>
              </div>
              <div class="flex justify-between items-center pt-3 border-t border-amber-200">
                <span class="text-sm font-medium text-amber-800">Biaya Kerusakan</span>
                <span class="font-bold text-amber-700">Rp {{ formatNumber(borrow.additional_fine) }}</span>
              </div>
            </div>

            <!-- Total to Pay -->
            <div class="flex justify-between items-center p-5 bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl text-white shadow-md shadow-orange-500/10">
              <span class="text-sm font-bold uppercase tracking-wider">Total Kewajiban</span>
              <span class="text-2xl font-black">Rp {{ formatNumber(borrow.total_fine) }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="p-6 sm:p-8">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Metode Pembayaran</h3>
          
          <div class="grid grid-cols-1 gap-4">
            <!-- QRIS Option -->
            <button
              @click="selectedMethod = 'qris'"
              :class="[
                'w-full p-5 rounded-2xl border-2 transition-all duration-200 flex items-start gap-4 text-left group relative overflow-hidden',
                selectedMethod === 'qris' 
                  ? 'border-orange-500 bg-orange-50/30 ring-4 ring-orange-500/5' 
                  : 'border-slate-200/80 bg-white hover:border-orange-200 hover:bg-orange-50/10'
              ]"
            >
              <div :class="[
                'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all duration-200',
                selectedMethod === 'qris' ? 'border-orange-500 bg-orange-500' : 'border-slate-300'
              ]">
                <svg v-if="selectedMethod === 'qris'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between gap-2">
                  <h4 class="font-bold text-slate-800 text-base">Instant QRIS</h4>
                  <span class="inline-flex items-center gap-1 text-[10px] font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    Otomatis
                  </span>
                </div>
                <p class="text-sm text-slate-500 mt-1 leading-relaxed">Bayar real-time via aplikasi e-wallet (Gopay, OVO, Dana) atau Mobile Banking Anda.</p>
              </div>
            </button>

            <!-- Cash Option -->
            <button
              @click="selectedMethod = 'cash'"
              :class="[
                'w-full p-5 rounded-2xl border-2 transition-all duration-200 flex items-start gap-4 text-left group relative overflow-hidden',
                selectedMethod === 'cash' 
                  ? 'border-orange-500 bg-orange-50/30 ring-4 ring-orange-500/5' 
                  : 'border-slate-200/80 bg-white hover:border-orange-200 hover:bg-orange-50/10'
              ]"
            >
              <div :class="[
                'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all duration-200',
                selectedMethod === 'cash' ? 'border-orange-500 bg-orange-500' : 'border-slate-300'
              ]">
                <svg v-if="selectedMethod === 'cash'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between gap-2">
                  <h4 class="font-bold text-slate-800 text-base">Tunai di Lokasi</h4>
                  <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    Layanan Kasir
                  </span>
                </div>
                <p class="text-sm text-slate-500 mt-1 leading-relaxed">Lakukan penyelesaian transaksi langsung melalui staf kasir meja sirkulasi perpustakaan.</p>
              </div>
            </button>
          </div>

          <!-- Additional Info Notes -->
          <div class="mt-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="flex gap-3">
              <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>
              <div class="text-xs text-slate-500 leading-relaxed">
                <p class="font-bold text-slate-700 mb-1">Panduan Prosedur:</p>
                <ul class="space-y-1 list-disc list-inside text-slate-600">
                  <li v-if="selectedMethod === 'qris'">Sistem akan menggenerasi kode QR dinamis setelah konfirmasi.</li>
                  <li v-if="selectedMethod === 'qris'">Status denda langsung diperbarui lunas setelah transaksi sukses.</li>
                  <li v-if="selectedMethod === 'cash'">Harap tunjukkan halaman pengajuan ini ke petugas perpustakaan.</li>
                  <li>Pastikan Anda menyimpan resi digital/cetak sebagai validasi akhir arsip.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
        <Link 
          href="/my-loans"
          class="order-2 sm:order-1 flex-1 py-4 px-6 rounded-2xl border border-slate-200 text-slate-600 font-bold text-center bg-white hover:bg-slate-50 hover:text-slate-800 active:scale-[0.99] transition-all duration-150 shadow-sm"
        >
          Batalkan
        </Link>
        <button
          @click="processPayment"
          :disabled="isProcessing"
          class="order-1 sm:order-2 flex-1 py-4 px-6 rounded-2xl bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300 text-white font-bold text-center shadow-md shadow-orange-500/10 active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
        >
          <svg v-if="isProcessing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ selectedMethod === 'qris' ? 'Generate Kode QRIS' : 'Ajukan Pembayaran Tunai' }}
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

function getDamageTypeLabel(type) {
  const labels = {
    lost: 'Buku Hilang',
    wet: 'Kondisi Basah / Lembab',
    torn: 'Halaman Robek',
    stained: 'Terkena Noda Permanen',
    other: 'Kerusakan Lainnya'
  };
  return labels[type] || type;
}

function processPayment() {
  if (!selectedMethod.value) {
    Swal.fire({
      title: 'Peringatan',
      text: 'Silakan pilih metode pembayaran terlebih dahulu.',
      icon: 'warning',
      confirmButtonColor: '#f97316'
    });
    return;
  }

  isProcessing.value = true;

  let confirmText = '';
  if (selectedMethod.value === 'qris') {
    confirmText = `Anda akan melanjutkan ke pembuatan invoice QRIS sebesar Rp ${formatNumber(props.borrow.total_fine)}`;
  } else {
    confirmText = `Konfirmasi pembayaran tunai sebesar Rp ${formatNumber(props.borrow.total_fine)} langsung ke kasir perpustakaan`;
  }

  Swal.fire({
    title: 'Konfirmasi Pembayaran',
    text: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: selectedMethod.value === 'qris' ? 'Lanjut Bayar' : 'Konfirmasi',
    cancelButtonText: 'Kembali'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/borrow/${props.borrow.id}/fine/process`, {
        payment_method: selectedMethod.value
      }, {
        onSuccess: () => {
          isProcessing.value = false;
          if (selectedMethod.value === 'qris') {
            Swal.fire({
              title: 'Berhasil',
              text: 'Silakan pindai QRIS yang tertera untuk menyelesaikan pembayaran.',
              icon: 'success',
              confirmButtonColor: '#f97316'
            });
          } else {
            Swal.fire({
              title: 'Permintaan Dikirim',
              text: 'Silakan menuju meja kasir sirkulasi untuk menyerahkan pembayaran senilai Rp ' + formatNumber(props.borrow.total_fine),
              icon: 'success',
              confirmButtonColor: '#f97316'
            });
          }
        },
        onError: (error) => {
          isProcessing.value = false;
          Swal.fire({
            title: 'Gagal',
            text: 'Terjadi kendala internal saat memproses data denda Anda.',
            icon: 'error',
            confirmButtonColor: '#f97316'
          });
        }
      });
    } else {
      isProcessing.value = false;
    }
  });
}
</script>