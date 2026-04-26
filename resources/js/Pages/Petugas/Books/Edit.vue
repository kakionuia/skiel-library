<template>
  <PetugasLayout title="Edit Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto">
        
        <div class="mb-6 flex items-center gap-4">
          <Link href="/petugas/books" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Edit Buku</h1>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
          <form @submit.prevent="updateBook">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-red-500">*</span></label>
                <input v-model="form.author" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                <input v-model="form.publisher" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                <input v-model="form.year" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                <input v-model="form.isbn" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                <input v-model="form.stock" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sinopsis</label>
                <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
              </div>
            </div>
            <div class="mt-6 flex gap-4">
              <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Simpan Perubahan
              </button>
              <Link href="/petugas/books" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                Batal
              </Link>
            </div>
          </form>
        </div>

      </div>
    </div>
  </PetugasLayout>
</template>

<script setup>
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  book: { type: Object, default: () => ({}) },
});

const form = ref({
  title: props.book.title || '',
  author: props.book.author || '',
  publisher: props.book.publisher || '',
  year: props.book.year || '',
  isbn: props.book.isbn || '',
  category: props.book.category || '',
  stock: props.book.stock || 1,
  description: props.book.description || '',
});

const updateBook = () => {
  router.put(route('petugas.books.update', props.book.id), form.value);
};
</script>