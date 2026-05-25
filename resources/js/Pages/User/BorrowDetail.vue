<template>
  <div class="min-h-screen bg-slate-50/50 font-sans text-slate-800 antialiased selection:bg-orange-100">
    
    <header class="sticky top-0 z-10 border-b border-slate-200/60 bg-white/80 backdrop-blur-md px-6 py-4">
      <div class="mx-auto max-w-6xl flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link 
            href="/my-loans" 
            class="group p-2 rounded-lg border border-slate-200 hover:border-orange-500 hover:bg-orange-50/50 transition-all duration-200"
          >
            <svg class="w-4 h-4 text-slate-500 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400">SKIE LIBRARY SYSTEM</p>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Detail Transaksi Peminjaman</h1>
          </div>
        </div>
        
        <a 
          :href="`/borrow/${borrow.id}/print`"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-semibold tracking-wide transition-all shadow-sm active:scale-95"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          Cetak Dokumen
        </a>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-6">
          <div class="aspect-[3/4.2] w-full overflow-hidden rounded-xl border border-slate-200/60 bg-white p-2.5 shadow-sm">
            <img 
              :src="borrow.book?.cover_url || 'https://placehold.co/400x560/f1f5f9/94a3b8?text=No+Cover'" 
              :alt="borrow.book?.title"
              class="h-full w-full object-cover rounded-lg"
            />
          </div>

          <div class="p-5 rounded-xl bg-white border border-slate-200/60 shadow-sm space-y-3">
            <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400">Status Transaksi</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-bold tracking-tight text-slate-900">{{ borrow.status_text }}</span>
              <span :class="getStatusDotClass(borrow.status)" class="h-2.5 w-2.5 rounded-full animate-pulse"></span>
            </div>
            
            <!-- Payment Status Badge -->
            <div v-if="borrow.payment_status && borrow.payment_status !== 'unpaid'" class="mt-3 pt-3 border-t border-slate-100">
              <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-2">Status Pembayaran</p>
              <span 
                class="px-2.5 py-1 rounded-md text-xs font-medium inline-block"
                :class="getPaymentStatusClass(borrow.payment_status)"
              >
                {{ getPaymentStatusLabel(borrow.payment_status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-8 bg-white border border-slate-200/60 rounded-xl p-8 shadow-sm">
          
          <!-- Book Info Header -->
          <div class="border-b border-slate-100 pb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
              <span class="text-orange-500 font-bold text-xs tracking-wider uppercase">Official Borrowing Receipt</span>
              <h2 class="mt-1.5 text-2xl font-bold text-slate-900 tracking-tight leading-tight">
                {{ borrow.book?.title }}
              </h2>
              <p class="mt-1 text-xs font-medium text-slate-500">Karya {{ borrow.book?.author }} &bull; {{ borrow.book?.publisher }}</p>
            </div>
            <div class="md:text-right shrink-0">
              <p class="text-[9px] font-bold tracking-wider text-slate-400 uppercase">No. Registrasi</p>
              <p class="text-lg font-mono font-bold text-orange-500">#{{ borrow.id.toString().padStart(6, '0') }}</p>
            </div>
          </div>

          <!-- Timeline -->
          <div class="space-y-3">
            <p class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400">Riwayat Log Alur</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              
              <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 flex gap-3.5 items-center">
                <div class="h-8 w-8 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center font-bold shrink-0 text-xs">1</div>
                <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Pengajuan</p>
                  <p class="text-xs font-bold text-slate-800">{{ formatDate(borrow.created_at) }}</p>
                </div>
              </div>

              <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 flex gap-3.5 items-center">
                <div 
                  :class="borrow.approved_at ? 'bg-orange-50 text-orange-500' : 'bg-slate-100 text-slate-400'" 
                  class="h-8 w-8 rounded-lg flex items-center justify-center font-bold shrink-0 text-xs"
                >
                  2
                </div>
                <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Disetujui</p>
                  <p class="text-xs font-bold text-slate-800">{{ borrow.approved_at ? formatDate(borrow.approved_at) : 'Menunggu' }}</p>
                </div>
              </div>

              <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 flex gap-3.5 items-center">
                <div 
                  :class="borrow.return_date ? 'bg-orange-50 text-orange-500' : 'bg-slate-100 text-slate-400'" 
                  class="h-8 w-8 rounded-lg flex items-center justify-center font-bold shrink-0 text-xs"
                >
                  3
                </div>
                <div>
                  <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Kembali</p>
                  <p class="text-xs font-bold text-slate-800">{{ borrow.return_date ? formatDate(borrow.return_date) : 'Belum Kembali' }}</p>
                </div>
              </div>

            </div>
          </div>

          <!-- Detail Info -->
          <div class="border-t border-slate-100 pt-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
            <div class="space-y-1">
              <label class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400">Anggota</label>
              <p class="text-sm font-semibold text-slate-800">{{ borrow.member?.name || 'Anggota Perpustakaan' }}</p>
            </div>
            
            <div class="space-y-1">
              <label class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400">Jumlah Buku</label>
              <p class="text-sm font-semibold text-slate-800">{{ borrow.quantity }} Eksemplar</p>
            </div>

            <div class="space-y-1">
              <label class="text-[9px] font-bold uppercase tracking-[0.15em] text-slate-400">Tanggal Mulai Pinjam</label>
              <p class="text-sm font-semibold text-slate-800">{{ formatDate(borrow.borrow_date) }}</p>
            </div>

            <div class="space-y-1">
              <label class="text-[9px] font-bold uppercase tracking-[0.15em] text-orange-500">Jatuh Tempo Pengembalian</label>
              <p :class="isOverdue(borrow.due_date) && !borrow.return_date ? 'text-red-600 font-extrabold' : 'text-orange-500'" class="text-sm font-bold">
                {{ formatDate(borrow.due_date) }}
              </p>
            </div>
          </div>

          <!-- FINE & PAYMENT SECTION -->
          <div class="space-y-4 pt-2">
            
            <!-- CASE 1: Book already returned -->
            <div v-if="borrow.return_date" class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="h-9 w-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs font-bold text-emerald-800">Buku Sudah Dikembalikan</p>
                    <p class="text-[11px] text-emerald-600/90">Tanggal: {{ formatDate(borrow.return_date) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-[10px] text-slate-400">Total Denda</p>
                  <p :class="borrow.late_fee > 0 ? 'text-red-600' : 'text-emerald-600'" class="text-base font-bold">
                    Rp {{ formatNumber(borrow.late_fee || 0) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- CASE 2: Overdue & Not Yet Returned -->
            <div v-else-if="isOverdue(borrow.due_date) && !borrow.return_date" class="space-y-4">
              <!-- Late Warning (Diubah dari bg-red-50/60 yang bernuansa pink menjadi bg-red-100/70 & border-red-200 yang merah tegas) -->
              <div class="p-5 bg-red-600 border border-red-600 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-red-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-xs font-bold text-white">Peringatan Keterlambatan</p>
                      <p class="text-[11px] text-slate-200 font-bold">Terlambat {{ calculateLateDays(borrow.due_date) }} Hari</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-[10px] text-white font-medium">Total Denda</p>
                    <p class="text-lg font-black text-white">
                      Rp {{ formatNumber(borrow.late_fee || calculateLateFee(borrow.due_date)) }}
                    </p>
                    <p class="text-[9px] text-red-600/80 font-medium">@ Rp 1.000/hari</p>
                  </div>
                </div>
                
                <div class="p-3 bg-white border border-slate-200 rounded-lg text-xs space-y-1.5 shadow-xs">
                  <div class="flex justify-between">
                    <span class="text-slate-500">Jatuh Tempo:</span>
                    <span class="font-bold text-red-600">{{ formatDate(borrow.due_date) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-slate-500">Hari ini:</span>
                    <span class="font-medium text-slate-700">{{ formatDate(new Date()) }}</span>
                  </div>
                </div>
              </div>

              <!-- Damage Report & Payment Info -->
              <div class="p-5 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-3">Tindakan yang Diperlukan</h3>
                
                <div class="space-y-3">
                  <!-- Step 1: Laporkan kondisi buku -->
                  <div class="p-4 border border-orange-100 rounded-lg">
                    <div class="flex items-center gap-2.5 mb-2">
                      <div class="w-6 h-6 rounded-md bg-orange-500 text-white flex items-center justify-center font-bold text-xs">1</div>
                      <p class="text-xs font-bold text-orange-900">Laporkan Kondisi Buku</p>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-3">Anda harus melaporkan kondisi fisik buku saat ini sebelum melakukan pelunasan.</p>
                    
                    <!-- Belum lapor -->
                    <Link 
                      v-if="borrow.is_damaged === null || borrow.is_damaged === undefined"
                      :href="`/fines/${borrow.id}/report-damage`"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-md text-xs font-medium transition-all shadow-sm"
                    >
                      Laporkan Kondisi Buku
                    </Link>
                    
                    <!-- Menunggu konfirmasi -->
                    <div v-else-if="borrow.is_damaged === true && borrow.damage_status === 'pending'" class=" border border-orange-200 text-orange-900 rounded-md p-2.5 text-xs">
                      <span class="font-semibold block">Menunggu konfirmasi petugas</span>
                      <span class="text-[11px] text-orange-800 block mt-0.5">Laporan kerusakan Anda sedang ditinjau.</span>
                      <span v-if="borrow.damage_type" class="text-[10px] text-orange-700/90 block mt-0.5">Jenis: {{ getDamageLabel(borrow.damage_type) }}</span>
                    </div>

                    <!-- Tanggapan Diterima -->
                    <div v-else-if="borrow.is_damaged === true && borrow.damage_status === 'responded' && hasNoPayment(borrow)" class=" border border-orange-200 text-orange-900 rounded-md p-2.5 text-xs">
                      <span class="font-semibold block">Tanggapan petugas diterima</span>
                      <span class="text-[11px] text-orange-800 mt-0.5 block italic">"{{ borrow.staff_comment }}"</span>
                    </div>
                    
                    <!-- Kondisi baik -->
                    <div v-else-if="borrow.is_damaged === false && hasNoPayment(borrow)" class="text-slate-900 border rounded-md p-2.5 text-xs">
                      <span class="font-semibold block">✅ Kondisi buku sudah dilaporkan</span>
                      <span class="text-[11px] text-slate-700 mt-0.5 block">Buku dalam kondisi baik. Silakan lanjut ke pembayaran.</span>
                    </div>
                  </div>

                  <!-- Step 2: Bayar Denda -->
                  <div class="p-4 bg-slate-50 border border-slate-100 rounded-lg">
                    <div class="flex items-center gap-2.5 mb-2">
                      <div class="w-6 h-6 rounded-md bg-slate-400 text-white flex items-center justify-center font-bold text-xs">2</div>
                      <p class="text-xs font-bold text-slate-800">Bayar Denda Keterlambatan</p>
                    </div>
                    
                    <!-- Tombol bayar -->
                    <Link 
                      v-if="hasNoPayment(borrow) && (borrow.is_damaged === false || (borrow.is_damaged === true && borrow.damage_status === 'responded'))"
                      :href="`/fines/${borrow.id}/pay`"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-md text-xs font-medium transition-all shadow-sm"
                    >
                      Bayar Denda
                    </Link>
                    
                    <!-- Payment pending -->
                    <div v-else-if="borrow.payment_status === 'pending'" class="space-y-2">
                      <div class="bg-white border border-orange-100 text-orange-900 rounded-md p-2.5 text-xs">
                        <span class="font-semibold block">Pembayaran Sedang Diproses</span>
                        <span v-if="borrow.payment_method && borrow.payment_method !== 'none'" class="text-[11px] text-orange-700 block mt-0.5">
                          Metode: {{ borrow.payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                        </span>
                      </div>
                      <Link 
                        :href="`/fines/${borrow.id}/waiting-payment`"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-md text-xs font-medium transition-all shadow-sm"
                      >
                        Lanjut ke Pembayaran
                      </Link>
                    </div>
                    
                    <!-- Confirmed -->
                    <div v-else-if="borrow.payment_status === 'confirmed'" class="bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-md p-2.5 text-xs font-semibold">
                      ✅ Pembayaran Selesai
                    </div>
                    
                    <!-- Belum lapor kondisi buku -->
                    <div v-else class="bg-slate-100/80 text-slate-400 rounded-md p-2.5 text-[11px]">
                      ⚠️ Selesaikan pelaporan kondisi buku terlebih dahulu (Langkah 1)
                    </div>
                  </div>

                  <!-- Additional Fine Info (Diubah dari bg-red-50/50 menjadi bg-red-100/70 & border-red-200) -->
                  <div v-if="borrow.additional_fine > 0" class="p-4 bg-red-100/70 border border-red-200 rounded-lg">
                    <p class="text-xs font-bold text-red-900">Denda Tambahan Kerusakan</p>
                    <p class="text-base font-black text-red-700 mt-0.5">Rp {{ formatNumber(borrow.additional_fine) }}</p>
                    <p v-if="borrow.staff_comment" class="text-[10px] text-red-600/90 mt-1 italic font-medium">"{{ borrow.staff_comment }}"</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- CASE 3: Not Overdue & Not Yet Returned -->
            <div v-else-if="!borrow.return_date && !isOverdue(borrow.due_date)" class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-bold text-emerald-800">Status Keuangan</p>
                  <p class="text-[11px] text-slate-500">Tidak ada denda. Masa peminjaman aktif normal.</p>
                </div>
              </div>
              <span class="text-base font-bold text-emerald-600">Rp 0</span>
            </div>

            <!-- Rejection Reason (Diubah dari bg-red-50/60 menjadi bg-red-100/70 & border-red-200) -->
            <div v-if="borrow.rejection_reason" class="p-4 bg-red-100/70 border border-red-200 rounded-xl">
              <p class="text-[9px] font-bold text-red-700 uppercase tracking-wider mb-0.5">Alasan Penolakan</p>
              <p class="text-xs text-red-900 font-semibold leading-relaxed">{{ borrow.rejection_reason }}</p>
            </div>

            <!-- Notes -->
            <div v-if="borrow.notes" class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Catatan Tambahan</p>
              <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ borrow.notes }}</p>
            </div>

          </div>

          <!-- Footer -->
          <div class="border-t border-slate-100 pt-6 flex flex-col md:flex-row md:justify-between items-center gap-4">
            <p class="text-[10px] text-slate-400 leading-relaxed text-center md:text-left max-w-sm">
              Dokumen digital ini merupakan bukti transaksi resmi sistem SKIE-LIBRARY segala bentuk keterlambatan mengikuti aturan administratif yang berlaku.
            </p>
            <Link 
              href="/my-loans" 
              class="text-xs font-semibold uppercase tracking-wider text-slate-400 hover:text-orange-500 transition-colors"
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
  const d = new Date(date);
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
}

function isOverdue(dueDate) {
  if (!dueDate) return false;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const due = new Date(dueDate);
  due.setHours(0, 0, 0, 0);
  return due < today;
}

function calculateLateDays(dueDate) {
  if (!dueDate) return 0;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const due = new Date(dueDate);
  due.setHours(0, 0, 0, 0);
  const diffTime = today.getTime() - due.getTime();
  return Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
}

function calculateLateFee(dueDate) {
  return calculateLateDays(dueDate) * 1000;
}

function getStatusDotClass(status) {
  const dots = {
    pending: 'bg-amber-500',
    approved: 'bg-blue-500',
    borrowed: 'bg-orange-500',
    returned: 'bg-emerald-500',
    overdue: 'bg-red-600',
    lost: 'bg-slate-400',
    rejected: 'bg-red-600'
  };
  return dots[status] || 'bg-slate-400';
}

function getPaymentStatusClass(status) {
  const classes = {
    pending: 'bg-orange-50 border border-orange-100 text-orange-700',
    confirmed: 'bg-emerald-50 border border-emerald-100 text-emerald-700',
    failed: 'bg-red-100 text-red-700 border border-red-200'
  };
  return classes[status] || 'bg-slate-50 text-slate-600';
}

function getPaymentStatusLabel(status) {
  const labels = {
    pending: 'Menunggu Pembayaran',
    confirmed: 'Lunas',
    failed: 'Gagal'
  };
  return labels[status] || status;
}

function hasNoPayment(borrow) {
  return !borrow.payment_status || borrow.payment_status === 'unpaid';
}

function getDamageLabel(type) {
  const labels = {
    lost: 'Hilang',
    wet: 'Basah',
    torn: 'Sobek',
    stained: 'Ternoda',
    other: 'Lainnya'
  };
  return labels[type] || type;
}
</script>