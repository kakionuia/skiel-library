<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <!-- Navbar -->
    <Navbar @open-auth="showAuthModal = true" />

    <!-- Hero Article Header -->
    <div class="bg-gradient-to-r from-orange-600 to-orange-400 text-white py-16 md:py-24">
      <div class="max-w-4xl mx-auto px-4 md:px-8">
        <!-- Category Badge -->
        <div class="inline-flex items-center gap-2 mb-6">
          <span class="px-4 py-1.5 bg-white/20 backdrop-blur-sm text-sm font-semibold rounded-full">
            {{ categoryLabel }}
          </span>
          <span class="text-white/70 text-sm">{{ formatDate(article.published_at) }}</span>
        </div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight mb-6 max-w-3xl">
          {{ article.title }}
        </h1>

        <!-- Meta Info -->
        <div class="flex flex-wrap items-center gap-6 text-white/90">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center font-bold text-lg">
              {{ article.author?.name?.charAt(0) || 'A' }}
            </div>
            <div>
              <p class="font-semibold">{{ article.author?.name || 'Admin' }}</p>
              <p class="text-white/70 text-sm">Penulis Artikel</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Featured Image -->
    <div v-if="article.featured_image" class="relative h-[400px] md:h-[500px] overflow-hidden">
      <img 
        :src="article.featured_image" 
        :alt="article.title"
        class="w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-black/20"></div>
    </div>

    <!-- Article Content -->
    <article class="max-w-3xl mx-auto px-4 md:px-8 py-16 md:py-24">
      <!-- Excerpt/Introduction -->
      <div class="mb-12 pb-8 border-b-2 border-slate-200">
        <p class="text-xl text-slate-700 leading-relaxed font-medium">
          {{ article.excerpt }}
        </p>
      </div>

      <!-- Main Content -->
      <div class="prose prose-lg max-w-none mb-16">
        <div v-html="article.content" class="text-slate-700 leading-relaxed space-y-6"></div>
      </div>

      <!-- Article Stats -->
      <div class="grid grid-cols-3 md:grid-cols-3 gap-4 my-16 p-8 bg-slate-100 rounded-2xl">
        <div class="text-center">
          <p class="text-3xl font-bold text-orange-600">{{ wordCount }}</p>
          <p class="text-slate-600 text-sm mt-2">Kata</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-orange-600">{{ readTime }}</p>
          <p class="text-slate-600 text-sm mt-2">Menit Baca</p>
        </div>
        <div class="text-center">
          <p class="text-3xl font-bold text-orange-600">{{ article.views || 0 }}</p>
          <p class="text-slate-600 text-sm mt-2">Dibaca</p>
        </div>
      </div>

      <!-- Divider -->
      <hr class="my-16 border-slate-200" />

      <!-- Author Info -->
      <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl p-8 md:p-12 mb-16">
        <div class="flex flex-col md:flex-row items-center gap-6">
          <div class="w-24 h-24 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white text-4xl font-bold flex-shrink-0">
            {{ article.author?.name?.charAt(0) || 'A' }}
          </div>
          <div class="flex-1 text-center md:text-left">
            <h3 class="text-2xl font-bold text-slate-900 mb-2">{{ article.author?.name || 'Admin' }}</h3>
            <p class="text-slate-600 mb-4">
              Tim penulis perpustakaan SMKN 1 Gunungputri yang berdedikasi untuk berbagi pengetahuan dan wawasan literasi kepada semua siswa.
            </p>
            <div class="flex justify-center md:justify-start gap-4">
              <a href="#" class="text-orange-600 hover:text-orange-700 font-semibold text-sm">Lihat Profil</a>
              <span class="text-slate-300">•</span>
              <a href="#" class="text-orange-600 hover:text-orange-700 font-semibold text-sm">Artikel Lainnya</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Articles -->
      <div class="mb-16">
        <h2 class="text-3xl font-bold text-slate-900 mb-8">Artikel Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="related in relatedArticles" 
            :key="related.id"
            class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 border border-slate-100"
          >
            <div class="aspect-video bg-gradient-to-br from-orange-200 to-orange-300 flex items-center justify-center">
              <img 
                v-if="related.featured_image" 
                :src="related.featured_image" 
                :alt="related.title"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-white text-2xl">📚</span>
            </div>
            <div class="p-6">
              <p class="text-orange-600 text-sm font-semibold mb-2">{{ related.category }}</p>
              <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2">{{ related.title }}</h3>
              <p class="text-slate-600 text-sm mb-4 line-clamp-2">{{ related.excerpt }}</p>
              <Link 
                :href="route('articles.show', related.slug)"
                class="text-orange-600 hover:text-orange-700 font-semibold text-sm inline-flex items-center gap-2 group"
              >
                Baca Selengkapnya
                <span class="group-hover:translate-x-1 transition-transform">→</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </article>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-orange-600 to-orange-400 text-white py-16 md:py-24">
      <div class="max-w-3xl mx-auto px-4 md:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Tertarik dengan Koleksi Kami?</h2>
        <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
          Kunjungi perpustakaan digital kami dan jelajahi ribuan judul buku berkualitas
        </p>
        <Link 
          href="/"
          class="inline-flex items-center gap-3 bg-white text-orange-600 px-8 py-4 rounded-full font-bold hover:bg-slate-100 transition-colors"
        >
          Kembali ke Beranda
          <span>→</span>
        </Link>
      </div>
    </section>

    <!-- Footer -->
    <FooterSection />

    <AuthModal :show="showAuthModal" @close="showAuthModal = false" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import FooterSection from '@/Components/FooterSection.vue';
