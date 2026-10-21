<template>
  <div class="min-h-screen bg-slate-50/50 font-sans text-slate-900 antialiased selection:bg-orange-100">
    <nav class="sticky top-0 z-10 border-b border-slate-200/80 bg-white/90 backdrop-blur-md px-8 py-5">
      <div class="mx-auto max-w-6xl flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link 
            href="/dashboard" 
            class="group p-2 rounded-xl border border-slate-200 hover:border-orange-500 hover:bg-orange-50/50 transition-all duration-200"
          >
            <svg class="w-5 h-5 text-slate-500 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">SKIE LIBRARY SYSTEM</p>
            <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">Formulir Pengajuan</h1>
          </div>
        </div>
        <span class="text-[10px] font-black tracking-widest text-orange-600 uppercase bg-orange-50 px-3 py-1.5 rounded-md border border-orange-100">
          Sirkulasi Buku
        </span>
      </div>
    </nav>

    <main class="mx-auto max-w-6xl px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <div class="lg:col-span-4 space-y-8">
          <div class="aspect-[3/4.2] w-full overflow-hidden rounded-2xl border border-slate-200/60 bg-white p-3 shadow-sm">
            <img 
              :src="book.cover_image || 'https://placehold.co/400x560/f1f5f9/94a3b8?text=No+Cover'" 
              :alt="book.title"
              class="h-full w-full object-cover rounded-xl"
            />
          </div>

          <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-3">
            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400">Ketersediaan Stok</p>
            <div class="flex items-center justify-between">
              <span class="text-lg font-extrabold tracking-tight text-slate-900">
                {{ book.available_quantity }} Eksemplar
              </span>
              <span 
                :class="book.available_quantity > 0 ? 'bg-green-500' : 'bg-red-500'" 
                class="h-3 w-3 rounded-full animate-pulse"
              ></span>
            </div>
          </div>
        </div>

        <div class="lg:col-span-8 bg-white border border-slate-200/80 rounded-3xl p-10 shadow-sm space-y-10">
          
          <div class="border-b border-slate-100 pb-8">
            <span class="text-orange-600 font-extrabold text-xs tracking-[0.25em] uppercase">Buku yang Dipilih</span>
            <h2 class="mt-2 text-3xl font-black text-slate-900 tracking-tight leading-tight">
              {{ book.title }}
            </h2>
            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-slate-500">
              <p><span class="text-slate-400">Penulis:</span> {{ book.author }}</p>
              <p>&bull;</p>
              <p><span class="text-slate-400">Penerbit:</span> {{ book.publisher }} ({{ book.year }})</p>
              <p>&bull;</p>
              <p><span class="text-slate-400">ISBN:</span> {{ book.isbn || '-' }}</p>
            </div>
          </div>

          <form @submit.prevent="submitBorrow" class="space-y-8">
            
            <div v-if="errors.submit" class="p-5 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 text-red-700 text-sm font-semibold">
              <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              {{ errors.submit }}
            </div>

            <div v-if="book.available_quantity === 0" class="p-6 bg-blue-50 border border-blue-200 rounded-2xl space-y-4">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                <div>
                  <h4 class="font-extrabold text-sm text-blue-900">Buku Sedang Habis</h4>
                  <p class="text-xs text-blue-700 mt-1">Buku ini sedang tidak tersedia. Anda dapat masuk antrian untuk mendapatkan notifikasi saat buku tersedia.</p>
                </div>
              </div>
            </div>

            <template v-if="book.available_quantity > 0">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2.5">
                  <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">
                    Tanggal Pengambilan <span class="text-orange-500">*</span>
                  </label>
                  <input
                    v-model="form.borrow_date"
                    type="date"
                    :min="today"
                    required
                    class="w-full px-5 py-3.5 bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-orange-500 focus:bg-white rounded-xl text-sm font-bold text-slate-800 outline-none transition duration-200"
                    @change="resetDueDate"
                  />
                </div>

                <div class="space-y-2.5">
                  <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">
                    Batas Pengembalian <span class="text-orange-500">*</span>
                  </label>
                  <div class="relative flex items-center">
                    <input
                      v-model="form.due_date"
                      type="date"
                      :min="form.borrow_date || today"
                      required
                      class="w-full px-5 py-3.5 bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-orange-500 focus:bg-white rounded-xl text-sm font-bold text-slate-800 outline-none transition duration-200"
                    />
                    <span class="absolute right-4 text-[10px] font-black text-orange-600 bg-orange-50 px-2 py-1 rounded border border-orange-100 uppercase tracking-wider">
                      {{ borrowDuration }} Hari
                    </span>
                  </div>
                </div>
              </div>

              <div class="space-y-2.5">
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">
                  Jumlah Eksplorasi <span class="text-orange-500">*</span>
                </label>
                <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl p-2 max-w-xs">
                  <button 
                    type="button"
                    @click="decreaseQuantity"
                    :disabled="form.quantity <= 1"
                    class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition font-black text-sm text-slate-600 shadow-sm"
                  >
                    &minus;
                  </button>
                  <input
                    v-model.number="form.quantity"
                    type="number"
                    min="1"
                    :max="book.available_quantity"
                    required
                    class="flex-1 bg-transparent border-0 outline-none text-center font-bold text-sm text-slate-800 pointer-events-none"
                  />
                  <button 
                    type="button"
                    @click="increaseQuantity"
                    :disabled="form.quantity >= book.available_quantity"
                    class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition font-black text-sm text-slate-600 shadow-sm"
                  >
                    &#43;
                  </button>
                </div>
              </div>

              <div class="space-y-2.5">
                <div class="flex justify-between items-center">
                  <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">
                    Catatan Tambahan
                  </label>
                  <span class="text-[10px] font-bold text-slate-400">{{ form.notes.length }}/500</span>
                </div>
                <textarea
                  v-model="form.notes"
                  rows="4"
                  maxlength="500"
                  class="w-full px-5 py-4 bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-orange-500 focus:bg-white rounded-xl text-sm font-semibold text-slate-700 outline-none transition duration-200 resize-none"
                  placeholder="Tulis keperluan atau pesan tambahan disini..."
                />
              </div>
            </template>

            <div class="p-6 bg-orange-50/40 border border-orange-100/40 rounded-2xl space-y-4">
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-orange-100/60 text-orange-600 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">Peraturan Peminjaman SKIE.LIB</h4>
              </div>
              <ul class="text-xs text-slate-500 space-y-2.5 pl-11 list-disc font-medium leading-relaxed">
                <li>Buku terlambat dikembalikan dikenakan denda <span class="font-bold text-orange-600">Rp 1.000 / hari</span>.</li>
                <li>Batas peminjaman maksimal dalam satu waktu adalah <span class="font-bold text-slate-700">3 eksemplar</span>.</li>
                <li>Anggota yang memiliki tunggakan tidak diperbolehkan mengajukan buku baru.</li>
              </ul>
            </div>

            <div class="border-t border-slate-100 pt-8 flex items-center justify-between gap-4">
              <Link 
                href="/dashboard"
                class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors"
              >
                Batal
              </Link>
              
              <div class="flex gap-3">
                <template v-if="book.available_quantity > 0">
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="inline-flex items-center gap-2.5 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black tracking-wider uppercase transition-all shadow-md shadow-orange-500/10 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="isSubmitting" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>{{ isSubmitting ? 'Memproses...' : 'Kirim Pengajuan' }}</span>
                  </button>
                </template>
                <template v-else>
                  <button
                    type="button"
                    @click="queueBook"
                    class="inline-flex items-center gap-2.5 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black tracking-wider uppercase transition-all shadow-md shadow-blue-500/10 active:scale-95"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0-6h-6"/>
                    </svg>
                    <span>⏳ Masuk Antrian</span>
                  </button>
                </template>
              </div>
            </div>

          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { dangerConfirm, successAlert } from '@/Utils/sweetAlert';
