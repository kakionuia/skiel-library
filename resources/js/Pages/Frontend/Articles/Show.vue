<template>
  <UserLayout>
    <div class="min-h-screen bg-gray-50 py-8">
      <article class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <Link :href="route('articles.index')" class="text-blue-600 hover:text-blue-800 font-semibold mb-6 inline-flex items-center gap-2">
          ← Kembali ke Daftar Artikel
        </Link>

        <!-- Article Header -->
        <header class="bg-white rounded-lg shadow-md p-8 mb-8">
          <!-- Category Badge -->
          <div class="mb-4">
            <span :class="[
              'inline-block px-4 py-2 text-xs font-bold rounded-full',
              article.category === 'berita'
                ? 'bg-red-100 text-red-700'
                : 'bg-blue-100 text-blue-700'
            ]">
              {{ article.category === 'berita' ? 'BERITA' : 'ARTIKEL' }}
            </span>
          </div>

          <!-- Title -->
          <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
            {{ article.title }}
          </h1>

          <!-- Meta Info -->
          <div class="flex flex-wrap gap-6 text-gray-600 border-t border-gray-200 pt-6">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
              </svg>
              <div>
                <p class="text-sm text-gray-500">Oleh</p>
                <p class="font-semibold text-gray-900">{{ article.author.name }}</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a2 2 0 012-2h8a2 2 0 012 2v9a2 2 0 01-2 2H8a2 2 0 01-2-2V7z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="text-sm text-gray-500">Dipublikasikan</p>
                <p class="font-semibold text-gray-900">{{ formatDate(article.published_at) }}</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2h2a2 2 0 012 2v9a2 2 0 01-2 2h-2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2H2a2 2 0 01-2-2v-9a2 2 0 012-2h2V5zm12-1H6v2h8V4z" />
              </svg>
              <div>
                <p class="text-sm text-gray-500">Waktu baca</p>
                <p class="font-semibold text-gray-900">{{ estimateReadTime() }} menit</p>
              </div>
            </div>
          </div>
        </header>

        <!-- Featured Image -->
        <div v-if="article.featured_image" class="mb-8 rounded-lg overflow-hidden shadow-lg">
          <img
            :src="getImageUrl(article.featured_image)"
            :alt="article.title"
            class="w-full h-96 object-cover"
          />
        </div>

        <!-- Excerpt -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8 border-l-4 border-blue-600">
          <p class="text-lg text-gray-700 font-semibold italic">{{ article.excerpt }}</p>
        </div>

        <!-- Article Content -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8 prose prose-lg max-w-none">
          <div v-html="formatContent(article.content)" class="text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
        </div>

        <!-- Share Buttons -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <p class="font-semibold text-gray-900 mb-4">Bagikan Artikel</p>
          <div class="flex gap-4">
            <button @click="shareVia('whatsapp')" class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.272-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.447-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371 0-.57 0-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421-7.403h-.004a9.87 9.87 0 00-5.031 1.378c-1.51.946-2.744 2.32-3.51 3.93C2.9 10.75 2.5 12.636 2.5 14.662c0 2.706.675 5.273 1.961 7.493l-2.087 6.224l6.378-2.09c2.139 1.16 4.561 1.773 7.036 1.773 7.57 0 13.720-6.15 13.720-13.72 0-3.66-1.505-7.125-4.093-9.585C19.625 3.006 16.149 1.5 12.408 1.5c-7.569 0-13.720 6.15-13.720 13.72z"/>
              </svg>
              WhatsApp
            </button>
            <button @click="shareVia('facebook')" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              Facebook
            </button>
            <button @click="shareVia('twitter')" class="flex items-center gap-2 px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 002.856-3.475 10 10 0 01-2.856 2.856c-1.435-1.435-3.5-2.333-5.757-2.333-4.418 0-8 3.582-8 8s3.582 8 8 8c4.418 0 8-3.582 8-8-1.435 1.435-3.5 2.333-5.757 2.333-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8z"/>
              </svg>
              Twitter
            </button>
          </div>
        </div>

        <!-- Related Articles -->
        <div v-if="relatedArticles.length > 0" class="bg-white rounded-lg shadow-md p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Link
              v-for="related in relatedArticles"
              :key="related.id"
              :href="route('articles.show', related.slug)"
              class="group"
            >
              <div class="bg-gray-100 rounded-lg h-40 mb-3 overflow-hidden">
                <img
                  v-if="related.featured_image"
                  :src="getImageUrl(related.featured_image)"
                  :alt="related.title"
                  class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                />
              </div>
              <h3 class="font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">
                {{ related.title }}
              </h3>
              <p class="text-sm text-gray-500 mt-2">{{ formatDate(related.published_at) }}</p>
            </Link>
          </div>
        </div>
      </article>
    </div>
  </UserLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

const props = defineProps({
  article: Object,
  relatedArticles: Array
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const estimateReadTime = () => {
  const wordCount = props.article.content.split(/\s+/).length
  return Math.ceil(wordCount / 200)
}

const formatContent = (content) => {
  return content
    .replace(/\n\n/g, '</p><p>')
    .replace(/^/, '<p>')
    .replace(/$/, '</p>')
}

const getImageUrl = (path) => {
  return `/storage/${path}`
}

const shareVia = (platform) => {
  const url = window.location.href
  const title = props.article.title

  const shareUrls = {
    whatsapp: `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
    twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`
  }

  window.open(shareUrls[platform], '_blank', 'width=600,height=400')
}
</script>
