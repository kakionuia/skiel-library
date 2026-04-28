<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Daftar Keterlambatan</h1>
            <p class="text-slate-500 text-sm mt-1">Monitoring anggota dengan masa pinjam melampaui batas</p>
          </div>
          <div class="flex items-center gap-4">
            <div class="bg-rose-50 px-4 py-2 rounded-xl border border-rose-100">
              <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest text-center leading-none mb-1">Status Alert</p>
              <p class="text-xs font-bold text-rose-700 leading-none">High Priority</p>
            </div>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-[1400px] mx-auto space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Kasus</p>
            <div class="flex items-end gap-3">
              <h3 class="text-4xl font-black text-slate-900">{{ overdueLoans.length }}</h3>
              <p class="text-xs font-bold text-rose-500 mb-2">Anggota</p>
            </div>
          </div>

          <div class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Akumulasi Denda</p>
            <div class="flex items-end gap-3">
              <h3 class="text-3xl font-black text-slate-900">Rp {{ totalFines.toLocaleString('id-ID') }}</h3>
            </div>
          </div>

          <div class="bg-slate-900 p-7 rounded-[2rem] shadow-xl shadow-slate-200">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Rata-rata Terlambat</p>
            <div class="flex items-end gap-3">
              <h3 class="text-4xl font-black text-white">{{ averageDaysOverdue }}</h3>
              <p class="text-xs font-bold text-slate-400 mb-2">Hari/Orang</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                  <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi Anggota</th>
                  <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Buku & Durasi</th>
                  <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Hari Terlambat</th>
                  <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Estimasi Denda</th>
                  <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="loan in overdueLoans" :key="loan.id" class="hover:bg-slate-50 transition-colors group">
                  <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">
                        {{ loan.member_name?.[0]?.toUpperCase() }}
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-800">{{ loan.member_name }}</p>
                        <p class="text-[11px] text-slate-500">ID: {{ loan.member_id }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-6">
                    <div>
                      <p class="text-sm font-bold text-slate-800">{{ loan.book_title }}</p>
                      <p class="text-[11px] text-slate-500 mt-1">Pinjam: {{ formatDate(loan.borrow_date) }}</p>
                    </div>
                  </td>
                  <td class="px-8 py-6">
                    <span class="text-sm font-black text-rose-600">{{ loan.days_overdue }} hari</span>
                  </td>
                  <td class="px-8 py-6">
                    <p class="text-sm font-bold text-slate-900">Rp {{ loan.fine.toLocaleString('id-ID') }}</p>
                  </td>
                  <td class="px-8 py-6 text-right">
                    <button class="px-3 py-1 text-[10px] font-bold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-all">
                      Proses
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';

defineProps({
  overdueLoans: { type: Array, default: () => [] },
  totalFines: { type: Number, default: 0 },
  averageDaysOverdue: { type: Number, default: 0 },
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>
