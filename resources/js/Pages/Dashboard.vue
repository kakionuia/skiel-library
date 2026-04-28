<template>
  <UserLayout>
    <div class="space-y-8">
      <!-- Header Section -->
      <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-black text-slate-900 mb-2">Selamat Datang, {{ user.name }}! 👋</h1>
            <p class="text-slate-600">Dashboard perpustakaan Anda - kelola peminjaman dan jelajahi koleksi buku</p>
          </div>
          <div class="hidden lg:block">
            <div class="text-right">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Status Akun</p>
              <span :class="{
                'bg-emerald-100 text-emerald-700': user.status === 'approved',
                'bg-amber-100 text-amber-700': user.status === 'pending',
                'bg-rose-100 text-rose-700': user.status === 'rejected'
              }" class="inline-block px-4 py-2 rounded-full font-bold text-sm mt-1">
                {{ user.status === 'approved' ? '✓ Disetujui' : user.status === 'pending' ? '⏳ Menunggu' : '✕ Ditolak' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-end justify-between">
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Buku Sedang Dipinjam</p>
              <h3 class="text-4xl font-black text-indigo-600">{{ activeBorrows.length }}</h3>
              <p class="text-xs text-slate-500 mt-1">dari {{ maxBooksPerUser }} buku maksimal</p>
            </div>
            <div class="p-4 bg-indigo-50 rounded-2xl">
              <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-end justify-between">
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Buku Terlambat</p>
              <h3 class="text-4xl font-black text-rose-600">{{ overdueCount }}</h3>
              <p class="text-xs text-slate-500 mt-1">{{ totalFines.toLocaleString('id-ID', {style: 'currency', currency: 'IDR'}) }}</p>
            </div>
            <div class="p-4 bg-rose-50 rounded-2xl">
              <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all">
          <div class="flex items-end justify-between">
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Peminjaman</p>
              <h3 class="text-4xl font-black text-emerald-600">{{ totalBorrows }}</h3>
              <p class="text-xs text-slate-500 mt-1">sepanjang waktu</p>
            </div>
            <div class="p-4 bg-emerald-50 rounded-2xl">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Borrows -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
          <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Buku Yang Sedang Dipinjam
          </h2>
          <div class="space-y-3">
            <div v-for="borrow in activeBorrows" :key="borrow.id" class="p-4 bg-slate-50 rounded-xl border border-slate-100 hover:border-indigo-200 transition-all">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <p class="font-bold text-slate-900">{{ borrow.book?.title || 'Buku' }}</p>
                  <p class="text-sm text-slate-600">{{ borrow.book?.author || '-' }}</p>
                  <div class="mt-3 flex gap-4 text-xs text-slate-500">
                    <span>📅 Dipinjam: {{ formatDate(borrow.borrow_date) }}</span>
                    <span>📌 Kembali: {{ formatDate(borrow.due_date) }}</span>
                  </div>
                </div>
                <span :class="{
                  'bg-emerald-100 text-emerald-700': daysUntilDue(borrow.due_date) > 3,
                  'bg-amber-100 text-amber-700': daysUntilDue(borrow.due_date) > 0 && daysUntilDue(borrow.due_date) <= 3,
                  'bg-rose-100 text-rose-700': daysUntilDue(borrow.due_date) <= 0
                }" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                  {{ daysUntilDue(borrow.due_date) > 0 ? daysUntilDue(borrow.due_date) + ' hari' : 'Sudah Terlambat' }}
                </span>
              </div>
            </div>
            <div v-if="activeBorrows.length === 0" class="text-center py-8 text-slate-500">
              <p class="text-sm">Anda belum meminjam buku. 📚</p>
            </div>
          </div>
        </div>

        <!-- Profile Card -->
        <div class="space-y-6">
          <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl border border-indigo-200 p-6">
            <div class="text-center">
              <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-3">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <h3 class="font-bold text-slate-900">{{ user.name }}</h3>
              <p class="text-sm text-slate-600 mt-1">{{ user.email }}</p>
              <div class="mt-4 pt-4 border-t border-indigo-200 space-y-2 text-left">
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">NISN</span>
                  <span class="font-semibold text-slate-900">{{ user.nisn || '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">Kelas</span>
                  <span class="font-semibold text-slate-900">{{ user.kelas || '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-slate-600">HP</span>
                  <span class="font-semibold text-slate-900">{{ user.phone || '-' }}</span>
                </div>
              </div>
            </div>
            <Link href="/profile" class="mt-4 w-full block text-center py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
              Edit Profil
            </Link>
          </div>

          <!-- Restrictions -->
          <div v-if="user.borrow_restricted" class="bg-rose-50 border border-rose-200 rounded-2xl p-4">
            <div class="flex gap-3">
              <div class="text-2xl">⚠️</div>
              <div>
                <p class="font-bold text-rose-900">Peminjaman Dibatasi</p>
                <p class="text-sm text-rose-700 mt-1">Peminjaman Anda saat ini dibatasi oleh admin. Hubungi petugas untuk informasi lebih lanjut.</p>
              </div>
            </div>
          </div>

          <!-- Info -->
          <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Kebijakan Peminjaman</p>
            <ul class="space-y-2 text-sm text-slate-600">
              <li class="flex gap-2">
                <span>📖</span>
                <span>Maksimal {{ maxBooksPerUser }} buku per peminjaman</span>
              </li>
              <li class="flex gap-2">
                <span>📅</span>
                <span>Durasi peminjaman {{ maxLoanDays }} hari</span>
              </li>
              <li class="flex gap-2">
                <span>💰</span>
                <span>Denda keterlambatan Rp {{ finePerDay.toLocaleString('id-ID') }}/hari</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Recent History -->
      <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-6">Riwayat Peminjaman (5 Terbaru)</h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-slate-200">
                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Buku</th>
                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Pinjam</th>
                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Kembali</th>
                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="borrow in recentBorrows" :key="borrow.id" class="hover:bg-slate-50 transition-colors">
                <td class="py-4 px-4">
                  <p class="font-medium text-slate-900">{{ borrow.book?.title || 'Buku' }}</p>
                  <p class="text-sm text-slate-500">{{ borrow.book?.author || '-' }}</p>
                </td>
                <td class="py-4 px-4 text-sm text-slate-600">{{ formatDate(borrow.borrow_date) }}</td>
                <td class="py-4 px-4 text-sm text-slate-600">{{ borrow.return_date ? formatDate(borrow.return_date) : '-' }}</td>
                <td class="py-4 px-4">
                  <span :class="{
                    'bg-emerald-100 text-emerald-700': borrow.status === 'returned',
                    'bg-indigo-100 text-indigo-700': borrow.status === 'borrowed',
                    'bg-rose-100 text-rose-700': borrow.status === 'overdue'
                  }" class="px-3 py-1 rounded-full text-xs font-bold">
                    {{ borrow.status === 'returned' ? 'Dikembalikan' : borrow.status === 'borrowed' ? 'Aktif' : 'Terlambat' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  user: { type: Object, required: true },
  activeBorrows: { type: Array, default: () => [] },
  recentBorrows: { type: Array, default: () => [] },
  overdueCount: { type: Number, default: 0 },
  totalBorrows: { type: Number, default: 0 },
  totalFines: { type: Number, default: 0 },
  maxBooksPerUser: { type: Number, default: 3 },
  maxLoanDays: { type: Number, default: 7 },
  finePerDay: { type: Number, default: 5000 },
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const daysUntilDue = (dueDate) => {
  if (!dueDate) return 0;
  const now = new Date();
  const due = new Date(dueDate);
  const diff = Math.ceil((due - now) / (1000 * 60 * 60 * 24));
  return diff;
};
</script>

