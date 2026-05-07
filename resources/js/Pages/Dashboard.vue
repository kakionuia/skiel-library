<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-800">
    
    <!-- ========== HERO SECTION ========== -->
    <section
      ref="heroSection"
      class="relative min-h-[600px] flex items-center overflow-hidden bg-gradient-to-br from-orange-700 to-orange-500 text-white"
    >
      <!-- Background Decorations -->
      <div class="absolute inset-0 z-0">
        <div class="absolute -left-20 -top-20 h-[500px] w-[500px] rounded-full bg-white/10 blur-[120px]"></div>
        <div class="absolute right-0 bottom-0 h-[400px] w-[400px] rounded-full bg-orange-400/20 blur-[100px]"></div>
        <div class="absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>
      </div>

      <div class="relative z-10 mx-auto max-w-7xl px-6 py-20 w-full">
        <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
          
          <!-- Sisi Kiri: Branding & Search -->
          <div class="space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs font-bold tracking-wider uppercase backdrop-blur-md">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
              </span>
              SKIE-Library System
            </div>
            
            <h1 class="text-5xl font-extrabold tracking-tight lg:text-6xl leading-[1.1]">
              Selamat Pagi, <br />
              <span class="text-amber-200">
                {{ user.name }}
              </span>
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
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-orange-200 group-focus-within:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Sisi Kanan: Quick Action Cards (Solid White) -->
          <div class="grid grid-cols-2 gap-4">
            <button
              v-for="action in quickActions"
              :key="action.label"
              class="group relative flex flex-col items-start gap-4 rounded-3xl bg-white p-6 text-left shadow-xl shadow-orange-900/10 transition-all duration-500 hover:-translate-y-2 overflow-hidden"
            >
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                <component :is="action.iconComp" class="h-6 w-6" />
              </div>
              <div>
                <h3 class="text-sm font-bold text-slate-900">{{ action.label }}</h3>
                <p class="text-[10px] text-slate-500 group-hover:text-slate-400 transition-colors leading-tight">{{ action.desc }}</p>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Section Divider -->
      <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
        <svg class="relative block h-12 w-full" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path d="M1200 120L0 120L0 0C161.333 46.6667 361.333 70 600 70C838.667 70 1038.67 46.6667 1200 0V120Z" fill="#f8fafc" />
        </svg>
      </div>
    </section>

    <!-- ========== STICKY NAVBAR ========== -->
    <nav
      ref="navbarRef"
      class="transition-all duration-300 w-full z-50 bg-slate-50"
      :class="navbarSticky ? 'sticky top-0 shadow-md' : ''"
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
              <path d="M4 5q-.825 0-1.412.587Q2 6.175 2 7v10q0 .825.588 1.413Q3.175 19 4 19h16q.825 0 1.413-.587Q22 17.825 22 17V7q0-.825-.587-1.413Q20.825 5 20 5h-3.15L15.45 3H8.55L7.15 5ZM12 17q-2.075 0-3.537-1.463Q7 14.075 7 12q0-2.075 1.463-3.537Q9.925 7 12 7q2.075 0 3.538 1.463Q17 9.925 17 12q0 2.075-1.462 3.537Q14.075 17 12 17Zm0-2q1.25 0 2.125-.875T15 12q0-1.25-.875-2.125T12 9q-1.25 0-2.125.875T9 12q0 1.25.875 2.125T12 15Z"/>
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
            :src="user.profile_photo_url || 'https://ui-avatars.com/api/?name=' + user.name" 
            class="h-12 w-12 rounded-full border-2 border-slate-100 object-cover hover:scale-105 transition shadow-sm" 
            alt="Profile" 
          />
        </Link>
      </div>

      <!-- Category Bar (Full Orange) -->
      <div class="bg-orange-500 shadow-md">
  <div class="mx-auto max-w-7xl px-4 py-3">
    <div class="flex items-center gap-6 overflow-x-auto whitespace-nowrap text-white font-bold text-sm no-scrollbar">
      <button class="text-white hover:scale-110 transition flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h18v-2H6v2z"/>
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
        v-for="cat in allCategories" 
        :key="cat"
        @click="activeCategory = cat"
        class="transition-opacity px-2 py-1 rounded-lg hover:bg-white/10 flex-shrink-0"
        :class="activeCategory === cat ? 'opacity-100 bg-white/20' : 'opacity-80 hover:opacity-100'"
      >
        {{ cat }}
      </button>
    </div>
  </div>
      </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:py-12">
      <!-- KATALOG BUKU -->
      <section class="space-y-16">
        <!-- Looping Grup Kategori -->
        <div v-for="group in filteredGroups" :key="group.name" class="space-y-6">
          <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900">{{ group.name }}</h2>
          </div>

          <!-- Grid Card: ukuran tetap 140px di semua mode -->
          <div 
            :class="isFiltered 
              ? 'grid gap-4 justify-center' 
              : 'flex flex-nowrap overflow-x-auto pb-6 gap-4 no-scrollbar'"
            :style="isFiltered ? { gridTemplateColumns: 'repeat(auto-fill, 140px)' } : {}"
          >
            <article 
              v-for="book in group.books" 
              :key="book.id"
              class="w-[140px] flex-shrink-0 bg-white shadow-sm border border-slate-100 flex flex-col transition-all hover:shadow-xl hover:-translate-y-2 overflow-hidden"
            >
              <!-- Cover -->
              <div class="aspect-[3/4] w-full overflow-hidden relative bg-slate-100">
                <img 
                  v-if="book.cover_image && !imgErrors[book.id]"
                  :src="book.cover_image" 
                  class="h-full w-full object-cover" 
                  alt="Cover"
                  @error="onImageError(book.id)"
                />
                <div 
                  v-else 
                  class="flex h-full w-full items-center justify-center bg-orange-50 text-orange-400 font-black text-4xl"
                >
                  {{ book.title.charAt(0).toUpperCase() }}
                </div>
                
                <!-- Badge Stock -->
                <div v-if="book.available_quantity === 0" class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center">
                  <span class="bg-red-500 text-white text-[10px] font-black px-3 py-1 uppercase tracking-widest">Habis</span>
                </div>
              </div>

              <!-- Content -->
              <div class="p-2 flex flex-col flex-1 text-center">
                <h3 class="text-[11px] font-black text-slate-900 line-clamp-1 mb-1">{{ book.title }}</h3>
                <p class="text-[9px] text-slate-500 font-bold mb-2 italic line-clamp-1">{{ book.author }}</p>
                <p class="text-[8px] text-orange-400 font-bold mb-1">{{ book.category }}</p>
                
                <button 
                  @click="handleBorrow(book)"
                  :disabled="book.available_quantity === 0"
                  class="mt-auto w-full py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95"
                  :class="book.available_quantity > 0 
                    ? 'bg-orange-500 hover:bg-[#b5652d] text-white' 
                    : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                >
                  Pinjam Buku
                </button>
              </div>
            </article>
          </div>

          <!-- Tombol "Tampilkan Lebih Banyak" hanya untuk mode filter -->
          <div v-if="isFiltered && hasMore && group.name === 'Hasil Pencarian'" class="text-center">
            <button 
              @click="loadMore" 
              class="mt-4 px-8 py-3 bg-white border border-orange-300 text-orange-600 font-bold rounded-full hover:bg-orange-50 shadow-md transition-all active:scale-95"
            >
              Tampilkan Lebih Banyak ({{ visibleCount }} / {{ totalFilteredCount }})
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredGroups.length === 0 && availableBooks.length > 0" class="py-32 text-center">
          <div class="text-6xl mb-6">🏜️</div>
          <h3 class="text-lg font-bold text-slate-900">Wah, bukunya nggak ketemu...</h3>
          <p class="text-slate-500 text-sm mt-1">Coba cari dengan kata kunci lain.</p>
          <button @click="resetFilters" class="mt-6 text-orange-600 font-bold text-sm underline">Reset Filter</button>
        </div>

        <!-- Empty State Kalau Memang Ga Ada Buku -->
        <div v-if="availableBooks.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">📚</div>
          <h3 class="text-lg font-bold text-slate-900">Belum ada buku tersedia</h3>
          <p class="text-slate-500 text-sm mt-1">Silahkan tambahkan buku di halaman admin.</p>
        </div>
      </section>
    </main>

    <!-- ========== BEST SELLER SECTION ========== -->
    <section class="relative py-20 bg-[#FDFCFB] overflow-hidden group/container">
      <!-- Decorative Elements -->
      <div class="absolute top-0 left-0 w-64 h-64 bg-[#E67E22]/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl pointer-events-none"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#2C3E50]/5 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl pointer-events-none"></div>

      <!-- Header Section -->
      <div class="relative text-center mb-16 px-4">
        <span class="text-[#E67E22] font-semibold tracking-widest uppercase text-sm mb-3 block">Top Picks</span>
        <h2 class="text-[#2C3E50] text-3xl md:text-4xl font-black uppercase tracking-tight mb-4">
          Best Seller <span class="text-[#E67E22]">Buku Terpopuler</span>
        </h2>
        <div class="w-20 h-1.5 bg-[#E67E22] mx-auto rounded-full"></div>
      </div>

      <!-- Carousel Container -->
      <div class="relative max-w-[1500px] mx-auto px-4 md:px-12">
        <swiper
          :modules="modules"
          effect="coverflow"
          :grab-cursor="true"
          :centered-slides="true"
          slides-per-view="auto"
          :loop="true"
          :speed="1000"
          :coverflow-effect="{
            rotate: 15,
            stretch: 0,
            depth: 200,
            modifier: 1,
            slideShadows: false,
          }"
          :autoplay="{
            delay: 4000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
          }"
          :navigation="{
            nextEl: '.btn-next',
            prevEl: '.btn-prev',
          }"
          class="book-carousel-swiper !pb-24 !pt-10"
        >
          <swiper-slide 
            v-for="(book, index) in bestSellerBooks" 
            :key="index" 
            class="!w-[220px] sm:!w-[280px] md:!w-[340px] transition-all duration-700 ease-out"
          >
            <div class="book-card-wrapper relative group/card perspective-1000">
              <!-- Glassmorphism Badge -->
              <div class="absolute -top-4 -right-4 z-20 bg-white/80 backdrop-blur-md border border-white/20 shadow-lg w-12 h-12 rounded-full flex items-center justify-center font-bold text-[#E67E22]">
                #{{ index + 1 }}
              </div>

              <div class="relative overflow-hidden rounded-2xl shadow-xl transition-all duration-500 group-hover/card:shadow-[0_20px_50px_rgba(230,126,34,0.3)]">
                <img 
                  :src="book.cover" 
                  :alt="book.title"
                  class="w-full aspect-[2/3] object-cover transition-transform duration-700 group-hover/card:scale-110"
                />
                
                <!-- Info Overlay on Hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 text-white">
                  <h3 class="font-bold text-lg leading-tight">{{ book.title }}</h3>
                  <p class="text-sm text-gray-300 mt-2">Tersedia untuk dipinjam</p>
                  <button class="mt-4 py-2 px-4 bg-[#E67E22] hover:bg-[#d35400] text-white text-xs font-bold rounded-lg transition-colors">
                    Detail Buku
                  </button>
                </div>
              </div>
            </div>
          </swiper-slide>
        </swiper>

        <!-- Navigation Custom -->
        <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2 md:px-4 pointer-events-none z-30">
          <button class="btn-prev pointer-events-auto w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-white shadow-2xl text-[#E67E22] opacity-0 -translate-x-10 group-hover/container:opacity-100 group-hover/container:translate-x-0 transition-all duration-500 hover:bg-[#E67E22] hover:text-white border border-gray-100">
            <i class="fa-solid fa-arrow-left"></i>
          </button>
          
          <button class="btn-next pointer-events-auto w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-white shadow-2xl text-[#E67E22] opacity-0 translate-x-10 group-hover/container:opacity-100 group-hover/container:translate-x-0 transition-all duration-500 hover:bg-[#E67E22] hover:text-white border border-gray-100">
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>
      </div>
    </section>

    <section class="bg-orange-500 p-6 md:p-12 min-h-screen flex items-center justify-center font-sans">
    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-10">
      
      <div class="flex flex-col">
        <h2 class="text-white text-2xl font-bold mb-4">Ulasan Terbaik</h2>
        
        <div class="bg-white rounded-[2rem] p-8 shadow-xl relative min-h-[400px] flex flex-col justify-between">
          <div v-for="(review, index) in bestReviews" :key="review.id" v-show="activeIndex === index" class="animate-fade-in">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
              <div>
                <h4 class="font-bold text-gray-800">{{ review.user }}</h4>
                <p class="text-sm text-gray-500">{{ review.class }}</p>
              </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
              <img :src="review.image" class="w-40 h-56 object-cover rounded-lg shadow-md" :alt="review.bookTitle">
              <div class="flex-1">
                <div class="flex text-yellow-400 mb-2">
                  <span v-for="i in 5" :key="i">★</span>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed italic">
                  "{{ review.comment }}"
                </p>
                <div class="flex gap-4 mt-6 text-gray-800">
                  <button class="hover:text-red-500 transition-colors"><i class="far fa-heart text-xl"></i></button>
                  <button class="hover:text-blue-500 transition-colors"><i class="far fa-comment text-xl"></i></button>
                  <button class="hover:text-orange-500 transition-colors ml-auto"><i class="far fa-bookmark text-xl"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-center gap-3 mt-6">
          <button 
            v-for="(_, index) in bestReviews" 
            :key="index"
            @click="activeIndex = index"
            :class="['h-3 w-3 rounded-full transition-all duration-300', activeIndex === index ? 'bg-white w-8' : 'bg-white/50']"
          ></button>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="flex justify-between items-end mb-4">
          <h2 class="text-white text-2xl font-bold">Terbaru</h2>
          <a href="#" class="text-white text-sm flex items-center gap-1 hover:underline">
            Lihat lebih banyak <i class="fas fa-chevron-right text-xs"></i>
          </a>
        </div>

        <div class="space-y-4">
          <div v-for="item in recentReviews" :key="item.id" class="bg-white rounded-3xl p-4 flex gap-4 shadow-md hover:scale-[1.02] transition-transform">
            <div class="w-14 h-14 bg-gray-200 rounded-full shrink-0"></div>
            <div class="flex-1">
              <div class="flex justify-between">
                <h4 class="font-bold text-gray-800 text-sm">{{ item.user }}</h4>
                <h4 class="font-bold text-gray-800 text-sm">{{ item.title }}</h4>
              </div>
              <div class="flex text-yellow-400 text-xs mb-2">
                <span v-for="i in 5" :key="i">★</span>
              </div>
              <p class="text-[10px] text-gray-500 line-clamp-2">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...
              </p>
              <div class="flex gap-3 mt-2 text-gray-700">
                 <i class="far fa-heart text-xs"></i>
                 <i class="far fa-comment text-xs"></i>
                 <i class="far fa-bookmark text-xs ml-auto"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

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

