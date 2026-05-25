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
              <h1 class="text-2xl font-bold tracking-tight text-slate-800">Edit Koleksi Buku</h1>
              <p class="text-slate-500 text-sm mt-1">Perbarui informasi detail buku</p>
            </div>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-2xl mx-auto space-y-8">
        <form @submit.prevent="updateBook" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Kode Barcode</label>
              <input v-model="form.barcode_code" type="text" placeholder="400/SKIE/senibersikapbodoamat" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium font-mono">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Tahun Terbit</label>
              <input v-model="form.year" type="number" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Kategori</label>
              <select v-model="form.category" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all bg-slate-50 focus:bg-white text-sm font-medium">
                <option value="">Pilih kategori</option>
                <option value="Fiksi">Fiksi</option>
                <option value="Non-Fiksi">Non-Fiksi</option>
                <option value="Referensi">Referensi</option>
                <option value="Cerita Anak">Cerita Anak</option>
              </select>
            </div>

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
              Simpan Perubahan
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

const props = defineProps({
  book: { type: Object, default: () => ({}) },
});

const form = ref({
  title: props.book.title || '',
  author: props.book.author || '',
  publisher: props.book.publisher || '',
  year: props.book.year || '',
  isbn: props.book.isbn || '',
  barcode_code: props.book.barcode_code || '',
  category: props.book.category || '',
  quantity: props.book.quantity || 1,
  description: props.book.description || '',
});

const updateBook = () => {
  router.put(route('petugas.books.update', props.book.id), form.value);
};
</script>
