<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { confirmDialog, dangerConfirm, successAlert } from '@/Utils/sweetAlert';
import { ref, computed } from 'vue';

const props = defineProps({
  books: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const selectedCategory = ref('');
const showCategoryForm = ref(false);
const newCategory = ref('');
const activeTab = ref('books');

const filteredBooks = computed(() => {
  return props.books.filter(book => {
    const matchesSearch = 
      book.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      book.author?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = selectedCategory.value ? book.category === selectedCategory.value : true;
    return matchesSearch && matchesCategory;
  });
});

async function addCategory() {
  if (!newCategory.value.trim()) {
    Swal.fire({
      title: 'Peringatan!',
      text: 'Harap masukkan nama kategori.',
      icon: 'warning',
      confirmButtonColor: '#ea580c'
    });
    return;
  }

  const result = await confirmDialog({
    title: 'Tambah Kategori?',
    html: `<p class="text-sm text-slate-600">Kategori baru: <strong class="text-slate-900">${newCategory.value}</strong></p>`,
    confirmButtonText: 'Ya, Tambah',
  });

  if (result) {
    router.post(route('admin.categories.store'), { name: newCategory.value }, {
      onSuccess: () => {
        newCategory.value = '';
        showCategoryForm.value = false;
        successAlert({
          title: 'Berhasil!',
          text: 'Kategori berhasil ditambahkan.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menambah kategori.',
          icon: 'error',
          confirmButtonColor: '#ea580c'
        });
      }
    });
  }
}

async function deleteCategory(category) {
  const result = await dangerConfirm({
    title: 'Hapus Kategori?',
    html: `<p class="text-sm text-slate-600">Kategori <strong class="text-slate-900">${category}</strong> akan dihapus.</p>`,
    confirmButtonText: 'Ya, Hapus',
  });

  if (result) {
    router.delete(route('admin.categories.destroy', category), {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Kategori berhasil dihapus.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menghapus kategori.',
          icon: 'error',
          confirmButtonColor: '#ea580c'
        });
      }
    });
  }
}

async function deleteBook(book) {
  const result = await dangerConfirm({
    title: 'Hapus Buku?',
    html: `<p class="text-sm text-slate-600">Buku <strong class="text-slate-900">${book.title}</strong> akan dihapus dari sistem.</p>`,
    confirmButtonText: 'Ya, Hapus',
  });

  if (result) {
    router.delete(route('admin.books.destroy', book.id), {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Buku berhasil dihapus.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menghapus buku.',
          icon: 'error',
          confirmButtonColor: '#ea580c'
        });
      },
    });
  }
}

function editBook(bookId) {
  router.visit(route('admin.books.edit', bookId));
}
</script>

<template>
  <AuthenticatedLayout title="Kelola Buku">
    <div class="min-h-screen bg-slate-50/50 py-6 ms-8">
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Buku</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ books.length }}</p>
          </div>
          <div class="rounded-xl bg-orange-50 p-3 text-orange-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Buku Tersedia</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ books.reduce((a, b) => a + (b.available_quantity || 0), 0) }}</p>
          </div>
          <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ categories.length }}</p>
          </div>
          <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Buku Habis</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ books.filter(b => b.available_quantity === 0).length }}</p>
          </div>
          <div class="rounded-xl bg-rose-50 p-3 text-rose-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="flex gap-2 border-b border-slate-200 mb-6">
        <button 
          @click="activeTab = 'books'"
          :class="[
            'px-4 py-2.5 text-sm font-semibold transition-all border-b-2 -mb-px',
            activeTab === 'books'
              ? 'border-orange-600 text-orange-600'
              : 'border-transparent text-slate-500 hover:text-slate-800'
          ]"
        >
          Daftar Buku
        </button>
        <button 
          @click="activeTab = 'categories'"
          :class="[
            'px-4 py-2.5 text-sm font-semibold transition-all border-b-2 -mb-px',
            activeTab === 'categories'
              ? 'border-orange-600 text-orange-600'
              : 'border-transparent text-slate-500 hover:text-slate-800'
          ]"
        >
          Kategori ({{ categories.length }})
        </button>
      </div>

      <div v-if="activeTab === 'books'" class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
          <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari judul atau penulis..." 
              class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 text-slate-800 transition-all placeholder:text-slate-400"
            />
          </div>

          <Link :href="route('admin.books.create')" class="w-full sm:w-auto px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-sm shadow-orange-600/10">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Buku
          </Link>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50/70 border-b border-slate-100">
                  <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Buku</th>
                  <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Penulis</th>
                  <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                  <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Stok</th>
                  <th class="px-6 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="book in filteredBooks" :key="book.id" class="hover:bg-slate-50/40 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-900 text-sm">{{ book.title }}</div>
                    <p class="text-xs text-slate-400 mt-0.5">ISBN: {{ book.isbn || '-' }}</p>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-600">{{ book.author }}</td>
                  <td class="px-6 py-4">
                    <span class="inline-flex px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-xs font-medium">
                      {{ book.category || 'Umum' }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs">
                      <p class="font-medium text-slate-700">{{ book.available_quantity }} dari {{ book.quantity }}</p>
                      <div class="w-20 h-1 bg-slate-100 rounded-full mt-1.5 overflow-hidden">
                        <div class="h-full bg-orange-500 rounded-full" :style="{ width: (book.available_quantity / book.quantity * 100) + '%' }"></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="[
                      'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium',
                      book.status === 'approved' 
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-amber-50 text-amber-700'
                    ]">
                      <span class="w-1.5 h-1.5 rounded-full" :class="book.status === 'approved' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                      {{ book.status === 'approved' ? 'Aktif' : 'Pending' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                      <button @click="editBook(book.id)" class="text-slate-400 hover:text-orange-600 transition-colors" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                      </button>
                      <button @click="deleteBook(book)" class="text-slate-400 hover:text-rose-600 transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredBooks.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Tidak ada buku yang ditemukan.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'categories'" class="space-y-4">
        <div v-if="!showCategoryForm" class="flex justify-end">
          <button @click="showCategoryForm = true" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kategori
          </button>
        </div>

        <div v-if="showCategoryForm" class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm space-y-4 max-w-xl">
  <h3 class="text-sm font-bold text-slate-800">Tambah Kategori Baru</h3>
  <div>
    <input 
      v-model="newCategory" 
      type="text" 
      placeholder="Masukkan nama kategori..." 
      class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-orange-500 text-slate-800"
    >
  </div>
  <div class="flex gap-2 justify-end">
    <button @click="showCategoryForm = false; newCategory = ''" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-200 transition-colors">
      Batal
    </button>
    <button @click="addCategory" class="px-3 py-1.5 bg-orange-600 text-white text-xs font-semibold rounded-lg hover:bg-orange-700 transition-colors">
      Simpan Kategori
    </button>
  </div>
</div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="category in categories" :key="category.id || category.name" class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-center justify-between group hover:border-slate-200 transition-all">
            <div>
              <h4 class="text-sm font-semibold text-slate-800">{{ category.name || category }}</h4>
              <p class="text-xs text-slate-400 mt-0.5">Kategori Master</p>
            </div>
            <button 
              @click="deleteCategory(category.name || category)" 
              class="text-slate-300 hover:text-rose-600 p-1 rounded-md transition-colors"
              title="Hapus Kategori"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>

          <div v-if="categories.length === 0" class="col-span-full text-center py-12 border border-dashed border-slate-200 rounded-xl bg-white text-slate-400 text-sm">
            Belum ada kategori data yang terdaftar.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>