<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { confirmDialog, dangerConfirm, successAlert, promptDialog } from '@/Utils/sweetAlert';
import { ref, computed } from 'vue';

const props = defineProps({
  books: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const selectedCategory = ref('');
const showCategoryForm = ref(false);
const showBookForm = ref(false);
const newCategory = ref('');

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
    });
    return;
  }

  const result = await confirmDialog({
    title: 'Tambah Kategori?',
    html: `<p class="text-sm">Kategori baru: <strong>${newCategory.value}</strong></p>`,
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
        });
      }
    });
  }
}

async function deleteCategory(category) {
  const result = await dangerConfirm({
    title: 'Hapus Kategori?',
    html: `<p class="text-sm mb-3">Kategori <strong>${category}</strong> akan dihapus.</p>`,
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
        });
      }
    });
  }
}

async function deleteBook(book) {
  const result = await dangerConfirm({
    title: 'Hapus Buku?',
    html: `<p class="text-sm mb-3">Buku <strong>${book.title}</strong> akan dihapus dari sistem.</p>`,
    confirmButtonText: 'Ya, Hapus',
  });

  if (result) {
    router.delete(route('petugas.books.destroy', book.id), {
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
        });
      }
    });
  }
}
</script>

<template>
  <AuthenticatedLayout title="Kelola Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kelola Buku & Kategori</h1>
          <p class="mt-2 text-sm text-gray-500">Tambah, edit, dan kelola koleksi buku perpustakaan</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-8 border-b border-gray-200">
          <button @click="selectedCategory = ''" class="px-4 py-2 border-b-2 font-semibold transition" :class="selectedCategory === '' ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600 hover:text-gray-900'">
            📚 Semua Buku ({{ books.length }})
          </button>
          <button @click="selectedCategory = 'categories'" class="px-4 py-2 border-b-2 font-semibold transition" :class="selectedCategory === 'categories' ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600 hover:text-gray-900'">
            🏷️ Kategori ({{ categories.length }})
          </button>
        </div>

        <!-- Books Section -->
        <div v-if="selectedCategory !== 'categories'" class="space-y-6">
          <!-- Search & Filter -->
          <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex flex-col md:flex-row gap-4">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari judul atau penulis..." 
              class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            >
            <Link :href="route('petugas.books.create')" class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-500 text-white rounded-xl font-semibold hover:shadow-lg transition">
              ➕ Tambah Buku
            </Link>
          </div>

          <!-- Books Table -->
          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-gray-50/50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider text-gray-400">Penulis</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider text-gray-400">Kategori</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider text-gray-400">Stok</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                    <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-wider text-gray-400">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="book in filteredBooks" :key="book.id" class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                      <div class="font-semibold text-gray-900">{{ book.title }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-600">{{ book.author }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ book.category || 'Umum' }}</span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm font-semibold">{{ book.available_quantity }}/{{ book.quantity }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <span :class="book.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
                        {{ book.status === 'approved' ? 'Aktif' : 'Pending' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                      <div class="flex items-center justify-center gap-2">
                        <Link :href="route('petugas.books.edit', book.id)" class="text-blue-600 hover:text-blue-900 font-semibold text-sm">
                          Edit
                        </Link>
                        <button @click="deleteBook(book)" class="text-red-600 hover:text-red-900 font-semibold text-sm">
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredBooks.length === 0">
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada buku ditemukan.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Categories Section -->
        <div v-if="selectedCategory === 'categories'" class="space-y-6">
          <!-- Add Category Form -->
          <div v-if="!showCategoryForm" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <button @click="showCategoryForm = true" class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-500 text-white rounded-xl font-semibold hover:shadow-lg transition">
              ➕ Tambah Kategori Baru
            </button>
          </div>

          <div v-if="showCategoryForm" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-gray-900">Tambah Kategori Baru</h3>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kategori</label>
              <input 
                v-model="newCategory" 
                type="text" 
                placeholder="Masukkan nama kategori..." 
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              >
            </div>
            <div class="flex gap-3">
              <button @click="addCategory" class="px-6 py-2.5 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition">
                Simpan Kategori
              </button>
              <button @click="showCategoryForm = false; newCategory = ''" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                Batal
              </button>
            </div>
          </div>

          <!-- Categories List -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="category in categories" :key="category.id || category.name" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h3 class="text-lg font-bold text-gray-900">{{ category.name || category }}</h3>
              <p class="text-sm text-gray-500 mt-2">🏷️ Kategori</p>
              <button 
                @click="deleteCategory(category.name || category)" 
                class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-100 transition border border-red-200"
              >
                Hapus Kategori
              </button>
            </div>
            <div v-if="categories.length === 0" class="col-span-full text-center py-8 text-gray-500">
              Belum ada kategori. Silakan tambah kategori baru.
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
