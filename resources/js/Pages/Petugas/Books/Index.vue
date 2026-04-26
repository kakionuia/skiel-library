<template>
  <PetugasLayout title="Kelola Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kelola Buku</h1>
          <p class="mt-2 text-sm text-gray-500">Tambah dan kelola data buku di perpustakaan.</p>
        </div>

        <!-- Add Book Form -->
        <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Buku Baru</h3>
          <form @submit.prevent="submitBook" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
              <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan judul buku">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
              <input v-model="form.author" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan nama penulis">
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
              <input v-model="form.isbn" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan ISBN">
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
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Eksemplar</label>
              <input v-model="form.stock" type="number" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Jumlah buku">
            </div>
            <div class="md:col-span-2 lg:col-span-3">
              <label class="block text-sm font-medium text-gray-700 mb-1">Sinopsis</label>
              <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan sinopsis buku (opsional)"></textarea>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
              <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Simpan Buku
              </button>
            </div>
          </form>
        </div>

        <!-- Books Table -->
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Buku</h3>
            <div class="flex gap-2">
              <input v-model="search" type="text" placeholder="Cari buku..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Judul</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Penulis</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Penerbit</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">ISBN</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Stok</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tersedia</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="book in books.data" :key="book.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ book.title }}</div>
                    <div class="text-xs text-gray-500">{{ book.category }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ book.author }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ book.publisher || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ book.year }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ book.isbn || '-' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 font-medium">{{ book.stock }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="book.available > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded text-xs font-medium">
                      {{ book.available }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex gap-2 justify-end">
                      <button @click="editBook(book)" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</button>
                      <button @click="deleteBook(book)" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!books.data?.length">
                  <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada buku.</td>
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
  books: { type: Object, default: () => ({ data: [] }) },
});

const search = ref('');

const form = ref({
  title: '',
  author: '',
  publisher: '',
  year: '',
  isbn: '',
  category: '',
  stock: 1,
  description: '',
});

const submitBook = () => {
  router.post(route('petugas.books.store'), form.value, {
    onSuccess: () => {
      form.value = { title: '', author: '', publisher: '', year: '', isbn: '', category: '', stock: 1, description: '' };
    }
  });
};

const editBook = (book) => {
  router.get(route('petugas.books.edit', book.id));
};

const deleteBook = (book) => {
  if (confirm(`Hapus buku "${book.title}"?`)) {
    router.delete(route('petugas.books.destroy', book.id));
  }
};
</script>