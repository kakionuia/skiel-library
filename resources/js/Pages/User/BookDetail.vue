<template>
  <div class="min-h-screen bg-white font-sans text-slate-800 antialiased flex flex-col">
    
    <div class="bg-white border-b border-slate-100 py-4 px-6 sm:px-10">
      <div class="max-w-7xl mx-auto flex items-center gap-3 text-xs sm:text-sm">
        <Link href="/dashboard" class="text-slate-400 hover:text-[#D37B3B] font-medium transition-colors">
          Beranda
        </Link>
        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-500 font-medium">{{ book.category || 'Semua Buku' }}</span>
        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold truncate max-w-[180px] sm:max-w-xs">{{ book.title }}</span>
      </div>
    </div>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 w-full">
      
      <div class="lg:col-span-5 bg-[#D37B3B] p-8 sm:p-16 flex flex-col justify-between relative min-h-[450px] lg:min-h-0">
        <Link 
          href="/dashboard" 
          class="absolute top-8 left-8 w-12 h-12 rounded-full bg-black/10 hover:bg-black/20 flex items-center justify-center text-white transition-all duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
        </Link>

        <div class="my-auto flex justify-center items-center">
          <div class="relative max-w-[280px] sm:max-w-[340px] w-full rounded-lg overflow-hidden shadow-2xl transition-transform duration-300 hover:scale-[1.01]">
            <img 
              :src="book.cover_image || 'https://placehold.co/400x600/E67E22/white?text=' + encodeURIComponent(book.title)"
              :alt="book.title"
              class="w-full h-auto object-cover aspect-[2/3]"
            />
          </div>
        </div>
      </div>

      <div class="lg:col-span-7 p-8 sm:p-16 lg:p-20 flex flex-col justify-between bg-white">
        <div class="space-y-10">
          
          <div>
            <h2 class="text-xs font-bold text-[#D37B3B] uppercase tracking-widest mb-2">Informasi Mengenai Buku</h2>
            <h1 class="text-3xl sm:text-4.5xl font-black text-slate-950 tracking-tight leading-tight">
              {{ book.title }}
            </h1>
            <div class="mt-4 inline-flex items-center gap-2 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2">
              <div class="flex items-center gap-0.5">
                <svg v-for="star in 5" :key="star" class="w-4 h-4" :class="star <= Math.round(book.average_rating || 0) ? 'text-amber-400' : 'text-slate-200'" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
              </div>
              <span class="text-sm font-bold text-slate-800">{{ book.average_rating || 0 }}</span>
              <span class="text-xs font-semibold text-slate-400">({{ book.reviews_count || 0 }} ulasan)</span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-y-6 gap-x-8 text-sm border-t border-slate-100 pt-8">
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">ID Buku</span>
              <span class="font-semibold text-slate-800">#{{ book.id }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">Status</span>
              <span 
                class="font-bold inline-flex items-center gap-1.5"
                :class="book.available_quantity > 0 ? 'text-emerald-600' : 'text-rose-600'"
              >
                <span class="w-2 h-2 rounded-full" :class="book.available_quantity > 0 ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                {{ book.available_quantity > 0 ? 'Tersedia' : 'Sedang Dipinjam' }}
              </span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">Nama Buku</span>
              <span class="font-semibold text-slate-800">{{ book.title }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">Status Antrean</span>
              <span class="font-semibold text-slate-700 flex items-center gap-1">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ queueInfo.queue_count || 0 }} menunggu antre
              </span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">Penulis</span>
              <span class="font-bold text-[#D37B3B] hover:underline cursor-pointer">{{ book.author }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">Peminjaman</span>
              <span class="font-bold text-slate-800">Telah Dipinjam {{ book.borrows_count || 0 }} kali</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block font-bold uppercase tracking-wider mb-1">Tahun Terbit</span>
              <span class="font-semibold text-slate-800">{{ book.year || '-' }}</span>
            </div>
          </div>

          <div class="pt-4">
            <button 
              v-if="book.available_quantity > 0"
              @click="handleBorrowClick"
              class="w-full sm:w-auto px-10 py-4 rounded-lg font-bold text-base tracking-wide transition-all duration-200 block text-center bg-[#D37B3B] text-white hover:bg-orange-700 active:scale-[0.99] shadow-lg shadow-orange-700/10"
            >
              Pinjam Buku
            </button>

            <button 
              v-else
              @click="addToQueue"
              :disabled="isInQueue || isAddingQueue"
              class="w-full sm:w-auto px-10 py-4 rounded-lg font-bold text-base tracking-wide transition-all duration-200 block text-center text-white disabled:opacity-50 disabled:cursor-not-allowed"
              :class="isInQueue 
                ? 'bg-slate-100 text-slate-500 border border-slate-200' 
                : 'bg-slate-900 hover:bg-black'"
            >
              {{ isInQueue ? `Posisi Antre: #${userQueuePosition}` : 'Antre Buku Ini' }}
            </button>
          </div>

          <div class="border-t border-slate-100 pt-8">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Ulasan Buku</h3>
              <button class="text-xs font-bold text-[#D37B3B] hover:text-orange-700 flex items-center gap-1 transition-colors">
                Selengkapnya
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>

            <div v-if="reviews.length > 0" class="space-y-4">
              <div v-for="review in reviews.slice(0, 3)" :key="review.id" class="flex items-start gap-4 rounded-lg border border-slate-100 bg-slate-50/50 p-4">
                <img 
                  :src="`https://ui-avatars.com/api/?name=${review.user_name}&background=D37B3B&color=white`" 
                  class="w-11 h-11 rounded-full object-cover"
                  :alt="review.user_name"
                />
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-3 mb-1">
                    <div>
                      <h4 class="font-bold text-sm text-slate-900 truncate">{{ review.user_name }}</h4>
                      <p class="text-[10px] text-slate-400 font-semibold">{{ review.user_class }}</p>
                    </div>
                    <div class="flex items-center gap-0.5">
                      <svg v-for="star in 5" :key="star" class="w-3 h-3" :class="star <= review.rating ? 'text-amber-400' : 'text-slate-200'" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                    </div>
                  </div>
                  <p class="text-slate-500 text-sm leading-relaxed">
                    {{ review.comment || 'Tidak ada komentar.' }}
                  </p>
                </div>
              </div>
            </div>
            
            <div v-else class="text-sm text-slate-400 italic">
              Belum ada ulasan untuk buku ini.
            </div>
          </div>
        </div>

        <div class="border-t border-slate-100 pt-8 mt-10">
          <div class="flex items-end justify-between gap-4 mb-4">
            <div>
              <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Buku Lainnya</h3>
              <p class="mt-1 text-xs text-slate-400">Koleksi lain dari katalog perpustakaan.</p>
            </div>
            <Link href="/dashboard" class="text-xs font-bold text-[#D37B3B] hover:text-orange-700">
              Semua buku
            </Link>
          </div>
          
          <div v-if="otherBooks && otherBooks.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="otherBook in otherBooks" :key="otherBook.id" class="group">
              <Link :href="`/books/${otherBook.id}`" class="block">
                <div class="aspect-[2/3] rounded-md overflow-hidden bg-slate-50 border border-slate-200/60 shadow-sm transition duration-200 group-hover:border-[#D37B3B]/50 group-hover:shadow-md">
                  <img 
                    :src="otherBook.cover_image || 'https://placehold.co/120x180/E67E22/white?text=' + encodeURIComponent(otherBook.title)" 
                    :alt="otherBook.title" 
                    class="w-full h-full object-cover"
                  />
                </div>
                <h4 class="mt-2 text-xs font-bold text-slate-900 line-clamp-2 group-hover:text-[#D37B3B]">
                  {{ otherBook.title }}
                </h4>
                <p class="mt-0.5 text-[11px] text-slate-400 truncate">{{ otherBook.author || '-' }}</p>
              </Link>
            </div>
          </div>

          <div v-else class="text-sm text-slate-400 italic">
            Tidak ada rekomendasi buku lainnya.
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  book: {
    type: Object,
    required: true
  },
  reviews: {
    type: Array,
    default: () => []
  },
  otherBooks: {
    type: Array,
    default: () => []
  },
  activeBorrow: {
    type: Object,
    default: null
  }
});

