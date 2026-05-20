<template>
  <div class="min-h-screen bg-white font-sans antialiased">
    <Navbar @open-auth="showAuthModal = true" />

    <main class="max-w-6xl mx-auto px-4 py-24 md:py-32 ">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
          
          <div>
            <span class="inline-block bg-orange-400 text-white text-xs font-bold px-4 py-1.5 rounded-full tracking-wide">
              {{ categoryLabel }}
            </span>
          </div>

          <h1 class="text-3xl md:text-4xl font-extrabold text-slate-950 leading-tight">
            {{ article.title }}
          </h1>

          <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="font-semibold text-slate-800">{{ article.author?.name || 'Admin' }}</span>
            <span class="text-gray-300">•</span>
            <span>{{ formatDate(article.published_at) }}</span>
          </div>

          <div v-if="article.featured_image" class="w-full">
            <div class="overflow-hidden bg-slate-100 rounded-sm">
              <img 
                :src="getImageUrl(article.featured_image)" 
                :alt="article.title"
                class="w-full h-auto object-cover max-h-[450px]"
                @error="handleImageError"
              />
            </div>
            <p class="text-center text-xs text-gray-500 mt-2 italic">
              Puing-puing reruntuhan bangunan {{ categoryLabel }}
            </p>
          </div>

          <article class="prose max-w-none pt-4">
            <div v-html="article.content" class="text-slate-800 text-sm leading-relaxed space-y-5"></div>
          </article>

          <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <span class="text-xs font-bold text-slate-900">Bagikan:</span>
            <button class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-white hover:opacity-90 transition">
              <span class="text-xs font-mono">w</span>
            </button>
            <button class="w-6 h-6 rounded-full bg-black flex items-center justify-center text-white hover:opacity-90 transition">
              <span class="text-xs font-mono">x</span>
            </button>
          </div>

          <section class="pt-8 border-t-2 border-gray-100">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-base font-bold text-slate-900">Komentar</h3>
              <span class="text-xs text-gray-500 font-medium">{{ totalComments }} Komentar</span>
            </div>
            
            <form v-if="authUser" @submit.prevent="submitComment" class="mb-8">
              <div class="flex gap-2">
                <input
                  v-model="commentForm.comment"
                  type="text"
                  placeholder="Ketikkan sesuatu untuk artikel"
                  class="flex-1 bg-slate-50 border border-gray-200 rounded-md px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-orange-500 placeholder-gray-400"
                />
                <button
                  type="submit"
                  :disabled="commentForm.processing || commentForm.comment.trim().length < 3"
                  class="bg-orange-500 text-white p-3 rounded-md hover:bg-orange-600 transition flex items-center justify-center shrink-0 disabled:opacity-50"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                  </svg>
                </button>
              </div>
            </form>

            <div v-else class="mb-8 rounded-md border border-gray-200 bg-slate-50 p-4 text-center">
              <p class="text-xs font-semibold text-slate-700">Masuk untuk ikut berdiskusi di artikel ini.</p>
              <button @click="showAuthModal = true" class="mt-2 rounded-md bg-orange-500 px-4 py-2 text-xs font-bold text-white hover:bg-orange-600 transition">
                Masuk
              </button>
            </div>

            <div v-if="comments.length === 0" class="text-center py-6 border border-dashed rounded-md text-sm text-gray-400">
              Belum ada komentar.
            </div>

            <div v-else class="space-y-4">
              <div v-for="comment in comments" :key="comment.id" class="space-y-3">
                
                <div class="border border-gray-200 rounded-md p-4 bg-white flex gap-3">
                  <div class="w-10 h-10 rounded-full bg-orange-500 shrink-0 text-white font-bold flex items-center justify-center text-xs">
                    {{ comment.user?.name?.charAt(0).toUpperCase() || 'U' }}
                  </div>
                  <div class="flex-1 space-y-1">
                    <div class="flex items-baseline justify-between">
                      <div>
                        <h5 class="text-xs font-bold text-slate-900">{{ comment.user?.name || 'User' }}</h5>
                        <p class="text-[10px] text-gray-400" v-if="comment.user?.major">
                          {{ comment.user.major }}
                        </p>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-400">{{ formatRelativeDate(comment.created_at) }}</span>
                        <button
                          v-if="authUser && comment.user_id === authUser.id"
                          @click="deleteComment(comment.id)"
                          class="text-[10px] font-semibold text-rose-600 hover:underline"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                    <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">{{ comment.comment }}</p>
                    
                    <div class="flex justify-end pt-1">
                      <button @click="startReply(comment)" class="text-[11px] text-gray-500 hover:underline flex items-center gap-1">
                        <span>Balas komentar</span>
                        <span class="text-[9px]">➔</span>
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="replyingTo?.id === comment.id" class="ml-12 bg-slate-50 border border-gray-200 rounded-md p-3">
                  <p class="text-[10px] font-semibold text-gray-500 mb-2">Membalas {{ comment.user?.name || 'User' }}</p>
                  <div class="flex gap-2">
                    <input
                      v-model="replyForm.comment"
                      type="text"
                      placeholder="Tulis balasan..."
                      class="flex-1 bg-white border border-gray-200 rounded-md px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-orange-500"
                    />
                    <div class="flex gap-1 shrink-0">
                      <button 
                        type="button" 
                        @click="cancelReply" 
                        class="px-2.5 py-2 border border-gray-200 rounded-md text-xs text-gray-500 bg-white hover:bg-gray-100 transition"
                      >
                        Batal
                      </button>
                      <button
                        @click="submitReply(comment)"
                        :disabled="replyForm.processing || replyForm.comment.trim().length < 3"
                        class="bg-orange-500 text-white px-3 py-2 rounded-md hover:bg-orange-600 text-xs font-bold transition disabled:opacity-50"
                      >
                        Kirim
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="comment.replies && comment.replies.length" class="ml-12 space-y-3 border-l-2 border-gray-100 pl-3">
                  <div v-for="reply in comment.replies" :key="reply.id" class="border border-gray-200 rounded-md p-3 bg-slate-50 flex gap-2">
                    <div class="w-8 h-8 rounded-full bg-amber-500 shrink-0 text-white font-bold flex items-center justify-center text-[10px]">
                      {{ reply.user?.name?.charAt(0).toUpperCase() || 'U' }}
                    </div>
                    <div class="flex-1 space-y-1">
                      <div class="flex items-baseline justify-between">
                        <h5 class="text-xs font-bold text-slate-900">{{ reply.user?.name || 'User' }}</h5>
                        <div class="flex items-center gap-2">
                          <span class="text-[10px] text-gray-400">{{ formatRelativeDate(reply.created_at) }}</span>
                          <button
                            v-if="authUser && reply.user_id === authUser.id"
                            @click="deleteComment(reply.id)"
                            class="text-[10px] font-semibold text-rose-600 hover:underline"
                          >
                            Hapus
                          </button>
                        </div>
                      </div>
                      <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">{{ reply.comment }}</p>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </section>

        </div>

        <div class="lg:col-span-1">
          <div class="border border-gray-100 rounded-lg p-6 shadow-sm sticky top-6 bg-white">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-gray-100">
              Artikel terbaru
            </h3>
            
            <div v-if="relatedArticles.length === 0" class="text-sm text-gray-400 py-2">
              Tidak ada artikel terbaru.
            </div>

            <div v-else class="space-y-6">
              <div v-for="related in relatedArticles.slice(0, 5)" :key="related.id" class="space-y-1">
                <span class="text-[10px] font-bold text-orange-500 uppercase tracking-wider block">
                  {{ relatedCategoryLabel(related.category) }}
                </span>
                <Link 
                  :href="route('articles.show', related.slug)"
                  class="text-xs font-bold text-slate-900 hover:text-orange-500 transition line-clamp-2 leading-snug block"
                >
                  {{ related.title }}
                </Link>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>

    <FooterSection />

    <AuthModal :show="showAuthModal" @close="showAuthModal = false" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
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
const imageError = ref(false);
const page = usePage();

