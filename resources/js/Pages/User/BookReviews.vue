<template>
  <div class="min-h-screen bg-white text-slate-900 font-sans antialiased">
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
      <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4 min-w-0">
          <Link href="/dashboard" class="p-2 -ml-2 rounded-full hover:bg-slate-200 transition text-slate-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </Link>
          <div class="min-w-0">
            <h1 class="text-base font-bold text-slate-900 truncate">{{ book.title }}</h1>
            <p class="text-xs text-slate-500 truncate">{{ book.author }} · {{ totalReviews }} Ulasan</p>
          </div>
        </div>
        <Link :href="route('books.show', book.id)" class="text-xs font-bold text-orange-500 hover:underline transition shrink-0">
          Detail Buku
        </Link>
      </div>
    </div>

    <main class="max-w-2xl mx-auto border-x border-slate-200 min-h-[calc(100vh-61px)] bg-white">
      <div v-if="reviews.data.length === 0" class="py-20 text-center px-4">
        <h2 class="text-xl font-bold text-slate-900 mb-2">Belum ada ulasan</h2>
        <p class="text-slate-500 mb-6 text-sm max-w-xs mx-auto">Jadilah yang pertama memberi ulasan untuk buku ini dan bagikan pengalaman membaca Anda.</p>
        <Link href="/dashboard" class="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white rounded-full font-bold text-sm hover:opacity-90 transition">
          Kembali ke Dashboard
        </Link>
      </div>

      <div v-else class="divide-y divide-slate-200">
        <div
          v-for="review in reviews.data"
          :key="review.id"
          :id="`review-${review.id}`"
          class="p-4 flex gap-3 hover:bg-slate-50/50 transition-colors duration-200"
        ><img 
            :src="review.user.avatar_url || review.user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(review.user.name)}&background=f97316&color=white&size=128`"
            :alt="review.user.name"
            class="w-10 h-10 rounded-full object-cover shrink-0 border border-slate-200"
          />

          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
              <div class="flex items-baseline gap-1.5 min-w-0 flex-wrap">
                <span class="text-sm font-bold text-slate-900 truncate">{{ review.user.name }}</span>
                <span class="text-xs text-slate-500 shrink-0">·</span>
                <span class="text-xs text-slate-500 shrink-0">{{ formatDate(review.created_at) }}</span>
              </div>
              <div class="flex items-center gap-0.5 text-xs font-bold text-slate-900 shrink-0">
                <span>★</span>
                <span>{{ review.rating }}</span>
              </div>
            </div>

            <p class="text-sm text-slate-900 leading-normal mt-1 whitespace-pre-wrap font-normal">
              {{ review.comment || 'Tidak ada komentar.' }}
            </p>

            <div v-if="review.image_path" class="mt-3 rounded-xl overflow-hidden border border-slate-200 max-h-80 bg-white">
              <img :src="review.image_path" :alt="review.user.name" class="w-full h-full object-cover" />
            </div>

            <div class="flex items-center justify-between max-w-md mt-3 -ml-2 text-slate-500">
              <button 
                @click="openCommentModal(review)"
                class="flex items-center gap-2 p-2 rounded-full hover:bg-orange-50 hover:text-orange-500 transition group text-left"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs font-medium group-hover:text-orange-500">{{ review.comments_count }}</span>
              </button>

              <button 
                @click="toggleLike(review)"
                :disabled="!authUser"
                class="flex items-center gap-2 p-2 rounded-full hover:bg-red-50 hover:text-red-600 transition group text-left"
                :class="{ 'text-red-600': review.user_liked }"
              >
                <svg class="w-4 h-4" :fill="review.user_liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-xs font-medium">{{ review.likes_count }}</span>
              </button>

              <button 
                @click="toggleBookmark(review)"
                :disabled="!authUser"
                class="flex items-center gap-2 p-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition group text-left"
                :class="{ 'text-blue-600': review.user_bookmarked }"
              >
                <svg class="w-4 h-4" :fill="review.user_bookmarked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <span class="text-xs font-medium">{{ review.bookmarks_count }}</span>
              </button>
            </div>

            <div v-if="review.comments_count > 0" class="mt-3 space-y-3 pl-2 border-l-2 border-slate-200">
              <div v-for="(comment, idx) in review.comments.slice(0, 2)" :key="idx" class="text-xs flex gap-2 items-start pt-1">
                <img 
  :src="selectedReview?.user.avatar_url || selectedReview?.user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedReview?.user.name || '')}&background=f97316&color=white&size=128`"
  class="w-10 h-10 rounded-full object-cover shrink-0 z-10 bg-white"
