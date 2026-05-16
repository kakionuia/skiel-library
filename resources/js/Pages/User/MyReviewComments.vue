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
              <h1 class="text-xl font-black text-slate-900">Komentar Saya</h1>
              <p class="text-xs text-slate-500 font-medium">Semua komentar yang Anda buat</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/my-reviews-likes" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-semibold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
              Disukai
            </Link>
            <Link href="/my-reviews-bookmarks" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-semibold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
              Disimpan
            </Link>
          </div>
        </div>
      </div>
    </div>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- No Data State -->
      <div v-if="reviewComments.data.length === 0" class="py-24 text-center">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-4">
          <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Komentar</h2>
        <p class="text-slate-500 mb-6">Mulai komentar pada ulasan untuk melihatnya di sini</p>
        <Link href="/dashboard" class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
          Kembali ke Dashboard
        </Link>
      </div>

      <!-- Comments List -->
      <div v-else class="space-y-6">
        <div
          v-for="comment in reviewComments.data"
          :key="comment.id"
          class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:border-orange-300 hover:shadow-lg transition-all duration-300"
        >
          <!-- Comment Header -->
          <div class="px-6 py-4 border-b border-slate-100">
            <div class="flex items-start justify-between gap-4 mb-4">
              <div>
                <h3 class="text-sm font-black text-slate-900 mb-1">Komentar Anda pada Ulasan</h3>
                <p class="text-xs text-slate-500 font-medium">{{ formatDate(comment.created_at) }}</p>
              </div>
              <button
                @click="deleteComment(comment.id)"
                class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>

            <!-- Original Review Info -->
            <div class="bg-slate-50 rounded-lg p-3 mb-4">
              <p class="text-xs font-semibold text-slate-600 mb-2">Ulasan dari {{ comment.review.user.name }}:</p>
              <p class="text-sm text-slate-700">{{ comment.review.comment }}</p>
              <p class="text-xs text-slate-500 mt-2">📚 {{ comment.review.book.title }}</p>
            </div>
          </div>

          <!-- Your Comment -->
          <div class="px-6 py-4">
            <div class="flex gap-3">
              <img 
                :src="`https://ui-avatars.com/api/?name=Me&background=ea580c&color=white`"
                class="w-10 h-10 rounded-full"
              />
              <div class="flex-1">
                <p class="text-sm font-bold text-slate-900">Anda</p>
                <p class="text-sm text-slate-700 mt-1">{{ comment.comment }}</p>
                <img v-if="comment.image_path" :src="comment.image_path" class="mt-3 rounded-lg max-w-xs h-auto" />
              </div>
            </div>
          </div>

          <!-- Interaction Stats -->
          <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 grid grid-cols-2 gap-2 text-xs text-center">
            <div class="py-2">
              <p class="font-bold text-slate-900">{{ comment.review.likes_count }}</p>
              <p class="text-slate-500">Suka pada ulasan</p>
            </div>
            <div class="py-2">
              <p class="font-bold text-slate-900">{{ comment.review.comments_count }}</p>
              <p class="text-slate-500">Total komentar</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="reviewComments.last_page > 1" class="flex items-center justify-center gap-2 mt-12">
        <Link
          v-if="reviewComments.current_page > 1"
          href="?page=1"
          class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition font-semibold"
        >
          Sebelumnya
        </Link>
        <div class="flex items-center gap-1">
          <button
            v-for="page in reviewComments.last_page"
            :key="page"
            @click="$inertia.visit(`?page=${page}`)"
            :class="page === reviewComments.current_page 
              ? 'bg-orange-600 text-white' 
              : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'"
            class="w-10 h-10 rounded-lg transition font-semibold"
          >
            {{ page }}
          </button>
        </div>
        <Link
          v-if="reviewComments.current_page < reviewComments.last_page"
          :href="`?page=${reviewComments.current_page + 1}`"
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
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
  reviewComments: Object,
});

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

function deleteComment(commentId) {
  Swal.fire({
    title: 'Hapus Komentar?',
    text: 'Tindakan ini tidak dapat dibatalkan',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(route('reviews.comment.delete', commentId));
    }
  });
}
</script>
