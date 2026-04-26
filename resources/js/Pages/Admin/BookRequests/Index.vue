<template>
  <AuthenticatedLayout title="Permintaan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Permintaan Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola permintaan penambahan buku dari petugas.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Petugas</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jumlah</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="req in requests.data" :key="req.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-gray-900">{{ req.title }}</div>
                    <div class="text-xs text-gray-500">{{ req.author }} • {{ req.year }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ req.petugas?.name || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ req.quantity }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700 ring-1 ring-amber-200': req.status === 'pending',
                      'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200': req.status === 'approved',
                      'bg-rose-100 text-rose-700 ring-1 ring-rose-200': req.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight">
                      {{ req.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="req.status === 'pending'" class="flex justify-end gap-2">
                      <button @click="approveRequest(req)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                        Setuju
                      </button>
                      <button @click="rejectRequest(req)" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                        Tolak
                      </button>
                    </div>
                    <span v-else class="text-xs text-gray-500">
                      {{ req.admin_note || '-' }}
                    </span>
                  </td>
                </tr>
                <tr v-if="requests.data?.length === 0">
                  <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada permintaan buku.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="requests.data?.length" class="px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Menampilkan {{ requests.data.length }} dari {{ requests.total }} data</p>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
  requests: { type: Object, default: () => ({ data: [] }) },
});

const approveRequest = (req) => {
  if (confirm('Setuju kan permintaan buku ini? Buku akan ditambahkan ke koleksi.')) {
    router.post(`/admin/book-requests/${req.id}/approve`);
  }
};

const rejectRequest = (req) => {
  const note = prompt('Masukkan alasan penolakan:');
  if (note) {
    router.post(`/admin/book-requests/${req.id}/reject`, { admin_note: note });
  }
};
</script>