import AuthModal from '@/Components/AuthModal.vue';

const props = defineProps({
  article: {
    type: Object,
    required: true
  },
  relatedArticles: {
    type: Array,
    default: () => []
  }
});

const showAuthModal = ref(false);

// Format date helper
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date);
};

// Computed properties
const categoryLabel = computed(() => {
  const category = props.article?.category || 'Artikel';
  return category.charAt(0).toUpperCase() + category.slice(1);
});

const wordCount = computed(() => {
  if (!props.article?.content) return 0;
  const text = props.article.content.replace(/<[^>]*>/g, '');
  return text.split(/\s+/).filter(word => word.length > 0).length;
});

const readTime = computed(() => {
  const avgWordsPerMinute = 200;
  return Math.ceil(wordCount.value / avgWordsPerMinute) || 1;
});
</script>

<style scoped>
.prose {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.8;
  color: #475569;
}

.prose :deep(h2) {
  @apply text-2xl md:text-3xl font-bold text-slate-900 mt-12 mb-6 first:mt-0;
}

.prose :deep(h3) {
  @apply text-xl md:text-2xl font-bold text-slate-900 mt-10 mb-4;
}

.prose :deep(p) {
  @apply mb-6 leading-relaxed;
}

.prose :deep(ul),
.prose :deep(ol) {
  @apply ml-6 mb-6;
}

.prose :deep(li) {
  @apply mb-2;
}

.prose :deep(blockquote) {
  @apply border-l-4 border-orange-500 pl-6 py-2 my-6 italic text-slate-600 bg-slate-50 py-4 px-6 rounded;
}

.prose :deep(strong) {
  @apply font-bold text-slate-900;
}

.prose :deep(em) {
  @apply italic text-slate-700;
}

.prose :deep(a) {
  @apply text-orange-600 hover:text-orange-700 underline;
}

.prose :deep(img) {
  @apply rounded-lg shadow-lg my-8 max-w-full h-auto;
}

.prose :deep(code) {
  @apply bg-slate-100 px-2 py-1 rounded text-sm font-mono text-slate-900;
}

.prose :deep(pre) {
  @apply bg-slate-900 text-white p-6 rounded-lg overflow-x-auto my-6;
}

.prose :deep(pre code) {
  @apply bg-transparent px-0 py-0 text-slate-100;
}
</style>
