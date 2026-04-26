<template>
  <PetugasLayout title="Pengajuan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengajuan Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Ajukan buku baru untuk koleksi perpustakaan.</p>
        </div>

        <!-- Add Book Request Form -->
        <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajukan Buku Baru</h3>
          <form @submit.prevent="submitRequest" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
              <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan judul buku">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Pengarang</label>
              <input v-model="form.author" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan nama pengarang">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
              <input v-model="form.publisher" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan nama penerbit">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
              <input v-model="form.year" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: 2024">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
              <input v-model="form.isbn" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan ISBN (opsional)">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <select v-model="form.category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Pilih kategori</option>
                <option value="Fiksi">Fiksi</option>
                <option value="Non-Fiksi">Non-Fiksi</option>
                <option value="Pendidikan">Pendidikan</option>
                <option value="Ilmu Pengetahuan">Ilmu Pengetahuan</option>
                <option value="Sejarah">Sejarah</option>
                <option value="Agama">Agama</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
              <input v-model="form.quantity" type="number" min="1" required class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Jumlah buku">
            </div>
            <div class="md:col-span-2">
              <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Kirim Pengajuan
              </button>
            </div>
          </form>
        </div>

        <!-- My Book Requests Table -->
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Pengajuan</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Judul</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Pengarang</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jumlah</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="request in bookRequests.data" :key="request.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ request.title }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ request.author }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ request.quantity }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700': request.status === 'pending',
                      'bg-green-100 text-green-700': request.status === 'approved',
                      'bg-red-100 text-red-700': request.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase">
                      {{ request.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ request.created_at }}</div>
                  </td>
                </tr>
                <tr v-if="!bookRequests.data?.length">
                  <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada pengajuan.</td>
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
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  bookRequests: { type: Object, default: () => ({ data: [] }) },
});

const form = ref({
  title: '',
  author: '',
  publisher: '',
  year: '',
  isbn: '',
  category: '',
  quantity: 1,
});

const submitRequest = () => {
  router.post(route('petugas.book-requests.store'), form.value);
};
</script>