/>
                <div class="flex-1 min-w-0">
                  <span class="font-bold text-slate-900 mr-1.5 hover:underline cursor-pointer">{{ comment.user.name }}</span>
                  <span class="text-slate-900 font-normal leading-normal">{{ comment.comment }}</span>
                </div>
              </div>
              
              <button
                @click="openCommentModal(review)"
                v-if="review.comments_count > 2"
                class="block text-[11px] text-orange-500 font-bold hover:underline transition pt-1 pl-7"
              >
                Lihat semua {{ review.comments_count }} komentar
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="reviews.last_page > 1" class="flex items-center justify-between p-4 border-t border-slate-200">
        <Link
          v-if="reviews.current_page > 1"
          :href="`?page=${reviews.current_page - 1}`"
          class="px-4 py-2 rounded-full border border-slate-200 text-slate-900 hover:bg-slate-50 transition font-bold text-xs"
        >
          Sebelumnya
        </Link>
        <span class="text-xs text-slate-500 font-medium mx-auto">Halaman {{ reviews.current_page }} dari {{ reviews.last_page }}</span>
        <Link
          v-if="reviews.current_page < reviews.last_page"
          :href="`?page=${reviews.current_page + 1}`"
          class="px-4 py-2 rounded-full border border-slate-200 text-slate-900 hover:bg-slate-50 transition font-bold text-xs"
        >
          Selanjutnya
        </Link>
      </div>
    </main>

    <div v-if="showCommentModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-start sm:items-center justify-center p-0 sm:p-4">
      <div class="w-full sm:max-w-xl bg-white h-full sm:h-auto sm:max-h-[90vh] border border-slate-200 flex flex-col overflow-hidden shadow-2xl mt-0 sm:mt-8">
        
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between shrink-0">
          <span class="text-sm font-bold text-slate-900">Balas Ulasan</span>
          <button @click="showCommentModal = false" class="p-1.5 rounded-full hover:bg-slate-100 text-slate-900 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          
          <div class="flex gap-3 relative">
            <div class="absolute top-10 bottom-[-16px] left-5 w-0.5 bg-slate-200"></div>
            
            <img 
  :src="selectedReview?.user.avatar_url || selectedReview?.user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedReview?.user.name || '')}&background=f97316&color=white&size=128`"
  class="w-10 h-10 rounded-full object-cover shrink-0 z-10 bg-white"
/>
            <div class="flex-1 min-w-0 pb-2">
              <div class="flex items-baseline gap-1.5">
                <span class="text-sm font-bold text-slate-900">{{ selectedReview?.user.name }}</span>
                <span class="text-xs text-slate-500">{{ formatDate(selectedReview?.created_at) }}</span>
              </div>
              <p class="text-sm text-slate-900 mt-1 leading-normal font-normal">{{ selectedReview?.comment }}</p>
              <p class="text-xs text-slate-500 mt-2">
                Membalas ke <span class="text-orange-500">@{{ selectedReview?.user.name.toLowerCase().replace(/\s+/g, '') }}</span>
              </p>
            </div>
          </div>

          <div v-if="authUser" class="flex gap-3 pt-2">
            <img 
  :src="authUser.avatar_url || authUser.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(authUser.name)}&background=f97316&color=white&size=128`"
  class="w-10 h-10 rounded-full object-cover shrink-0 z-10"
