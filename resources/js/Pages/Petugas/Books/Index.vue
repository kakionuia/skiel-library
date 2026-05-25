<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans antialiased text-slate-800">
    <!-- Sidebar -->
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/40" />

    <!-- Main Content -->
    <main class="flex-1 ml-80 min-h-screen pb-16">
      <!-- Header -->
      <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Koleksi Buku</h1>
            <p class="text-slate-400 text-sm mt-0.5">Katalog dan pengaturan inventori perpustakaan</p>
          </div>
          <Link :href="'/petugas/books/create'" class="bg-orange-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-orange-600 transition-all shadow-sm shadow-orange-500/10 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Koleksi
          </Link>
        </div>
      </header>

      <div class="px-10 py-8 max-w-[1400px] mx-auto space-y-6">
        
        <!-- Filter & Search Bar -->
        <div class="flex items-center gap-4 bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
          <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input v-model="searchQuery" type="text" placeholder="Cari judul, penulis, atau nomor ISBN..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-slate-800 transition-all placeholder:text-slate-400">
          </div>
          <select v-model="selectedCategory" class="bg-slate-50 border border-slate-200/60 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all">
            <option value="">Semua Kategori</option>
            <option value="Fiksi">Fiksi</option>
            <option value="Non-Fiksi">Non-Fiksi</option>
            <option value="Referensi">Referensi</option>
          </select>
        </div>

        <!-- Custom Navigation Tabs -->
        <div class="flex gap-2 border-b border-slate-200/80">
          <button @click="activeTab = 'available'" :class="tabButtonClass('available')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 -mb-px tracking-wide flex items-center gap-2">
            <svg class="w-4 h-4" :class="activeTab === 'available' ? 'text-orange-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Koleksi Tersedia ({{ availableCount }})
          </button>
          <button @click="activeTab = 'pending'" :class="tabButtonClass('pending')" class="px-5 py-3 font-semibold text-sm transition-all border-b-2 -mb-px tracking-wide flex items-center gap-2">
            <svg class="w-4 h-4" :class="activeTab === 'pending' ? 'text-orange-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Menunggu Approval ({{ pendingCount }})
          </button>
        </div>

        <!-- Info Description Banner (Minimalist) -->
        <div v-if="activeTab === 'available'" class="p-4 bg-white rounded-xl border-l-4 border-orange-500 shadow-sm">
          <p class="text-sm text-slate-600 font-medium">Menampilkan buku yang sudah disetujui oleh Administrator dan saat ini aktif di dalam katalog perpustakaan.</p>
        </div>
        <div v-else class="p-4 bg-white rounded-xl border-l-4 border-slate-400 shadow-sm">
          <p class="text-sm text-slate-600 font-medium">Menampilkan draf atau pengajuan buku baru dari petugas yang sedang menunggu verifikasi validasi dari Admin.</p>
        </div>

        <!-- Catalog Data Table -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Buku</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">ISBN</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Ketersediaan Stok</th>
                  <th class="px-8 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                  <th class="px-8 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="book in filteredBooks" :key="book.id" class="hover:bg-slate-50/40 transition-colors duration-150">
                  <!-- Book Title & Cover -->
                  <td class="px-8 py-4.5">
                    <div class="flex gap-4 items-center">
                      <div class="w-11 h-14 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 flex items-center justify-center border border-slate-200/60 shadow-sm">
                        <img v-if="book.cover" :src="book.cover" class="w-full h-full object-cover">
                        <svg v-else class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-slate-800 leading-snug">{{ book.title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ book.author }}</p>
                      </div>
                    </div>
                  </td>
                  
                  <!-- ISBN -->
                  <td class="px-8 py-4.5 text-xs font-mono text-slate-500">{{ book.isbn || '-' }}</td>
                  
                  <!-- Stock Indicator -->
                  <td class="px-8 py-4.5">
                    <div class="max-w-[130px]">
                      <p class="text-xs font-semibold text-slate-600">{{ book.available }} / {{ book.total }} <span class="text-slate-400 font-normal">buku</span></p>
                      <div class="w-full h-1.5 bg-slate-100 rounded-full mt-1.5 overflow-hidden">
                        <div :style="{ width: Math.min((book.available / book.total * 100), 100) + '%' }" class="h-full bg-orange-500 rounded-full transition-all duration-300"></div>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Category -->
                  <td class="px-8 py-4.5">
                    <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">{{ book.category }}</span>
                  </td>
                  
                  <!-- Status & Action Buttons -->
                  <td class="px-8 py-4.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                      <!-- Inline Minimalist Badge Status -->
                      <span v-if="book.status === 'available'" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 mr-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Aktif
                      </span>
                      <span v-else class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 mr-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Pending
                      </span>

                      <!-- Edit Button -->
                      <Link :href="`/petugas/books/${book.id}/edit`" class="p-1.5 text-slate-400 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all" title="Edit Koleksi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                      </Link>

                      <!-- Delete Button -->
                      <button @click="deleteBook(book)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Koleksi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                
                <!-- Empty State -->
                <tr v-if="filteredBooks.length === 0">
                  <td colspan="5" class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                      <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                      <p class="text-slate-500 font-semibold text-sm mt-1">Katalog data kosong</p>
                      <p class="text-slate-400 text-xs">Tidak ada data koleksi buku pada kategori atau tab filter yang Anda pilih.</p>
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
    ? 'border-orange-500 text-orange-500 font-bold'
    : 'border-transparent text-slate-400 hover:text-slate-700 font-medium';
};

const deleteBook = async (book) => {
  if (await dangerConfirm({
    title: 'Hapus buku ini?',
    text: `"${book.title}" akan dihapus permanen dari data inventori perpustakaan.`,
    confirmButtonText: 'Ya, hapus',
  })) {
    router.delete(route('petugas.books.destroy', book.id));
  }
};
</script>