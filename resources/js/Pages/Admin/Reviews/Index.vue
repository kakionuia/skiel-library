<template>
  <AuthenticatedLayout title="Ulasan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Ulasan Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola ulasan yang diberikan oleh anggota.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Rating</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Komentar</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ review.book?.title || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ review.user?.name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ review.user?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-1">
                      <span v-for="i in 5" :key="i" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'">★</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ review.comment || '-' }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ review.created_at }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <button @click="deleteReview(review)" class="text-red-600 hover:text-red-800 text-sm">
                      Hapus
                    </button>
                  </td>
                </tr>
                <tr v-if="!reviews.data?.length">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada ulasan.</td>
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
import { dangerConfirm } from '@/Utils/sweetAlert';

defineProps({
  reviews: { type: Object, default: () => ({ data: [] }) },
});

const deleteReview = async (review) => {
  if (await dangerConfirm({
    title: 'Hapus ulasan ini?',
    text: `Ulasan untuk buku "${review.book?.title || 'ini'}" akan dihapus permanen.`,
    confirmButtonText: 'Ya, hapus',
  })) {
    router.delete(`/admin/reviews/${review.id}`);
  }
};
</script>
