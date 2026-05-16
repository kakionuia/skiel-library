<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <Link href="/dashboard" class="flex items-center justify-center h-10 w-10 rounded-full hover:bg-slate-100 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </Link>
            <div>
              <h1 class="text-2xl font-black text-slate-900">Ulasan Komunitas</h1>
              <p class="text-xs text-slate-500 font-medium mt-0.5">Feed ulasan dari semua buku & anggota</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tabs -->
      <div class="flex items-center gap-2 mb-8 border-b border-slate-200">
        <button
          @click="activeTab = 'latest'"
          :class="[
            'px-6 py-4 font-bold text-sm transition-all duration-300 border-b-2 -mb-[2px]',
            activeTab === 'latest'
              ? 'text-orange-600 border-orange-600'
              : 'text-slate-500 hover:text-slate-700 border-transparent'
          ]"
        >
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Terbaru
          </span>
        </button>
        <button
          @click="activeTab = 'best'"
          :class="[
            'px-6 py-4 font-bold text-sm transition-all duration-300 border-b-2 -mb-[2px]',
            activeTab === 'best'
              ? 'text-orange-600 border-orange-600'
              : 'text-slate-500 hover:text-slate-700 border-transparent'
          ]"
        >
          <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Terbaik
          </span>
        </button>
      </div>

      <!-- Feed Container -->
      <div class="space-y-6">
        <!-- Latest Reviews Feed -->
        <div v-if="activeTab === 'latest'">
          <div v-if="latestReviews.length > 0" class="space-y-6">
            <div
              v-for="review in latestReviews"
              :key="review.id"
              class="bg-white rounded-2xl overflow-hidden border border-slate-200 hover:shadow-lg transition-all duration-300"
            >
              <!-- Review Header -->
              <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-4 min-w-0">
                  <img 
                    :src="`https://ui-avatars.com/api/?name=${review.user.name}&background=ea580c&color=white`"
                    :alt="review.user.name"
                    class="w-12 h-12 rounded-full object-cover"
                  />
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-bold text-slate-900">{{ review.user.name }}</p>
                      <span class="text-xs text-slate-400">{{ formatDate(review.created_at) }}</span>
                    </div>
                    <Link :href="route('books.show', review.book.id)" class="text-xs text-orange-600 hover:text-orange-700 font-bold transition">
                      {{ review.book.title }}
                    </Link>
                  </div>
                </div>
                <div class="flex items-center gap-0.5">
                  <span v-for="star in 5" :key="star" class="text-base" :class="star <= review.rating ? 'text-amber-400' : 'text-slate-200'">★</span>
                </div>
              </div>

              <!-- Review Content -->
              <div class="px-6 py-4">
                <p class="text-sm text-slate-700 leading-relaxed mb-4">{{ review.comment || 'Tidak ada komentar.' }}</p>
                <div v-if="review.image_path" class="rounded-lg overflow-hidden bg-slate-100 mb-4">
                  <img :src="review.image_path" :alt="review.user.name" class="w-full h-auto max-h-96 object-cover" />
                </div>
              </div>

              <!-- Review Stats -->
              <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 grid grid-cols-3 gap-2 text-xs text-center">
                <div class="py-2">
                  <p class="font-bold text-slate-900">{{ review.likes_count }}</p>
                  <p class="text-slate-500">Suka</p>
                </div>
                <div class="py-2">
                  <p class="font-bold text-slate-900">{{ review.comments_count }}</p>
                  <p class="text-slate-500">Komentar</p>
                </div>
                <div class="py-2">
                  <p class="font-bold text-slate-900">{{ review.bookmarks_count }}</p>
                  <p class="text-slate-500">Simpan</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="px-6 py-4 border-t border-slate-100 flex items-center gap-2">
                <button
                  @click="toggleLike(review)"
                  :class="[
                    'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-bold text-sm transition-all duration-300',
                    review.user_liked
                      ? 'bg-red-50 text-red-600 border border-red-200'
                      : 'bg-slate-50 text-slate-600 hover:bg-red-50 hover:text-red-600 border border-slate-200 hover:border-red-200'
                  ]"
                >
                  <svg class="w-4 h-4" :class="review.user_liked ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                  </svg>
                  Suka
                </button>

                <button
                  @click="openCommentModal(review)"
                  class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-bold text-sm transition-all duration-300 bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200 hover:border-blue-200"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  Balas
                </button>

                <button
                  @click="toggleBookmark(review)"
                  :class="[
                    'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-bold text-sm transition-all duration-300',
                    review.user_bookmarked
                      ? 'bg-blue-50 text-blue-600 border border-blue-200'
                      : 'bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200 hover:border-blue-200'
                  ]"
                >
                  <svg class="w-4 h-4" :class="review.user_bookmarked ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 19V5z"/>
                  </svg>
                  Simpan
                </button>
              </div>
            </div>
          </div>
          <div v-else class="py-24 text-center">
            <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-slate-100 mb-4">
              <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <p class="text-lg font-bold text-slate-400">Belum ada ulasan</p>
            <p class="text-sm text-slate-400 mt-2">Mulai baca dan tulis ulasan untuk buku favoritmu</p>
          </div>
        </div>

        <!-- Best Reviews Feed -->
        <div v-if="activeTab === 'best'">
          <div v-if="bestReviews.length > 0" class="space-y-6">
            <div
              v-for="review in bestReviews"
              :key="review.id"
              class="bg-white rounded-2xl overflow-hidden border border-slate-200 hover:shadow-lg transition-all duration-300"
            >
              <!-- Review Header with Badge -->
              <div class="relative px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50 to-amber-50">
                <div class="absolute top-3 right-3 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full">
                  🔥 TRENDING
                </div>
                
                <div class="flex items-center gap-4 min-w-0">
                  <img 
                    :src="`https://ui-avatars.com/api/?name=${review.user.name}&background=ea580c&color=white`"
                    :alt="review.user.name"
                    class="w-12 h-12 rounded-full object-cover"
                  />
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-bold text-slate-900">{{ review.user.name }}</p>
                      <span class="text-xs text-slate-400">{{ formatDate(review.created_at) }}</span>
                    </div>
                    <Link :href="route('books.show', review.book.id)" class="text-xs text-orange-600 hover:text-orange-700 font-bold transition">
                      {{ review.book.title }}
                    </Link>
                  </div>
                </div>
              </div>

              <!-- Rating Stars -->
              <div class="px-6 pt-4 pb-2 flex items-center gap-1">
                <span v-for="star in 5" :key="star" class="text-2xl" :class="star <= review.rating ? 'text-amber-400' : 'text-slate-200'">★</span>
                <span class="ml-auto text-sm font-black text-amber-600">{{ review.rating }}/5</span>
              </div>

              <!-- Review Content -->
              <div class="px-6 py-4">
                <p class="text-sm text-slate-700 leading-relaxed mb-4">{{ review.comment || 'Tidak ada komentar.' }}</p>
                <div v-if="review.image_path" class="rounded-lg overflow-hidden bg-slate-100 mb-4">
                  <img :src="review.image_path" :alt="review.user.name" class="w-full h-auto max-h-96 object-cover" />
                </div>
              </div>

              <!-- Review Stats - Highlighted -->
              <div class="px-6 py-4 border-t border-slate-100 bg-gradient-to-r from-orange-50 to-amber-50 grid grid-cols-3 gap-2 text-xs text-center">
                <div class="py-2">
                  <p class="font-black text-orange-600">{{ review.likes_count }}</p>
                  <p class="text-slate-600 font-bold">❤️ Suka</p>
                </div>
                <div class="py-2 border-l border-r border-orange-200">
                  <p class="font-black text-orange-600">{{ review.comments_count }}</p>
                  <p class="text-slate-600 font-bold">💬 Komentar</p>
                </div>
                <div class="py-2">
                  <p class="font-black text-orange-600">{{ review.bookmarks_count }}</p>
                  <p class="text-slate-600 font-bold">🔖 Simpan</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="px-6 py-4 border-t border-slate-100 flex items-center gap-2">
                <button
                  @click="toggleLike(review)"
                  :class="[
                    'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-bold text-sm transition-all duration-300',
                    review.user_liked
                      ? 'bg-red-50 text-red-600 border border-red-200'
                      : 'bg-slate-50 text-slate-600 hover:bg-red-50 hover:text-red-600 border border-slate-200 hover:border-red-200'
                  ]"
                >
                  <svg class="w-4 h-4" :class="review.user_liked ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                  </svg>
                  Suka
                </button>

                <button
                  @click="openCommentModal(review)"
                  class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-bold text-sm transition-all duration-300 bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200 hover:border-blue-200"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  Balas
                </button>

                <button
                  @click="toggleBookmark(review)"
                  :class="[
                    'flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-bold text-sm transition-all duration-300',
                    review.user_bookmarked
                      ? 'bg-blue-50 text-blue-600 border border-blue-200'
                      : 'bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200 hover:border-blue-200'
                  ]"
                >
                  <svg class="w-4 h-4" :class="review.user_bookmarked ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 19V5z"/>
                  </svg>
                  Simpan
                </button>
              </div>
            </div>
          </div>
          <div v-else class="py-24 text-center">
            <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-slate-100 mb-4">
              <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <p class="text-lg font-bold text-slate-400">Belum ada ulasan terbaik</p>
            <p class="text-sm text-slate-400 mt-2">Ulasan dengan rating tinggi akan muncul di sini</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  latestReviews: Array,
  bestReviews: Array,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const activeTab = ref('latest');

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
  if (!authUser.value) {
    Swal.fire({
      title: 'Masuk Dulu',
      text: 'Silakan masuk untuk menyukai ulasan',
      icon: 'info',
      confirmButtonColor: '#ea580c'
    });
    return;
  }
  
  router.post(route('reviews.like', review.id));
}

function toggleBookmark(review) {
  if (!authUser.value) {
    Swal.fire({
      title: 'Masuk Dulu',
      text: 'Silakan masuk untuk menyimpan ulasan',
      icon: 'info',
      confirmButtonColor: '#ea580c'
    });
    return;
  }
  
  router.post(route('reviews.bookmark', review.id));
}

function openCommentModal(review) {
  if (!authUser.value) {
    Swal.fire({
      title: 'Masuk Dulu',
      text: 'Silakan masuk untuk mengomentari ulasan',
      icon: 'info',
      confirmButtonColor: '#ea580c'
    });
    return;
  }
  
  router.visit(route('books.reviews', review.book.id));
}
</script>
