<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Hero Section -->
      <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white py-12">
        <div class="max-w-6xl mx-auto px-4">
          <h1 class="text-4xl md:text-5xl font-bold mb-2">Artikel & Berita</h1>
          <p class="text-blue-100">Dapatkan informasi terbaru tentang perpustakaan dan dunia literasi</p>
        </div>
      </div>

      <div class="max-w-6xl mx-auto px-4 py-12">
        <!-- Search & Filter -->
        <div class="mb-8 space-y-4">
          <form @submit.prevent="performSearch" class="flex gap-3">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari artikel..."
              class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
              Cari
            </button>
          </form>

          <!-- Category Filter -->
          <div class="flex gap-2 flex-wrap">
            <button
              @click="selectedCategory = null"
              :class="[
                'px-4 py-2 rounded-full font-semibold transition',
                selectedCategory === null
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-500'
              ]"
            >
              Semua
            </button>
            <button
              @click="selectedCategory = 'artikel'"
              :class="[
                'px-4 py-2 rounded-full font-semibold transition',
                selectedCategory === 'artikel'
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-500'
              ]"
            >
              Artikel
            </button>
            <button
              @click="selectedCategory = 'berita'"
              :class="[
                'px-4 py-2 rounded-full font-semibold transition',
                selectedCategory === 'berita'
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-500'
              ]"
            >
              Berita
            </button>
          </div>
        </div>

        <!-- Articles Grid -->
        <div v-if="filteredArticles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
          <Link
            v-for="article in filteredArticles"
            :key="article.id"
            :href="route('articles.show', article.slug)"
            class="group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition duration-300 hover:scale-105"
          >
            <!-- Featured Image -->
            <div class="h-48 bg-gradient-to-br from-blue-200 to-indigo-200 overflow-hidden relative">
              <img
                v-if="article.featured_image"
                :src="getImageUrl(article.featured_image)"
                :alt="article.title"
                class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>

              <!-- Category Badge -->
              <div class="absolute top-3 right-3">
                <span :class="[
                  'px-3 py-1 text-xs font-bold rounded-full',
                  article.category === 'berita'
                    ? 'bg-red-500 text-white'
                    : 'bg-blue-500 text-white'
                ]">
                  {{ article.category === 'berita' ? 'BERITA' : 'ARTIKEL' }}
                </span>
              </div>
            </div>

            <!-- Content -->
            <div class="p-5 flex flex-col h-full">
              <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition">
                {{ article.title }}
              </h3>

              <p class="text-sm text-gray-600 mb-4 line-clamp-2 flex-grow">
                {{ article.excerpt }}
              </p>

              <!-- Meta Info -->
              <div class="flex justify-between items-center text-xs text-gray-500 border-t border-gray-100 pt-3 mt-auto">
                <span>{{ formatDate(article.published_at) }}</span>
                <span class="text-blue-600 font-semibold group-hover:text-blue-700">Baca →</span>
              </div>
            </div>
          </Link>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6m-6-6v-6m0 0v-6m0 6h-6m0 0H6" />
          </svg>
          <p class="text-gray-500 text-lg">Tidak ada artikel ditemukan</p>
        </div>

        <!-- Pagination -->
        <div v-if="articles.links && articles.links.length > 0" class="flex justify-center gap-2 mt-12">
          <template v-for="link in articles.links" :key="link.label">
            <Link
              v-if="link.url"
              :href="link.url"
              :class="[
                'px-3 py-2 rounded-lg text-sm font-semibold transition',
                link.active
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-500'
              ]"
              v-html="link.label"
            />
            <span v-else class="px-3 py-2 text-sm text-gray-400" v-html="link.label" />
          </template>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

const props = defineProps({
  articles: Object,
  filters: Object
})

const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || null)

const filteredArticles = computed(() => {
  let filtered = props.articles.data || []

  if (selectedCategory.value) {
    filtered = filtered.filter(a => a.category === selectedCategory.value)
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(a =>
      a.title.toLowerCase().includes(query) ||
      a.excerpt.toLowerCase().includes(query)
    )
  }

  return filtered
})

const performSearch = () => {
  router.get(route('articles.index'), {
    search: searchQuery.value,
    category: selectedCategory.value
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getImageUrl = (path) => {
  return `/storage/${path}`
}
</script>
