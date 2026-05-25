<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <nav class="border-b border-slate-200 bg-white">
      <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4">
        <Link href="/my-loans" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-orange-700">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Rak Buku
        </Link>
        <Link :href="`/books/${book.id}`" class="text-sm font-semibold text-orange-700 hover:text-orange-800">
          Detail Buku
        </Link>
      </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-8">
      <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
        <aside class="h-fit rounded-xl border border-slate-200 bg-white p-5">
          <img
            :src="book.cover_image || 'https://placehold.co/320x480/E67E22/white?text=' + encodeURIComponent(book.title)"
            :alt="book.title"
            class="aspect-[2/3] w-full rounded-lg object-cover"
          />
          <h1 class="mt-5 text-xl font-black leading-tight">{{ book.title }}</h1>
          <p class="mt-1 text-sm font-medium text-slate-500">{{ book.author || '-' }}</p>
          <p class="mt-3 w-fit rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-700 ring-1 ring-orange-100">
            {{ book.category }}
          </p>
        </aside>

        <section class="rounded-xl border border-slate-200 bg-white p-6 md:p-8">
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Ulasan Buku</p>
          <h2 class="mt-2 text-2xl font-black tracking-tight">Bagikan penilaian kamu</h2>
          <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500">
            Ulasan ini akan tampil di halaman detail buku dan ikut menghitung rata-rata rating buku.
          </p>

          <form class="mt-8 space-y-6" @submit.prevent="submitReview">
            <div>
              <label class="mb-3 block text-sm font-bold text-slate-900">Rating</label>
              <div class="flex items-center gap-2">
                <button
                  v-for="star in 5"
                  :key="star"
                  type="button"
                  @click="form.rating = star"
                  @mouseenter="hoveredRating = star"
                  @mouseleave="hoveredRating = 0"
                  class="rounded-md p-1 transition hover:bg-amber-50"
                  :aria-label="`${star} bintang`"
                >
                  <svg class="h-9 w-9" :class="star <= displayRating ? 'text-amber-400' : 'text-slate-200'" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                </button>
                <span class="ml-2 text-sm font-bold text-slate-600">{{ form.rating }} / 5</span>
              </div>
              <p v-if="form.errors.rating" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.rating }}</p>
            </div>

            <div>
              <label class="mb-3 block text-sm font-bold text-slate-900">Komentar</label>
              <textarea
                v-model="form.comment"
                rows="7"
                maxlength="1500"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                placeholder="Tulis pengalaman membaca buku ini..."
              />
              <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
                <span v-if="form.errors.comment" class="font-semibold text-red-600">{{ form.errors.comment }}</span>
                <span class="ml-auto">{{ form.comment?.length || 0 }} / 1500</span>
              </div>
            </div>

            <div>
              <label class="mb-3 block text-sm font-bold text-slate-900">Lampirkan Foto (Opsional)</label>
              <div class="relative">
                <input
                  ref="fileInput"
                  type="file"
                  @change="handleImageUpload"
                  accept="image/*"
                  class="sr-only"
                />
                <div
                  @click="$refs.fileInput.click()"
                  class="border-2 border-dashed border-slate-200 rounded-lg p-6 text-center cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-all"
                  :class="imagePreview ? 'border-orange-400 bg-orange-50' : 'bg-slate-50'"
                >
                  <div v-if="!imagePreview" class="py-4">
                    <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2 text-sm font-semibold text-slate-700">Klik untuk memilih gambar</p>
                    <p class="text-xs text-slate-500 mt-1">JPG, PNG, atau WebP (Maks. 2MB)</p>
                  </div>
                  <div v-else class="relative">
                    <img :src="imagePreview" class="w-full h-48 object-cover rounded-lg" />
                    <button
                      type="button"
                      @click.stop="removeImage"
                      class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <p v-if="form.errors.image" class="mt-2 text-sm font-semibold text-red-600">{{ form.errors.image }}</p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
              <Link :href="`/books/${book.id}`" class="inline-flex justify-center rounded-lg border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
                Batal
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center rounded-lg bg-orange-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                {{ form.processing ? 'Menyimpan...' : 'Simpan Ulasan' }}
              </button>
            </div>
          </form>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  book: {
    type: Object,
    required: true
  },
  existingReview: {
    type: Object,
    default: null
  }
});

const hoveredRating = ref(0);
const imagePreview = ref(null);
const fileInput = ref(null);

const form = useForm({
  book_id: props.book.id,
  rating: props.existingReview?.rating || 5,
  comment: props.existingReview?.comment || '',
  image: null
});

const displayRating = computed(() => hoveredRating.value || form.rating);

function handleImageUpload(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  // Validate file type
  if (!file.type.startsWith('image/')) {
    form.setError('image', 'File harus berupa gambar');
    return;
  }

  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    form.setError('image', 'Ukuran gambar maksimal 2MB');
    return;
  }

  form.image = file;

  // Preview image
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target?.result;
  };
  reader.readAsDataURL(file);
}

function removeImage() {
  form.image = null;
  imagePreview.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

function submitReview() {
  const formData = new FormData();
  formData.append('book_id', form.book_id);
  formData.append('rating', form.rating);
  formData.append('comment', form.comment || '');
  
  if (form.image) {
    formData.append('image', form.image);
  }

  router.post(route('dashboard.reviews.store'), formData, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        title: 'Ulasan Tersimpan',
        text: 'Terima kasih, ulasan kamu sudah tampil di detail buku.',
        icon: 'success',
        confirmButtonColor: '#ea580c'
      }).then(() => router.visit(`/books/${props.book.id}`));
    },
    onError: (errors) => {
      if (errors.image) {
        form.setError('image', errors.image);
      }
    }
  });
}
</script>
