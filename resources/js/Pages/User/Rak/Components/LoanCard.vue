<!-- LoanCard.vue -->
<script setup>
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  loan: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['extend', 'cancel', 'pay-fine']);

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
}

function formatNumber(number) {
  const validNumber = Number(number) || 0;
  return new Intl.NumberFormat('id-ID').format(Math.round(validNumber));
}

function getDaysLeft(dueDate) {
  if (!dueDate) return '-';
  const diffDays = Math.ceil((new Date(dueDate) - new Date()) / (1000 * 60 * 60 * 24));
  if (diffDays < 0) return `${Math.abs(diffDays)} hari terlambat`;
  if (diffDays === 0) return 'Hari ini';
  return `${diffDays} hari lagi`;
}

function isOverdue(dueDate) {
  return dueDate ? new Date(dueDate) < new Date() : false;
}

function getProgressPercentage(dueDate) {
  if (!props.loan.borrow_date || !dueDate) return 0;
  const now = new Date();
  const due = new Date(dueDate);
  const borrow = new Date(props.loan.borrow_date);
  const totalDays = (due - borrow) / (1000 * 60 * 60 * 24);
  const elapsedDays = (now - borrow) / (1000 * 60 * 60 * 24);
  if (totalDays <= 0) return 100;
  return Math.min(100, Math.max(0, (elapsedDays / totalDays) * 100));
}

function getCleanStatusInfo(loan) {

  // kalau pembayaran sudah dikonfirmasi petugas
  if (
    loan.payment_status === 'confirmed' &&
    loan.status === 'returned'
  ) {
    return {
      text: 'Selesai',
      class: 'text-emerald-600 bg-emerald-50/60'
    };
  }

  if (
    isOverdue(loan.due_date) &&
    loan.status !== 'returned'
  ) {
    return {
      text: 'Terlambat',
      class: 'text-red-600 bg-red-50/60'
    };
  }

  const statuses = {
    pending: {
      text: 'Menunggu',
      class: 'text-amber-600 bg-amber-50/60'
    },
    approved: {
      text: 'Disetujui',
      class: 'text-emerald-600 bg-emerald-50/60'
    },
    borrowed: {
      text: 'Dipinjam',
      class: 'text-orange-600 bg-orange-50/60'
    },
    returned: {
      text: 'Selesai',
      class: 'text-slate-500 bg-slate-100'
    },
    lost: {
      text: 'Hilang',
      class: 'text-slate-800 bg-slate-100'
    },
    rejected: {
      text: 'Ditolak',
      class: 'text-rose-600 bg-rose-50/60'
    }
  };

  return statuses[loan.status] || {
    text: loan.status_text,
    class: 'text-slate-500 bg-slate-100'
  };
}

function hasNoPayment(loan) {
  return !loan.payment_status || loan.payment_status === 'unpaid';
}