import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation, EffectCoverflow } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/effect-coverflow';

const modules = [Autoplay, Navigation, EffectCoverflow];

// Props
const props = defineProps({
  user: { type: Object, required: true },
  availableBooks: { type: Array, default: () => [] },
});

// Quick actions
const quickActions = [
  { label: 'Scan QR', desc: 'Pinjam via barcode', iconComp: QrCodeIcon },
  { label: 'Katalog', desc: 'Cari koleksi buku', iconComp: BookOpenIcon },
  { label: 'Riwayat', desc: 'Aktivitas baca kamu', iconComp: ClockIcon },
  { label: 'Diskusi', desc: 'Forum ulasan siswa', iconComp: ChatBubbleLeftRightIcon },
];

// Best seller books (hardcoded untuk demo)
const bestSellerBooks = [
  { title: 'One Piece', cover: 'https://images.unsplash.com/photo-1589998059171-988d887df646?q=80&w=2076&auto=format&fit=crop' },
  { title: 'Ronggeng Dukuh Paruk', cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=1974&auto=format&fit=crop' },
  { title: 'Witch Fantasy', cover: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1996&auto=format&fit=crop' },
  { title: 'Hujan', cover: 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?q=80&w=2029&auto=format&fit=crop' },
  { title: 'Buku 5', cover: 'https://placehold.co/400x600/E67E22/white?text=Koleksi+5' },
  { title: 'Buku 6', cover: 'https://placehold.co/400x600/2C3E50/white?text=Koleksi+6' },
];

// State
const bookSearch = ref('');
const activeCategory = ref('');
const visibleCount = ref(9);
const imgErrors = reactive({});

// SEMUA kategori yang seharusnya muncul
const ALL_EXPECTED_CATEGORIES = ['Fiksi', 'Non Fiksi', 'Referensi', 'Cerita Anak'];

// Ambil kategori dari data buku + kategori yang diharapkan
const allCategories = computed(() => {
  // Ambil dari data buku yang ada
  const bookCategories = props.availableBooks
    .map(b => b.category)
    .filter(Boolean)
    .map(cat => cat.trim());
  
  // Gabungkan dengan kategori yang diharapkan
  const mergedCategories = [...new Set([...bookCategories, ...ALL_EXPECTED_CATEGORIES])];
  
  // Sort
  return mergedCategories.sort();
});

// Cek apakah sedang dalam mode filter
const isFiltered = computed(() => bookSearch.value !== '' || activeCategory.value !== '');

// Reset visible count ketika filter berubah
watch([bookSearch, activeCategory], () => {
  visibleCount.value = 9;
});

// Filter buku
const filteredBooks = computed(() => {
  return props.availableBooks.filter(book => {
    const search = bookSearch.value.toLowerCase();
    const matchesSearch = 
      book.title?.toLowerCase().includes(search) || 
      book.author?.toLowerCase().includes(search) ||
      book.category?.toLowerCase().includes(search);
    const matchesCategory = activeCategory.value === '' || book.category === activeCategory.value;
    return matchesSearch && matchesCategory;
  });
});

const totalFilteredCount = computed(() => filteredBooks.value.length);
const displayedFilteredBooks = computed(() => filteredBooks.value.slice(0, visibleCount.value));
const hasMore = computed(() => visibleCount.value < totalFilteredCount.value);

function loadMore() {
  visibleCount.value += 9;
}

function resetFilters() {
  bookSearch.value = '';
  activeCategory.value = '';
}

// Grup buku untuk ditampilkan
const filteredGroups = computed(() => {
  // Mode filter
  if (isFiltered.value) {
    const books = displayedFilteredBooks.value;
    if (books.length === 0) return [];
    const name = activeCategory.value || 'Hasil Pencarian';
    return [{ name, books }];
  }

  // Mode normal: grup per kategori
  const groups = {};
  
  // Inisialisasi semua kategori yang diharapkan
  ALL_EXPECTED_CATEGORIES.forEach(cat => {
    groups[cat] = [];
  });
  
  // Isi buku ke kategori masing-masing
  props.availableBooks.forEach(book => {
    const cat = book.category?.trim() || 'Lain-lain';
    if (!groups[cat]) {
      groups[cat] = [];
    }
    groups[cat].push(book);
  });
  
  // Convert ke array dan filter yang kosong
  return Object.entries(groups)
    .filter(([_, books]) => books.length > 0)
    .map(([name, books]) => ({ name, books }));
});

// Handle image error
function onImageError(bookId) {
  imgErrors[bookId] = true;
}

// Handle borrow
const handleBorrow = (book) => {
  if (book.available_quantity > 0) {
    alert(`Mendaftarkan peminjaman untuk: ${book.title}\nSilakan hubungi petugas perpustakaan.`);
  }
};

// Sticky navbar logic
const heroSection = ref(null);
const navbarSticky = ref(false);
let observer;

onMounted(() => {
  observer = new IntersectionObserver(([entry]) => {
    navbarSticky.value = !entry.isIntersecting;
  }, { threshold: 0.05 });
  
  if (heroSection.value) {
    observer.observe(heroSection.value);
  }
});

onBeforeUnmount(() => {
  if (observer) observer.disconnect();
});

// Data Mockup untuk Carousel
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
  },
  // Tambahkan data lainnya jika perlu
]);

const activeIndex = ref(0);

// Data Mockup untuk Sidebar Kanan
const recentReviews = ref([
  { id: 1, user: 'Elliot Zaky Iman', title: 'Hujan - Tere Liye', rating: 4 },
  { id: 2, user: 'Elliot Zaky Iman', title: 'Hujan - Tere Liye', rating: 4 },
  { id: 3, user: 'Elliot Zaky Iman', title: 'Hujan - Tere Liye', rating: 4 },
]);
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { 
  display: none; 
}

.no-scrollbar { 
  -ms-overflow-style: none; 
  scrollbar-width: none; 
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

article {
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
}

.perspective-1000 {
  perspective: 1000px;
}

.swiper-slide {
  filter: saturate(0.5) blur(1px);
  transform: scale(0.85);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.swiper-slide-active {
  filter: saturate(1) blur(0);
  transform: scale(1.05);
  z-index: 10 !important;
}

.book-carousel-swiper {
  cursor: grab;
}

.book-carousel-swiper:active {
  cursor: grabbing;
}

.swiper-slide-shadow-left,
.swiper-slide-shadow-right {
  border-radius: 1rem;
}

.animate-fade-in {
  animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>