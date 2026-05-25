<template>
  <AuthenticatedLayout title="Dashboard">
    <div class="min-h-screen bg-[#f8fafc] pb-12">
      <div class="relative overflow-hidden bg-white pb-32 pt-12 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
              <h1 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center">
                Dashboard Overview
                <span class="ml-3 inline-flex items-center rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-700/10">
                  Admin Access
                </span>
              </h1>
              <p class="mt-2 text-base text-slate-500">
                Selamat datang kembali. Berikut adalah ringkasan aktivitas perpustakaan hari ini.
              </p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
               <button @click="downloadReport" class="inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-all">
                Download Report
              </button>
            </div>
          </div>
        </div>
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 blur-3xl opacity-20 pointer-events-none">
          <div class="aspect-square w-[400px] bg-gradient-to-tr from-orange-500 to-orange-400 rounded-full"></div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-20">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div v-for="stat in mainStats" :key="stat.label" 
            class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white transition-all hover:translate-y-[-4px] hover:shadow-[0_20px_40px_rgb(0,0,0,0.08)]">
            <div class="flex items-center justify-between">
              <div :class="['rounded-xl p-3', stat.bgIcon]">
                <component :is="stat.icon" :class="['h-6 w-6', stat.iconColor]" />
              </div>
              <span class="text-xs font-medium text-slate-400 group-hover:text-slate-600 transition-colors">Total Data</span>
            </div>
            <div class="mt-5">
              <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">{{ stat.label }}</p>
              <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ stat.value }}</p>
                <span v-if="stat.trend" class="text-xs font-semibold text-green-600">+{{ stat.trend }}%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
          <div class="lg:col-span-2 rounded-2xl bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
            <div class="flex items-center justify-between mb-8">
              <div>
                <h2 class="text-lg font-bold text-slate-900">Analisis Peminjaman</h2>
                <p class="text-sm text-slate-500">Aktivitas 6 bulan terakhir</p>
              </div>
              <select class="text-sm border-none bg-slate-50 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option>Tahun {{ new Date().getFullYear() }}</option>
              </select>
            </div>
            
            <div class="h-72">
              <div class="flex h-full items-end justify-between gap-3">
                <div v-for="(item, index) in monthlyBorrows" :key="index" class="group relative flex-1 flex flex-col items-center">
                  <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-[10px] px-2 py-1 rounded-md mb-2 pointer-events-none">
                    {{ item.count }} Buku
                  </div>
                  <div class="w-full relative flex-1 flex items-end px-1 sm:px-2">
                    <div 
                      class="w-full bg-orange-500 rounded-t-md transition-all duration-700 ease-out group-hover:bg-orange-600 shadow-sm"
                      :style="{ height: `${(item.count / maxBorrow) * 100}%` }"
                    ></div>
                  </div>
                  <p class="mt-4 text-[11px] font-bold text-slate-400 group-hover:text-orange-600 uppercase tracking-tighter">{{ item.month.substring(0, 3) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="rounded-2xl bg-slate-900 p-8 text-white shadow-xl relative overflow-hidden group">
              <div class="relative z-10">
                <p class="text-slate-400 text-sm font-medium">Total Denda Terkumpul</p>
                <h3 class="text-3xl font-bold mt-2 tracking-tight">Rp {{ formatNumber(stats.totalLateFee) }}</h3>
                <div class="mt-6 flex items-center text-xs text-slate-300">
                  <span class="bg-white/10 px-2 py-1 rounded mr-2">Info</span>
                  Berdasarkan keterlambatan bulan ini
                </div>
              </div>
              <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-orange-500/20 rounded-full blur-2xl group-hover:bg-orange-500/40 transition-all"></div>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
              <div class="p-5 border-b border-gray-50 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm">Status Notifikasi</h3>
                <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
              </div>
              <div class="p-0">
                <Link href="/admin/late-returns" class="flex items-center px-6 py-4 hover:bg-slate-50 transition-colors">
                  <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center mr-4">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900">{{ stats.lateReturnsCount }} Buku Terlambat</p>
                    <p class="text-xs text-slate-500">Perlu tindak lanjut segera</p>
                  </div>
                  <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="2" /></svg>
                </Link>
                <Link href="/admin/book-submissions" class="flex items-center px-6 py-4 border-t border-gray-50 hover:bg-slate-50 transition-colors">
                  <div class="h-10 w-10 rounded-full bg-orange-50 flex items-center justify-center mr-4">
                    <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900">{{ stats.pendingBookSubmissions }} Permintaan</p>
                    <p class="text-xs text-slate-500">Menunggu review admin</p>
                  </div>
                  <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 5l7 7-7 7" stroke-width="2" /></svg>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8">
          <div class="flex items-center space-x-2 mb-6">
            <div class="h-1.5 w-8 bg-orange-600 rounded-full"></div>
            <h2 class="text-xl font-bold text-slate-900">Akses Cepat</h2>
          </div>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Link v-for="menu in quickMenus" :key="menu.title" :href="menu.link" 
              class="group flex flex-col p-5 rounded-2xl bg-white border border-gray-100 shadow-sm transition-all hover:border-orange-200 hover:shadow-md">
              <div :class="['h-12 w-12 rounded-xl flex items-center justify-center mb-4 transition-transform group-hover:scale-110', menu.bg]">
                <component :is="menu.icon" :class="['h-6 w-6', menu.iconColor]" />
              </div>
              <h3 class="font-bold text-slate-900">{{ menu.title }}</h3>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ menu.desc }}</p>
            </Link>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, markRaw } from 'vue';
