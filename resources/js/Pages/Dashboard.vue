<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-800">
    <!-- ========== HERO SECTION ========== -->
    <section
      ref="heroSection"
      class="relative min-h-[600px] flex items-center overflow-hidden bg-gradient-to-br from-orange-700 to-orange-500 text-white"
    >
      <!-- Background Decorations -->
      <div class="absolute inset-0 z-0">
        <div class="absolute -left-20 -top-20 h-[500px] w-[500px] rounded-full bg-white/10 blur-[120px]" />
        <div class="absolute right-0 bottom-0 h-[400px] w-[400px] rounded-full bg-orange-400/20 blur-[100px]" />
        <div 
          class="absolute inset-0 opacity-[0.05]" 
          style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;" 
        />
      </div>

      <div class="relative z-10 mx-auto max-w-7xl px-6 py-20 w-full">
        <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
          <!-- Sisi Kiri: Branding & Search -->
          <div class="space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs font-bold tracking-wider uppercase backdrop-blur-md">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75" />
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white" />
              </span>
              SKIE-Library System
            </div>

            <h1 class="text-5xl font-extrabold tracking-tight lg:text-6xl leading-[1.1]">
              Selamat Pagi, <br />
              <span class="text-amber-200">{{ user.name }}</span>
            </h1>

            <p class="max-w-md text-lg text-orange-50 leading-relaxed">
              Tingkatkan literasi digital dan temukan referensi terbaik untuk mendukung perjalanan akademikmu.
            </p>

            <!-- Search Input -->
            <div class="relative max-w-md group">
              <input
                v-model="bookSearch"
                type="text"
                placeholder="Cari judul buku, penulis, atau kategori..."
                class="w-full bg-white/10 border border-white/20 rounded-2xl py-4 pl-12 pr-4 text-sm text-white placeholder:text-orange-100 focus:bg-white/20 focus:border-white/40 focus:ring-0 transition-all outline-none backdrop-blur-md shadow-inner"
              />
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-orange-200 group-focus-within:text-white transition-colors" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Sisi Kanan: Quick Action Cards -->
          <div class="grid grid-cols-2 gap-4">
            <button
              v-for="action in quickActions"
              :key="action.label"
              class="group relative flex flex-col items-start gap-4 rounded-3xl bg-white p-6 text-left shadow-xl shadow-orange-900/10 transition-all duration-500 hover:-translate-y-2 overflow-hidden"
            >
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                <component :is="action.icon" class="h-6 w-6" />
              </div>
              <div>
                <h3 class="text-sm font-bold text-slate-900">{{ action.label }}</h3>
                <p class="text-[10px] text-slate-500 group-hover:text-slate-400 transition-colors leading-tight">
                  {{ action.description }}
                </p>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Section Divider -->
      <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
        <svg class="relative block h-12 w-full" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path 
            d="M1200 120L0 120L0 0C161.333 46.6667 361.333 70 600 70C838.667 70 1038.67 46.6667 1200 0V120Z" 
            fill="#f8fafc" 
          />
        </svg>
      </div>
    </section>

    <!-- ========== STICKY NAVBAR ========== -->
    <nav
      ref="navbarRef"
      class="transition-all duration-300 w-full z-50 bg-slate-50"
      :class="{ 'sticky top-0 shadow-md': navbarSticky }"
    >
      <!-- Top Bar -->
      <div class="mx-auto max-w-7xl px-8 py-4 flex justify-between items-center gap-8 md:gap-5">
        <button class="text-orange-500 p-1 transition-transform active:scale-90">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="sharp" stroke-linejoin="sharp" stroke-width="2.5" d="M4 8h16M4 12h16M4 16h16" />
          </svg>
        </button>

        <div class="flex gap-2 w-full">
          <!-- Scan/Camera Button -->
          <button class="flex-shrink-0 bg-white border border-orange-500 text-orange-500 p-2.5 rounded-2xl shadow-lg shadow-black/5 hover:bg-orange-50 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M4 5q-.825 0-1.412.587Q2 6.175 2 7v10q0 .825.588 1.413Q3.175 19 4 19h16q.825 0 1.413-.587Q22 17.825 22 17V7q0-.825-.587-1.413Q20.825 5 20 5h-3.15L15.45 3H8.55L7.15 5ZM12 17q-2.075 0-3.537-1.463Q7 14.075 7 12q0-2.075 1.463-3.537Q9.925 7 12 7q2.075 0 3.538 1.463Q17 9.925 17 12q0 2.075-1.462 3.537Q14.075 17 12 17Zm0-2q1.25 0 2.125-.875T15 12q0-1.25-.875-2.125T12 9q-1.25 0-2.125.875T9 12q0 1.25.875 2.125T12 15Z" />
            </svg>
          </button>

          <!-- Search Bar -->
          <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
              <input
                v-model="bookSearch"
                type="text"
                placeholder="Cari buku, penulis, dan tahun terbit"
                class="w-full border border-orange-500 rounded-2xl py-3 px-6 shadow-lg shadow-black/5 focus:ring-0 outline-none text-slate-400 placeholder:text-slate-300 transition"
              />
            </div>
            <button class="bg-orange-500 text-white p-3 rounded-2xl shadow-lg shadow-black/5 hover:bg-orange-700 transition-all active:scale-95">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>

        <Link href="/profile" class="flex-shrink-0">
          <img
            :src="userAvatar"
            class="h-12 w-12 rounded-full border-2 border-slate-100 object-cover hover:scale-105 transition shadow-sm"
            alt="Profile"
          />
        </Link>
      </div>

      <!-- Category Bar -->
      <div class="bg-orange-500 shadow-md">
        <div class="mx-auto max-w-7xl px-4 py-3">
          <div class="flex items-center gap-6 overflow-x-auto whitespace-nowrap text-white font-bold text-sm no-scrollbar">
            <button class="text-white hover:scale-110 transition flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h18v-2H6v2z" />
              </svg>
            </button>

            <button
              @click="activeCategory = ''"
              class="transition-opacity px-2 py-1 rounded-lg hover:bg-white/10 flex-shrink-0"
              :class="activeCategory === '' ? 'opacity-100 bg-white/20' : 'opacity-80 hover:opacity-100'"
            >
              Semua Buku
            </button>

            <button
              v-for="category in allCategories"
              :key="category"
              @click="activeCategory = category"
              class="transition-opacity px-2 py-1 rounded-lg hover:bg-white/10 flex-shrink-0"
              :class="activeCategory === category ? 'opacity-100 bg-white/20' : 'opacity-80 hover:opacity-100'"
            >
              {{ category }}
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:py-12">
      <!-- Katalog Buku -->
      <section class="space-y-16">
        <!-- Empty State: Tidak Ada Buku -->
        <div v-if="availableBooks.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">📚</div>
          <h3 class="text-lg font-bold text-slate-900">Belum ada buku tersedia</h3>
          <p class="text-slate-500 text-sm mt-1">Silahkan tambahkan buku di halaman admin.</p>
        </div>

        <!-- Empty State: Hasil Filter Kosong -->
        <div v-else-if="filteredGroups.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">🏜️</div>
          <h3 class="text-lg font-bold text-slate-900">Wah, bukunya nggak ketemu...</h3>
          <p class="text-slate-500 text-sm mt-1">Coba cari dengan kata kunci lain.</p>
          <button 
            @click="resetFilters" 
            class="mt-6 text-orange-600 font-bold text-sm underline hover:text-orange-800 transition-colors"
          >
            Reset Filter
          </button>
        </div>

        <!-- Grup Kategori -->
        <div 
          v-for="group in filteredGroups" 
          :key="group.name" 
          class="space-y-6"
        >
          <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900">{{ group.name }}</h2>
          </div>

          <!-- Grid Buku -->
          <div
            :class="isFiltered 
              ? 'grid gap-4 justify-center' 
              : 'flex flex-nowrap overflow-x-auto pb-6 gap-4 no-scrollbar'"
            :style="isFiltered ? { gridTemplateColumns: 'repeat(auto-fill, 140px)' } : {}"
          >
            <BookCard
              v-for="book in group.books"
              :key="book.id"
              :book="book"
              :img-errors="imgErrors"
              @borrow="handleBorrow"
              @image-error="onImageError"
            />
          </div>

          <!-- Tombol "Tampilkan Lebih Banyak" -->
          <div 
            v-if="isFiltered && hasMore && group.name === 'Hasil Pencarian'" 
            class="text-center"
          >
            <button
              @click="loadMore"
              class="mt-4 px-8 py-3 bg-white border border-orange-300 text-orange-600 font-bold rounded-full hover:bg-orange-50 shadow-md transition-all active:scale-95"
            >
              Tampilkan Lebih Banyak ({{ visibleCount }} / {{ totalFilteredCount }})
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- ========== BEST SELLER SECTION ========== -->
    <BestSellerSection :books="bestSellerBooks" />

    <!-- ========== REVIEWS SECTION ========== -->
    <ReviewsSection 
      :best-reviews="bestReviews"
      :recent-reviews="recentReviews"
    />
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref, reactive, watch, onMounted, onBeforeUnmount } from 'vue';
import {
  QrCodeIcon,
  BookOpenIcon,
  ClockIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';

import BookCard from '@/Components/BookCard.vue';
import BestSellerSection from '@/Components/BestSellerSection.vue';
import ReviewsSection from '@/Components/ReviewsSection.vue';

// Constants
const ALL_EXPECTED_CATEGORIES = ['Fiksi', 'Non Fiksi', 'Referensi', 'Cerita Anak'];
const ITEMS_PER_PAGE = 9;

// Props
const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  availableBooks: {
    type: Array,
    default: () => []
  }
});

