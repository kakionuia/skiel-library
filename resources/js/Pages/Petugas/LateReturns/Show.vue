<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div class="flex items-center gap-4">
            <Link href="/petugas/late-returns" class="p-2 rounded-xl hover:bg-slate-100 transition-colors">
              <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h1 class="text-2xl font-bold tracking-tight text-slate-800">Detail Keterlambatan</h1>
              <p class="text-slate-500 text-sm mt-1">Informasi lengkap anggota dengan peminjaman terlambat</p>
            </div>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-[1400px] mx-auto space-y-8">
        <!-- User Info Card -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8">
          <div class="flex items-start gap-6">
            <div class="w-20 h-20 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center font-black text-2xl">
              {{ member.name?.charAt(0) }}
            </div>
            <div class="flex-1">
              <h2 class="text-2xl font-black text-slate-900">{{ member.name }}</h2>
              <p class="text-sm text-slate-500">{{ member.email }}</p>
              
              <div class="grid grid-cols-4 gap-4 mt-6">
                <div>
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NISN</p>
                  <p class="text-sm font-bold text-slate-900 mt-1">{{ member.nisn || '-' }}</p>
                </div>
                <div>
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kelas</p>
                  <p class="text-sm font-bold text-slate-900 mt-1">{{ member.kelas || '-' }}</p>
                </div>
                <div>
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Denda</p>
                  <p class="text-lg font-black text-red-600 mt-1">Rp {{ formatNumber(totalFine) }}</p>
                </div>
                <div>
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Terlambat</p>
                  <p class="text-lg font-black text-orange-600 mt-1">{{ totalLateDays }} Hari</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Late Borrows List -->
        <div class="space-y-6">
          <div v-for="borrow in lateBorrows" :key="borrow.id" class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <!-- Borrow Info -->
            <div class="p-8 border-b border-slate-100">
              <div class="flex items-start justify-between">
                <div class="flex gap-4">
                  <div class="w-16 h-20 rounded-xl bg-slate-100 flex items-center justify-center text-2xl">
                    📚
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ borrow.book?.title }}</h3>
                    <p class="text-sm text-slate-500">{{ borrow.book?.author }}</p>
                    
                    <div class="flex gap-6 mt-3">
                      <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jatuh Tempo</p>
                        <p class="text-sm font-bold text-red-600">{{ formatDate(borrow.due_date) }}</p>
                      </div>
                      <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Terlambat</p>
                        <p class="text-sm font-bold text-orange-600">{{ borrow.late_days }} Hari</p>
                      </div>
                      <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Denda</p>
                        <p class="text-sm font-bold text-red-600">Rp {{ formatNumber(borrow.late_fee) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="text-right">
                  <span 
                    class="px-3 py-1 rounded-full text-xs font-bold"
                    :class="getPaymentStatusClass(borrow.payment_status)"
                  >
                    {{ getPaymentStatusLabel(borrow.payment_status) }}
                  </span>
                  <p v-if="borrow.payment_method" class="text-xs text-slate-500 mt-2">
                    {{ borrow.payment_method === 'qris' ? '📱 QRIS' : '💵 Tunai' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Damage Report Section -->
            <div v-if="borrow.is_damaged" class="p-8 bg-red-50/50 border-b border-red-100">
              <h4 class="text-sm font-black text-red-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Laporan Kerusakan
              </h4>
              
              <div class="grid grid-cols-2 gap-6">
                <div class="space-y-3">
                  <div>
                    <p class="text-xs font-bold text-slate-600 mb-1">Jenis Kerusakan</p>
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm font-bold">
                      {{ getDamageTypeLabel(borrow.damage_type) }}
                    </span>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-600 mb-1">Deskripsi</p>
                    <p class="text-sm text-slate-700">{{ borrow.damage_description }}</p>
                  </div>
                </div>
                
                <div v-if="borrow.damage_images?.length" class="space-y-3">
                  <p class="text-xs font-bold text-slate-600">Foto Kerusakan</p>
                  <div class="grid grid-cols-2 gap-3">
                    <img 
                      v-for="(image, index) in borrow.damage_images" 
                      :key="index"
                      :src="image"
                      class="w-full h-32 object-cover rounded-xl border-2 border-red-200"
                    />
                  </div>
                </div>
              </div>

              <!-- Additional Fine Section -->
              <div class="mt-6 pt-6 border-t border-red-200">
                <h5 class="text-sm font-bold text-red-900 mb-3">Biaya Tambahan Kerusakan</h5>
                <p v-if="borrow.damage_status === 'responded'" class="text-xs text-blue-700 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 mb-3">
                  Tanggapan sudah dikirim ke siswa. Siswa sudah bisa memilih pembayaran QRIS atau tunai.
                </p>
                
                <div class="flex items-end gap-4">
                  <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Nominal Denda Tambahan (Rp)</label>
                    <input 
                      v-model="damageForm[borrow.id].additionalFine"
                      type="number"
                      class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-orange-500 outline-none transition-all"
                      placeholder="Masukkan nominal denda tambahan"
                    />
                  </div>
                  <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Komentar Petugas</label>
                    <input 
                      v-model="damageForm[borrow.id].staffComment"
                      type="text"
                      class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-orange-500 outline-none transition-all"
                      placeholder="Komentar tentang kerusakan..."
                    />
                  </div>
                  <button 
                    @click="submitAdditionalFine(borrow.id)"
                    class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold transition-all"
                  >
                    Kirim Denda Tambahan
                  </button>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-8 flex justify-end gap-4">
              <button 
                @click="markAsResolved(borrow.id)"
                class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Tandai Masalah Selesai
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="lateBorrows.length === 0" class="text-center py-20">
          <div class="text-6xl mb-4">📚</div>
          <h3 class="text-xl font-bold text-slate-900">Tidak ada keterlambatan</h3>
          <p class="text-slate-500 mt-2">Semua peminjaman anggota ini tepat waktu</p>
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

// Initialize damage form for each borrow
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
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function getDamageTypeLabel(type) {
  const labels = {
    lost: 'Hilang',
    wet: 'Basah',
    torn: 'Sobek',
    stained: 'Tertimpa Noda',
    other: 'Lainnya'
  };
  return labels[type] || type;
}

function getPaymentStatusLabel(status) {
  if (status === 'confirmed') return '✓ Lunas';
  if (status === 'pending') return '⏳ Menunggu Bayar';
  return 'Belum Pilih Bayar';
}

function getPaymentStatusClass(status) {
  if (status === 'confirmed') return 'bg-green-100 text-green-700';
  if (status === 'pending') return 'bg-yellow-100 text-yellow-700';
  return 'bg-slate-100 text-slate-600';
}

function submitAdditionalFine(borrowId) {
  Swal.fire({
    title: 'Konfirmasi Denda Tambahan',
    html: `
      <div class="text-left space-y-2">
        <p class="text-sm">Anda akan mengirimkan denda tambahan sebesar:</p>
        <p class="text-2xl font-bold text-red-600">Rp ${formatNumber(damageForm[borrowId].additionalFine)}</p>
        <p class="text-xs text-slate-500">Siswa harus membayar denda tambahan ini</p>
      </div>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Kirim',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/petugas/late-returns/${borrowId}/additional-fine`, {
        additional_fine: damageForm[borrowId].additionalFine,
        staff_comment: damageForm[borrowId].staffComment
      }, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Denda tambahan telah dikirim.', 'success');
        }
      });
    }
  });
}

function markAsResolved(borrowId) {
  Swal.fire({
    title: 'Tandai Masalah Selesai?',
    text: 'Masalah keterlambatan ini akan ditandai sebagai selesai.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Selesaikan',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/petugas/late-returns/${borrowId}/resolve`, {}, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Masalah telah diselesaikan.', 'success');
        }
      });
    }
  });
}
</script>
