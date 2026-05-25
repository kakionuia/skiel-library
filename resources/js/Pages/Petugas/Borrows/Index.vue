<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen pb-12 relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Kelola Peminjaman</h1>
            <p class="text-slate-500 text-sm">Sistem manajemen sirkulasi buku perpustakaan</p>
          </div>
          <div class="flex items-center gap-6">
            <div class="text-right border-r pr-6 border-slate-200 hidden sm:block">
              <p class="text-sm font-bold text-slate-700">{{ formattedDate }}</p>
              <p class="text-[10px] text-slate-400 uppercase tracking-[0.2em] font-black">Waktu Server</p>
            </div>
          </div>
        </div>
      </header>

      <div class="px-10 py-8 max-w-[1400px] mx-auto">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Peminjaman Aktif</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.activeCount }}</h3>
              <div class="p-3 rounded-2xl bg-orange-50 text-orange-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Menunggu Persetujuan</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.pendingCount || 0 }}</h3>
              <div class="p-3 rounded-2xl bg-yellow-50 text-yellow-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Terindikasi Terlambat</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.overdueCount }}</h3>
              <div class="p-3 rounded-2xl bg-red-50 text-red-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Total Peminjam</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.totalBorrowers }}</h3>
              <div class="p-3 rounded-2xl bg-rose-50 text-rose-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292M15 21H3a6 6 0 016-6h6a6 6 0 016 6v0"/></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-slate-200/60 p-4 mb-6 flex flex-wrap items-center gap-4 shadow-sm">
          <div class="relative flex-1 min-w-[300px]">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input v-model="searchQuery" type="text" placeholder="Cari peminjam atau judul buku..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-slate-200 transition-all text-sm">
          </div>
          <select v-model="selectedStatus" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 focus:ring-2 focus:ring-slate-200 transition-all">
            <option value="">Semua Status</option>
            <option value="pending">Menunggu Persetujuan</option>
            <option value="approved">Disetujui</option>
            <option value="borrowed">Aktif</option>
            <option value="returned">Dikembalikan</option>
            <option value="overdue">Terlambat</option>
            <option value="rejected">Ditolak</option>
          </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Peminjam</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi Buku</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tgl Pinjam</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Batas Waktu</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Denda</th>
                  <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="loan in filteredBorrows" :key="loan.id" class="hover:bg-slate-50/50 transition-colors group">
                  <!-- Peminjam -->
                  <td class="px-8 py-5">
                    <button @click="openDetailModal(loan)" class="flex items-center gap-4 text-left hover:opacity-80 transition">
                      <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center font-bold text-xs border border-orange-100">
                        {{ loan.member_name?.[0]?.toUpperCase() }}
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-800 hover:text-orange-600 transition">{{ loan.member_name }}</p>
                        <p class="text-[11px] text-slate-400">{{ loan.member_id }}</p>
                      </div>
                    </button>
                  </td>
                  <!-- Buku -->
                  <td class="px-8 py-5">
                    <p class="text-sm font-bold text-slate-700">{{ loan.book_title }}</p>
                    <p class="text-xs text-slate-400">{{ loan.book_isbn }}</p>
                    <p class="text-xs text-slate-400">Qty: {{ loan.quantity || 1 }}</p>
                  </td>
                  <!-- Tgl Pinjam -->
                  <td class="px-8 py-5 text-sm font-medium text-slate-600">
                    {{ formatDate(loan.borrow_date) }}
                  </td>
                  <!-- Batas Waktu -->
                  <td class="px-8 py-5 text-sm font-medium" :class="isOverdue(loan) ? 'text-red-600' : 'text-slate-600'">
                    <div>
                      {{ formatDate(loan.due_date) }}
                    </div>
                    <div v-if="isOverdue(loan)" class="text-xs text-red-500 font-bold">
                      Terlambat {{ getOverdueDays(loan) }} hari
                    </div>
                  </td>
                  <!-- Status -->
                  <td class="px-8 py-5">
                    <span :class="getStatusClass(loan.status)" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider whitespace-nowrap">
                      {{ getStatusText(loan.status) }}
                    </span>
                  </td>
                  <!-- Denda -->
                  <td class="px-8 py-5 text-sm font-bold" :class="loan.late_fee > 0 ? 'text-red-600' : 'text-slate-400'">
                    {{ loan.late_fee > 0 ? 'Rp ' + formatNumber(loan.late_fee) : '-' }}
                  </td>
                  <!-- Aksi -->
                  <td class="px-8 py-5">
                    <div class="flex items-center justify-center gap-2">
                      <!-- Approve button untuk pending -->
                      <button 
                        v-if="loan.status === 'pending'" 
                        @click="approveLoan(loan)" 
                        class="p-2 hover:bg-emerald-50 text-emerald-600 rounded-xl transition-all border border-transparent hover:border-emerald-100"
                        title="Setujui Peminjaman"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </button>
                      
                      <!-- Reject button untuk pending -->
                      <button 
                        v-if="loan.status === 'pending'" 
                        @click="openRejectModal(loan)" 
                        class="p-2 hover:bg-red-50 text-red-600 rounded-xl transition-all border border-transparent hover:border-red-100"
                        title="Tolak Peminjaman"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>

                      <!-- Confirm Pickup untuk approved -->
                      <button 
                        v-if="loan.status === 'approved'" 
                        @click="confirmPickup(loan)" 
                        class="p-2 hover:bg-blue-50 text-blue-600 rounded-xl transition-all border border-transparent hover:border-blue-100"
                        title="Konfirmasi Pengambilan Buku"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                      </button>

                      <!-- Return button untuk approved/borrowed -->
                      <button 
                        v-if="loan.status === 'borrowed' || loan.status === 'approved'" 
                        @click="returnBook(loan)" 
                        class="p-2 hover:bg-purple-50 text-purple-600 rounded-xl transition-all border border-transparent hover:border-purple-100"
                        title="Proses Pengembalian"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                      </button>

                      <!-- Detail button -->
                      <button 
                        @click="openDetailModal(loan)" 
                        class="p-2 hover:bg-slate-100 text-slate-400 rounded-xl transition-all border border-transparent hover:border-slate-200"
                        title="Lihat Detail"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <!-- Empty State -->
                <tr v-if="filteredBorrows.length === 0">
                  <td colspan="7" class="px-8 py-20 text-center">
                    <div class="text-4xl mb-3">📋</div>
                    <p class="text-slate-500 font-medium">Tidak ada data peminjaman</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>

    <!-- Detail Modal -->
    <Transition name="fade">
      <div v-if="showDetailModal" class="fixed inset-0 z-[60] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm" @click.self="showDetailModal = false">
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200 max-h-[90vh] overflow-y-auto">
          <div class="px-10 pt-10 pb-4 flex justify-between items-start">
            <div>
              <h3 class="text-xl font-black text-slate-900">Detail Peminjaman</h3>
              <p class="text-sm text-slate-500 mt-1">No. Peminjaman: #{{ selectedLoan?.id }}</p>
            </div>
            <button @click="showDetailModal = false" class="p-2 hover:bg-slate-100 rounded-xl transition">
              <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          
          <div v-if="selectedLoan" class="p-10 space-y-6">
            <!-- Status Timeline -->
            <div class="bg-slate-50 rounded-2xl p-6">
              <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Status Peminjaman</h4>
              <div class="flex items-center gap-2">
                <div class="flex-1 text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center" :class="getTimelineColor(selectedLoan, 'pending')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                  </div>
                  <p class="text-[10px] mt-1 font-bold">Pengajuan</p>
                </div>
                <div class="w-8 h-px bg-slate-300"></div>
                <div class="flex-1 text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center" :class="getTimelineColor(selectedLoan, 'approved')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <p class="text-[10px] mt-1 font-bold">Disetujui</p>
                </div>
                <div class="w-8 h-px bg-slate-300"></div>
                <div class="flex-1 text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center" :class="getTimelineColor(selectedLoan, 'borrowed')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
                  </div>
                  <p class="text-[10px] mt-1 font-bold">Dipinjam</p>
                </div>
                <div class="w-8 h-px bg-slate-300"></div>
                <div class="flex-1 text-center">
                  <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center" :class="getTimelineColor(selectedLoan, 'returned')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                  </div>
                  <p class="text-[10px] mt-1 font-bold">Kembali</p>
                </div>
              </div>
            </div>

            <!-- Detail Info -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-black mb-1">Peminjam</p>
                <p class="text-sm font-bold text-slate-900">{{ selectedLoan.member_name }}</p>
                <p class="text-xs text-slate-500">{{ selectedLoan.member_id }}</p>
              </div>
              <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-black mb-1">Buku</p>
                <p class="text-sm font-bold text-slate-900">{{ selectedLoan.book_title }}</p>
                <p class="text-xs text-slate-500">{{ selectedLoan.book_isbn }}</p>
              </div>
              <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-black mb-1">Tanggal Pinjam</p>
                <p class="text-sm font-bold text-slate-900">{{ formatDate(selectedLoan.borrow_date) }}</p>
              </div>
              <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-black mb-1">Jatuh Tempo</p>
                <p class="text-sm font-bold" :class="isOverdue(selectedLoan) ? 'text-red-600' : 'text-slate-900'">{{ formatDate(selectedLoan.due_date) }}</p>
              </div>
              <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-black mb-1">Jumlah</p>
                <p class="text-sm font-bold text-slate-900">{{ selectedLoan.quantity || 1 }} buku</p>
              </div>
              <div class="bg-slate-50 rounded-2xl p-4">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider font-black mb-1">Denda</p>
                <p class="text-sm font-bold" :class="selectedLoan.late_fee > 0 ? 'text-red-600' : 'text-slate-900'">
                  {{ selectedLoan.late_fee > 0 ? 'Rp ' + formatNumber(selectedLoan.late_fee) : '-' }}
                </p>
              </div>
            </div>

            <!-- Catatan -->
            <div v-if="selectedLoan.notes" class="bg-orange-50 rounded-2xl p-4 border border-orange-100">
              <p class="text-[10px] text-orange-400 uppercase tracking-wider font-black mb-1">Catatan Peminjam</p>
              <p class="text-sm text-orange-700">{{ selectedLoan.notes }}</p>
            </div>

            <!-- Rejection Reason -->
            <div v-if="selectedLoan.status === 'rejected' && selectedLoan.rejection_reason" class="bg-red-50 rounded-2xl p-4 border border-red-100">
              <p class="text-[10px] text-red-400 uppercase tracking-wider font-black mb-1">Alasan Penolakan</p>
              <p class="text-sm text-red-600">{{ selectedLoan.rejection_reason }}</p>
            </div>

            <!-- Damage Information -->
            <div v-if="selectedLoan.is_damaged" class="bg-red-50 rounded-2xl p-4 border border-red-200">
              <p class="text-[10px] text-red-400 uppercase tracking-wider font-black mb-3">Laporan Kerusakan</p>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-red-700">Jenis Kerusakan:</span>
                  <span class="font-bold text-red-900">{{ getDamageTypeLabel(selectedLoan.damage_type) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-red-700">Status:</span>
                  <span class="font-bold text-red-900">{{ getDamageStatusLabel(selectedLoan.damage_status) }}</span>
                </div>
                <div v-if="selectedLoan.additional_fine" class="flex justify-between">
                  <span class="text-red-700">Denda Tambahan:</span>
                  <span class="font-bold text-red-900">Rp {{ formatNumber(selectedLoan.additional_fine) }}</span>
                </div>
                <div v-if="selectedLoan.staff_comment" class="pt-2 border-t border-red-200">
                  <p class="text-red-700 text-xs mb-1">Komentar Petugas:</p>
                  <p class="text-red-800 text-xs italic">{{ selectedLoan.staff_comment }}</p>
                </div>
              </div>
            </div>

            <!-- Mark Damage Resolved Button -->
            <div v-if="selectedLoan.is_damaged && selectedLoan.damage_status === 'responded' && selectedLoan.payment_status === 'confirmed'" class="flex gap-3">
              <button 
                @click="markDamageResolved(selectedLoan)" 
                class="flex-1 px-6 py-4 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-2xl transition-all shadow-xl shadow-emerald-200"
              >
                ✓ Tandai Masalah Selesai
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Reject Modal -->
    <Transition name="fade">
      <div v-if="showRejectModal" class="fixed inset-0 z-[60] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm" @click.self="showRejectModal = false">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200">
          <div class="px-10 pt-10 pb-4">
            <h3 class="text-xl font-black text-slate-900">Tolak Peminjaman</h3>
            <p class="text-sm text-slate-500 mt-1">Berikan alasan penolakan</p>
          </div>
          <div class="p-10 space-y-6">
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alasan Penolakan</label>
              <textarea v-model="rejectionReason" rows="4" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-red-500/20 resize-none" placeholder="Tulis alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-4">
              <button @click="showRejectModal = false" class="flex-1 px-6 py-4 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-2xl transition-all">Batal</button>
              <button @click="confirmReject" class="flex-1 px-6 py-4 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-2xl transition-all shadow-xl shadow-red-200">Tolak Peminjaman</button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import Swal from 'sweetalert2';
import { confirmDialog, dangerConfirm, successAlert } from '@/Utils/sweetAlert';
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  borrows: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const selectedStatus = ref('');
const formattedDate = ref('');
const showDetailModal = ref(false);
const showRejectModal = ref(false);
const selectedLoan = ref(null);
const rejectionReason = ref('');

const filteredBorrows = computed(() => {
  return props.borrows.filter(loan => {
    const matchesSearch = loan.member_name?.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         loan.book_title?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = selectedStatus.value ? loan.status === selectedStatus.value : true;
    return matchesSearch && matchesStatus;
  });
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatNumber = (number) => {
  return new Intl.NumberFormat('id-ID').format(number);
};

const isOverdue = (loan) => {
  if (!loan.due_date) return false;
  if (loan.status === 'returned') return false;
  return new Date(loan.due_date) < new Date();
};

const getOverdueDays = (loan) => {
  if (!loan.due_date) return 0;
  const due = new Date(loan.due_date);
  const now = new Date();
  return Math.floor((now - due) / (1000 * 60 * 60 * 24));
};

const getStatusClass = (status) => {
  switch(status) {
    case 'pending': return 'bg-yellow-50 text-yellow-600 border border-yellow-100';
    case 'approved': return 'bg-blue-50 text-blue-600 border border-blue-100';
    case 'borrowed': return 'bg-orange-50 text-orange-600 border border-orange-100';
    case 'returned': return 'bg-emerald-50 text-emerald-600 border border-emerald-100';
    case 'overdue': return 'bg-rose-50 text-rose-600 border border-rose-100';
    case 'rejected': return 'bg-red-50 text-red-600 border border-red-100';
    default: return 'bg-slate-50 text-slate-600';
  }
};

const getStatusText = (status) => {
  switch(status) {
    case 'pending': return 'Menunggu';
    case 'approved': return 'Disetujui';
    case 'borrowed': return 'Aktif';
    case 'returned': return 'Kembali';
    case 'overdue': return 'Terlambat';
    case 'rejected': return 'Ditolak';
    default: return status;
  }
};

const getTimelineColor = (loan, stage) => {
  const stages = ['pending', 'approved', 'borrowed', 'returned'];
  const currentIndex = stages.indexOf(loan.status);
  const stageIndex = stages.indexOf(stage);
  
  if (loan.status === 'rejected') {
    if (stage === 'pending') return 'bg-red-100 text-red-600';
    return 'bg-slate-100 text-slate-400';
  }
  
  if (stageIndex <= currentIndex) {
    return 'bg-green-100 text-green-600';
  }
  return 'bg-slate-100 text-slate-400';
};

// Actions
const approveLoan = async (loan) => {
  const result = await confirmDialog({
    title: 'Setujui Peminjaman?',
    html: `<p class="text-sm">Buku <strong>${loan.book_title}</strong> untuk <strong>${loan.member_name}</strong> akan disetujui.</p>`,
    confirmButtonText: 'Ya, Setujui',
  });
  
  if (result) {
    router.post(`/petugas/borrows/${loan.id}/approve`, {}, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Peminjaman berhasil disetujui.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menyetujui peminjaman.',
          icon: 'error',
        });
      }
    });
  }
};

