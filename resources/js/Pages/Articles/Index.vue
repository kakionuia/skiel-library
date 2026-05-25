<template>
  <div class="min-h-screen bg-white font-sans antialiased">
    <Navbar @open-auth="showAuthModal = true" />

    <header class="border-b border-gray-100 bg-white">
      <div class="max-w-6xl mx-auto px-4 py-28 md:py-32 text-center space-y-4">
        <div>
          <span class="inline-block px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full tracking-wide">
            Artikel, Berita & Event
          </span>
        </div>
        <h1 class="text-3xl md:text-5xl font-extrabold text-slate-950 tracking-tight">
          Jelajahi Wawasan & Informasi
        </h1>
        <p class="text-gray-500 text-sm md:text-base max-w-xl mx-auto leading-relaxed">
          Temukan artikel inspiratif, berita terbaru, dan event literasi dari Perpustakaan SMKN 1 Gunungputri.
        </p>
        
        <div class="pt-4 max-w-xl mx-auto">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari artikel berdasarkan judul..."
              class="w-full px-4 py-3 pl-11 bg-slate-50 border border-gray-200 rounded-md text-sm text-slate-800 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:bg-white transition-all"
              @input="debounceSearch"
            />
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>
    </header>

    <div class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
      <div class="max-w-6xl mx-auto px-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-3 gap-4">
          
          <div class="flex flex-wrap gap-x-5 gap-y-2">
            <button
              v-for="cat in categories"
              :key="cat.value"
              @click="activeCategory = cat.value"
              :class="[
                'text-xs font-bold uppercase tracking-wider transition-colors py-1',
                activeCategory === cat.value 
                  ? 'text-orange-500 border-b-2 border-orange-500' 
                  : 'text-gray-400 hover:text-slate-900'
              ]"
            >
              {{ cat.label }}
              <span v-if="cat.count" class="ml-1 opacity-60">({{ cat.count }})</span>
            </button>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Urutan:</span>
            <select 
              v-model="sortBy"
              class="px-3 py-1.5 bg-slate-50 border border-gray-200 rounded-md text-xs font-bold text-slate-700 focus:outline-none focus:border-orange-500 cursor-pointer"
            >
              <option value="latest">Terbaru</option>
              <option value="oldest">Terlama</option>
              <option value="title_asc">Judul A-Z</option>
              <option value="title_desc">Judul Z-A</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-10">
      
      <div v-if="loading" class="flex justify-center py-16">
        <div class="flex flex-col items-center gap-2">
          <div class="w-8 h-8 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
          <p class="text-xs text-gray-400 font-medium">Memuat konten...</p>
        </div>
      </div>

      <div v-else-if="filteredArticles.length === 0" class="text-center py-16 border border-dashed border-gray-200 rounded-md">
        <h3 class="text-base font-bold text-slate-900 mb-1">Tidak ada konten ditemukan</h3>
        <p class="text-xs text-gray-400 max-w-xs mx-auto leading-normal">
          Coba ubah kata kunci pencarian atau pilih filter kategori yang lain.
        </p>
        <button 
          @click="resetFilters"
          class="mt-4 px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-md hover:bg-orange-600 transition"
        >
          Reset Filter
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="article in paginatedArticles"
          :key="article.id"
          class="group bg-white border border-gray-200 rounded-md overflow-hidden flex flex-col transition-all duration-300 hover:border-gray-300"
        >
          <Link :href="contentHref(article)" class="relative overflow-hidden block bg-slate-100">
            <div class="aspect-[16/10]">
              <img
                :src="getArticleImage(article.featured_image)"
                :alt="article.title"
                class="w-full h-full object-cover group-hover:opacity-90 transition-opacity duration-300"
                @error="(e) => e.target.src = 'https://via.placeholder.com/800x500?text=No+Image'"
              />
            </div>
            <div class="absolute top-3 left-3">
              <span class="px-2.5 py-1 bg-white/95 text-[10px] font-bold text-slate-900 uppercase tracking-wider rounded-sm shadow-sm border border-gray-100">
                {{ categoryLabel(article) }}
              </span>
            </div>
          </Link>

          <div class="p-5 flex flex-col flex-1 space-y-2.5">
            
            <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium">
              <span>{{ formatDate(article.published_at) }}</span>
              <template v-if="article.is_event">
                <span>•</span>
                <span class="font-bold text-orange-500">{{ article.status_label }}</span>
              </template>
              <template v-else-if="article.readTime">
                <span>•</span>
                <span>{{ article.readTime }} mnt baca</span>
              </template>
            </div>

            <Link :href="contentHref(article)" class="block">
              <h3 class="text-sm font-bold text-slate-900 line-clamp-2 group-hover:text-orange-500 transition-colors leading-snug">
                {{ article.title }}
              </h3>
            </Link>

            <p class="text-gray-500 text-xs leading-relaxed line-clamp-3 flex-1">
              {{ article.excerpt || 'Tidak ada ringkasan atau deskripsi tambahan untuk konten ini.' }}
            </p>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
              <div class="flex items-center gap-2 text-xs">
                <template v-if="!article.is_event">
                  <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-slate-700 text-[10px] font-bold">
                    {{ article.author?.name?.charAt(0).toUpperCase() || 'A' }}
                  </div>
                  <span class="font-bold text-slate-800 text-[11px]">{{ article.author?.name || 'Admin' }}</span>
                </template>
                <template v-else>
                  <span class="text-[11px] font-bold text-slate-700">
                    {{ article.participants_count || 0 }} Peserta Terdaftar
                  </span>
                </template>
              </div>

              <Link 
                :href="contentHref(article)"
                class="text-orange-500 font-bold text-xs hover:underline flex items-center gap-0.5"
              >
                <span>{{ article.is_event ? 'Detail' : 'Baca' }}</span>
                <span class="text-[10px]">➔</span>
              </Link>
            </div>
          </div>
        </article>
      </div>

      <div v-if="totalPages > 1" class="mt-12 flex justify-center">
        <div class="flex items-center gap-1.5">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-2 border border-gray-200 rounded-md text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 bg-white hover:bg-slate-50"
          >
            ←
          </button>
          
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page !== '...'"
              @click="currentPage = page"
              :class="[
                'w-8 h-8 rounded-md text-xs font-bold transition-all border',
                currentPage === page
                  ? 'bg-orange-500 text-white border-orange-500'
                  : 'bg-white border-gray-200 text-slate-700 hover:bg-slate-50'
              ]"
            >
              {{ page }}
            </button>
            <span v-else class="px-1 text-xs text-gray-400">...</span>
          </template>

          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 border border-gray-200 rounded-md text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 bg-white hover:bg-slate-50"
          >
            →
          </button>
        </div>
      </div>
    </div>

    <FooterSection />
    <AuthModal :show="showAuthModal" @close="showAuthModal = false" />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import FooterSection from '@/Components/FooterSection.vue';
