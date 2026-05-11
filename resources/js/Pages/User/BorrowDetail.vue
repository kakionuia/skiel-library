<template>
  <div class="min-h-screen bg-slate-50/50 font-sans text-slate-900 antialiased selection:bg-orange-100">
    
    <header class="sticky top-0 z-10 border-b border-slate-200/80 bg-white/90 backdrop-blur-md px-8 py-5">
      <div class="mx-auto max-w-6xl flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link 
            href="/my-loans" 
            class="group p-2 rounded-xl border border-slate-200 hover:border-orange-500 hover:bg-orange-50/50 transition-all duration-200"
          >
            <svg class="w-5 h-5 text-slate-500 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">SKIE LIBRARY SYSTEM</p>
            <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">Detail Transaksi Peminjaman</h1>
          </div>
        </div>
        
        <a 
          :href="`/borrow/${borrow.id}/print`"
          class="inline-flex items-center gap-2.5 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black tracking-wider uppercase transition-all shadow-md shadow-orange-500/10 active:scale-95"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          Cetak Dokumen
        </a>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <div class="lg:col-span-4 space-y-8">
          <div class="aspect-[3/4.2] w-full overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-3 shadow-sm">
            <img 
              :src="borrow.book?.cover_url || 'https://placehold.co/400x560/f1f5f9/94a3b8?text=No+Cover'" 
              :alt="borrow.book?.title"
              class="h-full w-full object-cover rounded-xl"
            />
          </div>

          <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-3">
            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Status Transaksi</p>
            <div class="flex items-center justify-between">
              <span class="text-xl font-extrabold tracking-tight text-slate-900 italic">{{ borrow.status_text }}</span>
              <span :class="getStatusDotClass(borrow.status)" class="h-3 w-3 rounded-full animate-pulse"></span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-8 space-y-10 bg-white border border-slate-200/80 rounded-3xl p-10 shadow-sm">
          
          <div class="border-b border-slate-100 pb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
              <span class="text-orange-600 font-extrabold text-xs tracking-[0.25em] uppercase">Official Borrowing Receipt</span>
              <h2 class="mt-2 text-3xl font-black text-slate-900 tracking-tight leading-tight">
                {{ borrow.book?.title }}
              </h2>
              <p class="mt-1 text-sm font-medium text-slate-500">Karya {{ borrow.book?.author }} &bull; {{ borrow.book?.publisher }}</p>
            </div>
            <div class="md:text-right shrink-0">
              <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase">No. Registrasi</p>
              <p class="text-xl font-mono font-bold text-orange-600">#{{ borrow.id.toString().padStart(6, '0') }}</p>
            </div>
          </div>

          <div class="space-y-4">
            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Riwayat Log Alur</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              
              <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex gap-4 items-center">
                <div class="h-9 w-9 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold shrink-0 text-sm">1</div>
                <div>
                  <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pengajuan</p>
                  <p class="text-sm font-extrabold text-slate-800">{{ formatDate(borrow.created_at) }}</p>
                </div>
              </div>

              <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex gap-4 items-center">
                <div 
                  :class="borrow.approved_at ? 'bg-orange-50 text-orange-600' : 'bg-slate-100 text-slate-400'" 
                  class="h-9 w-9 rounded-lg flex items-center justify-center font-bold shrink-0 text-sm"
                >
                  2
                </div>
                <div>
                  <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Disetujui</p>
                  <p class="text-sm font-extrabold text-slate-800">{{ borrow.approved_at ? formatDate(borrow.approved_at) : 'Menunggu' }}</p>
                </div>
              </div>

              <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex gap-4 items-center">
                <div 
                  :class="borrow.return_date ? 'bg-orange-50 text-orange-600' : 'bg-slate-100 text-slate-400'" 
                  class="h-9 w-9 rounded-lg flex items-center justify-center font-bold shrink-0 text-sm"
                >
                  3
                </div>
                <div>
                  <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Kembali</p>
                  <p class="text-sm font-extrabold text-slate-800">{{ borrow.return_date ? formatDate(borrow.return_date) : 'Belum Kembali' }}</p>
                </div>
              </div>

            </div>
          </div>

          <div class="border-t border-slate-100 pt-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
            <div class="space-y-1">
              <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Penerima Manfaat / Anggota</label>
              <p class="text-base font-bold text-slate-800">Petugas Perpustakaan</p>
            </div>
            
            <div class="space-y-1">
              <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Jumlah Buku</label>
              <p class="text-base font-bold text-slate-800">{{ borrow.quantity }} Eksemplar</p>
            </div>

            <div class="space-y-1">
              <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Tanggal Mulai Pinjam</label>
              <p class="text-base font-bold text-slate-800">{{ formatDate(borrow.borrow_date) }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-500">Jatuh Tempo Pengembalian</label>
              <p :class="isOverdue(borrow.due_date) ? 'text-red-600' : 'text-orange-600'" class="text-base font-black">
                {{ formatDate(borrow.due_date) }}
              </p>
            </div>
          </div>

          <div class="space-y-4 pt-4">
            
            <div v-if="borrow.late_fee > 0" class="p-5 bg-red-50 border border-red-100 rounded-2xl flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-extrabold uppercase text-red-500 tracking-wider">Terlambat {{ borrow.late_days }} Hari</p>
                  <p class="text-sm text-red-700">Denda administrasi yang terakumulasi.</p>
                </div>
              </div>
              <span class="text-xl font-black text-red-600">Rp {{ formatNumber(borrow.late_fee) }}</span>
            </div>

            <div v-else class="p-5 bg-orange-50/40 border border-orange-100/40 rounded-2xl flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-orange-100/50 text-orange-600 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-extrabold uppercase text-orange-600 tracking-wider">Status Keuangan</p>
                  <p class="text-sm text-slate-500">Tidak ada tangguhan denda berjalan.</p>
                </div>
              </div>
              <span class="text-lg font-black text-orange-600">Rp 0</span>
            </div>

            <div v-if="borrow.rejection_reason" class="p-5 bg-red-50 border border-red-100 rounded-2xl">
              <p class="text-xs font-extrabold text-red-500 uppercase tracking-widest mb-1">Alasan Penolakan</p>
              <p class="text-sm text-red-800 leading-relaxed font-semibold">{{ borrow.rejection_reason }}</p>
            </div>

            <div v-if="borrow.notes" class="p-5 bg-slate-50 border border-slate-200/60 rounded-2xl">
              <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-1">Catatan Tambahan</p>
              <p class="text-sm text-slate-600 leading-relaxed font-semibold">{{ borrow.notes }}</p>
            </div>

          </div>

          <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row md:justify-between items-center gap-4">
            <p class="text-[11px] text-slate-400 leading-relaxed text-center md:text-left max-w-sm">
              Dokumen lembar digital ini adalah bukti transaksi resmi sistem SKIE.LIB. Keterlambatan pengembalian buku berdampak pada sanksi administratif berkelanjutan.
            </p>
            <Link 
              href="/my-loans" 
              class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-orange-600 transition-colors"
            >
              Tutup Transaksi
            </Link>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  borrow: {
    type: Object,
    required: true
  }
});

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(Math.round(number));
}

function isOverdue(dueDate) {
  if (!dueDate) return false;
  return new Date(dueDate) < new Date();
}

function getStatusDotClass(status) {
  const dots = {
    pending: 'bg-yellow-500',
    approved: 'bg-blue-500',
    borrowed: 'bg-orange-500',
    returned: 'bg-green-500',
    overdue: 'bg-red-500',
    lost: 'bg-slate-400',
    rejected: 'bg-red-500'
  };
  return dots[status] || 'bg-slate-400';
}
</script>