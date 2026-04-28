<template>
  <div class="flex min-h-screen bg-[#f8fafc] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-5">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Dashboard Operasional</h1>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Petugas Sesi Aktif</p>
            </div>
          </div>
          <div class="flex items-center gap-6">
            <div class="text-right border-r border-slate-200 pr-6 hidden sm:block">
              <p class="text-sm font-bold text-slate-700">{{ formattedDate }}</p>
              <p class="text-[11px] font-semibold text-indigo-600 uppercase tracking-widest">{{ currentTime }} WIB</p>
            </div>
            <button class="relative p-2.5 rounded-xl bg-slate-50 text-slate-500 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200">
              <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
            </button>
          </div>
        </div>
      </header>

      <div class="p-8 max-w-[1400px] mx-auto space-y-10">
        <section>
          <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm group hover:border-indigo-500 transition-all duration-300">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded">LIVE</span>
              </div>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Peminjaman Aktif</p>
              <h3 class="text-3xl font-black text-slate-900 mt-1">{{ stats.activeLoans }}</h3>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm group hover:border-emerald-500 transition-all duration-300">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
              </div>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kembali Hari Ini</p>
              <h3 class="text-3xl font-black text-slate-900 mt-1">{{ stats.todayReturns }}</h3>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm group hover:border-indigo-500 transition-all duration-300">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
              </div>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Koleksi</p>
              <h3 class="text-3xl font-black text-slate-900 mt-1">{{ stats.totalBooks }}</h3>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm group hover:border-amber-500 transition-all duration-300">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                </div>
              </div>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Denda Pending</p>
              <h3 class="text-2xl font-black text-slate-900 mt-1">Rp {{ formatCurrency(stats.totalFines) }}</h3>
            </div>
          </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
              <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-bold text-slate-900">Aktivitas Sirkulasi</h3>
                  <p class="text-xs text-slate-500">Pantau proses keluar masuk buku secara real-time</p>
                </div>
              </div>
              <div class="divide-y divide-slate-100">
                <div v-if="recentActivities && recentActivities.length" v-for="activity in recentActivities" :key="activity.id" class="px-8 py-5 flex items-center gap-5 hover:bg-slate-50 transition-colors group">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 font-bold border border-slate-200 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all duration-300">
                    {{ activity.user_name?.[0]?.toUpperCase() || 'U' }}
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <p class="text-sm font-bold text-slate-800">{{ activity.description }}</p>
                      <span class="text-[10px] font-bold text-slate-400">{{ formatTime(activity.created_at) }}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                      <p class="text-xs text-slate-500">Oleh: {{ activity.user_name }}</p>
                      <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                      <p class="text-xs text-indigo-600 font-medium">{{ formatDate(activity.created_at) }}</p>
                    </div>
                  </div>
                </div>
                <div v-else class="px-8 py-6 text-center text-slate-400">Belum ada aktivitas</div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-indigo-950 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl shadow-indigo-200 group">
              <div class="relative z-10">
                <h3 class="text-xl font-bold mb-2">Layanan Cepat</h3>
                <p class="text-sm text-indigo-200/80 mb-6 leading-relaxed">Pilih modul yang ingin Anda operasikan sekarang.</p>
                <div class="space-y-3">
                  <Link :href="route('petugas.borrows')" class="flex items-center justify-between w-full p-4 bg-white/10 hover:bg-white text-white hover:text-indigo-950 rounded-2xl transition-all duration-300 font-bold text-sm border border-white/10 group/btn">
                    Input Sirkulasi
                    <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                  </Link>
                  <Link :href="route('petugas.books')" class="flex items-center justify-between w-full p-4 bg-white/10 hover:bg-white text-white hover:text-indigo-950 rounded-2xl transition-all duration-300 font-bold text-sm border border-white/10 group/btn">
                    Katalog Buku
                    <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                  </Link>
                </div>
              </div>
              <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl group-hover:bg-indigo-500/40 transition-all duration-700"></div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
              <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span>
                Perhatian Petugas
              </h4>
              <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 mb-3">
                <p class="text-xs text-amber-800 leading-relaxed font-medium">Terdapat <strong>{{ lateCount }}</strong> peminjaman jatuh tempo. Segera proses notifikasi.</p>
              </div>
              <Link :href="route('petugas.late-returns')" class="text-xs font-bold text-indigo-600 hover:underline">Lihat daftar keterlambatan &rarr;</Link>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
  stats: { type: Object, default: () => ({ activeLoans: 0, todayReturns: 0, totalBooks: 0, totalFines: 0 }) },
  recentActivities: { type: Array, default: () => [] },
  lateCount: { type: Number, default: 0 },
});

const formattedDate = ref('');
const currentTime = ref('');

const updateDateTime = () => {
  const now = new Date();
  formattedDate.value = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

const formatDate = (datetime) => {
  const date = new Date(datetime);
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long' });
};

const formatTime = (datetime) => {
  const date = new Date(datetime);
  return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

const formatCurrency = (amount) => {
  return amount?.toLocaleString('id-ID') || '0';
};

onMounted(() => {
  updateDateTime();
  setInterval(updateDateTime, 60000);
});
</script>