import Swal from 'sweetalert2';

const props = defineProps({
  book: {
    type: Object,
    required: true
  }
});

const form = useForm({
  book_id: computed(() => props.book.id),
  borrow_date: '',
  due_date: '',
  quantity: 1,
  notes: ''
});

const isSubmitting = ref(false);
const errors = ref({});

const today = computed(() => {
  const date = new Date();
  return date.toISOString().split('T')[0];
});

const borrowDuration = computed(() => {
  if (!form.borrow_date || !form.due_date) return 0;
  const start = new Date(form.borrow_date);
  const end = new Date(form.due_date);
  return Math.ceil((end - start) / (1000 * 60 * 60 * 24));
});

watch(() => props.book, () => {
  form.borrow_date = today.value;
  const nextDate = new Date();
  nextDate.setDate(nextDate.getDate() + 7);
  form.due_date = nextDate.toISOString().split('T')[0];
  errors.value = {};
}, { immediate: true });

function resetDueDate() {
  if (form.borrow_date) {
    const date = new Date(form.borrow_date);
    date.setDate(date.getDate() + 7);
    form.due_date = date.toISOString().split('T')[0];
  }
}

function increaseQuantity() {
  if (form.quantity < props.book.available_quantity) {
    form.quantity++;
  }
}

function decreaseQuantity() {
  if (form.quantity > 1) {
    form.quantity--;
  }
}

