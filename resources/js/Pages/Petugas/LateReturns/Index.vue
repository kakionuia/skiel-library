<template>
  <PetugasLayout title="Tunggakan Siswa">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tunggakan Siswa</h1>
          <p class="mt-2 text-sm text-gray-500">Daftar siswa yang terlambat mengembalikan buku.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm font-medium text-gray-500">Total Siswa Terlambat</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ summary.totalLateStudents }}</div>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm font-medium text-gray-500">Total Buku Terlambat</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ summary.totalLateBooks }}</div>
          </div>
          <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm font-medium text-gray-500">Total Denda</div>
            <div class="mt-2 text-3xl font-bold text-orange-600">Rp {{ formatNumber(summary.totalLateFee) }}</div>
          </div>
        </div>

        <!-- Late Returns Table -->
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Siswa</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jatuh Tempo</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Terlambat</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Denda</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="late in lateReturns.data" :key="late.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ late.user?.name }}</div>
                    <div class="text-xs text-gray-500">{{ late.user?.kelas }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ late.book?.title }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ late.due_date }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-red-600 font-medium">{{ late.late_days || 0 }} hari</span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-orange-600 font-medium">Rp {{ formatNumber(late.late_fee) }}</span>
                  </td>
                </tr>
                <tr v-if="!lateReturns.data?.length">
                  <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada tunggakan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </PetugasLayout>
</template>

<script setup>
import PetugasLayout from '@/Layouts/PetugasLayout.vue';

defineProps({
  lateReturns: { type: Object, default: () => ({ data: [] }) },
  summary: { type: Object, default: () => ({}) },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};
</script>