const openRejectModal = (loan) => {
  selectedLoan.value = loan;
  rejectionReason.value = '';
  showRejectModal.value = true;
};

const confirmReject = async () => {
  if (!rejectionReason.value.trim()) {
    Swal.fire({
      title: 'Peringatan!',
      text: 'Harap isi alasan penolakan.',
      icon: 'warning',
    });
    return;
  }
  
  const result = await dangerConfirm({
    title: 'Tolak Peminjaman?',
    html: `<p class="text-sm mb-3">Buku <strong>${selectedLoan.value.book_title}</strong> akan ditolak.</p><p class="text-xs bg-red-50 border border-red-200 p-2 rounded text-red-800"><strong>Alasan:</strong> ${rejectionReason.value}</p>`,
    confirmButtonText: 'Ya, Tolak',
  });
  
  if (result) {
    router.post(`/petugas/borrows/${selectedLoan.value.id}/reject`, {
      rejection_reason: rejectionReason.value
    }, {
      onSuccess: () => {
        showRejectModal.value = false;
        selectedLoan.value = null;
        successAlert({
          title: 'Berhasil!',
          text: 'Peminjaman berhasil ditolak.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menolak peminjaman.',
          icon: 'error',
        });
      }
    });
  }
};

const confirmPickup = async (loan) => {
  const result = await confirmDialog({
    title: 'Konfirmasi Pengambilan?',
    html: `<p class="text-sm">Konfirmasi bahwa buku <strong>${loan.book_title}</strong> sudah diambil oleh <strong>${loan.member_name}</strong>.</p>`,
    confirmButtonText: 'Ya, Konfirmasi',
  });
  
  if (result) {
    router.post(`/petugas/borrows/${loan.id}/confirm-pickup`, {}, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Pengambilan buku berhasil dikonfirmasi.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal mengkonfirmasi.',
          icon: 'error',
        });
      }
    });
  }
};

