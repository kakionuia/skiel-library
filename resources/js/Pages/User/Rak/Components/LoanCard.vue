<script setup>
import { Link } from '@inertiajs/vue3';
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
  return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
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

// Skema warna badge status yang lebih soft, clean, dan profesional
function statusClass(status) {
  const classes = {
    pending: 'bg-amber-50 text-amber-700 border-amber-200/60',
    approved: 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
    borrowed: 'bg-orange-50 text-orange-700 border-orange-200/60',
    returned: 'bg-slate-50 text-slate-600 border-slate-200',
    overdue: 'bg-rose-50 text-rose-700 border-rose-200/60',
    lost: 'bg-slate-900 text-white border-slate-950',
    rejected: 'bg-rose-100 text-rose-800 border-rose-200'
  };
  return classes[status] || 'bg-slate-50 text-slate-600 border-slate-200';
}

function confirmExtend() {
  Swal.fire({
    title: 'Perpanjang Peminjaman?',
    html: `<p class="text-sm">Buku <strong>${props.loan.book?.title}</strong> akan diperpanjang selama 7 hari lagi.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Perpanjang',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) emit('extend', props.loan);
  });
}
</script>

<template>
  <article 
    class="group relative flex flex-col md:flex-row gap-6 p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-orange-200/80 transition-all duration-300 overflow-hidden"
  >
    <div 
      class="absolute left-0 top-0 bottom-0 w-[4px] transition-all duration-300 group-hover:w-[6px]"
      :class="[
        loan.status === 'borrowed' ? 'bg-orange-500' : '',
        loan.status === 'pending' ? 'bg-amber-500' : '',
        loan.status === 'approved' ? 'bg-emerald-500' : '',
        loan.status === 'returned' ? 'bg-slate-300' : '',
        isOverdue(loan.due_date) && loan.status !== 'returned' ? 'bg-rose-500' : ''
      ]"
    ></div>

    <div class="relative w-24 aspect-[3/4.2] shrink-0 overflow-hidden rounded-xl border border-slate-100 p-1 bg-slate-50 group-hover:scale-[1.02] transition-transform duration-300 shadow-2xs mx-auto md:mx-0">
      <img 
        :src="loan.book?.cover_url || 'https://placehold.co/120x180/f1f5f9/94a3b8?text=No+Cover'" 
        :alt="loan.book?.title"
        class="h-full w-full object-cover rounded-lg"
      />
    </div>

    <div class="flex-1 flex flex-col justify-between min-w-0 py-1">
      <div>
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-3">
          <span 
            class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider border transition-colors duration-200"
            :class="statusClass(loan.status)"
          >
            {{ loan.status_text }}
          </span>
          <span v-if="loan.late_fee > 0" class="px-2.5 py-1 bg-rose-600 text-white rounded-md text-[10px] font-black uppercase tracking-wider animate-pulse">
            Denda: Rp {{ formatNumber(loan.late_fee) }}
          </span>
        </div>

        <div class="text-center md:text-left">
          <h3 class="text-base font-black text-slate-900 tracking-tight leading-snug group-hover:text-orange-600 transition-colors duration-200 truncate">
            {{ loan.book?.title }}
          </h3>
          <p class="text-xs font-bold text-slate-500 mt-0.5">
            Oleh {{ loan.book?.author || 'Penulis tidak tersedia' }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 pt-4 border-t border-slate-100 mt-4">
        <div>
          <p class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Tanggal Pinjam</p>
          <p class="text-xs font-bold text-slate-700 mt-0.5">{{ formatDate(loan.borrow_date || loan.created_at) }}</p>
        </div>
        <div>
          <p class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Batas Tempo</p>
          <p 
            class="text-xs font-bold mt-0.5"
            :class="isOverdue(loan.due_date) ? 'text-rose-600' : 'text-slate-700'"
          >
            {{ formatDate(loan.due_date) }}
          </p>
        </div>
        <div class="col-span-2 lg:col-span-1">
          <p class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Jumlah Buku</p>
          <p class="text-xs font-bold text-slate-700 mt-0.5">{{ loan.quantity }} Eksemplar</p>
        </div>
      </div>

      <div v-if="loan.status === 'approved' || loan.status === 'borrowed'" class="mt-4 pt-4 border-t border-slate-50">
        <div class="mb-2 flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-slate-400">
          <span>Progress Peminjaman</span>
          <span 
            :class="isOverdue(loan.due_date) ? 'text-rose-600 font-black' : 'text-orange-600'"
          >
            {{ getDaysLeft(loan.due_date) }}
          </span>
        </div>
        <div class="h-2 overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200/40">
          <div 
            class="h-full rounded-full transition-all duration-500" 
            :class="isOverdue(loan.due_date) ? 'bg-rose-500' : 'bg-orange-600'"
            :style="{ width: getProgressPercentage(loan.due_date) + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <div class="flex flex-row md:flex-col justify-end items-center gap-2.5 w-full md:w-auto self-stretch md:self-auto pt-4 md:pt-0 border-t border-slate-100 md:border-t-0 md:pl-5 md:border-l md:border-slate-100 shrink-0">
      
      <Link 
        :href="`/borrow/${loan.id}`" 
        class="flex-1 md:flex-none w-full text-center px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 shadow-3xs active:scale-98"
      >
        Detail
      </Link>

      <button
        v-if="loan.status === 'pending'"
        @click="emit('cancel', loan)"
        class="flex-1 md:flex-none w-full px-4 py-2.5 bg-slate-50 hover:bg-rose-50 text-slate-500 hover:text-rose-600 border border-slate-200 hover:border-rose-200 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 active:scale-98"
      >
        Batalkan
      </button>

      <button
        v-if="!isOverdue(loan.due_date) && loan.status === 'borrowed'"
        @click="confirmExtend"
        class="flex-1 md:flex-none w-full px-4 py-2.5 bg-white hover:bg-orange-50 text-orange-700 border border-orange-200/60 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 active:scale-98"
      >
        Perpanjang
      </button>

      <Link
        v-if="loan.status === 'approved' || loan.status === 'borrowed'"
        :href="`/borrow/${loan.id}/print`"
        class="flex-1 md:flex-none w-full text-center px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 active:scale-98"
      >
        Bukti
      </Link>

      <Link
        v-if="loan.status === 'returned'"
        :href="`/books/${loan.book?.id}/review`"
        class="flex-1 md:flex-none w-full text-center px-4 py-2.5 bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white border border-orange-200/40 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 active:scale-98"
      >
        Ulas Buku
      </Link>

      <button
        v-if="loan.late_fee > 0 && loan.status !== 'returned'"
        @click="emit('pay-fine', loan)"
        class="flex-1 md:flex-none w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 shadow-sm active:scale-98"
      >
        Bayar Denda
      </button>
    </div>
  </article>
</template>

<style scoped>
/* Transisi halus saat hover */
.active\:scale-98:active {
  transform: scale(0.98);
}
</style>