// Quick Actions Configuration
const quickActions = [
  {
    label: 'Scan QR',
    description: 'Pinjam via barcode',
    icon: QrCodeIcon
  },
  {
    label: 'Katalog',
    description: 'Cari koleksi buku',
    icon: BookOpenIcon
  },
  {
    label: 'Riwayat',
    description: 'Aktivitas baca kamu',
    icon: ClockIcon
  },
  {
    label: 'Diskusi',
    description: 'Forum ulasan siswa',
    icon: ChatBubbleLeftRightIcon
  }
];

// Best Seller Data
const bestSellerBooks = [
  {
    title: 'One Piece',
    cover: 'https://images.unsplash.com/photo-1589998059171-988d887df646?q=80&w=2076&auto=format&fit=crop'
  },
  {
    title: 'Ronggeng Dukuh Paruk',
    cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=1974&auto=format&fit=crop'
  },
  {
    title: 'Witch Fantasy',
    cover: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1996&auto=format&fit=crop'
  },
  {
    title: 'Hujan',
    cover: 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?q=80&w=2029&auto=format&fit=crop'
  },
  {
    title: 'Buku 5',
    cover: 'https://placehold.co/400x600/E67E22/white?text=Koleksi+5'
  },
  {
    title: 'Buku 6',
    cover: 'https://placehold.co/400x600/2C3E50/white?text=Koleksi+6'
  }
];

