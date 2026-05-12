<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
  book: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
});

const form = ref({
  title: props.book.title || '',
  author: props.book.author || '',
  publisher: props.book.publisher || '',
  year: props.book.year || new Date().getFullYear(),
  isbn: props.book.isbn || '',
  category: props.book.category || '',
  quantity: props.book.quantity || 1,
  available_quantity: props.book.available_quantity || 1,
  description: props.book.description || '',
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

  formData.append('_method', 'PUT');

  router.post(route('admin.books.update', props.book.id), formData, {
    onSuccess: () => {
      Swal.fire({
        title: 'Berhasil!',
        text: 'Buku berhasil diperbarui.',
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
        text: 'Gagal memperbarui buku. Periksa kembali input Anda.',
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
  <AuthenticatedLayout title="Edit Buku">
    <div class="min-h-screen bg-slate-50/50 py-8">
      <div class="max-w-3xl mx-auto px-4">
        
        <div class="mb-5">
          <Link :href="route('admin.books')" class="text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors flex items-center gap-1.5 uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar
          </Link>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 md:p-8">
          <div class="border-b border-slate-100 pb-5 mb-6">
            <h1 class="text-xl font-bold text-slate-900">Edit Informasi Buku</h1>
            <p class="text-sm text-slate-400 mt-1">Perbarui rincian logistik dan data buku untuk "{{ book.title }}"</p>
          </div>

          <form @submit.prevent="submitForm" class="space-y-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Buku <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.title" 
                type="text" 
                placeholder="Masukkan judul lengkap buku"
                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-slate-300"
                :class="{ 'border-rose-400 focus:ring-rose-500/20 focus:border-rose-500': errors.title }"
              />
              <p v-if="errors.title" class="text-rose-500 text-xs mt-1">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penulis <span class="text-rose-500">*</span></label>
              <input 
                v-model="form.author" 
                type="text" 
                placeholder="Nama penulis atau pencipta"
                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-slate-300"
                :class="{ 'border-rose-400 focus:ring-rose-500/20 focus:border-rose-500': errors.author }"
              />
              <p v-if="errors.author" class="text-rose-500 text-xs mt-1">{{ errors.author }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">ISBN <span class="text-rose-500">*</span></label>
                <input 
                  v-model="form.isbn" 
                  type="text" 
                  placeholder="Kode ISBN buku"
                  class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-slate-300"
                  :class="{ 'border-rose-400 focus:ring-rose-500/20 focus:border-rose-500': errors.isbn }"
                />
                <p v-if="errors.isbn" class="text-rose-500 text-xs mt-1">{{ errors.isbn }}</p>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori <span class="text-rose-500">*</span></label>
                <select 
                  v-model="form.category" 
                  class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all"
                  :class="{ 'border-rose-400 focus:ring-rose-500/20 focus:border-rose-500': errors.category }"
                >
                  <option value="">Pilih Kategori...</option>
                  <option v-for="category in categories" :key="category.id" :value="category.name">
                    {{ category.name }}
                  </option>
                </select>
                <p v-if="errors.category" class="text-rose-500 text-xs mt-1">{{ errors.category }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penerbit</label>
                <input 
                  v-model="form.publisher" 
                  type="text" 
                  placeholder="Nama perusahaan penerbit"
                  class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-slate-300"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Terbit</label>
                <input 
                  v-model.number="form.year" 
                  type="number" 
                  placeholder="Contoh: 2024"
                  class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all placeholder:text-slate-300"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Total Stok <span class="text-rose-500">*</span></label>
                <input 
                  v-model.number="form.quantity" 
                  type="number" 
                  min="1"
                  class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all"
                  :class="{ 'border-rose-400 focus:ring-rose-500/20 focus:border-rose-500': errors.quantity }"
                />
                <p v-if="errors.quantity" class="text-rose-500 text-xs mt-1">{{ errors.quantity }}</p>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Stok Tersedia di Rak</label>
                <input 
                  v-model.number="form.available_quantity" 
                  type="number" 
                  min="0"
                  class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sinopsis / Deskripsi</label>
              <textarea 
                v-model="form.description" 
                placeholder="Tulis ringkasan mengenai buku..."
                rows="4"
                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all resize-none placeholder:text-slate-300"
              ></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Gambar Sampul</label>
              <input 
                type="file" 
                accept="image/*"
                @change="handleFileChange"
                class="w-full text-sm text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer transition-all border border-slate-200 rounded-lg p-1"
              />
              <p class="text-slate-400 text-[11px] mt-1.5">Mendukung format JPEG, PNG, JPG maksimal 2MB</p>
            </div>

            <div class="flex gap-2 pt-4 border-t border-slate-100 justify-end">
              <Link 
                :href="route('admin.books')"
                class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 text-sm font-semibold transition-colors"
              >
                Batal
              </Link>
              
              <button 
                type="submit"
                :disabled="isSubmitting"
                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-semibold transition-colors disabled:opacity-50"
              >
                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>