<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Navbar -->
    <Navbar @open-auth="showAuthModal = true" />

    <!-- Hero Header -->
    <div class="bg-gradient-to-br from-orange-500 via-orange-600 to-amber-600 text-white">
      <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
        <div class="text-center max-w-3xl mx-auto">
          <span class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold mb-6 tracking-wide">
            📚 Artikel & Berita
          </span>
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight mb-4">
            Jelajahi Wawasan & Informasi
          </h1>
          <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            Temukan artikel inspiratif, berita terbaru, dan informasi seputar literasi dari Perpustakaan SMKN 1 Gunungputri
          </p>
          
          <!-- Search Bar -->
          <div class="mt-10 max-w-2xl mx-auto">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari artikel berdasarkan judul..."
                class="w-full px-6 py-4 pl-14 bg-white/10 backdrop-blur-sm border-2 border-white/20 rounded-2xl text-white placeholder-white/60 focus:outline-none focus:border-white/50 focus:bg-white/20 transition-all text-lg"
                @input="debounceSearch"
              />
              <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter & Sort Bar -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
          <!-- Category Filter -->
          <div class="flex flex-wrap gap-2">
            <button
              v-for="cat in categories"
              :key="cat.value"
              @click="activeCategory = cat.value"
              :class="[
                'px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300',
                activeCategory === cat.value 
                  ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' 
                  : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
              ]"
            >
              {{ cat.label }}
              <span v-if="cat.count" class="ml-1.5 text-xs opacity-70">({{ cat.count }})</span>
            </button>
          </div>

          <!-- Sort Options -->
          <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500 font-medium">Urutkan:</span>
            <select 
              v-model="sortBy"
              class="px-4 py-2.5 bg-slate-100 border-0 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 cursor-pointer"
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

    <!-- Articles Grid -->
    <div class="max-w-7xl mx-auto px-4 py-12">
      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-20">
        <div class="flex flex-col items-center gap-4">
          <div class="w-12 h-12 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
          <p class="text-slate-500 font-medium">Memuat artikel...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredArticles.length === 0" class="text-center py-20">
        <div class="text-6xl mb-6">📭</div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2">Tidak ada artikel ditemukan</h3>
        <p class="text-slate-500 max-w-md mx-auto">
          Coba ubah kata kunci pencarian atau filter kategori yang berbeda
        </p>
        <button 
          @click="resetFilters"
          class="mt-6 px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition"
        >
          Reset Filter
        </button>
      </div>

      <!-- Articles List -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="article in filteredArticles"
          :key="article.id"
          class="group bg-white rounded-2xl shadow-md hover:shadow-2xl border border-slate-100 overflow-hidden transition-all duration-500 hover:-translate-y-1 flex flex-col"
        >
          <!-- Image -->
          <Link :href="route('articles.show', article.slug)" class="relative overflow-hidden block">
            <div class="aspect-[16/10] overflow-hidden bg-slate-200">
              <img
                :src="getArticleImage(article.featured_image)"
                :alt="article.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                @error="(e) => e.target.src = 'https://via.placeholder.com/800x500?text=No+Image'"
              />
            </div>
            <!-- Category Overlay -->
            <div class="absolute top-4 left-4">
              <span class="px-4 py-2 bg-white/90 backdrop-blur-sm rounded-xl text-xs font-bold text-orange-600 shadow-lg">
                {{ article.category === 'berita' ? '📰 Berita' : '📝 Artikel' }}
              </span>
            </div>
          </Link>

          <!-- Content -->
          <div class="p-6 flex flex-col flex-1">
            <!-- Meta Info -->
            <div class="flex items-center gap-3 mb-4 text-xs text-slate-500">
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(article.published_at) }}
              </div>
              <span>•</span>
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ article.readTime || '5' }} min baca
              </div>
            </div>

            <!-- Title -->
            <Link :href="route('articles.show', article.slug)">
              <h3 class="text-xl font-bold text-slate-900 mb-3 line-clamp-2 group-hover:text-orange-600 transition-colors leading-snug">
                {{ article.title }}
              </h3>
            </Link>

            <!-- Excerpt -->
            <p class="text-slate-600 text-sm leading-relaxed line-clamp-3 mb-4 flex-1">
              {{ article.excerpt || 'Tidak ada ringkasan' }}
            </p>

            <!-- Author & Read More -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
              <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                  {{ article.author?.name?.charAt(0) || 'A' }}
                </div>
                <span class="text-sm font-semibold text-slate-700">{{ article.author?.name || 'Admin' }}</span>
              </div>
              <Link 
                :href="route('articles.show', article.slug)"
                class="text-orange-600 font-bold text-sm hover:underline inline-flex items-center gap-1"
              >
                Baca <span class="text-lg leading-none">→</span>
              </Link>
            </div>
          </div>
        </article>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-16 flex justify-center">
        <div class="flex items-center gap-2">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="currentPage === 1 ? 'bg-slate-100 text-slate-400' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-orange-300'"
          >
            ← Sebelumnya
          </button>
          
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page !== '...'"
              @click="currentPage = page"
              :class="[
                'w-11 h-11 rounded-xl text-sm font-bold transition-all',
                currentPage === page
                  ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20'
                  : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-orange-300'
              ]"
            >
              {{ page }}
            </button>
            <span v-else class="px-2 text-slate-400">...</span>
          </template>

          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            :class="currentPage === totalPages ? 'bg-slate-100 text-slate-400' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-orange-300'"
          >
            Selanjutnya →
          </button>
        </div>
      </div>
    </div>

    <!-- Footer -->
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
  articles: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
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
  { label: '📝 Artikel', value: 'artikel', count: null },
  { label: '📰 Berita', value: 'berita', count: null },
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
  let articles = [...props.articles];

  // Filter by search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    articles = articles.filter(a => 
      a.title.toLowerCase().includes(query) || 
      a.excerpt?.toLowerCase().includes(query)
    );
  }

  // Filter by category
  if (activeCategory.value !== 'all') {
    articles = articles.filter(a => a.category === activeCategory.value);
  }

  // Sort
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

// Watch for category/sort changes
watch([activeCategory, sortBy], () => {
  currentPage.value = 1;
  fetchArticles();
});
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #f97316;
  border-radius: 3px;
}
</style>