// Reviews Data
const bestReviews = ref([
  {
    id: 1,
    user: 'Elliot Zaky Iman',
    class: 'XI Las 2',
    bookTitle: 'Bintang',
    rating: 4.5,
    comment: 'Lorem Ipsum is simply dummy text of the printing and typesetting industry...',
    image: 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1464601444i/30325492.jpg'
  },
  {
    id: 2,
    user: 'Aurel Filza',
    class: 'XI RPL 1',
    bookTitle: 'Hujan',
    rating: 5,
    comment: 'Cerita yang sangat menyentuh tentang melupakan dan menerima kenyataan...',
    image: 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1453303632i/28595941.jpg'
  }
]);

const recentReviews = ref([
  {
    id: 1,
    user: 'Elliot Zaky Iman',
    title: 'Hujan - Tere Liye',
    rating: 4
  },
  {
    id: 2,
    user: 'Elliot Zaky Iman',
    title: 'Hujan - Tere Liye',
    rating: 4
  },
  {
    id: 3,
    user: 'Elliot Zaky Iman',
    title: 'Hujan - Tere Liye',
    rating: 4
  }
]);

// State
const heroSection = ref(null);
const navbarRef = ref(null);
const navbarSticky = ref(false);
const bookSearch = ref('');
const activeCategory = ref('');
const visibleCount = ref(ITEMS_PER_PAGE);
const imgErrors = reactive({});
let intersectionObserver;

