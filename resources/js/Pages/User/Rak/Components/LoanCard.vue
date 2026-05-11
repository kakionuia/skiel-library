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

function statusClass(status) {
  const classes = {
    pending: 'bg-orange-50 text-orange-700 ring-orange-200',
    approved: 'bg-orange-100 text-orange-800 ring-orange-200',
    borrowed: 'bg-orange-600 text-white ring-orange-600',
    returned: 'bg-white text-orange-700 ring-orange-200',
    overdue: 'bg-orange-900 text-white ring-orange-900',
    lost: 'bg-slate-100 text-slate-700 ring-slate-200',
    rejected: 'bg-white text-orange-900 ring-orange-200'
  };
  return classes[status] || 'bg-orange-50 text-orange-700 ring-orange-200';
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
  <article class="overflow-hidden rounded-lg border border-orange-100 bg-white transition hover:border-orange-300">
    <div class="grid gap-4 p-4 sm:grid-cols-[96px_1fr]">
      <Link :href="`/borrow/${loan.id}`" class="block overflow-hidden rounded-md bg-orange-50 ring-1 ring-orange-100">
        <img
          :src="loan.book?.cover_url || 'https://placehold.co/160x240/E67E22/white?text=Book'"
          :alt="loan.book?.title"
          class="h-40 w-full object-cover"
        />
      </Link>

      <div class="min-w-0">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <div class="min-w-0">
            <Link :href="`/borrow/${loan.id}`" class="text-base font-black leading-tight text-slate-950 transition hover:text-orange-700">
              {{ loan.book?.title }}
            </Link>
            <p class="mt-1 text-sm font-medium text-slate-500">{{ loan.book?.author || 'Penulis tidak tersedia' }}</p>
          </div>
          <span class="w-fit rounded-md px-2.5 py-1 text-xs font-black uppercase tracking-wide ring-1" :class="statusClass(loan.status)">
            {{ loan.status_text }}
          </span>
        </div>

        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
          <div class="rounded-lg bg-orange-50/70 p-3 ring-1 ring-orange-100">
            <p class="text-[11px] font-bold uppercase tracking-wide text-orange-700">Pinjam</p>
            <p class="mt-1 text-sm font-black text-slate-900">{{ formatDate(loan.borrow_date || loan.created_at) }}</p>
          </div>
          <div class="rounded-lg bg-white p-3 ring-1 ring-orange-100">
            <p class="text-[11px] font-bold uppercase tracking-wide text-orange-700">Tempo</p>
            <p class="mt-1 text-sm font-black" :class="isOverdue(loan.due_date) ? 'text-orange-900' : 'text-slate-900'">{{ formatDate(loan.due_date) }}</p>
          </div>
          <div class="rounded-lg bg-white p-3 ring-1 ring-orange-100">
            <p class="text-[11px] font-bold uppercase tracking-wide text-orange-700">Jumlah</p>
            <p class="mt-1 text-sm font-black text-slate-900">{{ loan.quantity }} buku</p>
          </div>
          <div class="rounded-lg bg-orange-600 p-3 text-white">
            <p class="text-[11px] font-bold uppercase tracking-wide text-orange-100">{{ loan.late_fee > 0 ? 'Denda' : 'Sisa' }}</p>
            <p class="mt-1 text-sm font-black">{{ loan.late_fee > 0 ? `Rp ${formatNumber(loan.late_fee)}` : getDaysLeft(loan.due_date) }}</p>
          </div>
        </div>

        <div v-if="loan.status === 'approved' || loan.status === 'borrowed'" class="mt-4">
          <div class="mb-2 flex items-center justify-between text-xs font-bold text-slate-500">
            <span>Progress peminjaman</span>
            <span :class="isOverdue(loan.due_date) ? 'text-orange-900' : 'text-orange-700'">{{ getDaysLeft(loan.due_date) }}</span>
          </div>
          <div class="h-2 overflow-hidden rounded bg-orange-100">
            <div class="h-full rounded bg-orange-600 transition-all duration-500" :style="{ width: getProgressPercentage(loan.due_date) + '%' }"></div>
          </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
          <Link :href="`/borrow/${loan.id}`" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-orange-700">
            Lihat Detail
          </Link>
          <button
            v-if="loan.status === 'pending'"
            @click="emit('cancel', loan)"
            class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-bold text-orange-800 transition hover:bg-orange-50"
          >
            Batalkan
          </button>
          <button
            v-if="!isOverdue(loan.due_date) && loan.status === 'borrowed'"
            @click="confirmExtend"
            class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-bold text-orange-800 transition hover:bg-orange-50"
          >
            Perpanjang
          </button>
          <Link
            v-if="loan.status === 'approved' || loan.status === 'borrowed'"
            :href="`/borrow/${loan.id}/print`"
            class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-bold text-orange-800 transition hover:bg-orange-50"
          >
            Cetak Bukti
          </Link>
          <Link
            v-if="loan.status === 'returned'"
            :href="`/books/${loan.book?.id}/review`"
            class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-orange-700"
          >
            Ulas Buku
          </Link>
          <button
            v-if="loan.late_fee > 0 && loan.status !== 'returned'"
            @click="emit('pay-fine', loan)"
            class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-bold text-orange-900 transition hover:bg-orange-100"
          >
            Bayar Denda
          </button>
        </div>
      </div>
    </div>
  </article>
</template>