/>
            <div class="flex-1 min-w-0">
              <form @submit.prevent="submitComment">
                <textarea
                  v-model="commentForm.comment"
                  placeholder="Tulis balasan Anda..."
                  class="w-full px-0 py-1.5 border-0 bg-transparent text-slate-900 focus:ring-0 placeholder:text-slate-400 text-sm resize-none font-normal outline-none min-h-[60px]"
                  rows="3"
                ></textarea>
                
                <div v-if="commentForm.image" class="mt-2 mb-3 relative inline-block">
                  <span class="text-[11px] text-slate-700 bg-slate-100 border border-slate-200 pl-2 pr-8 py-1 rounded-md max-w-xs truncate block">
                    File: {{ commentForm.image.name }}
                  </span>
                  <button type="button" @click="commentForm.image = null" class="absolute right-1.5 top-1 text-slate-400 hover:text-slate-900 font-bold text-xs">
                    ✕
                  </button>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 mt-1">
                  <label class="cursor-pointer text-slate-500 hover:text-orange-500 transition p-1.5 rounded-full hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="file" @change="handleImageUpload" accept="image/*" class="hidden" />
                  </label>

                  <button
                    type="submit"
                    :disabled="!commentForm.comment.trim() || commentForm.uploading"
                    class="px-4 py-1.5 bg-orange-500 text-white rounded-full font-bold text-xs disabled:opacity-50 disabled:cursor-not-allowed transition shrink-0"
                  >
                    {{ commentForm.uploading ? 'Mengirim...' : 'Balas' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div v-if="selectedReview?.comments?.length > 0" class="border-t border-slate-100 pt-4">
            <h3 class="text-xs font-bold text-slate-400 mb-4 tracking-wider uppercase">Balasan Lainnya</h3>
            
            <div class="space-y-4">
              <div v-for="comment in selectedReview?.comments" :key="comment.id" class="flex gap-3 items-start">
                <img 
                  :src="comment.user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=f97316&color=white`"
                  class="w-8 h-8 rounded-full object-cover shrink-0"
                />
                <div class="flex-1 min-w-0 border-b border-slate-50 pb-3">
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-baseline gap-1.5">
                      <span class="text-xs font-bold text-slate-900">{{ comment.user.name }}</span>
                      <span class="text-[10px] text-slate-400 font-medium">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    <button
                      v-if="comment.user_id === authUser?.id"
                      @click="deleteComment(comment.id)"
                      class="text-[11px] text-slate-400 hover:text-red-500 font-medium transition"
                    >
                      Hapus
                    </button>
                  </div>
                  <p class="text-xs text-slate-900 mt-0.5 leading-normal font-normal whitespace-pre-wrap">{{ comment.comment }}</p>
                  <img v-if="comment.image_path" :src="comment.image_path" class="mt-2 rounded-md max-w-xs h-auto border border-slate-200" />
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  book: Object,
  reviews: Object,
  totalReviews: Number,
});

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const showCommentModal = ref(false);
const selectedReview = ref(null);
const commentForm = ref({
  comment: '',
  image: null,
  uploading: false,
});

onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  const reviewId = params.get('review');
  if (!reviewId) return;

  requestAnimationFrame(() => {
    document.getElementById(`review-${reviewId}`)?.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  });
});

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
      confirmButtonColor: '#0f172a'
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
      confirmButtonColor: '#0f172a'
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
      confirmButtonColor: '#0f172a'
    });
    return;
  }
  
  selectedReview.value = review;
  showCommentModal.value = true;
  commentForm.value = { comment: '', image: null, uploading: false };
}

function handleImageUpload(e) {
  commentForm.value.image = e.target.files[0];
}

function submitComment() {
  if (!commentForm.value.comment.trim()) return;

  commentForm.value.uploading = true;
  const formData = new FormData();
  formData.append('comment', commentForm.value.comment);
  if (commentForm.value.image) {
    formData.append('image', commentForm.value.image);
  }

  fetch(route('reviews.comment.store', selectedReview.value.id), {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      'Accept': 'application/json',
    },
    body: formData,
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const newComment = data.comment;
      
      const targetReview = props.reviews.data.find(item => item.id === selectedReview.value.id);
      if (targetReview) {
        targetReview.comments = [newComment, ...(targetReview.comments || [])];
        targetReview.comments_count = data.comments_count;
        selectedReview.value = targetReview;
      }

      commentForm.value = { comment: '', image: null, uploading: false };
    }
  })
  .catch(err => console.error(err))
  .finally(() => {
    commentForm.value.uploading = false;
  });
}

function deleteComment(commentId) {
  fetch(route('reviews.comment.delete', commentId), {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      'Accept': 'application/json',
    },
  })
  .then(res => res.json())
  .then(data => {
    const targetReview = props.reviews.data.find(item => item.id === selectedReview.value.id);
    if (targetReview) {
      targetReview.comments = (targetReview.comments || []).filter(comment => comment.id !== commentId);
      targetReview.comments_count = data.comments_count;
      selectedReview.value = targetReview;
    }
  })
  .catch(err => console.error(err));
}
</script>