const isInQueue = ref(false);
const isAddingQueue = ref(false);
const userQueuePosition = ref(null);
const queueInfo = ref({
  queue_count: 0,
  in_queue: false,
  position: null
});

onMounted(async () => {
  await checkQueueStatus();
});

function handleBorrowClick() {
  if (!props.activeBorrow) {
    router.visit(`/borrow/create/${props.book.id}`);
    return;
  }

  const messages = {
    pending: 'Buku ini sudah ada di daftar pengajuan dan sedang menunggu konfirmasi pustakawan.',
    approved: 'Peminjaman buku ini sudah disetujui. Silakan cek rak buku untuk melihat bukti dan status pengambilan.',
    borrowed: 'Buku ini sedang kamu pinjam. Detailnya bisa dilihat di rak buku.'
  };

  Swal.fire({
    title: 'Buku Sudah Dalam Proses',
    text: messages[props.activeBorrow.status] || 'Buku ini sudah ada dalam daftar peminjaman kamu.',
    icon: 'info',
    confirmButtonColor: '#D37B3B',
    confirmButtonText: 'Lihat Rak Buku',
    showCancelButton: true,
    cancelButtonText: 'Tetap di Sini'
  }).then((result) => {
    if (result.isConfirmed) router.visit('/my-loans');
  });
}

async function checkQueueStatus() {
  try {
    const response = await fetch(`/api/queue/book/${props.book.id}`);
    const data = await response.json();
    queueInfo.value = data;
    isInQueue.value = data.user_in_queue;
    userQueuePosition.value = data.user_position;
  } catch (error) {
    console.error('Error checking queue status:', error);
  }
}

async function addToQueue() {
  isAddingQueue.value = true;
  try {
    const response = await fetch('/queue/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
      },
      body: JSON.stringify({
        book_id: props.book.id
      })
    });

    if (!response.ok) {
      const error = await response.json();
      alert(error.message || 'Gagal menambahkan ke antrian');
      return;
    }

    await checkQueueStatus();
    alert('Anda berhasil bergabung dalam antrian!');
  } catch (error) {
    console.error('Error adding to queue:', error);
    alert('Terjadi kesalahan saat menambahkan ke antrian');
  } finally {
    isAddingQueue.value = false;
  }
}
</script>
