<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <Link href="/dashboard" class="flex items-center justify-center h-10 w-10 rounded-full hover:bg-slate-100 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </Link>
            <div>
              <h1 class="text-xl font-black text-slate-900">Ulasan yang Disukai</h1>
              <p class="text-xs text-slate-500 font-medium">Koleksi ulasan favorit Anda</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/my-reviews-bookmarks" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-semibold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
              Disimpan
            </Link>
            <Link href="/my-review-comments" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-semibold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
              Komentar
            </Link>
          </div>
        </div>
      </div>
    </div>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- No Data State -->
      <div v-if="reviewLikes.data.length === 0" class="py-24 text-center">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
          </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Ulasan Favorit</h2>
        <p class="text-slate-500 mb-6">Sukai ulasan menarik untuk menyimpannya di sini</p>
        <Link href="/dashboard" class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
          Kembali ke Dashboard
        </Link>
      </div>

      <!-- Reviews Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="like in reviewLikes.data"
          :key="like.id"
          class="bg-white rounded-2xl overflow-hidden border border-slate-200 hover:border-orange-300 hover:shadow-lg transition-all duration-300 flex flex-col"
        >
          <!-- Review Header -->
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <img 
                :src="`https://ui-avatars.com/api/?name=${like.review.user.name}&background=ea580c&color=white`"
                :alt="like.review.user.name"
                class="w-10 h-10 rounded-full object-cover"
              />
              <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-900 truncate">{{ like.review.user.name }}</p>
                <p class="text-xs text-slate-400 font-medium">{{ formatDate(like.review.created_at) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-0.5">
              <span v-for="star in 5" :key="star" class="text-lg" :class="star <= like.review.rating ? 'text-amber-400' : 'text-slate-200'">★</span>
            </div>
          </div>

          <!-- Review Content -->
          <div class="px-5 py-4 flex-1">
            <p class="text-sm text-slate-700 leading-relaxed line-clamp-4">{{ like.review.comment || 'Tidak ada komentar.' }}</p>
            <div v-if="like.review.image_path" class="mt-3 rounded-lg overflow-hidden bg-slate-100">
              <img :src="like.review.image_path" :alt="like.review.user.name" class="w-full h-32 object-cover" />
            </div>
          </div>

          <!-- Review Stats -->
          <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 grid grid-cols-3 gap-2 text-xs text-center">
            <div class="py-2">
              <p class="font-bold text-slate-900">{{ like.review.likes_count }}</p>
              <p class="text-slate-500">Suka</p>
            </div>
            <div class="py-2">
              <p class="font-bold text-slate-900">{{ like.review.comments_count }}</p>
              <p class="text-slate-500">Komentar</p>
            </div>
            <div class="py-2">
              <p class="font-bold text-slate-900">{{ like.review.bookmarks_count }}</p>
              <p class="text-slate-500">Simpan</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="reviewLikes.last_page > 1" class="flex items-center justify-center gap-2 mt-12">
        <Link
          v-if="reviewLikes.current_page > 1"
          href="?page=1"
          class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition font-semibold"
        >
          Sebelumnya
        </Link>
        <div class="flex items-center gap-1">
          <button
            v-for="page in reviewLikes.last_page"
            :key="page"
            @click="$inertia.visit(`?page=${page}`)"
            :class="page === reviewLikes.current_page 
              ? 'bg-orange-600 text-white' 
              : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'"
            class="w-10 h-10 rounded-lg transition font-semibold"
          >
            {{ page }}
          </button>
        </div>
        <Link
          v-if="reviewLikes.current_page < reviewLikes.last_page"
          :href="`?page=${reviewLikes.current_page + 1}`"
          class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition font-semibold"
        >
          Selanjutnya
        </Link>
      </div>
    </main>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

defineProps({
  reviewLikes: Object,
});

const page = usePage();

function formatDate(date) {
  const d = new Date(date);
  const now = new Date();
  const diff = now - d;
  
  if (diff < 60000) return 'Baru saja';
  if (diff < 3600000) return Math.floor(diff / 60000) + ' menit lalu';
  if (diff < 86400000) return Math.floor(diff / 3600000) + ' jam lalu';
  if (diff < 604800000) return Math.floor(diff / 86400000) + ' hari lalu';
  
  return d.toLocaleDateString('id-ID');
}

function toggleLike(review) {
  router.post(route('reviews.like', review.id));
}

function toggleBookmark(review) {
  router.post(route('reviews.bookmark', review.id));
}

function openCommentModal(review) {
  // Will be implemented in a modal component
  console.log('Open comment modal for review:', review.id);
}
</script>
