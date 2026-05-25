<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans antialiased text-slate-800">
    <!-- Sidebar Component -->
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/40" />

    <!-- Main Content Area -->
    <main class="flex-1 ml-80 min-h-screen pb-16">
      
      <!-- Header -->
      <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Keterlambatan</h1>
            <p class="text-slate-400 text-sm mt-0.5">Monitoring anggota dengan masa pinjam melampaui batas</p>
          </div>
          <div class="flex items-center gap-4">
            <div
              class="px-4 py-2 rounded-xl border flex items-center gap-2"
              :class="priorityStatus.badgeClass"
            >
              <span class="w-2 h-2 rounded-full" :class="priorityStatus.dotClass"></span>
              <p class="text-xs font-bold tracking-wide">{{ priorityStatus.label }}</p>
            </div>
          </div>
        </div>
      </header>

      <!-- Content Container -->
      <div class="p-10 max-w-[1400px] mx-auto space-y-8">
        
        <!-- Stats Grid Metric -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Card 1 -->
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kasus Aktif</p>
            <div class="flex items-end gap-2 mt-4">
              <h3 class="text-3xl font-extrabold text-slate-900 leading-none">{{ totalCases }}</h3>
              <p class="text-xs font-medium text-slate-400 mb-0.5">Anggota Melanggar</p>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-all duration-300">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akumulasi Finansial Denda</p>
            <div class="flex items-end gap-1 mt-4">
              <span class="text-sm font-bold text-slate-400 mb-1 mr-1">Rp</span>
              <h3 class="text-3xl font-extrabold text-slate-900 leading-none">{{ formatNumber(totalFines) }}</h3>
            </div>
          </div>

          <!-- Card 3 (Premium Dark Card accented with Orange) -->
          <div class="bg-slate-900 p-6 rounded-2xl shadow-lg shadow-slate-900/10 flex flex-col justify-between hover:bg-slate-800 transition-colors duration-300">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-rata Durasi Keterlambatan</p>
            <div class="flex items-end gap-2 mt-4">
              <h3 class="text-3xl font-extrabold text-white leading-none">
                {{ typeof averageDaysOverdue === 'number' ? averageDaysOverdue.toFixed(0) : averageDaysOverdue }}
              </h3>
              <p class="text-xs font-medium text-orange-400 mb-0.5">Hari / Orang</p>
            </div>
          </div>
        </div>

        <!-- Data Overdue Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Anggota</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Buku & Tanggal Pinjam</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Durasi Lewat</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Estimasi Denda</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kondisi & Status</th>
                  <th class="px-8 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="loan in overdueLoans" :key="loan.id" class="hover:bg-slate-50/40 transition-colors duration-150">
                  
                  <!-- Member Info -->
                  <td class="px-8 py-4.5">
                    <div class="flex items-center gap-3.5">
                      <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs border border-orange-100/40">
                        {{ loan.member_name?.[0]?.toUpperCase() || '?' }}
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-slate-800 leading-none">{{ loan.member_name }}</p>
                        <p class="text-xs font-mono text-slate-400 mt-1.5">ID: {{ loan.member_id }}</p>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Book Info -->
                  <td class="px-8 py-4.5">
                    <div>
                      <p class="text-sm font-semibold text-slate-800 leading-snug">{{ loan.book_title }}</p>
                      <p class="text-xs text-slate-400 mt-1">Pinjam: {{ formatDate(loan.borrow_date) }}</p>
                    </div>
                  </td>
                  
                  <!-- Days Overdue -->
                  <td class="px-8 py-4.5">
                    <span class="text-sm font-bold text-orange-500 font-mono">{{ loan.days_overdue }} Hari</span>
                  </td>
                  
                  <!-- Fine Estimate -->
                  <td class="px-8 py-4.5">
                    <p class="text-sm font-semibold text-slate-800 font-mono">Rp {{ formatNumber(loan.fine) }}</p>
                  </td>
                  
                  <!-- Multi Status Condition (Minimalist Monochrome Dots) -->
                  <td class="px-8 py-4.5">
                    <div class="flex flex-col gap-1.5 max-w-[100px]">
                      <!-- Damage Status -->
                      <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                        <span class="w-1.5 h-1.5 rounded-full" :class="loan.is_damaged ? 'bg-orange-500' : 'bg-slate-300'"></span>
                        {{ loan.is_damaged ? 'Kondisi Rusak' : 'Kondisi Baik' }}
                      </span>
                      <!-- Payment Status -->
                      <span v-if="loan.payment_status" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full" :class="loan.payment_status === 'confirmed' ? 'bg-slate-600' : 'bg-slate-200'"></span>
                        {{ loan.payment_status === 'confirmed' ? 'Lunas' : 'Menunggu' }}
                      </span>
                    </div>
                  </td>
                  
                  <!-- Actions -->
                  <td class="px-8 py-4.5 text-right">
                    <Link 
                      :href="`/petugas/late-returns/${loan.user_id}`"
                      class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 bg-white hover:border-orange-500 hover:text-orange-500 hover:bg-orange-50/30 transition-all duration-150 inline-flex items-center gap-1"
                    >
                      Detail
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="totalCases === 0">
                  <td colspan="6" class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                      <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                      <p class="text-slate-500 font-semibold text-sm mt-1">Tidak ada berkas keterlambatan</p>
                      <p class="text-slate-400 text-xs">Semua riwayat peminjaman buku tercatat aman dan dikembalikan tepat pada waktunya.</p>
                    </div>
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
import { Link } from '@inertiajs/vue3';
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { computed } from 'vue';

const props = defineProps({
  overdueLoans: { type: Array, default: () => [] },
  totalFines: { type: Number, default: 0 },
  averageDaysOverdue: { type: Number, default: 0 },
});

const toNumber = (value) => {
  const number = Number(value);
  return Number.isFinite(number) ? number : 0;
};

const totalCases = computed(() => props.overdueLoans?.length || 0);
const normalizedTotalFines = computed(() => toNumber(props.totalFines));
const normalizedAverageDays = computed(() => toNumber(props.averageDaysOverdue));

const priorityStatus = computed(() => {
  if (totalCases.value === 0 || normalizedTotalFines.value === 0) {
    return {
      label: 'Aman',
      badgeClass: 'bg-emerald-50/80 border-emerald-100/80 text-emerald-700',
      dotClass: 'bg-emerald-500',
    };
  }

  if (totalCases.value >= 5 || normalizedAverageDays.value >= 7 || normalizedTotalFines.value >= 50000) {
    return {
      label: 'High Priority Action',
      badgeClass: 'bg-orange-50/60 border-orange-100/70 text-orange-700',
      dotClass: 'bg-orange-500 animate-pulse',
    };
  }

  return {
    label: 'Perlu Dipantau',
    badgeClass: 'bg-amber-50/70 border-amber-100/80 text-amber-700',
    dotClass: 'bg-amber-500',
  };
});

const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(toNumber(value));

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>
