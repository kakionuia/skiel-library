<template>
  <AuthenticatedLayout title="Saran">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Saran</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola saran yang disampaikan oleh anggota.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Isi Saran</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Catatan Admin</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="suggestion in suggestions.data" :key="suggestion.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ suggestion.user?.name || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ suggestion.content }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700': suggestion.status === 'pending',
                      'bg-green-100 text-green-700': suggestion.status === 'approved',
                      'bg-red-100 text-red-700': suggestion.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase">
                      {{ suggestion.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ suggestion.admin_note || '-' }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ formatDateTime(suggestion.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex gap-2 justify-end">
                      <button v-if="suggestion.status === 'pending'" @click="updateStatus(suggestion.id, 'approved')" class="text-green-600 hover:text-green-800 text-sm">
                        Setuju
                      </button>
                      <button v-if="suggestion.status === 'pending'" @click="updateStatus(suggestion.id, 'rejected')" class="text-red-600 hover:text-red-800 text-sm">
                        Tolak
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!suggestions.data?.length">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada saran.</td>
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
import { router } from '@inertiajs/vue3';
import { confirmDialog, dangerConfirm } from '@/Utils/sweetAlert';

defineProps({
  suggestions: { type: Object, default: () => ({ data: [] }) },
});

const formatDateTime = (dateString) => {
  if (!dateString) return '-';

  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '-';

  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Jakarta',
    timeZoneName: 'short',
  }).format(date);
};

const updateStatus = async (id, status) => {
  const isApproved = status === 'approved';
  const confirmed = isApproved
    ? await confirmDialog({
        title: 'Setujui saran ini?',
        text: 'Status saran akan diperbarui menjadi disetujui.',
        confirmButtonText: 'Ya, setujui',
      })
    : await dangerConfirm({
        title: 'Tolak saran ini?',
        text: 'Status saran akan diperbarui menjadi ditolak.',
        confirmButtonText: 'Ya, tolak',
      });

  if (confirmed) {
    router.post(route('admin.suggestions.update', id), { status });
  }
};
</script>
