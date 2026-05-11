<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="close"></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="sticky top-0 bg-gradient-to-r from-orange-600 to-orange-500 p-6 text-white flex justify-between items-center">
        <div>
          <h2 class="text-2xl font-bold">Pinjam Buku</h2>
          <p class="text-orange-100 text-sm mt-1">{{ book.title }}</p>
        </div>
        <button @click="close" class="text-white hover:text-orange-200 transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 md:p-8">
        <!-- Book Info -->
        <div class="mb-8 flex gap-6 pb-8 border-b border-slate-200">
          <img 
            :src="book.cover_image || 'https://placehold.co/120x180/E67E22/white?text=Book'"
            :alt="book.title"
            class="w-32 h-44 object-cover rounded-xl shadow-md flex-shrink-0"
          />
          <div class="flex-1">
            <h3 class="text-2xl font-bold text-slate-900 mb-2">{{ book.title }}</h3>
            <p class="text-slate-600 mb-1"><span class="text-slate-400">Penulis:</span> {{ book.author }}</p>
            <p class="text-slate-600 mb-1"><span class="text-slate-400">Penerbit:</span> {{ book.publisher }}</p>
            <p class="text-slate-600 mb-1"><span class="text-slate-400">Tahun:</span> {{ book.year }}</p>
            <p class="text-slate-600 mb-4"><span class="text-slate-400">Kategori:</span> {{ book.category }}</p>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Tersedia {{ book.available_quantity }} eksemplar
            </div>
          </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitBorrow" class="space-y-6">
          <!-- Error Messages -->
          <div v-if="errors.submit" class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            {{ errors.submit }}
          </div>

          <!-- Tanggal Peminjaman -->
          <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
              Tanggal Peminjaman
              <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.borrow_date"
              type="date"
              :min="today"
              required
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition"
              @change="resetDueDate"
            />
            <p class="text-slate-500 text-xs mt-1">Pilih tanggal Anda akan mengambil buku</p>
          </div>

          <!-- Tanggal Jatuh Tempo -->
          <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
              Tanggal Jatuh Tempo
              <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.due_date"
              type="date"
              :min="form.borrow_date || today"
              required
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition"
            />
            <p class="text-slate-500 text-xs mt-1">
              Durasi peminjaman: {{ borrowDuration }} hari
              <span v-if="borrowDuration > 14" class="text-orange-600 font-semibold">
                (Max 14 hari, akan dipotong)
              </span>
            </p>
          </div>

          <!-- Jumlah Buku -->
          <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
              Jumlah Buku
              <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-3">
              <button 
                type="button"
                @click="decreaseQuantity"
                :disabled="form.quantity <= 1"
                class="w-10 h-10 rounded-xl border border-slate-300 flex items-center justify-center hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                −
              </button>
              <input
                v-model.number="form.quantity"
                type="number"
                min="1"
                :max="book.available_quantity"
                required
                class="flex-1 px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition text-center"
              />
              <button 
                type="button"
                @click="increaseQuantity"
                :disabled="form.quantity >= book.available_quantity"
                class="w-10 h-10 rounded-xl border border-slate-300 flex items-center justify-center hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                +
              </button>
            </div>
            <p class="text-slate-500 text-xs mt-1">Maks {{ book.available_quantity }} eksemplar</p>
          </div>

          <!-- Catatan Tambahan -->
          <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">
              Catatan Tambahan (Opsional)
            </label>
            <textarea
              v-model="form.notes"
              rows="3"
              maxlength="500"
              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition resize-none"
              placeholder="Contoh: Buku diperlukan untuk tugas sekolah..."
            />
            <p class="text-slate-500 text-xs mt-1">{{ form.notes.length }} / 500 karakter</p>
          </div>

          <!-- Info Penting -->
          <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-3">
            <h4 class="font-bold text-amber-900 flex items-center gap-2">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Peraturan Peminjaman
            </h4>
            <ul class="text-sm text-amber-800 space-y-1 ml-7 list-disc">
              <li>Perpanjangan peminjaman berlaku jika buku tidak sedang dicari oleh orang lain</li>
              <li>Buku yang terlambat dikenakan denda Rp 1.000 per hari</li>
              <li>Maksimal 3 buku dalam satu waktu</li>
              <li>Jika Anda memiliki tunggakan, tidak dapat meminjam</li>
            </ul>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="close"
              class="flex-1 px-6 py-3 border border-slate-300 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-500 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2"
            >
              <svg v-if="isSubmitting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <span>{{ isSubmitting ? 'Sedang Memproses...' : 'Ajukan Peminjaman' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  book: {
    type: Object,
    required: true
  },
  isOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'success']);

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

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    form.borrow_date = today.value;
    const nextDate = new Date();
    nextDate.setDate(nextDate.getDate() + 7);
    form.due_date = nextDate.toISOString().split('T')[0];
    errors.value = {};
  }
});

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
      emit('success');
      close();
    },
    onError: (error) => {
      errors.value = error;
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
}

function close() {
  form.reset();
  errors.value = {};
  emit('close');
}
</script>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  border-radius: 4px;
  margin-right: 2px;
  opacity: 0.6;
  filter: invert(0.8);
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
  opacity: 1;
}
</style>
