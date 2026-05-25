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
              <img
                :src="bookCoverUrl"
                :alt="borrow.book?.title || 'Cover buku'"
                class="h-full w-full object-cover"
                @error="coverLoadFailed = true"
              />
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
                <tbody class="divide-y divide-slate-100">

  <tr>
    <td class="px-6 py-4 text-sm font-medium text-slate-600">
      Jumlah Eksemplar
    </td>

    <td class="px-6 py-4 text-right text-sm font-black text-slate-900">
      {{ borrow.quantity }} Unit
    </td>
  </tr>

 <tr v-if="paymentMethod">
  <td class="px-6 py-4 text-sm font-medium text-slate-600">
    Metode Pembayaran
  </td>

  <td class="px-6 py-4 text-right">
    <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-xs font-black text-orange-700">
      {{ paymentMethod }}
    </span>
  </td>
</tr>

 <tr v-if="paymentStatus">
  <td class="px-6 py-4 text-sm font-medium text-slate-600">
    Status Pembayaran
  </td>

  <td class="px-6 py-4 text-right">
    <span
      class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-[11px] font-black tracking-wide"
      :class="paymentStatus === 'LUNAS'
        ? 'border-green-200 bg-green-50 text-green-700'
        : 'border-orange-200 bg-orange-50 text-orange-600'"
    >
      <span
        class="h-2 w-2 rounded-full"
        :class="paymentStatus === 'LUNAS'
          ? 'bg-green-500'
          : 'bg-orange-500'"
      ></span>

      {{ paymentStatus }}
    </span>
  </td>
</tr>

  <tr
    v-if="liveLateDays > 0"
    class="bg-orange-50/40"
  >
    <td class="px-6 py-4 text-sm font-medium text-orange-700">
      Keterlambatan
    </td>

    <td class="px-6 py-4 text-right text-sm font-black text-orange-600">
      {{ liveLateDays }} Hari
    </td>
  </tr>

  <tr
    v-if="liveFine > 0"
    class="bg-orange-50/40"
  >
    <td class="px-6 py-4 text-sm font-bold italic text-orange-700">
      Total Denda Berjalan
    </td>

    <td class="px-6 py-4 text-right text-lg font-black text-orange-600">
      Rp {{ formatNumber(liveFine) }}
    </td>
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
import { computed, ref } from 'vue';
const props = defineProps({
  borrow: {
    type: Object,
    required: true
  }
});

const coverLoadFailed = ref(false);
const placeholderCover = computed(() => {
  const title = props.borrow.book?.title || 'No Cover';
  return `https://placehold.co/420x560/f1f5f9/64748b?text=${encodeURIComponent(title)}`;
});

const bookCoverUrl = computed(() => {
  if (coverLoadFailed.value) {
    return placeholderCover.value;
  }

  return props.borrow.book?.cover_url || placeholderCover.value;
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



const hasFine = computed(() => {
  return liveFine.value > 0;
});

const paymentStatus = computed(() => {

  // kalau tidak ada denda
  if (!hasFine.value) {
    return null;
  }

  // kalau sudah jadi riwayat / returned
  // otomatis lunas
  if (
    props.borrow.status === 'returned' ||
    props.borrow.return_date
  ) {
    return 'LUNAS';
  }

  return 'BELUM LUNAS';
});

const paymentMethod = computed(() => {

  // kalau tidak ada denda
  // tidak usah tampil metode pembayaran
  if (!hasFine.value) {
    return null;
  }

  return props.borrow.payment_method === 'qris'
    ? 'QRIS'
    : 'Tunai';
});
const liveLateDays = computed(() => {
  if (!props.borrow.due_date || props.borrow.status === 'returned') {
    return props.borrow.late_days || 0;
  }

  const due = new Date(props.borrow.due_date);
  const now = new Date();

  if (now <= due) return 0;

  const diff = Math.floor(
    (now - due) / (1000 * 60 * 60 * 24)
  );

  return diff;
});

const liveFine = computed(() => {

  if (paymentStatus.value === 'LUNAS') {
    return props.borrow.late_fee || 0;
  }

  const finePerDay = props.borrow.fine_per_day || 1000;

  return liveLateDays.value * finePerDay;
});
</script>

<style>
@media print {

  @page {
    size: A4 portrait;
    margin: 10mm;
  }

  html,
  body {
    background: white !important;
    zoom: 0.82;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  nav {
    display: none !important;
  }

  main {
    max-width: 100% !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* Layout jadi compact */
  .grid {
    gap: 20px !important;
  }

  /* Cover buku kecil elegant */
  .aspect-\[3\/4\] {
    width: 180px !important;
    max-height: 250px !important;
    margin: 0 auto !important;
  }

  .aspect-\[3\/4\] img {
    object-fit: cover !important;
  }

  /* Semua shadow dimatikan */
  .shadow-2xl,
  .shadow-xl,
  .shadow-lg {
    box-shadow: none !important;
  }

  /* Card */
  .rounded-2xl {
    border-radius: 18px !important;
  }

  /* Judul buku */
  h2 {
    font-size: 30px !important;
    line-height: 1.1 !important;
  }

  /* Spacing dipadatkan */
  .space-y-12 > * + * {
    margin-top: 24px !important;
  }

  .space-y-8 > * + * {
    margin-top: 18px !important;
  }

  .pb-10 {
    padding-bottom: 20px !important;
  }

  .py-12 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  /* Table compact */
  table td,
  table th {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
  }

  /* Hindari kepotong */
  table,
  tr,
  td,
  th,
  img {
    page-break-inside: avoid !important;
  }

  /* Footer */
  .text-xs {
    font-size: 10px !important;
  }

  /* Hilangin animasi */
  * {
    animation: none !important;
    transition: none !important;
  }
}

table {
  border-collapse: separate;
  border-spacing: 0;
}

table th {
  color: rgb(100 116 139);
  font-weight: 800;
}

table td {
  border-color: rgb(241 245 249) !important;
}

.rounded-2xl {
  border-radius: 1.25rem;
}

.border-slate-100 {
  border-color: rgb(241 245 249 / 0.7) !important;
}
</style>
