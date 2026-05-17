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
        
        <!-- Sidebar -->
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
            
            <!-- Payment Status Badge -->
            <div v-if="borrow.payment_status && borrow.payment_status !== 'unpaid'" class="mt-3 pt-3 border-t border-slate-100">
              <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-2">Status Pembayaran</p>
              <span 
                class="px-3 py-1 rounded-full text-xs font-bold"
                :class="getPaymentStatusClass(borrow.payment_status)"
              >
                {{ getPaymentStatusLabel(borrow.payment_status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-10 bg-white border border-slate-200/80 rounded-3xl p-10 shadow-sm">
          
          <!-- Book Info Header -->
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

          <!-- Timeline -->
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

          <!-- Detail Info -->
          <div class="border-t border-slate-100 pt-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
            <div class="space-y-1">
              <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Anggota</label>
              <p class="text-base font-bold text-slate-800">{{ borrow.member?.name || 'Anggota Perpustakaan' }}</p>
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
              <p :class="isOverdue(borrow.due_date) && !borrow.return_date ? 'text-red-600' : 'text-orange-600'" class="text-base font-black">
                {{ formatDate(borrow.due_date) }}
              </p>
            </div>
          </div>

          <!-- FINE & PAYMENT SECTION -->
          <div class="space-y-4 pt-4">
            
            <!-- CASE 1: Book already returned -->
            <div v-if="borrow.return_date" class="p-5 bg-green-50 border border-green-100 rounded-2xl">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div class="h-11 w-11 rounded-xl bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs font-extrabold uppercase text-green-600 tracking-wider">✅ Buku Sudah Dikembalikan</p>
                    <p class="text-sm text-green-700">Tanggal: {{ formatDate(borrow.return_date) }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-xs text-slate-500">Total Denda</p>
                  <p :class="borrow.late_fee > 0 ? 'text-red-600' : 'text-green-600'" class="text-xl font-black">
                    Rp {{ formatNumber(borrow.late_fee || 0) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- CASE 2: Overdue & Not Yet Returned -->
            <div v-else-if="isOverdue(borrow.due_date) && !borrow.return_date" class="space-y-4">
              <!-- Late Warning -->
              <div class="p-6 bg-red-50 border border-red-200 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                  <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-xs font-extrabold uppercase text-red-500 tracking-wider">⚠️ Peringatan Keterlambatan</p>
                      <p class="text-sm text-red-700 font-bold">Terlambat {{ calculateLateDays(borrow.due_date) }} Hari</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-xs text-slate-600 font-semibold">Total Denda</p>
                    <p class="text-2xl font-black text-red-600">
                      Rp {{ formatNumber(borrow.late_fee || calculateLateFee(borrow.due_date)) }}
                    </p>
                    <p class="text-[10px] text-slate-500 mt-1">@ Rp 1.000/hari</p>
                  </div>
                </div>
                
                <div class="mt-4 p-4 bg-white rounded-xl">
                  <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-600">Jatuh Tempo:</span>
                    <span class="font-bold text-red-600">{{ formatDate(borrow.due_date) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Hari ini:</span>
                    <span class="font-bold text-slate-800">{{ formatDate(new Date()) }}</span>
                  </div>
                </div>
              </div>

              <!-- Damage Report & Payment Info -->
              <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 mb-4">Tindakan yang Diperlukan</h3>
                
                <div class="space-y-4">
                  <!-- Step 1: Laporkan kondisi buku -->
                  <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="w-8 h-8 rounded-full bg-orange-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                      <p class="text-sm font-bold text-orange-900">Laporkan Kondisi Buku</p>
                    </div>
                    <p class="text-xs text-orange-700 mb-3">Anda harus melaporkan kondisi buku sebelum melakukan pembayaran denda.</p>
                    
                    <!-- Jika belum lapor kerusakan -->
                    <Link 
                      v-if="borrow.is_damaged === null || borrow.is_damaged === undefined"
                      :href="`/fines/${borrow.id}/report-damage`"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold transition-all"
                    >
                      📋 Laporkan Kondisi Buku
                    </Link>
                    
                    <!-- Jika sudah lapor & menunggu konfirmasi -->
                    <div v-else-if="borrow.is_damaged === true && borrow.damage_status === 'pending'" class="bg-yellow-100 border border-yellow-200 rounded-lg p-3">
                      <p class="text-xs font-bold text-yellow-800">⏳ Menunggu konfirmasi petugas</p>
                      <p class="text-[10px] text-yellow-700 mt-1">Laporan kerusakan Anda sedang ditinjau oleh petugas.</p>
                      <p v-if="borrow.damage_type" class="text-[10px] text-yellow-700 mt-1">Jenis: {{ getDamageLabel(borrow.damage_type) }}</p>
                    </div>
                    <div v-else-if="borrow.is_damaged === true && borrow.damage_status === 'responded' && hasNoPayment(borrow)" class="bg-blue-100 border border-blue-200 rounded-lg p-3">
                      <p class="text-xs font-bold text-blue-800">Tanggapan petugas diterima</p>
                      <p class="text-[10px] text-blue-700 mt-1">{{ borrow.staff_comment }}</p>
                    </div>
                    
                    <!-- Jika tidak rusak, langsung bisa bayar -->
                    <div v-else-if="borrow.is_damaged === false && hasNoPayment(borrow)" class="bg-green-100 border border-green-200 rounded-lg p-3">
                      <p class="text-xs font-bold text-green-800">✅ Kondisi buku sudah dilaporkan</p>
                      <p class="text-[10px] text-green-700 mt-1">Buku dalam kondisi baik. Silakan lanjut ke pembayaran.</p>
                    </div>
                  </div>

                  <!-- Step 2: Bayar Denda -->
                  <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="w-8 h-8 rounded-full bg-slate-600 text-white flex items-center justify-center font-bold text-sm">2</div>
                      <p class="text-sm font-bold text-slate-900">Bayar Denda Keterlambatan</p>
                    </div>
                    
                    <!-- Tombol bayar hanya muncul jika kondisi buku sudah dilaporkan dan tidak rusak/pending -->
                    <Link 
                      v-if="hasNoPayment(borrow) && (borrow.is_damaged === false || (borrow.is_damaged === true && borrow.damage_status === 'responded'))"
                      :href="`/fines/${borrow.id}/pay`"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all"
                    >
                      💳 Bayar Denda
                    </Link>
                    
                    <!-- Jika payment pending -->
                    <div v-else-if="borrow.payment_status === 'pending'" class="space-y-2">
                      <div class="bg-yellow-100 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs font-bold text-yellow-800">⏳ Pembayaran Sedang Diproses</p>
                        <p class="text-[10px] text-yellow-700 mt-1">
                          Metode: {{ borrow.payment_method === 'qris' ? '📱 QRIS' : '💵 Tunai' }}
                        </p>
                      </div>
                      <Link 
                        :href="`/fines/${borrow.id}/waiting-payment`"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-xs font-bold transition-all animate-pulse"
                      >
                        ⏳ Lanjut ke Pembayaran
                      </Link>
                    </div>
                    
                    <!-- Jika sudah confirmed -->
                    <div v-else-if="borrow.payment_status === 'confirmed'" class="bg-green-100 border border-green-200 rounded-lg p-3">
                      <p class="text-xs font-bold text-green-800">✅ Pembayaran Selesai</p>
                    </div>
                    
                    <!-- Jika belum lapor kondisi buku -->
                    <div v-else class="bg-slate-100 border border-slate-200 rounded-lg p-3">
                      <p class="text-xs text-slate-500">⚠️ Laporkan kondisi buku terlebih dahulu (Step 1)</p>
                    </div>
                  </div>

                  <!-- Additional Fine Info -->
                  <div v-if="borrow.additional_fine > 0" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs font-bold text-red-800 mb-2">Denda Tambahan Kerusakan</p>
                    <p class="text-lg font-black text-red-600">Rp {{ formatNumber(borrow.additional_fine) }}</p>
                    <p v-if="borrow.staff_comment" class="text-[10px] text-red-600 mt-1 italic">"{{ borrow.staff_comment }}"</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- CASE 3: Not Overdue & Not Yet Returned -->
            <div v-else-if="!borrow.return_date && !isOverdue(borrow.due_date)" class="p-5 bg-green-50 border border-green-100 rounded-2xl flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-extrabold uppercase text-green-600 tracking-wider">Status Keuangan</p>
                  <p class="text-sm text-slate-500">Tidak ada denda. Buku dalam masa peminjaman normal.</p>
                </div>
              </div>
              <span class="text-lg font-black text-green-600">Rp 0</span>
            </div>

            <!-- Rejection Reason -->
            <div v-if="borrow.rejection_reason" class="p-5 bg-red-50 border border-red-100 rounded-2xl">
              <p class="text-xs font-extrabold text-red-500 uppercase tracking-widest mb-1">Alasan Penolakan</p>
              <p class="text-sm text-red-800 leading-relaxed font-semibold">{{ borrow.rejection_reason }}</p>
            </div>

            <!-- Notes -->
            <div v-if="borrow.notes" class="p-5 bg-slate-50 border border-slate-200/60 rounded-2xl">
              <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-1">Catatan Tambahan</p>
              <p class="text-sm text-slate-600 leading-relaxed font-semibold">{{ borrow.notes }}</p>
            </div>

          </div>

          <!-- Footer -->
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

function getPaymentStatusClass(status) {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-slate-100 text-slate-600';
}

function getPaymentStatusLabel(status) {
  const labels = {
    pending: '⏳ Menunggu Pembayaran',
    confirmed: '✅ Lunas',
    failed: '❌ Gagal'
  };
  return labels[status] || status;
}

function hasNoPayment(borrow) {
  return !borrow.payment_status || borrow.payment_status === 'unpaid';
}

function getDamageLabel(type) {
  const labels = {
    lost: '📕 Hilang',
    wet: '💧 Basah',
    torn: '📄 Sobek',
    stained: '🎨 Ternoda',
    other: '📋 Lainnya'
  };
  return labels[type] || type;
}
</script>