function confirmExtend() {
  Swal.fire({
    title: 'Perpanjang Peminjaman?',
    html: `<p class="text-sm">Buku <strong>${props.loan.book?.title}</strong> akan diperpanjang selama 7 hari lagi.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Perpanjang',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) emit('extend', props.loan);
  });
}

function handlePayFine() {
  if (props.loan.is_damaged === null || props.loan.is_damaged === undefined) {
    router.visit(`/fines/${props.loan.id}/report-damage`);
  } else {
    router.visit(`/fines/${props.loan.id}/pay`);
  }
}

function handleContinuePayment() {
  if (props.loan.payment_status === 'pending') {
    router.visit(`/fines/${props.loan.id}/waiting-payment`);
  }
}
</script>

<template>
  <article 
    class="group relative flex flex-col sm:flex-row gap-5 p-5 bg-white border border-slate-100/80 shadow-xs hover:shadow-sm transition-all duration-300 overflow-hidden"
  >
    <!-- Book Cover (Menggunakan shadow halus, tanpa border tebal) -->
    <div class="relative w-20 aspect-[3/4.2] shrink-0 overflow-hidden bg-slate-50 mx-auto sm:mx-0 shadow-2xs">
      <img 
        :src="loan.book?.cover_url || 'https://placehold.co/120x180/f1f5f9/94a3b8?text=No+Cover'" 
        :alt="loan.book?.title"
        class="h-full w-full object-cover"
      />
    </div>

    <div class="flex-1 flex flex-col justify-between min-w-0 py-0.5">
      <div>
        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-2">
          <span 
            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
            :class="getCleanStatusInfo(loan).class"
          >
            {{ getCleanStatusInfo(loan).text }}
          </span>

          <span v-if="loan.payment_method && loan.payment_method !== 'none'" class="text-[11px] text-slate-400">
            • {{ loan.payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
          </span>

          <span v-if="loan.is_damaged" class="text-[11px] text-slate-400 font-medium">
            • Kondisi Rusak/Hilang
          </span>

          <span v-if="loan.payment_status === 'pending'" class="text-[11px] text-amber-500 font-medium">
            • Menunggu Pembayaran
          </span>
        </div>

        <!-- Book Title -->
        <div class="text-center sm:text-left">
          <h3 class="text-sm font-bold text-slate-800 tracking-tight leading-snug group-hover:text-orange-500 transition-colors duration-200 truncate">
            {{ loan.book?.title }}
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">
            Oleh <span class="text-slate-500 font-medium">{{ loan.book?.author || 'Penulis tidak tersedia' }}</span>
          </p>
        </div>
      </div>

      <!-- Live Notification Banner (Menggunakan warna netral slate tanpa border warna) -->
      <div v-if="Number(loan.late_fee) > 0 && loan.status !== 'returned'" class="mt-2.5 px-3 py-1.5 bg-slate-50 rounded-lg flex items-center justify-between text-xs text-slate-600">
      <span class="font-medium text-slate-500">Total Denda</span>
      <span class="font-bold text-slate-800">
        Rp {{ formatNumber(Number(loan.late_fee || 0) + Number(loan.additional_fine || 0)) }}
      </span>
      </div>

      <!-- Dates Grid -->
      <div class="grid grid-cols-3 gap-2 pt-2.5 border-t border-slate-100 mt-3 text-center sm:text-left">
        <div>
          <p class="text-[9px] font-medium uppercase tracking-wider text-slate-400">Pinjam</p>
          <p class="text-xs font-medium text-slate-600 mt-0.5">{{ formatDate(loan.borrow_date || loan.created_at) }}</p>
        </div>
        <div>
          <p class="text-[9px] font-medium uppercase tracking-wider text-slate-400">Tempo</p>
          <p class="text-xs font-semibold mt-0.5" :class="isOverdue(loan.due_date) && loan.status !== 'returned' ? 'text-red-500' : 'text-slate-600'">
            {{ formatDate(loan.due_date) }}
          </p>
        </div>
        <div>
          <p class="text-[9px] font-medium uppercase tracking-wider text-slate-400">Jumlah</p>
          <p class="text-xs font-medium text-slate-600 mt-0.5">{{ loan.quantity }} Buku</p>
        </div>
      </div>

      <!-- Progress Bar (Sleek Orange/Rose Line) -->
      <div v-if="loan.status === 'approved' || loan.status === 'borrowed'" class="mt-3">
        <div class="mb-1 flex items-center justify-between text-[10px] font-semibold text-slate-400">
          <span>Sisa Waktu</span>
          <span :class="isOverdue(loan.due_date) ? 'text-rose-500 font-bold' : 'text-orange-500 font-medium'">
            {{ getDaysLeft(loan.due_date) }}
          </span>
        </div>
        <div class="h-1 overflow-hidden rounded-full bg-slate-100">
          <div 
            class="h-full rounded-full transition-all duration-500" 
            :class="isOverdue(loan.due_date) ? 'bg-red-500' : 'bg-orange-500'"
            :style="{ width: getProgressPercentage(loan.due_date) + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Action Buttons Side Row (Sleek layout, border pemisah netral) -->
    <div class="flex flex-row sm:flex-col justify-end items-center gap-2 w-full sm:w-32 self-stretch sm:self-auto pt-3 sm:pt-0 border-t border-slate-100 sm:border-t-0 sm:pl-4 sm:border-l sm:border-slate-100/80 shrink-0 text-xs">
      
      <!-- Detail Button -->
      <Link 
        :href="`/borrow/${loan.id}`" 
        class="flex-1 sm:flex-none w-full text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg font-medium transition-colors"
      >
        Detail
      </Link>

      <!-- Cancel Button -->
      <button
        v-if="loan.status === 'pending'"
        @click="emit('cancel', loan)"
        class="flex-1 sm:flex-none w-full py-2 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-lg font-medium transition-colors"
      >
        Batalkan
      </button>

      <!-- Extend Button -->
      <button
        v-if="!isOverdue(loan.due_date) && loan.status === 'borrowed'"
        @click="confirmExtend"
        class="flex-1 sm:flex-none w-full py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
      >
        Perpanjang
      </button>

      <!-- Proof Button -->
      <Link
v-if="
  (loan.status === 'approved' || loan.status === 'borrowed') &&
  loan.payment_status !== 'confirmed'
"        :href="`/borrow/${loan.id}/print`"
        class="flex-1 sm:flex-none w-full text-center py-2 hover:bg-slate-50 text-slate-500 rounded-lg font-medium transition-colors"
      >
        Cetak Bukti
      </Link>

      <!-- Review Button -->
      <Link
        v-if="loan.status === 'returned'"
        :href="`/books/${loan.book?.id}/review`"
        class="flex-1 sm:flex-none w-full text-center py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg font-medium transition-colors"
      >
        Ulas Buku
      </Link>

      <!-- Pay Fine Button -->
      <button
v-if="
  loan.late_fee > 0 &&
  loan.payment_status !== 'confirmed' &&
  loan.status !== 'returned' &&
  hasNoPayment(loan)
"        @click="handlePayFine"
        type="button"
        class="flex-1 sm:flex-none w-full text-center py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
      >
        Urus Denda
      </button>

      <!-- Continue Payment Button -->
      <button
        v-if="loan.payment_status === 'pending'"
        @click="handleContinuePayment"
        class="flex-1 sm:flex-none w-full py-2 bg-orange-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors"
      >
        Lanjut Bayar
      </button>
    </div>
  </article>
</template>