const returnBook = async (loan) => {
  const result = await confirmDialog({
    title: 'Proses Pengembalian Buku?',
    html: `<p class="text-sm">Buku <strong>${loan.book_title}</strong> dari <strong>${loan.member_name}</strong> akan dikembalikan.</p>`,
    confirmButtonText: 'Ya, Kembalikan',
  });
  
  if (result) {
    router.post(`/petugas/borrows/${loan.id}/return`, {
      return_date: new Date().toISOString().split('T')[0]
    }, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Buku berhasil dikembalikan!',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal memproses pengembalian.',
          icon: 'error',
        });
      }
    });
  }
};

const markDamageResolved = async (loan) => {
  const result = await confirmDialog({
    title: 'Tandai Masalah Kerusakan Selesai?',
    html: `<p class="text-sm">Kerusakan buku <strong>${loan.book_title}</strong> dari <strong>${loan.member_name}</strong> sudah dibayar dan akan ditandai selesai.</p>`,
    confirmButtonText: 'Ya, Tandai Selesai',
  });
  
  if (result) {
    router.post(`/petugas/damage-reports/${loan.id}/resolve`, {}, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Status kerusakan berhasil diperbarui menjadi selesai.',
        });
        showDetailModal.value = false;
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal memperbarui status.',
          icon: 'error',
        });
      }
    });
  }
};

const getDamageTypeLabel = (type) => {
  const labels = {
    lost: '🗑️ Hilang',
    wet: '💧 Basah',
    torn: '✂️ Sobek',
    stained: '🔴 Ternoda',
    other: '❓ Lainnya'
  };
  return labels[type] || type;
};

const getDamageStatusLabel = (status) => {
  const labels = {
    pending: '⏳ Menunggu Verifikasi',
    responded: '📋 Sudah Direspons',
    resolved: '✅ Selesai'
  };
  return labels[status] || status;
};

const openDetailModal = (loan) => {
  selectedLoan.value = loan;
  showDetailModal.value = true;
};

onMounted(() => {
  formattedDate.value = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>