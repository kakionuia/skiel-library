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
    pending: 'bg-amber-50 text-amber-700 border-amber-200',
    approved: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    borrowed: 'bg-orange-50 text-orange-700 border-orange-200',
    returned: 'bg-slate-50 text-slate-600 border-slate-200',
    overdue: 'bg-rose-50 text-rose-700 border-rose-200',
    lost: 'bg-slate-900 text-white border-slate-950',
    rejected: 'bg-rose-100 text-rose-800 border-rose-200'
  };
  return classes[status] || 'bg-slate-50 text-slate-600 border-slate-200';
}

function getPaymentStatusBadge(loan) {
  if (!loan.payment_status || loan.payment_status === 'unpaid') return null;
  
  const badges = {
    pending: { class: 'bg-yellow-100 text-yellow-800 border-yellow-200', text: '⏳ Menunggu Bayar' },
    confirmed: { class: 'bg-green-100 text-green-800 border-green-200', text: '✓ Lunas' },
    failed: { class: 'bg-red-100 text-red-800 border-red-200', text: '✗ Gagal' }
  };
  
  return badges[loan.payment_status] || null;
}

function hasNoPayment(loan) {
  return !loan.payment_status || loan.payment_status === 'unpaid';
}

function getDamageBadge(loan) {
  if (!loan.is_damaged) return null;
  
  const damageTypes = {
    lost: '📕 Hilang',
    wet: '💧 Basah',
    torn: '📄 Sobek',
    stained: '🎨 Ternoda',
    other: '📋 Lainnya'
  };

  const damageStatuses = {
    pending: '⏳ Menunggu Verifikasi',
    responded: '📋 Sudah Direspons',
    resolved: '✅ Selesai'
  };
  
  return {
    text: `${damageTypes[loan.damage_type] || '⚠ Rusak'} (${damageStatuses[loan.damage_status] || loan.damage_status})`,
    class: loan.damage_status === 'resolved' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-red-100 text-red-800 border-red-200'
  };
}