// Computed
const userAvatar = computed(() => {
  return props.user.profile_photo_url || `https://ui-avatars.com/api/?name=${props.user.name}`;
});

const allCategories = computed(() => {
  const bookCategories = props.availableBooks
    .map(book => book.category)
    .filter(Boolean)
    .map(cat => cat.trim());

  const mergedCategories = [...new Set([...bookCategories, ...ALL_EXPECTED_CATEGORIES])];
  return mergedCategories.sort();
});

const isFiltered = computed(() => {
  return bookSearch.value !== '' || activeCategory.value !== '';
});

const filteredBooks = computed(() => {
  return props.availableBooks.filter(book => {
    const searchTerm = bookSearch.value.toLowerCase();
    const matchesSearch = searchTerm === '' || 
      book.title?.toLowerCase().includes(searchTerm) ||
      book.author?.toLowerCase().includes(searchTerm) ||
      book.category?.toLowerCase().includes(searchTerm);

    const matchesCategory = activeCategory.value === '' || 
      book.category === activeCategory.value;

    return matchesSearch && matchesCategory;
  });
});

const totalFilteredCount = computed(() => filteredBooks.value.length);

const displayedFilteredBooks = computed(() => {
  return filteredBooks.value.slice(0, visibleCount.value);
});

const hasMore = computed(() => {
  return visibleCount.value < totalFilteredCount.value;
});

const filteredGroups = computed(() => {
  if (isFiltered.value) {
    const books = displayedFilteredBooks.value;
    if (books.length === 0) return [];

    const groupName = activeCategory.value || 'Hasil Pencarian';
    return [{ name: groupName, books }];
  }

  // Mode normal: grup berdasarkan kategori
  const groups = {};

  // Inisialisasi kategori yang diharapkan
  ALL_EXPECTED_CATEGORIES.forEach(cat => {
    groups[cat] = [];
  });

  // Distribusikan buku ke kategorinya
  props.availableBooks.forEach(book => {
    const category = book.category?.trim() || 'Lain-lain';
    if (!groups[category]) {
      groups[category] = [];
    }
    groups[category].push(book);
  });

  // Convert ke array dan filter kategori kosong
  return Object.entries(groups)
    .filter(([_, books]) => books.length > 0)
    .map(([name, books]) => ({ name, books }));
});

// Watchers
watch([bookSearch, activeCategory], () => {
  visibleCount.value = ITEMS_PER_PAGE;
});

// Methods
function onImageError(bookId) {
  imgErrors[bookId] = true;
}

function handleBorrow(book) {
  if (book.available_quantity > 0) {
    alert(`Mendaftarkan peminjaman untuk: ${book.title}\nSilakan hubungi petugas perpustakaan.`);
  }
}

function loadMore() {
  visibleCount.value += ITEMS_PER_PAGE;
}

function resetFilters() {
  bookSearch.value = '';
  activeCategory.value = '';
}

// Lifecycle
onMounted(() => {
  intersectionObserver = new IntersectionObserver(
    ([entry]) => {
      navbarSticky.value = !entry.isIntersecting;
    },
    { threshold: 0.05 }
  );

  if (heroSection.value) {
    intersectionObserver.observe(heroSection.value);
  }
});

onBeforeUnmount(() => {
  if (intersectionObserver) {
    intersectionObserver.disconnect();
  }
});
</script>

<style scoped>
/* Utility Classes */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>