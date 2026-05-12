<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
  categories: { type: Array, default: () => [] },
});

const form = ref({
  title: '',
  author: '',
  publisher: '',
  year: new Date().getFullYear(),
  isbn: '',
  category: '',
  quantity: 1,
  description: '',
  cover_image: null,
});

const errors = ref({});
const isSubmitting = ref(false);

function handleFileChange(event) {
  form.value.cover_image = event.target.files[0];
}

async function submitForm() {
  isSubmitting.value = true;
  errors.value = {};

  const formData = new FormData();
  Object.keys(form.value).forEach(key => {
    if (form.value[key] !== null) {
      formData.append(key, form.value[key]);
    }
  });

  router.post(route('admin.books.store'), formData, {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil!',
        text: 'Buku berhasil ditambahkan.',
        icon: 'success',
        confirmButtonColor: '#ea580c',
      }).then(() => {
        router.visit(route('admin.books'));
      });
    },
    onError: (err) => {
      errors.value = err;
      Swal.fire({
        title: 'Gagal!',
        text: 'Gagal menambah buku. Periksa kembali input Anda.',
        icon: 'error',
        confirmButtonColor: '#ea580c',
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}
</script>

<template>
  <AuthenticatedLayout title="Tambah Buku">
    <div class="min-h-screen">
      <div class="max-w-4xl mx-auto">
        <div class="mb-8">
          <Link :href="route('admin.books')" class="text-orange-600 hover:text-orange-700 font-semibold flex items-center gap-2 transition-colors">
            ← Kembali ke Daftar Buku
          </Link>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-slate-100 p-8">
          <h1 class="text-3xl font-bold text-slate-900 mb-8">Tambah Buku Baru</h1>

          <form @submit.prevent="submitForm" class="space-y-6">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Buku *</label>
              <input 
                v-model="form.title" 
                type="text" 
                placeholder="Masukkan judul buku..."
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': errors.title }"
              />
              <p v-if="errors.title" class="text-red-600 text-sm mt-1">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Penulis *</label>
              <input 
                v-model="form.author" 
                type="text" 
                placeholder="Masukkan nama penulis..."
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': errors.author }"
              />
              <p v-if="errors.author" class="text-red-600 text-sm mt-1">{{ errors.author }}</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">ISBN *</label>
              <input 
                v-model="form.isbn" 
                type="text" 
                placeholder="Masukkan ISBN..."
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': errors.isbn }"
              />
              <p v-if="errors.isbn" class="text-red-600 text-sm mt-1">{{ errors.isbn }}</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori *</label>
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
              <p v-if="errors.category" class="text-red-600 text-sm mt-1">{{ errors.category }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Penerbit</label>
                <input 
                  v-model="form.publisher" 
                  type="text" 
                  placeholder="Masukkan nama penerbit..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun Terbit</label>
                <input 
                  v-model.number="form.year" 
                  type="number" 
                  placeholder="Masukkan tahun terbit..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Stok *</label>
              <input 
                v-model.number="form.quantity" 
                type="number" 
                min="1"
                placeholder="Masukkan jumlah buku..."
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"
                :class="{ 'border-red-500 focus:ring-red-500/20 focus:border-red-500': errors.quantity }"
              />
              <p v-if="errors.quantity" class="text-red-600 text-sm mt-1">{{ errors.quantity }}</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
              <textarea 
                v-model="form.description" 
                placeholder="Masukkan deskripsi buku..."
                rows="5"
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all resize-none"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar Sampul</label>
              <input 
                type="file" 
                accept="image/*"
                @change="handleFileChange"
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 transition-all"
              />
              <p class="text-slate-500 text-xs mt-2">Format: JPEG, PNG, JPG, GIF. Ukuran maksimal: 2MB</p>
            </div>

            <div class="flex gap-4 pt-6 border-t border-slate-100">
              <button 
                type="submit"
                :disabled="isSubmitting"
                class="px-8 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm shadow-orange-600/10"
              >
                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Buku' }}
              </button>

              <Link 
                :href="route('admin.books')"
                class="px-8 py-3 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 font-semibold transition-colors flex items-center justify-center"
              >
                Batal
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>