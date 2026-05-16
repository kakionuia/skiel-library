<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <Link href="/dashboard" class="flex items-center justify-center h-10 w-10 rounded-full hover:bg-slate-100 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </Link>
            <div>
              <h1 class="text-xl font-black text-slate-900">{{ book.title }}</h1>
              <p class="text-xs text-slate-500 font-medium">{{ book.author }} • {{ totalReviews }} Ulasan</p>
            </div>
          </div>
          <div class="hidden sm:flex items-center gap-3">
            <Link :href="route('books.show', book.id)" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition font-semibold text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
              Detail Buku
            </Link>
          </div>
        </div>
      </div>
    </div>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- No Reviews State -->
      <div v-if="reviews.data.length === 0" class="py-24 text-center">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-slate-100 mb-4">
          <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2h-3l-4 4z"/>
          </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-2">Belum Ada Ulasan</h2>
        <p class="text-slate-500 mb-6">Jadilah yang pertama memberi ulasan untuk buku ini</p>
        <Link href="/dashboard" class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
          Kembali ke Dashboard
        </Link>
      </div>

      <!-- Reviews Grid (Instagram-like) -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="review in reviews.data"
          :key="review.id"
          class="bg-white rounded-2xl overflow-hidden border border-slate-200 hover:border-orange-300 hover:shadow-lg transition-all duration-300 flex flex-col"
        >
          <!-- Review Header -->
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <img 
                :src="`https://ui-avatars.com/api/?name=${review.user.name}&background=ea580c&color=white`"
                :alt="review.user.name"
                class="w-10 h-10 rounded-full object-cover"
              />
              <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-900 truncate">{{ review.user.name }}</p>
                <p class="text-xs text-slate-400 font-medium">{{ formatDate(review.created_at) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-0.5">
              <span v-for="star in 5" :key="star" class="text-lg" :class="star <= review.rating ? 'text-amber-400' : 'text-slate-200'">★</span>
            </div>
          </div>

          <!-- Review Content -->
          <div class="px-5 py-4 flex-1">
            <p class="text-sm text-slate-700 leading-relaxed line-clamp-4">{{ review.comment || 'Tidak ada komentar.' }}</p>
            <div v-if="review.image_path" class="mt-3 rounded-lg overflow-hidden bg-slate-100">
              <img :src="review.image_path" :alt="review.user.name" class="w-full h-32 object-cover" />
            </div>
          </div>

          <!-- Review Stats -->
          <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 grid grid-cols-3 gap-2 text-xs text-center">
            <div class="py-2 rounded-lg hover:bg-slate-200 transition cursor-pointer">
              <p class="font-bold text-slate-900">{{ review.likes_count }}</p>
              <p class="text-slate-500">Suka</p>
            </div>
            <div class="py-2 rounded-lg hover:bg-slate-200 transition cursor-pointer">
              <p class="font-bold text-slate-900">{{ review.comments_count }}</p>
              <p class="text-slate-500">Komentar</p>
            </div>
            <div class="py-2 rounded-lg hover:bg-slate-200 transition cursor-pointer">
              <p class="font-bold text-slate-900">{{ review.bookmarks_count }}</p>
              <p class="text-slate-500">Simpan</p>
            </div>
          </div>

          <!-- Review Actions -->
          <div class="px-5 py-4 border-t border-slate-100 flex items-center gap-2">
            <button
              @click="toggleLike(review)"
              :disabled="!authUser"
              class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg transition font-semibold text-sm"
              :class="review.user_liked 
                ? 'bg-red-50 text-red-600 hover:bg-red-100' 
                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
            >
              <svg class="w-4 h-4" :fill="review.user_liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
              {{ review.user_liked ? 'Disukai' : 'Sukai' }}
            </button>
            <button
              @click="openCommentModal(review)"
              :disabled="!authUser"
              class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-semibold text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
              Komentar
            </button>
            <button
              @click="toggleBookmark(review)"
              :disabled="!authUser"
              class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg transition font-semibold text-sm"
              :class="review.user_bookmarked 
                ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' 
                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
            >
              <svg class="w-4 h-4" :fill="review.user_bookmarked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
              {{ review.user_bookmarked ? 'Tersimpan' : 'Simpan' }}
            </button>
          </div>

          <!-- Comments Preview -->
          <div v-if="review.comments_count > 0" class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs font-semibold text-slate-600 mb-2">Komentar Terbaru:</p>
            <div v-for="(comment, idx) in review.comments.slice(0, 2)" :key="idx" class="mb-2 pb-2 border-b border-slate-200 last:border-0 last:pb-0 last:mb-0">
              <p class="text-xs font-medium text-slate-900">{{ comment.user.name }}</p>
              <p class="text-xs text-slate-600 line-clamp-2">{{ comment.comment }}</p>
            </div>
            <button
              @click="openCommentModal(review)"
              v-if="review.comments_count > 2"
              class="text-xs text-orange-600 font-semibold hover:underline"
            >
              Lihat {{ review.comments_count - 2 }} komentar lainnya
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="reviews.last_page > 1" class="flex items-center justify-center gap-2 mt-12">
        <Link
          v-if="reviews.current_page > 1"
          :href="`?page=${reviews.current_page - 1}`"
          class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition font-semibold"
        >
          Sebelumnya
        </Link>
        <div class="flex items-center gap-1">
          <button
            v-for="page in reviews.last_page"
            :key="page"
            @click="$inertia.visit(`?page=${page}`)"
            :class="page === reviews.current_page 
              ? 'bg-orange-600 text-white' 
              : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'"
            class="w-10 h-10 rounded-lg transition font-semibold"
          >
            {{ page }}
          </button>
        </div>
        <Link
          v-if="reviews.current_page < reviews.last_page"
          :href="`?page=${reviews.current_page + 1}`"
          class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition font-semibold"
        >
          Selanjutnya
        </Link>
      </div>
    </main>

    <!-- Comment Modal -->
    <div v-if="showCommentModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-end sm:items-center justify-center">
      <div class="w-full sm:max-w-lg bg-white rounded-t-3xl sm:rounded-3xl border border-slate-200 p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-black text-slate-900">Komentar Ulasan</h2>
            <p class="text-sm text-slate-500 mt-1">dari {{ selectedReview?.user.name }}</p>
          </div>
          <button
            @click="showCommentModal = false"
            class="p-2 rounded-lg hover:bg-slate-100 transition"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Original Review -->
        <div class="mb-6 pb-6 border-b border-slate-200">
          <div class="flex items-start gap-3 mb-4">
            <img 
              :src="`https://ui-avatars.com/api/?name=${selectedReview?.user.name}&background=ea580c&color=white`"
              :alt="selectedReview?.user.name"
              class="w-12 h-12 rounded-full"
            />
            <div class="flex-1">
              <p class="font-bold text-slate-900">{{ selectedReview?.user.name }}</p>
              <div class="flex items-center gap-1 mt-1">
                <span v-for="star in 5" :key="star" class="text-base" :class="star <= (selectedReview?.rating || 0) ? 'text-amber-400' : 'text-slate-200'">★</span>
              </div>
              <p class="text-sm text-slate-700 mt-2">{{ selectedReview?.comment }}</p>
            </div>
          </div>
        </div>

        <!-- Comments List -->
        <div class="mb-6 space-y-4 max-h-96 overflow-y-auto">
          <div v-if="!selectedReview?.comments || selectedReview.comments.length === 0" class="text-center py-8">
            <p class="text-slate-500 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
          </div>
          <div v-for="comment in selectedReview?.comments" :key="comment.id" class="flex items-start gap-3 pb-4 border-b border-slate-100 last:border-0">
            <img 
              :src="`https://ui-avatars.com/api/?name=${comment.user.name}&background=ea580c&color=white`"
              :alt="comment.user.name"
              class="w-9 h-9 rounded-full"
            />
            <div class="flex-1 min-w-0">
              <p class="font-bold text-sm text-slate-900">{{ comment.user.name }}</p>
              <p class="text-xs text-slate-400 mb-1">{{ formatDate(comment.created_at) }}</p>
              <p class="text-sm text-slate-700">{{ comment.comment }}</p>
              <img v-if="comment.image_path" :src="comment.image_path" :alt="comment.user.name" class="mt-2 rounded-lg max-w-48 h-auto" />
              <div v-if="comment.user_id === authUser?.id" class="mt-2 flex items-center gap-2">
                <button
                  @click="deleteComment(comment.id)"
                  class="text-xs text-red-600 hover:text-red-700 font-semibold"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Comment Form -->
        <form @submit.prevent="submitComment" class="border-t border-slate-200 pt-4" v-if="authUser">
          <div class="space-y-3">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-2">Komentar Anda</label>
              <textarea
                v-model="commentForm.comment"
                placeholder="Tulis komentar..."
                class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition text-sm resize-none"
                rows="3"
              ></textarea>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-2">Tambah Foto (Opsional)</label>
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-orange-500 outline-none text-sm"
              />
            </div>
            <button
              type="submit"
              :disabled="!commentForm.comment.trim() || commentForm.uploading"
              class="w-full px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ commentForm.uploading ? 'Mengirim...' : 'Kirim Komentar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
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
  
  selectedReview.value = review;
  showCommentModal.value = true;
  commentForm.value = { comment: '', image: null, uploading: false };
}

function handleImageUpload(e) {
  commentForm.value.image = e.target.files[0];
}

function submitComment() {
  if (!commentForm.value.comment.trim()) {
    Swal.fire({
      title: 'Komentar Kosong',
      text: 'Harap tulis komentar sebelum mengirim',
      icon: 'warning',
      confirmButtonColor: '#ea580c'
    });
    return;
  }

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
    },
    body: formData,
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      showCommentModal.value = false;
      Swal.fire({
        title: 'Berhasil!',
        text: 'Komentar Anda telah dikirim',
        icon: 'success',
        confirmButtonColor: '#ea580c'
      });
      location.reload();
    }
  })
  .finally(() => {
    commentForm.value.uploading = false;
  });
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
      fetch(route('reviews.comment.delete', commentId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        },
      })
      .then(() => location.reload())
      .catch(err => console.error(err));
    }
  });
}
</script>
