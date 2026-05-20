<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans antialiased text-slate-800">
    <!-- Sidebar Component -->
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/40" />

    <!-- Main Content Area -->
    <main class="flex-1 ml-80 min-h-screen pb-16">
      
      <!-- Header -->
      <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div class="flex items-center gap-4">
            <Link href="/petugas/late-returns" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h1 class="text-2xl font-bold tracking-tight text-slate-900">Detail Keterlambatan</h1>
              <p class="text-slate-400 text-sm mt-0.5">Informasi komprehensif riwayat pelanggaran dan denda anggota</p>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Container Content -->
      <div class="p-10 max-w-[1400px] mx-auto space-y-8">
        
        <!-- User Info Card Profile -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
          <div class="flex items-start gap-6">
            <div class="w-16 h-16 rounded-2xl bg-orange-50 text-orange-600 border border-orange-100/40 flex items-center justify-center font-extrabold text-xl shadow-sm">
              {{ member.name?.charAt(0)?.toUpperCase() }}
            </div>
            <div class="flex-1">
              <div class="flex items-baseline justify-between">
                <div>
                  <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ member.name }}</h2>
                  <p class="text-xs font-medium text-slate-400 mt-0.5">{{ member.email }}</p>
                </div>
              </div>
              
              <!-- Metrics Specs Grid -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6 pt-6 border-t border-slate-100">
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nomor NISN</p>
                  <p class="text-sm font-semibold text-slate-700 font-mono mt-1">{{ member.nisn || '-' }}</p>
                </div>
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas Akademik</p>
                  <p class="text-sm font-semibold text-slate-700 mt-1">{{ member.kelas || '-' }}</p>
                </div>
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akumulasi Denda</p>
                  <p class="text-base font-bold text-slate-900 font-mono mt-1">Rp {{ formatNumber(totalFine) }}</p>
                </div>
                <div>
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Keterlambatan</p>
                  <p class="text-base font-bold text-orange-500 font-mono mt-1">{{ totalLateDays }} Hari</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Late Borrows List Section -->
        <div class="space-y-6">
          <div v-for="borrow in lateBorrows" :key="borrow.id" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            
            <!-- Individual Borrow Base Info -->
            <div class="p-8 border-b border-slate-100">
              <div class="flex items-start justify-between gap-4">
                <div class="flex gap-4">
                  <div class="w-12 h-16 rounded-lg bg-slate-50 flex items-center justify-center border border-slate-200/60 shadow-sm text-slate-400 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                  </div>
                  <div>
                    <h3 class="text-base font-bold text-slate-900 leading-snug">{{ borrow.book?.title }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Penulis: {{ borrow.book?.author }}</p>
                    
                    <!-- Sub Timeline Specifications -->
                    <div class="flex gap-6 mt-4">
                      <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Jatuh Tempo</p>
                        <p class="text-xs font-semibold text-slate-700 font-mono mt-0.5">{{ formatDate(borrow.due_date) }}</p>
                      </div>
                      <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Masa Terlambat</p>
                        <p class="text-xs font-bold text-orange-500 font-mono mt-0.5">{{ borrow.late_days }} Hari</p>
                      </div>
                      <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Denda Pokok</p>
                        <p class="text-xs font-semibold text-slate-800 font-mono mt-0.5">Rp {{ formatNumber(borrow.late_fee) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Right Status Header -->
                <div class="text-right flex flex-col items-end gap-1.5">
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 bg-slate-50 border border-slate-100 rounded-lg text-slate-600">
                    <span class="w-1.5 h-1.5 rounded-full" :class="borrow.payment_status === 'confirmed' ? 'bg-slate-600' : 'bg-orange-500'"></span>
                    {{ getPaymentStatusLabel(borrow.payment_status) }}
                  </span>
                  <p v-if="borrow.payment_method && borrow.payment_method !== 'none'" class="text-xs font-medium text-slate-400 font-sans mt-0.5">
                    Metode: {{ borrow.payment_method === 'qris' ? 'QRIS Digital' : 'Tunai / Kasir' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Damage Report Section (Clean Slate Styled Accent) -->
            <div v-if="borrow.is_damaged" class="p-8 bg-slate-50/50 border-b border-slate-100">
              <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Verifikasi Laporan Fisik Rusak / Hilang
              </h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                  <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Klasifikasi Kerusakan</p>
                    <span class="inline-block text-xs font-semibold bg-slate-200/60 text-slate-700 px-2.5 py-1 rounded-md">
                      {{ getDamageTypeLabel(borrow.damage_type) }}
                    </span>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Catatan Deskripsi Siswa</p>
                    <p class="text-sm text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-100 shadow-inner">{{ borrow.damage_description || 'Tidak ada deskripsi tambahan.' }}</p>
                  </div>
                </div>
                
                <div v-if="borrow.damage_images?.length" class="space-y-2">
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Lampiran Bukti Foto</p>
                  <div class="grid grid-cols-2 gap-3">
                    <img 
                      v-for="(image, index) in borrow.damage_images" 
                      :key="index"
                      :src="image"
                      class="w-full h-28 object-cover rounded-xl border border-slate-200 shadow-sm hover:opacity-90 transition-opacity"
                    />
                  </div>
                </div>
              </div>

              <!-- Action Penalty Fine Injection Form -->
              <div class="mt-6 pt-6 border-t border-slate-200/60">
                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Tindakan Penalti & Kompensasi</h5>
                
                <div v-if="borrow.damage_status === 'responded'" class="p-3 bg-white rounded-xl border-l-4 border-orange-500 shadow-sm mb-4">
                  <p class="text-xs text-slate-600 font-medium">Tanggapan kalkulasi denda penalti kerusakan telah diteruskan. Siswa dipersilakan melakukan konfirmasi pembayaran.</p>
                </div>
                
                <div class="flex flex-col md:flex-row items-stretch md:items-end gap-4">
                  <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Nominal Denda Tambahan (Rp)</label>
                    <input 
                      v-model="damageForm[borrow.id].additionalFine"
                      type="number"
                      class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all text-slate-800"
                      placeholder="Contoh: 15000"
                    />
                  </div>
                  <div class="flex-2 md:w-1/2">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Komentar / Instruksi Petugas</label>
                    <input 
                      v-model="damageForm[borrow.id].staffComment"
                      type="text"
                      class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all text-slate-800"
                      placeholder="Tulis alasan penalti fisik..."
                    />
                  </div>
                  <button 
                    @click="submitAdditionalFine(borrow.id)"
                    class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-semibold transition-all shadow-sm shadow-orange-500/10 flex items-center justify-center whitespace-nowrap"
                  >
                    Kirim Denda
                  </button>
                </div>
              </div>
            </div>

            <!-- Bottom Base Action Buttons Row -->
            <div class="p-6 bg-slate-50/30 border-t border-slate-100 flex justify-end">
              <button 
                @click="markAsResolved(borrow.id)"
                class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shadow-sm"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Tandai Selesai & Selesaikan Masalah
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State View Condition -->
        <div v-if="lateBorrows.length === 0" class="text-center py-20 bg-white border border-slate-100 rounded-2xl shadow-sm">
          <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-slate-500 font-semibold text-sm mt-1">Berkas bersih</p>
            <p class="text-slate-400 text-xs">Seluruh transaksi peminjaman buku sirkulasi anggota ini dalam status normal.</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  member: { type: Object, required: true },
  lateBorrows: { type: Array, default: () => [] }
});

const damageForm = reactive({});

// Initialize damage form dynamically for each borrow record safely
props.lateBorrows.forEach(borrow => {
  if (borrow.is_damaged) {
    damageForm[borrow.id] = {
      additionalFine: borrow.additional_fine || 0,
      staffComment: borrow.staff_comment || ''
    };
  }
});

const totalFine = props.lateBorrows.reduce((sum, b) => sum + (b.late_fee || 0) + (b.additional_fine || 0), 0);
const totalLateDays = props.lateBorrows.reduce((sum, b) => sum + (b.late_days || 0), 0);

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function getDamageTypeLabel(type) {
  const labels = {
    lost: 'Hilang / Sirna',
    wet: 'Terkena Cairan / Basah',
    torn: 'Halaman Sobek / Rusak',
    stained: 'Noda Permanen',
    other: 'Lain-lain / Degradasi Fisik'
  };
  return labels[type] || type;
}

function getPaymentStatusLabel(status) {
  if (status === 'confirmed') return 'Lunas Terverifikasi';
  if (status === 'pending') return 'Menunggu Pembayaran';
  return 'Belum Ditindaklanjuti';
}

function submitAdditionalFine(borrowId) {
  Swal.fire({
    title: 'Konfirmasi Denda Tambahan',
    html: `
      <div class="text-left font-sans space-y-2 p-1">
        <p class="text-sm text-slate-600">Sistem akan menetapkan biaya penalti fisik sebesar:</p>
        <p class="text-xl font-bold text-slate-900 font-mono">Rp ${formatNumber(damageForm[borrowId].additionalFine)}</p>
        <p class="text-xs text-slate-400">Tanggapan ini akan segera diteruskan secara realtime ke portal akun siswa yang bersangkutan.</p>
      </div>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Kirim Denda',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/petugas/late-returns/${borrowId}/additional-fine`, {
        additional_fine: damageForm[borrowId].additionalFine,
        staff_comment: damageForm[borrowId].staffComment
      }, {
        onSuccess: () => {
          Swal.fire({
            title: 'Berhasil!',
            text: 'Denda tambahan serta catatan kompensasi berhasil diterbitkan.',
            icon: 'success',
            confirmButtonColor: '#f97316'
          });
        }
      });
    }
  });
}

function markAsResolved(borrowId) {
  Swal.fire({
    title: 'Selesaikan Kasus?',
    text: 'Seluruh tanggungan denda sirkulasi item ini akan dianggap selesai/ditutup oleh sistem.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#0f172a',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Konfirmasi Selesai',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/petugas/late-returns/${borrowId}/resolve`, {}, {
        onSuccess: () => {
          Swal.fire({
            title: 'Terselesaikan',
            text: 'Status peminjaman buku resmi ditutup.',
            icon: 'success',
            confirmButtonColor: '#0f172a'
          });
        }
      });
    }
  });
}
</script>