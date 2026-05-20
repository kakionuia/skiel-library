<template>
  <div class="min-h-screen bg-white text-slate-900 font-sans antialiased">
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
      <div class="max-w-2xl mx-auto px-4 py-3 flex items-center gap-6">
        <Link href="/dashboard" class="p-2 -ml-2 rounded-full hover:bg-slate-200 text-slate-900 transition shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </Link>
        <div class="min-w-0">
          <h1 class="text-base font-black text-slate-900 tracking-tight">Ulasan Komunitas</h1>
          <p class="text-xs text-slate-500 font-normal truncate">Feed ulasan dari semua buku & anggota</p>
        </div>
      </div>
    </div>

    <main class="max-w-2xl mx-auto border-x border-slate-200 min-h-[calc(100vh-53px)] bg-white">
      
      <div class="flex border-b border-slate-200 bg-white/80 backdrop-blur-md sticky top-[53px] z-30">
        <button
          @click="activeTab = 'latest'"
          class="flex-1 text-center py-3.5 hover:bg-slate-50 transition relative font-bold text-sm"
          :class="activeTab === 'latest' ? 'text-slate-900' : 'text-slate-500 font-medium'"
        >
          Terbaru
          <div v-if="activeTab === 'latest'" class="absolute bottom-0 left-1/2 -translate-x-1/2 w-12 h-1 bg-orange-500 rounded-full"></div>
        </button>
        <button
          @click="activeTab = 'best'"
          class="flex-1 text-center py-3.5 hover:bg-slate-50 transition relative font-bold text-sm"
          :class="activeTab === 'best' ? 'text-slate-900' : 'text-slate-500 font-medium'"
        >
          Terbaik 🔥
          <div v-if="activeTab === 'best'" class="absolute bottom-0 left-1/2 -translate-x-1/2 w-12 h-1 bg-orange-500 rounded-full"></div>
        </button>
      </div>

      <div class="divide-y divide-slate-200">
        
        <div v-if="activeTab === 'latest'">
          <div v-if="latestReviews.length > 0" class="divide-y divide-slate-200">
            <div
              v-for="review in latestReviews"
              :key="review.id"
              class="p-4 flex gap-3 hover:bg-slate-50/50 transition duration-200 items-start"
            >
              <img 
                :src="review.user.avatar_url || review.user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(review.user.name)}&background=f97316&color=white&size=128`"
                :alt="review.user.name"
                class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200"
              />

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-baseline gap-1.5 min-w-0 flex-wrap">
                    <span class="text-sm font-bold text-slate-900 truncate">{{ review.user.name }}</span>
                    <span class="text-xs text-slate-500">·</span>
                    <span class="text-xs text-slate-500">{{ formatDate(review.created_at) }}</span>
                  </div>
                  <div class="flex items-center gap-0.5 text-xs font-bold text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded">
                    <span class="text-amber-500">★</span>
                    <span>{{ review.rating }}</span>
                  </div>
                </div>

                <div class="text-xs mt-0.5">
                  <span class="text-slate-500">Mengulas </span>
                  <Link :href="route('books.show', review.book.id)" class="font-bold text-orange-500 hover:underline">
                    {{ review.book.title }}
                  </Link>
                </div>

                <p class="text-sm text-slate-900 leading-normal mt-2 whitespace-pre-wrap font-normal">
                  {{ review.comment || 'Tidak ada komentar.' }}
                </p>

                <div v-if="review.image_path" class="mt-3 rounded-xl overflow-hidden border border-slate-200 max-h-80 bg-white">
                  <img :src="review.image_path" :alt="review.user.name" class="w-full h-full object-cover" />
                </div>

                <div class="flex items-center justify-between max-w-md mt-3 -ml-2 text-slate-500">
                  <button 
                    @click="openCommentModal(review)"
                    class="flex items-center gap-2 p-2 rounded-full hover:bg-orange-50 hover:text-orange-500 transition group"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span class="text-xs font-medium group-hover:text-orange-500">{{ review.comments_count }}</span>
                  </button>

                  <button 
                    @click="toggleLike(review)"
                    :disabled="!authUser"
                    class="flex items-center gap-2 p-2 rounded-full hover:bg-red-50 hover:text-red-600 transition group"
                    :class="{ 'text-red-600': review.user_liked }"
                  >
                    <svg class="w-4 h-4" :fill="review.user_liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="text-xs font-medium group-hover:text-red-600">{{ review.likes_count }}</span>
                  </button>

                  <button 
                    @click="toggleBookmark(review)"
                    :disabled="!authUser"
                    class="flex items-center gap-2 p-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition group"
                    :class="{ 'text-blue-600': review.user_bookmarked }"
                  >
                    <svg class="w-4 h-4" :fill="review.user_bookmarked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <span class="text-xs font-medium group-hover:text-blue-600">{{ review.bookmarks_count }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="py-20 text-center px-4">
            <p class="text-sm font-bold text-slate-500">Belum ada ulasan terbaru</p>
            <p class="text-xs text-slate-400 mt-1">Mulai tulis ulasan untuk buku favoritmu.</p>
          </div>
        </div>

        <div v-if="activeTab === 'best'">
          <div v-if="bestReviews.length > 0" class="divide-y divide-slate-200">
            <div
              v-for="review in bestReviews"
              :key="review.id"
              class="p-4 flex gap-3 hover:bg-slate-50/50 transition duration-200 items-start"
            >
              <img 
                :src="review.user.avatar_url || review.user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(review.user.name)}&background=ea580c&color=white&size=128`"
                :alt="review.user.name"
                class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200"
              />

              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-baseline gap-1.5 min-w-0 flex-wrap">
                    <span class="text-sm font-bold text-slate-900 truncate">{{ review.user.name }}</span>
                    <span class="text-xs text-slate-500">·</span>
                    <span class="text-xs text-slate-500">{{ formatDate(review.created_at) }}</span>
                  </div>
                  <span class="text-[10px] font-black tracking-wider text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">TRENDING</span>
                </div>

                <div class="text-xs mt-0.5 flex items-center justify-between">
                  <div>
                    <span class="text-slate-500">Mengulas </span>
                    <Link :href="route('books.show', review.book.id)" class="font-bold text-orange-500 hover:underline">
                      {{ review.book.title }}
                    </Link>
                  </div>
                  <span class="text-xs font-bold text-amber-500">★ {{ review.rating }}/5</span>
                </div>

                <p class="text-sm text-slate-900 leading-normal mt-2 whitespace-pre-wrap font-normal">
                  {{ review.comment || 'Tidak ada komentar.' }}
                </p>

                <div v-if="review.image_path" class="mt-3 rounded-xl overflow-hidden border border-slate-200 max-h-80 bg-white">
                  <img :src="review.image_path" :alt="review.user.name" class="w-full h-full object-cover" />
                </div>

                <div class="flex items-center justify-between max-w-md mt-3 -ml-2 text-slate-500">
                  <button @click="openCommentModal(review)" class="flex items-center gap-2 p-2 rounded-full hover:bg-orange-50 hover:text-orange-500 transition group">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span class="text-xs font-medium group-hover:text-orange-500">{{ review.comments_count }}</span>
                  </button>

                  <button @click="toggleLike(review)" :disabled="!authUser" class="flex items-center gap-2 p-2 rounded-full hover:bg-red-50 hover:text-red-600 transition group" :class="{ 'text-red-600': review.user_liked }">
                    <svg class="w-4 h-4" :fill="review.user_liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="text-xs font-medium group-hover:text-red-600">{{ review.likes_count }}</span>
                  </button>

                  <button @click="toggleBookmark(review)" :disabled="!authUser" class="flex items-center gap-2 p-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition group" :class="{ 'text-blue-600': review.user_bookmarked }">
                    <svg class="w-4 h-4" :fill="review.user_bookmarked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    <span class="text-xs font-medium group-hover:text-blue-600">{{ review.bookmarks_count }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="py-20 text-center px-4">
            <p class="text-sm font-bold text-slate-500">Belum ada ulasan terbaik</p>
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
  if (diff < 3600000) return Math.floor(diff / 60000) + 'm';
  if (diff < 86400000) return Math.floor(diff / 3600000) + 'j';
  if (diff < 604800000) return Math.floor(diff / 86400000) + 'h';
  
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
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
  router.post(route('reviews.like', review.id), {}, { preserveScroll: true });
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
  router.post(route('reviews.bookmark', review.id), {}, { preserveScroll: true });
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
  router.visit(route('books.reviews', review.book.id) + `?review=${review.id}`);
}
</script>