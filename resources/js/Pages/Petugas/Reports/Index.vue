<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Laporan Sirkulasi Harian</h1>
            <p class="text-slate-500 text-sm mt-1">Pantau performa dan statistik operasional hari ini</p>
          </div>
          <div class="flex items-center gap-6">
            <div class="text-right border-r border-slate-200 pr-6 hidden sm:block">
              <p class="text-sm font-bold text-slate-700">{{ formattedDate }}</p>
              <p class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em]">Live Report</p>
            </div>
            <button class="bg-white border border-slate-200 p-2.5 rounded-xl text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </button>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-[1400px] mx-auto space-y-10">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          <div class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-5">
              <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <span class="text-[10px] font-black text-orange-600 bg-orange-50 px-2 py-1 rounded-lg italic">DAILY</span>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Peminjaman Hari Ini</p>
            <h3 class="text-4xl font-black text-slate-900 mt-1">{{ todayLoans }} <span class="text-sm font-medium text-slate-400">Buku</span></h3>
          </div>

          <div class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-5">
              <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Pengembalian Hari Ini</p>
            <h3 class="text-4xl font-black text-slate-900 mt-1">{{ todayReturns }}</h3>
          </div>

          <div class="bg-white p-7 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-5">
              <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
              </div>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Denda Terkumpul</p>
            <h3 class="text-3xl font-black text-slate-900 mt-1">Rp {{ formatCurrency(totalFines) }}</h3>
          </div>

          <div class="bg-slate-900 p-7 rounded-[2rem] shadow-xl shadow-slate-200 group">
            <div class="flex justify-between items-start mb-5">
              <div class="w-12 h-12 rounded-2xl bg-slate-700/50 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
              </div>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Aktivitas Kritis</p>
            <h3 class="text-3xl font-black text-white mt-1">{{ criticalActivity }}</h3>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Ringkasan Aktivitas</h3>
            <div class="space-y-4">
              <div v-for="item in activitySummary" :key="item.label" class="flex items-center justify-between pb-4 border-b border-slate-100 last:border-b-0">
                <div class="flex items-center gap-3">
                  <div :class="`w-3 h-3 rounded-full ${item.color}`"></div>
                  <span class="text-sm font-medium text-slate-700">{{ item.label }}</span>
                </div>
                <span class="text-sm font-bold text-slate-900">{{ item.value }}</span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Top Peminjam Hari Ini</h3>
            <div class="space-y-3">
              <div v-for="member in topMembers" :key="member.id" class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-sm font-bold text-orange-600">
                    {{ member.name?.[0]?.toUpperCase() }}
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-800">{{ member.name }}</p>
                    <p class="text-[10px] text-slate-500">{{ member.borrow_count }} peminjaman</p>
                  </div>
                </div>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-lg">{{ member.borrow_count }}x</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { ref } from 'vue';

defineProps({
  todayLoans: { type: Number, default: 0 },
  todayReturns: { type: Number, default: 0 },
  totalFines: { type: Number, default: 0 },
  criticalActivity: { type: Number, default: 0 },
  activitySummary: { type: Array, default: () => [] },
  topMembers: { type: Array, default: () => [] },
});

const formattedDate = ref('');

const formatCurrency = (amount) => {
  return amount?.toLocaleString('id-ID') || '0';
};

const now = new Date();
formattedDate.value = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
</script>