// Auth States
const authUser = computed(() => page.props.auth?.user || null);

// Comments States & Logika Hitung Jumlah Komentar
const comments = computed(() => props.article.comments || []);
const totalComments = computed(() => {
  return comments.value.reduce((total, comment) => total + 1 + (comment.replies?.length || 0), 0);
});

const commentForm = ref({
  comment: '',
  processing: false
});

// Reply States
const replyingTo = ref(null);
const replyForm = ref({
  comment: '',
  processing: false
});

const getImageUrl = (path) => {
  if (!path) return '/images/placeholder.jpg';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  if (path.startsWith('storage/')) return `/${path}`;
  if (path.startsWith('/')) return path;
  return `/storage/${path}`;
};

const handleImageError = (e) => {
  imageError.value = true;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

const categoryLabel = computed(() => {
  const category = props.article?.category || 'Sekolah';
  return category.charAt(0).toUpperCase() + category.slice(1);
});

const relatedCategoryLabel = (category) => {
  const labels = {
    artikel: 'Artikel',
    berita: 'Berita',
    event: 'Event',
    prestasi: 'Prestasi'
  };
  return labels[category] || category || 'Prestasi';
};

function formatRelativeDate(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  const diff = Date.now() - date.getTime();
  if (diff < 60000) return 'Baru saja';
  if (diff < 3600000) return `${Math.floor(diff / 60000)} menit lalu`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)} jam lalu`;
  return formatDate(dateString);
}

// Submit Komentar Induk/Utama
function submitComment() {
  if (!commentForm.value.comment.trim() || commentForm.value.comment.trim().length < 3) return;

  commentForm.value.processing = true;
  router.post(route('articles.comments.store', props.article.id), {
    comment: commentForm.value.comment.trim()
  }, {
    preserveScroll: true,
    onSuccess: () => {
      commentForm.value.comment = '';
    },
    onFinish: () => {
      commentForm.value.processing = false;
    }
  });
}

// Fungsi Aktivasi Balas Komentar
function startReply(comment) {
  replyingTo.value = comment;
  replyForm.value.comment = '';
}

function cancelReply() {
  replyingTo.value = null;
  replyForm.value.comment = '';
}

// Submit Komentar Balasan (Mengirim dengan parent_id)
function submitReply(comment) {
  if (!replyForm.value.comment.trim() || replyForm.value.comment.trim().length < 3) return;

  replyForm.value.processing = true;
  router.post(route('articles.comments.store', props.article.id), {
    comment: replyForm.value.comment.trim(),
    parent_id: comment.id // Menandakan bahwa ini adalah balasan dari komentar tersebut
  }, {
    preserveScroll: true,
    onSuccess: () => {
      cancelReply();
    },
    onFinish: () => {
      replyForm.value.processing = false;
    }
  });
}

// Hapus Komentar / Balasan
function deleteComment(commentId) {
  if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
    router.delete(route('articles.comments.delete', commentId), {
      preserveScroll: true
    });
  }
}
</script>

<style scoped>
.prose :deep(p) {
  @apply mb-4 text-slate-700 text-sm leading-relaxed text-justify;
}
.prose :deep(h2) {
  @apply text-lg font-bold text-slate-900 mt-6 mb-2;
}
.prose :deep(h3) {
  @apply text-base font-bold text-slate-900 mt-4 mb-2;
}
</style>