import AuthModal from '@/Components/AuthModal.vue';

const props = defineProps({
  articles: { type: Array, default: () => [] },
  events: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) }
});

const showAuthModal = ref(false);
const searchQuery = ref(props.filters?.search || '');
const activeCategory = ref(props.filters?.category || 'all');
const sortBy = ref(props.filters?.sort || 'latest');
const currentPage = ref(props.filters?.page || 1);
const loading = ref(false);
const perPage = 9;

const categories = [
  { label: 'Semua', value: 'all', count: null },
  { label: 'Artikel', value: 'artikel', count: null },
  { label: 'Berita', value: 'berita', count: null },
  { label: 'Event', value: 'event', count: null },
];

let searchTimeout;

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchArticles();
  }, 500);
};

const filteredArticles = computed(() => {
  let articles = [...props.articles, ...props.events];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    articles = articles.filter(a => 
      a.title.toLowerCase().includes(query) || 
      a.excerpt?.toLowerCase().includes(query)
    );
  }

  if (activeCategory.value !== 'all') {
    articles = articles.filter(a => a.category === activeCategory.value);
  }

  switch (sortBy.value) {
    case 'oldest':
      articles.sort((a, b) => new Date(a.published_at) - new Date(b.published_at));
      break;
    case 'title_asc':
      articles.sort((a, b) => a.title.localeCompare(b.title));
      break;
    case 'title_desc':
      articles.sort((a, b) => b.title.localeCompare(a.title));
      break;
    case 'latest':
    default:
      articles.sort((a, b) => new Date(b.published_at) - new Date(a.published_at));
  }

  return articles;
});

const totalPages = computed(() => Math.ceil(filteredArticles.value.length / perPage));

const paginatedArticles = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredArticles.value.slice(start, start + perPage);
});

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 3) {
      pages.push(1, 2, 3, 4, '...', total);
    } else if (current >= total - 2) {
      pages.push(1, '...', total - 3, total - 2, total - 1, total);
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total);
    }
  }
  return pages;
});

const fetchArticles = () => {
  loading.value = true;
  router.get(route('articles.index'), {
    search: searchQuery.value,
    category: activeCategory.value,
    sort: sortBy.value,
    page: currentPage.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      loading.value = false;
    }
  });
};

const resetFilters = () => {
  searchQuery.value = '';
  activeCategory.value = 'all';
  sortBy.value = 'latest';
  currentPage.value = 1;
  fetchArticles();
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date);
};

const getArticleImage = (path) => {
  if (!path) return 'https://via.placeholder.com/800x500?text=No+Image';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  if (path.startsWith('storage/')) return `/${path}`;
  if (path.startsWith('/')) return path;
  return `/storage/${path}`;
};

const contentHref = (item) => {
  return item.is_event ? route('events.show', item.slug) : route('articles.show', item.slug);
};

const categoryLabel = (item) => {
  if (item.is_event) return item.event_category === 'karya' ? 'Event Karya' : 'Event Poin';
  return item.category === 'berita' ? 'Berita' : 'Artikel';
};

watch([activeCategory, sortBy], () => {
  currentPage.value = 1;
  fetchArticles();
});
</script>

<style scoped>
/* Pengaturan custom scrollbar tipis netral */
::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
</style>