// Import icons (Assuming you use standard Heroicons or similar SVG paths)
import { 
  BookOpenIcon, 
  UsersIcon, 
  UserGroupIcon, 
  ClockIcon,
  ShieldCheckIcon,
  ClipboardDocumentListIcon,
  ExclamationCircleIcon,
  ChatBubbleLeftRightIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  monthlyBorrows: { type: Array, default: () => [] },
});

// Helper formatted stats for the cards
const mainStats = computed(() => [
  { label: 'Total Buku', value: props.stats.totalBooks, icon: markRaw(BookOpenIcon), bgIcon: 'bg-orange-50', iconColor: 'text-orange-600', trend: 12 },
  { label: 'Anggota', value: props.stats.totalMembers, icon: markRaw(UserGroupIcon), bgIcon: 'bg-green-50', iconColor: 'text-green-600', trend: 5 },
  { label: 'Petugas', value: props.stats.totalPetugas, icon: markRaw(ShieldCheckIcon), bgIcon: 'bg-orange-50', iconColor: 'text-orange-600' },
  { label: 'Pending', value: props.stats.pendingMembers, icon: markRaw(ClockIcon), bgIcon: 'bg-amber-50', iconColor: 'text-amber-600' },
]);

const quickMenus = [
  { title: 'Kelola Akun', desc: 'Pengaturan hak akses anggota & petugas.', link: '/admin/accounts', icon: markRaw(UsersIcon), bg: 'bg-orange-50', iconColor: 'text-orange-600' },
  { title: 'Permintaan Buku', desc: 'Review pengajuan pengadaan buku baru.', link: '/admin/book-requests', icon: markRaw(ClipboardDocumentListIcon), bg: 'bg-green-50', iconColor: 'text-green-600' },
  { title: 'Penunggakan', desc: 'Monitoring keterlambatan pengembalian.', link: '/admin/late-returns', icon: markRaw(ExclamationCircleIcon), bg: 'bg-red-50', iconColor: 'text-red-600' },
  { title: 'Keluhan & Saran', desc: 'Merespon feedback dari pengguna.', link: '/admin/complaints', icon: markRaw(ChatBubbleLeftRightIcon), bg: 'bg-yellow-50', iconColor: 'text-yellow-600' },
];

const maxBorrow = computed(() => {
  const max = Math.max(...props.monthlyBorrows.map(b => b.count));
  return max > 0 ? max : 1;
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};

const downloadReport = () => {
  // Navigate to download endpoint
  window.location.href = '/admin/download-report';
};
</script>

<style scoped>
/* Custom curve for chart bars if needed */
.rounded-t-md {
  border-top-left-radius: 6px;
  border-top-right-radius: 6px;
}
</style>
