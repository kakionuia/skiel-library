<template>
  <AuthenticatedLayout title="Penunggakan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Penunggakan Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola anggota dengan buku yang terlambat dikembalikan.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jatuh Tempo</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Hari Terlambat</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Denda</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="borrow in lateBorrows.data" :key="borrow.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-gray-900">{{ borrow.user?.name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ borrow.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ borrow.book?.title || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ borrow.due_date }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-red-600 font-medium">{{ borrow.late_days || 0 }} hari</span>
                  </td>
                  <td class="px-6 py-4">
                    <span class="text-orange-600 font-medium">Rp {{ formatNumber(borrow.late_fee) }}</span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link :href="`/admin/late-returns/${borrow.user_id}`" class="text-orange-600 hover:text-orange-800 text-sm">
                      Detail →
                    </Link>
                  </td>
                </tr>
                <tr v-if="!lateBorrows.data?.length">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada penunggakan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  lateBorrows: { type: Object, default: () => ({ data: [] }) },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};
</script>