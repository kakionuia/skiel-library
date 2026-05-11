<template>
  <div class="min-h-screen bg-white font-sans text-slate-900 selection:bg-orange-100">
    <nav class="border-b border-slate-100 bg-white/80 backdrop-blur-md sticky top-0 z-10 px-8 py-4">
      <div class="mx-auto max-w-6xl flex justify-between items-center">
        <div class="flex items-center gap-6">
          <Link href="/my-loans" class="group flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-orange-600 transition-colors">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            KEMBALI
          </Link>
          <div class="h-4 w-[1px] bg-slate-200"></div>
          <span class="text-xs font-black tracking-[0.2em] text-slate-400 uppercase">Arsip Peminjaman</span>
        </div>
        <button @click="printPage" class="flex items-center gap-2 px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-full text-xs font-black transition-all shadow-lg shadow-orange-200 active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
          UNDUH PDF
        </button>
      </div>
    </nav>

    <main class="mx-auto max-w-6xl px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <div class="lg:col-span-4 space-y-8">
          <div class="relative group">
            <div class="absolute -inset-4 bg-orange-50 rounded-[2rem] scale-95 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"></div>
            <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl shadow-slate-200 border border-slate-100">
              <img :src="borrow.book?.cover_url" class="h-full w-full object-cover" />
            </div>
          </div>

          <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 space-y-4">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Current Status</p>
            <div class="flex items-center gap-3">
              <div class="h-3 w-3 rounded-full bg-orange-500 animate-pulse"></div>
              <h3 class="text-xl font-bold text-slate-900 italic">{{ borrow.status_text }}</h3>
            </div>
          </div>
        </div>

        <div class="lg:col-span-8">
          <div class="space-y-12">
            <div class="border-b border-slate-100 pb-10">
              <p class="text-orange-600 font-black text-sm tracking-widest uppercase mb-2">SKIE Library Digital Receipt</p>
              <h2 class="text-5xl font-black tracking-tighter text-slate-900 leading-none">
                {{ borrow.book?.title }}
              </h2>
              <p class="mt-4 text-xl text-slate-400 font-medium italic">Karya {{ borrow.book?.author }}</p>
            </div>

            <div class="grid grid-cols-2 gap-x-12 gap-y-10">
              <div class="space-y-1">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Peminjam Resmi</p>
                <p class="text-lg font-bold text-slate-800">{{ $page.props.auth.user.name }}</p>
              </div>
              <div class="space-y-1 text-right lg:text-left">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">ID Transaksi</p>
                <p class="text-lg font-mono font-bold text-slate-800">#{{ borrow.id?.toString().padStart(6, '0') }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tanggal Pinjam</p>
                <p class="text-lg font-bold text-slate-800">{{ formatDate(borrow.borrow_date) }}</p>
              </div>
              <div class="space-y-1 text-right lg:text-left">
                <p class="text-[10px] font-black uppercase tracking-widest text-orange-400">Batas Pengembalian</p>
                <p class="text-lg font-bold text-orange-600">{{ formatDate(borrow.due_date) }}</p>
              </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white">
              <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                  <tr>
                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Keterangan Administrasi</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase text-slate-400">Nilai</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr>
                    <td class="px-6 py-4 text-sm font-medium text-slate-600">Jumlah Eksemplar</td>
                    <td class="px-6 py-4 text-right text-sm font-black text-slate-900">{{ borrow.quantity }} Unit</td>
                  </tr>
                  <tr v-if="borrow.late_fee > 0" class="bg-orange-50/30">
                    <td class="px-6 py-4 text-sm font-medium text-orange-700 font-bold italic">Denda Keterlambatan ({{ borrow.late_days }} hari)</td>
                    <td class="px-6 py-4 text-right text-sm font-black text-orange-600">Rp {{ formatNumber(borrow.late_fee) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="pt-8 text-center lg:text-left">
               <p class="text-xs text-slate-400 leading-relaxed max-w-md">
                 Dokumen ini merupakan bukti sah peminjaman buku di SKIE Library. 
                 Harap mengembalikan buku tepat waktu untuk menghindari denda tambahan.
               </p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
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
  return new Intl.NumberFormat('id-ID').format(number);
}

function isOverdue(dueDate) {
  return new Date(dueDate) < new Date();
}

function getStatusBadgeClass(status) {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-700',
    approved: 'bg-blue-100 text-blue-700',
    borrowed: 'bg-orange-100 text-orange-700',
    returned: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
    lost: 'bg-gray-100 text-gray-700'
  };
  return classes[status] || 'bg-slate-100 text-slate-700';
}

function printPage() {
  window.print();
}
const steps = computed(() => [
  { label: 'Pengajuan Terverifikasi', time: formatDate(props.borrow.created_at), completed: true },
  { label: 'Persetujuan Admin', time: formatDate(props.borrow.approved_at), completed: !!props.borrow.approved_at },
  { label: 'Pengembalian Selesai', time: formatDate(props.borrow.return_date), completed: props.borrow.status === 'returned' }
]);
</script>