function confirmExtend() {
  Swal.fire({
    title: 'Perpanjang Peminjaman?',
    html: `<p class="text-sm">Buku <strong>${props.loan.book?.title}</strong> akan diperpanjang selama 7 hari lagi.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Ya, Perpanjang',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) emit('extend', props.loan);
  });
}

function handlePayFine() {
  // Jika belum ada laporan kerusakan, arahkan ke form laporan dulu
  if (props.loan.is_damaged === null || props.loan.is_damaged === undefined) {
    router.visit(`/fines/${props.loan.id}/report-damage`);
  } else {
    // Jika sudah lapor, langsung ke pembayaran
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
    class="group relative flex flex-col sm:flex-row gap-5 p-5 bg-white rounded-2xl border border-slate-100 shadow-xs hover:shadow-md hover:border-orange-200 transition-all duration-300 overflow-hidden"
  >
    <!-- Left border indicator -->
    <div 
      class="absolute left-0 top-0 bottom-0 w-[4px] transition-all duration-300 group-hover:w-[5px]"
      :class="[
        loan.status === 'borrowed' ? 'bg-orange-500' : '',
        loan.status === 'pending' ? 'bg-amber-500' : '',
        loan.status === 'approved' ? 'bg-emerald-500' : '',
        loan.status === 'returned' ? 'bg-slate-300' : '',
        isOverdue(loan.due_date) && loan.status !== 'returned' ? 'bg-rose-500' : ''
      ]"
    ></div>

    <!-- Book Cover -->
    <div class="relative w-24 aspect-[3/4.2] shrink-0 overflow-hidden rounded-xl border border-slate-100 p-1 bg-slate-50 group-hover:scale-[1.02] transition-transform duration-300 shadow-2xs mx-auto sm:mx-0">
      <img 
        :src="loan.book?.cover_url || 'https://placehold.co/120x180/f1f5f9/94a3b8?text=No+Cover'" 
        :alt="loan.book?.title"
        class="h-full w-full object-cover rounded-lg"
      />
    </div>

    <!-- Book Info -->
    <div class="flex-1 flex flex-col justify-between min-w-0 py-0.5">
      <div>
        <!-- Status Badges -->
        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-2.5">
          <!-- Status -->
          <span 
            class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border transition-colors duration-200"
            :class="statusClass(loan.status)"
          >
            {{ loan.status_text }}
          </span>
          
          <!-- Fine Badge -->
          <span 
            v-if="loan.late_fee > 0 && loan.status !== 'returned'" 
            class="px-2.5 py-0.5 bg-rose-500 text-white rounded-md text-[10px] font-bold uppercase tracking-wider animate-pulse"
          >
            Denda: Rp {{ formatNumber(loan.late_fee) }}
          </span>
          
          <!-- Payment Status Badge -->
          <span 
            v-if="getPaymentStatusBadge(loan)" 
            class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border"
            :class="getPaymentStatusBadge(loan).class"
          >
            {{ getPaymentStatusBadge(loan).text }}
          </span>
          
          <!-- Damage Badge -->
          <span 
            v-if="getDamageBadge(loan)" 
            class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border"
            :class="getDamageBadge(loan).class"
          >
            {{ getDamageBadge(loan).text }}
          </span>
          
          <!-- Payment Method Badge -->
          <span 
            v-if="loan.payment_method" 
            class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border bg-blue-100 text-blue-800 border-blue-200"
          >
            {{ loan.payment_method === 'qris' ? '📱 QRIS' : '💵 Tunai' }}
          </span>
        </div>

        <!-- Book Title -->
        <div class="text-center sm:text-left">
          <h3 class="text-base font-bold text-slate-800 tracking-tight leading-snug group-hover:text-orange-600 transition-colors duration-200 truncate">
            {{ loan.book?.title }}
          </h3>
          <p class="text-xs font-medium text-slate-400 mt-0.5">
            Oleh <span class="text-slate-600 font-semibold">{{ loan.book?.author || 'Penulis tidak tersedia' }}</span>
          </p>
        </div>
      </div>

      <!-- Dates Grid -->
      <div class="grid grid-cols-3 gap-3 pt-3 border-t border-slate-100/70 mt-3 text-center sm:text-left">
        <div>
          <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">Pinjam</p>
          <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ formatDate(loan.borrow_date || loan.created_at) }}</p>
        </div>
        <div>
          <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">Tempo</p>
          <p 
            class="text-xs font-semibold mt-0.5"
            :class="isOverdue(loan.due_date) ? 'text-rose-600' : 'text-slate-700'"
          >
            {{ formatDate(loan.due_date) }}
          </p>
        </div>
        <div>
          <p class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">Jumlah</p>
          <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ loan.quantity }} Bks</p>
        </div>
      </div>

      <!-- Progress Bar -->
      <div v-if="loan.status === 'approved' || loan.status === 'borrowed'" class="mt-3.5 pt-3 border-t border-slate-50">
        <div class="mb-1.5 flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-slate-400">
          <span>Progress Masa Pinjam</span>
          <span :class="isOverdue(loan.due_date) ? 'text-rose-600 font-bold' : 'text-orange-600'">
            {{ getDaysLeft(loan.due_date) }}
          </span>
        </div>
        <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
          <div 
            class="h-full rounded-full transition-all duration-500" 
            :class="isOverdue(loan.due_date) ? 'bg-rose-500' : 'bg-orange-500'"
            :style="{ width: getProgressPercentage(loan.due_date) + '%' }"
          ></div>
        </div>
      </div>
      
      <!-- Damage Status Section -->
      <div v-if="loan.is_damaged && loan.damage_status === 'responded'" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1">
            <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">📋 Respons Kerusakan Diterima</p>
            <p v-if="loan.staff_comment" class="text-[9px] text-blue-600 mt-1.5 leading-relaxed">{{ loan.staff_comment }}</p>
            <div v-if="loan.additional_fine > 0" class="mt-2 flex items-center gap-2">
              <span class="text-[10px] font-bold text-blue-700">Denda Tambahan:</span>
              <span class="text-xs font-black text-blue-700">Rp {{ formatNumber(loan.additional_fine) }}</span>
            </div>
          </div>
        </div>
        <div class="mt-2 flex gap-2">
          <Link 
            v-if="hasNoPayment(loan)"
            :href="`/fines/${loan.id}/pay`"
            class="text-center flex-1 px-2 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-[9px] font-bold uppercase tracking-wider transition-colors"
          >
            Lanjut Bayar
          </Link>
          <Link
            v-else-if="loan.payment_status === 'pending'"
            :href="`/fines/${loan.id}/waiting-payment`"
            class="text-center flex-1 px-2 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-[9px] font-bold uppercase tracking-wider transition-colors"
          >
            Menunggu Bayar
          </Link>
          <span v-else class="text-center flex-1 px-2 py-1.5 bg-emerald-600 text-white rounded-lg text-[9px] font-bold uppercase tracking-wider">
            ✓ Pembayaran Diterima
          </span>
        </div>
      </div>
      
      <!-- Additional Fine Info (Traditional) -->
      <div v-else-if="loan.additional_fine > 0" class="mt-2 p-2 bg-red-50 rounded-lg border border-red-200">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-bold text-red-700 uppercase">Denda Kerusakan</span>
          <span class="text-xs font-black text-red-600">Rp {{ formatNumber(loan.additional_fine) }}</span>
        </div>
        <p v-if="loan.staff_comment" class="text-[9px] text-red-600 mt-1 italic">{{ loan.staff_comment }}</p>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-row sm:flex-col justify-end items-center gap-2 w-full sm:w-36 self-stretch sm:self-auto pt-3 sm:pt-0 border-t border-slate-100 sm:border-t-0 sm:pl-4 sm:border-l sm:border-slate-100 shrink-0">
      
      <!-- Detail Button (Always shown) -->
      <Link 
        :href="`/borrow/${loan.id}`" 
        class="flex-1 sm:flex-none w-full text-center px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Detail
      </Link>

      <!-- Cancel Button (Pending only) -->
      <button
        v-if="loan.status === 'pending'"
        @click="emit('cancel', loan)"
        class="flex-1 sm:flex-none w-full px-3 py-2 bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200/60 hover:border-rose-200 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Batalkan
      </button>

      <!-- Extend Button (Borrowed & not overdue) -->
      <button
        v-if="!isOverdue(loan.due_date) && loan.status === 'borrowed'"
        @click="confirmExtend"
        class="flex-1 sm:flex-none w-full px-3 py-2 bg-white hover:bg-orange-50 text-orange-700 border border-orange-200 text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Perpanjang
      </button>

      <!-- Proof Button (Approved or Borrowed) -->
      <Link
        v-if="loan.status === 'approved' || loan.status === 'borrowed'"
        :href="`/borrow/${loan.id}/print`"
        class="flex-1 sm:flex-none w-full text-center px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Bukti
      </Link>

      <!-- Review Button (Returned) -->
      <Link
        v-if="loan.status === 'returned'"
        :href="`/books/${loan.book?.id}/review`"
        class="flex-1 sm:flex-none w-full text-center px-3 py-2 bg-orange-50 hover:bg-orange-600 text-orange-700 hover:text-white border border-orange-100 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Ulas Buku
      </Link>

      <!-- Pay Fine Button (Has fine and no pending damage verification) -->
      <button
        v-if="loan.late_fee > 0 && loan.status !== 'returned' && hasNoPayment(loan) && !(loan.is_damaged === true && loan.damage_status === 'pending')"
        @click="handlePayFine"
        type="button"
        class="flex-1 sm:flex-none w-full text-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Bayar Denda
      </button>

      <!-- Report Damage Button (optional, available before payment) -->
      <Link
        v-if="loan.late_fee > 0 && loan.status !== 'returned' && hasNoPayment(loan) && (loan.is_damaged === null || loan.is_damaged === undefined)"
        :href="`/fines/${loan.id}/report-damage`"
        class="flex-1 sm:flex-none w-full text-center px-3 py-2 bg-white hover:bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98]"
      >
        Lapor Rusak
      </Link>

      <!-- Continue Payment Button (Payment Pending) -->
      <button
        v-if="loan.payment_status === 'pending'"
        @click="handleContinuePayment"
        class="flex-1 sm:flex-none w-full px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-xs font-semibold tracking-wide transition-all duration-200 active:scale-[0.98] animate-pulse"
      >
        Lanjut Bayar
      </button>
      
      <!-- Payment Confirmed Badge -->
      <div
        v-if="loan.payment_status === 'confirmed'"
        class="flex-1 sm:flex-none w-full px-3 py-2 bg-green-100 text-green-700 border border-green-200 rounded-xl text-xs font-semibold text-center tracking-wide"
      >
        ✓ Pembayaran Diterima
      </div>
    </div>
  </article>
</template>
