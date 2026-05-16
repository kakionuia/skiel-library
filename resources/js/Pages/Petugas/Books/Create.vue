<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div class="flex items-center gap-4">
            <Link :href="route('petugas.books')" class="text-slate-400 hover:text-slate-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
              <h1 class="text-2xl font-bold tracking-tight text-slate-800">Tambah Koleksi Buku</h1>
              <p class="text-slate-500 text-sm mt-1">Daftarkan judul baru ke perpustakaan</p>
            </div>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-2xl mx-auto space-y-8">
        <form @submit.prevent="createBook" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm md:col-span-2">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Foto Sampul Buku</label>
              <div class="flex gap-4 items-start">
                <div class="w-32 h-48 rounded-xl overflow-hidden border-2 border-dashed border-slate-200 flex items-center justify-center bg-slate-50">
                  <img v-if="form.cover_preview" :src="form.cover_preview" class="w-full h-full object-cover">
                  <svg v-else class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1">
                  <input @change="handleImageUpload" type="file" accept="image/*" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
                  <p class="text-[10px] text-slate-400 mt-2">Format: JPG, PNG, atau WEBP (Max 2MB)</p>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Judul Buku <span class="text-rose-500">*</span></label>
              <input v-model="form.title" type="text" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Penulis <span class="text-rose-500">*</span></label>
              <input v-model="form.author" type="text" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Penerbit</label>
              <input v-model="form.publisher" type="text" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">ISBN</label>
              <input v-model="form.isbn" type="text" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium font-mono">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Tahun Terbit</label>
              <input v-model="form.year" type="number" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
            </div>

            <select 
                v-model="form.category" 
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': errors.category }"
              >
                <option value="">Pilih Kategori...</option>
                <option v-for="category in categories" :key="category.id" :value="category.name">
                  {{ category.name }}
                </option>
            </select>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Jumlah Eksemplar</label>
              <input v-model="form.quantity" type="number" min="0" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
            </div>
          </div>

          <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Sinopsis</label>
            <textarea v-model="form.description" rows="4" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium resize-none"></textarea>
          </div>

          <div class="flex gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-colors shadow-md shadow-orange-200">
              Tambah Buku
            </button>
            <Link :href="route('petugas.books')" class="px-6 py-3 border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-50 transition-colors">
              Batal
            </Link>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = ref({
  title: '',
  author: '',
  publisher: '',
  year: new Date().getFullYear(),
  isbn: '',
  category: '',
  quantity: 1,
  description: '',
  cover: null,
  cover_preview: null,
});

const handleImageUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.cover = file;
    const reader = new FileReader();
    reader.onload = (event) => {
      form.value.cover_preview = event.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const createBook = () => {
  const formData = new FormData();
  formData.append('title', form.value.title);
  formData.append('author', form.value.author);
  formData.append('publisher', form.value.publisher);
  formData.append('year', form.value.year);
  formData.append('isbn', form.value.isbn);
  formData.append('category', form.value.category);
  formData.append('quantity', form.value.quantity);
  formData.append('description', form.value.description);
  if (form.value.cover) {
    formData.append('cover', form.value.cover);
  }

  router.post(route('petugas.books.store'), formData, {
    onSuccess: () => {
      router.visit(route('petugas.books'));
    },
  });
};
</script>
