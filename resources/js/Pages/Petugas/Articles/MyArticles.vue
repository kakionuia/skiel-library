<template>
  <AuthenticatedLayout title="Artikel Saya">
    <div class="min-h-screen">
      <div class="mb-8 flex justify-between items-start">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Artikel Saya</h1>
          <p class="text-gray-600 mt-2">Kelola artikel dan berita yang telah Anda buat</p>
        </div>
        <Link :href="route('petugas.articles.create')" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
          Buat Artikel Baru
        </Link>
      </div>

      <!-- Status Filter -->
      <div class="mb-6 flex gap-2">
        <button
          v-for="status in ['all', 'draft', 'pending', 'approved', 'rejected']"
          :key="status"
          @click="filterStatus = status"
          :class="[
            'px-4 py-2 rounded-lg font-semibold transition',
            filterStatus === status
              ? 'bg-blue-600 text-white'
              : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-500'
          ]"
        >
          {{ status === 'all' ? 'Semua' : status === 'pending' ? 'Menunggu' : status.charAt(0).toUpperCase() + status.slice(1) }}
        </button>
      </div>

      <!-- Articles Grid -->
      <div v-if="filteredArticles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="article in filteredArticles" :key="article.id" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
          <!-- Featured Image -->
          <div v-if="article.featured_image" class="h-40 bg-gray-200 overflow-hidden">
            <img :src="getImageUrl(article.featured_image)" :alt="article.title" class="w-full h-full object-cover" />
          </div>
          <div v-else class="h-40 bg-gradient-to-br from-blue-200 to-indigo-200 flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>

          <!-- Content -->
          <div class="p-4 space-y-3">
            <!-- Title & Status -->
            <div class="space-y-2">
              <h3 class="text-lg font-bold text-gray-900 line-clamp-2">{{ article.title }}</h3>
              <div class="flex gap-2 items-center">
                <span :class="[
                  'inline-block px-2 py-1 text-xs font-bold rounded',
                  article.category === 'berita' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'
                ]">
                  {{ article.category === 'berita' ? 'BERITA' : 'ARTIKEL' }}
                </span>
                <span :class="[
                  'inline-block px-2 py-1 text-xs font-bold rounded',
                  {
                    'bg-yellow-100 text-yellow-700': article.status === 'pending',
                    'bg-green-100 text-green-700': article.status === 'approved',
                    'bg-red-100 text-red-700': article.status === 'rejected',
                    'bg-gray-100 text-gray-700': article.status === 'draft'
                  }
                ]">
                  {{ getStatusText(article.status) }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <p class="text-sm text-gray-600 line-clamp-2">{{ article.excerpt }}</p>

            <!-- Meta Info -->
            <div class="text-xs text-gray-500 space-y-1">
              <p>Dibuat: {{ formatDate(article.created_at) }}</p>
              <p v-if="article.status === 'rejected'" class="text-red-600 font-semibold">
                Alasan: {{ article.rejection_reason }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 pt-3 border-t border-gray-100">
              <Link
                :href="route('petugas.articles.edit', article.id)"
                class="flex-1 px-3 py-2 text-xs font-semibold text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition text-center"
              >
                Edit
              </Link>
              <button
                @click="deleteArticle(article.id)"
                class="flex-1 px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded hover:bg-red-100 transition"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m0 0h6m-6-6v-6m0 0v-6m0 6h-6m0 0H6" />
        </svg>
        <p class="text-gray-500 text-lg font-medium">Belum ada artikel</p>
        <p class="text-gray-400 mt-1">Mulai tulis artikel pertama Anda sekarang!</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, computed } from 'vue'
import Swal from 'sweetalert2'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  articles: Array
})

const filterStatus = ref('all')

const filteredArticles = computed(() => {
  if (filterStatus.value === 'all') {
    return props.articles
  }
  return props.articles.filter(a => a.status === filterStatus.value)
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusText = (status) => {
  const texts = {
    'draft': 'Draft',
    'pending': 'Menunggu Persetujuan',
    'approved': 'Disetujui',
    'rejected': 'Ditolak'
  }
  return texts[status] || status
}

const getImageUrl = (path) => {
  return `/storage/${path}`
}

const deleteArticle = async (articleId) => {
  const result = await Swal.fire({
    title: 'Hapus Artikel?',
    text: 'Tindakan ini tidak dapat dibatalkan',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  })

  if (result.isConfirmed) {
    router.delete(route('petugas.articles.destroy', articleId), {
      onSuccess: () => {
        Swal.fire({
          title: 'Berhasil!',
          text: 'Artikel berhasil dihapus',
          icon: 'success',
          timer: 2000
        })
      }
    })
  }
}
</script>
