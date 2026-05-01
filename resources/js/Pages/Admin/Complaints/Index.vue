<template>
  <AuthenticatedLayout title="Keluhan">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Keluhan</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola keluhan yang disampaikan oleh anggota.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Subjek</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Deskripsi</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="complaint in complaints.data" :key="complaint.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ complaint.user?.name || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ complaint.subject }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ complaint.description }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700': complaint.status === 'pending',
                      'bg-orange-100 text-orange-700': complaint.status === 'reviewed',
                      'bg-green-100 text-green-700': complaint.status === 'resolved',
                      'bg-red-100 text-red-700': complaint.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase">
                      {{ complaint.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ complaint.created_at }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link :href="`/admin/complaints/${complaint.id}`" class="text-orange-600 hover:text-orange-800 text-sm">
                      Tangani →
                    </Link>
                  </td>
                </tr>
                <tr v-if="!complaints.data?.length">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada keluhan.</td>
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
  complaints: { type: Object, default: () => ({ data: [] }) },
});
</script>