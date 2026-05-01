<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen pb-12">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Koleksi Buku</h1>
            <p class="text-slate-500 text-sm">Katalog dan pengaturan inventori perpustakaan</p>
          </div>
          <Link :href="'/petugas/books/create'" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Koleksi
          </Link>
        </div>
      </header>

      <div class="px-10 py-8 max-w-[1400px] mx-auto">
        <div class="flex items-center gap-4 mb-8 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
          <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input v-model="searchQuery" type="text" placeholder="Cari judul, penulis, atau ISBN..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-slate-200 text-sm">
          </div>
          <select v-model="selectedCategory" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 focus:ring-2 focus:ring-slate-200">
            <option value="">Semua Kategori</option>
            <option value="Fiksi">Fiksi</option>
            <option value="Non-Fiksi">Non-Fiksi</option>
            <option value="Referensi">Referensi</option>
          </select>
        </div>

        <div class="flex gap-3 mb-8">
          <button @click="activeTab = 'available'" :class="tabButtonClass('available')" class="px-6 py-3 rounded-xl font-bold text-sm transition-all">
            <span v-if="activeTab === 'available'" class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6H8a2 2 0 012 2v10a2 2 0 01-2 2H6h-.5a1.5 1.5 0 01-1.5-1.5V7H3a2 2 0 01-2-2V5zm2.5 7a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm2.5 4a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
              Koleksi Tersedia ({{ availableCount }})
            </span>
            <span v-else>Koleksi Tersedia ({{ availableCount }})</span>
          </button>
          <button @click="activeTab = 'pending'" :class="tabButtonClass('pending')" class="px-6 py-3 rounded-xl font-bold text-sm transition-all">
            <span v-if="activeTab === 'pending'" class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/></svg>
              Menunggu Approval ({{ pendingCount }})
            </span>
            <span v-else>Menunggu Approval ({{ pendingCount }})</span>
          </button>
        </div>

        <div v-if="activeTab === 'available'" class="mb-4 p-4 bg-emerald-50 rounded-xl border border-emerald-200">
          <p class="text-sm text-emerald-700 font-medium">✓ Buku yang sudah disetujui admin dan tersedia di katalog perpustakaan</p>
        </div>
        <div v-else class="mb-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
          <p class="text-sm text-amber-700 font-medium">⏳ Pengajuan buku yang sedang menunggu persetujuan dari admin</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Judul & Penulis</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">ISBN</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Ketersediaan</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</th>
                  <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Status & Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="book in filteredBooks" :key="book.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-8 py-5">
                    <div class="flex gap-4 items-start">
                      <div class="w-12 h-16 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 flex items-center justify-center">
                        <img v-if="book.cover" :src="book.cover" class="w-full h-full object-cover">
                        <svg v-else class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17.25m20-11.002c5.003 5.987 8.5 10.736 8.5 11.002M15.75 13v.008v-.008m0 0v.008v-.008m0 0h.008h-.008m0 0h.008h-.008"/></svg>
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-800">{{ book.title }}</p>
                        <p class="text-[11px] text-slate-400">{{ book.author }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-5 text-[11px] font-medium text-slate-600 font-mono">{{ book.isbn }}</td>
                  <td class="px-8 py-5">
                    <div class="space-y-2">
                      <p class="text-[11px] font-semibold text-slate-600">{{ book.available }}/{{ book.total }} Tersedia</p>
                      <div class="w-32 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div :style="{ width: (book.available / book.total * 100) + '%' }" class="h-full bg-emerald-500 rounded-full transition-all"></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <span class="text-xs font-semibold text-slate-600 bg-slate-50 px-3 py-1.5 rounded-lg">{{ book.category }}</span>
                  </td>
                  <td class="px-8 py-5 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <span v-if="book.status === 'available'" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg ring-1 ring-emerald-200">
                        ✓ Tersedia
                      </span>
                      <span v-else class="px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg ring-1 ring-amber-200">
                        ⏳ Menunggu
                      </span>
                      <Link :href="`/petugas/books/${book.id}/edit`" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-bold text-orange-600 hover:bg-orange-50 rounded-lg transition-all border border-transparent hover:border-orange-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                      </Link>
                      <button @click="deleteBook(book)" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition-all border border-transparent hover:border-rose-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredBooks.length === 0">
                  <td colspan="5" class="px-8 py-12 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                      <svg v-if="activeTab === 'available'" class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                      <svg v-else class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      <p v-if="activeTab === 'available'" class="text-slate-500 font-medium">Belum ada buku yang disetujui</p>
                      <p v-else class="text-slate-500 font-medium">Belum ada pengajuan buku yang menunggu</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { dangerConfirm } from '@/Utils/sweetAlert';

const props = defineProps({
  books: { type: Array, default: () => [] },
});

const activeTab = ref('available');
const searchQuery = ref('');
const selectedCategory = ref('');

const filteredBooks = computed(() => {
  return props.books.filter(book => {
    const matchesSearch = book.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         book.author?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         book.isbn?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = selectedCategory.value ? book.category === selectedCategory.value : true;
    const matchesTab = activeTab.value === 'available' ? book.status === 'available' : book.status === 'pending';
    return matchesSearch && matchesCategory && matchesTab;
  });
});

const availableCount = computed(() => {
  return props.books.filter(b => b.status === 'available').length;
});

const pendingCount = computed(() => {
  return props.books.filter(b => b.status === 'pending').length;
});

const tabButtonClass = (tab) => {
  return activeTab.value === tab
    ? 'bg-slate-900 text-white border border-slate-700 shadow-lg shadow-slate-200'
    : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100';
};

const deleteBook = async (book) => {
  if (await dangerConfirm({
    title: 'Hapus buku ini?',
    text: `"${book.title}" akan dihapus dan tindakan ini tidak dapat dibatalkan.`,
    confirmButtonText: 'Ya, hapus',
  })) {
    router.delete(route('petugas.books.destroy', book.id));
  }
};
</script>