function submitBorrow() {
  errors.value = {};
  isSubmitting.value = true;

  form.post(route('borrow.store'), {
    onSuccess: () => {
      window.location.href = '/my-loans?success=1';
    },
    onError: (err) => {
      isSubmitting.value = false;
      errors.value = err;

      // Ambil semua pesan error dari objek `err` dan gabungkan jadi satu string
      const errorMessage = Object.values(err).join(' ').toLowerCase();

      // 1. Kasus: Batas Maksimal Peminjaman (3 Buku)
      if (errorMessage.includes('batas maksimal') || errorMessage.includes('3 buku')) {
        Swal.fire({
          title: '⚠️ Batas Peminjaman Tercapai',
          html: `<p class="text-sm mb-3">Anda sudah mencapai batas maksimal peminjaman <strong>(3 buku)</strong>.</p><p class="text-sm text-slate-600">Kembalikan salah satu buku Anda terlebih dahulu untuk dapat meminjam buku lainnya.</p>`,
          icon: 'warning',
          confirmButtonText: 'Lihat Peminjaman Saya',
          confirmButtonColor: '#f97316',
          showCancelButton: true,
          cancelButtonText: 'Batal',
          cancelButtonColor: '#94a3b8',
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = '/my-loans';
          }
        });
      } 
      // 2. Kasus: Akun Dibatasi / Suspended
      else if (errorMessage.includes('dibatasi') || errorMessage.includes('dinonaktifkan') || errorMessage.includes('suspend')) {
        Swal.fire({
          title: '🚫 Peminjaman Dibatasi',
          html: `<p class="text-sm mb-3">Akun Anda saat ini <strong>sedang dibatasi</strong> untuk melakukan peminjaman.</p><p class="text-sm text-slate-600">Silakan hubungi petugas perpustakaan untuk informasi lebih lanjut.</p>`,
          icon: 'error',
          confirmButtonText: 'Ngerti',
          confirmButtonColor: '#ef4444',
        });
      }
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

async function queueBook() {
  const result = await dangerConfirm({
    title: 'Masuk Antrian Buku?',
    html: `<p class="text-sm mb-3">Tambahkan <strong>${props.book.title}</strong> ke antrian buku Anda?</p><p class="text-xs text-slate-500">Anda akan menerima notifikasi ketika buku tersedia.</p>`,
    confirmButtonText: 'Ya, Masuk Antrian',
  });

  if (result) {
    router.post(route('queue.store'), {
      book_id: props.book.id
    }, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Buku berhasil ditambahkan ke antrian. Anda akan menerima notifikasi saat buku tersedia.',
        });
        setTimeout(() => {
          window.location.href = '/my-queue';
        }, 1500);
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menambahkan ke antrian.',
          icon: 'error',
        });
      }
    });
  }
}
</script>
