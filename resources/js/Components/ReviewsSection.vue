<template>
  <section class="bg-orange-500 p-6 md:p-12 flex items-center justify-center font-sans">
    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-10">
      <!-- Best Reviews Column -->
      <div class="flex flex-col">
        <h2 class="text-white text-2xl font-bold mb-4">Ulasan Terbaik</h2>

        <!-- Empty State Best Reviews -->
        <div v-if="!bestReviews || bestReviews.length === 0" class="bg-white rounded-[2rem] p-8 shadow-xl min-h-[400px] flex flex-col items-center justify-center text-center">
          <div class="text-5xl mb-4">⭐</div>
          <h3 class="text-lg font-bold text-gray-800 mb-2">Belum Ada Ulasan Terbaik</h3>
          <p class="text-gray-500 text-sm max-w-xs">Tulis ulasan yang menarik dan berkualitas, nanti ulasanmu akan jadi yang terbaik di sini!</p>
        </div>

        <!-- Best Reviews Content -->
        <template v-else>
          <div class="bg-white rounded-[2rem] p-8 shadow-xl relative min-h-[400px] flex flex-col justify-between">
            <div
              v-for="(review, index) in bestReviews"
              :key="review.id"
              v-show="activeIndex === index"
              class="animate-fade-in"
            >
              <div class="flex items-center gap-4 mb-6">
                <img 
                  :src="review.user_avatar || `https://ui-avatars.com/api/?name=${review.user}&background=E67E22&color=white`" 
                  class="w-12 h-12 rounded-full object-cover"
                  :alt="review.user"
                />
                <div>
                  <h4 class="font-bold text-gray-800">{{ review.user }}</h4>
                  <p class="text-sm text-gray-500">{{ review.class || 'Anggota' }}</p>
                </div>
              </div>

              <div class="flex flex-col md:flex-row gap-6">
                <img
                  :src="review.book_cover || 'https://placehold.co/160x224/E67E22/white?text=Book'"
                  class="w-40 h-56 object-cover rounded-lg shadow-md"
                  :alt="review.book_title"
                />
                <div class="flex-1">
                  <div class="flex text-yellow-400 mb-2">
                    <span v-for="i in 5" :key="i">
                      {{ i <= review.rating ? '★' : '☆' }}
                    </span>
                  </div>
                  <p class="text-gray-600 text-sm leading-relaxed italic">
                    "{{ review.comment }}"
                  </p>
                  <div class="flex gap-4 mt-6 text-gray-800">
                    <button class="hover:text-red-500 transition-colors">
                      <i class="far fa-heart text-xl" />
                    </button>
                    <button class="hover:text-blue-500 transition-colors">
                      <i class="far fa-comment text-xl" />
                    </button>
                    <button class="hover:text-orange-500 transition-colors ml-auto">
                      <i class="far fa-bookmark text-xl" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dot Indicators -->
          <div v-if="bestReviews.length > 1" class="flex justify-center gap-3 mt-6">
            <button
              v-for="(_, index) in bestReviews"
              :key="index"
              @click="activeIndex = index"
              :class="[
                'h-3 w-3 rounded-full transition-all duration-300',
                activeIndex === index ? 'bg-white w-8' : 'bg-white/50'
              ]"
            />
          </div>
        </template>
      </div>

      <!-- Recent Reviews Column -->
      <div class="flex flex-col">
        <div class="flex justify-between items-end mb-4">
          <h2 class="text-white text-2xl font-bold">Terbaru</h2>
          <Link v-if="recentReviews && recentReviews.length > 0" href="/reviews-showcase" class="text-white text-sm flex items-center gap-1 hover:underline">
            Lihat lebih banyak
            <i class="fas fa-chevron-right text-xs" />
          </Link>
        </div>

        <!-- Empty State Recent Reviews -->
        <div v-if="!recentReviews || recentReviews.length === 0" class="bg-white rounded-3xl p-8 flex flex-col items-center justify-center text-center min-h-[200px]">
          <div class="text-4xl mb-3">💬</div>
          <h3 class="text-base font-bold text-gray-800 mb-1">Belum Ada Ulasan</h3>
          <p class="text-gray-500 text-sm">Jadilah yang pertama memberikan ulasan!</p>
        </div>

        <!-- Recent Reviews Content -->
        <div v-else class="space-y-4">
          <div
            v-for="item in recentReviews"
            :key="item.id"
            class="bg-white rounded-3xl p-4 flex gap-4 shadow-md hover:scale-[1.02] transition-transform"
          >
            <img 
              :src="item.user_avatar || `https://ui-avatars.com/api/?name=${item.user}&background=E67E22&color=white`" 
              class="w-14 h-14 rounded-full object-cover shrink-0"
              :alt="item.user"
            />
            <div class="flex-1">
              <div class="flex justify-between">
                <h4 class="font-bold text-gray-800 text-sm">{{ item.user }}</h4>
                <h4 class="font-bold text-gray-800 text-sm truncate ml-2 max-w-[150px]">{{ item.book_title }}</h4>
              </div>
              <div class="flex text-yellow-400 text-xs mb-2">
                <span v-for="i in 5" :key="i">
                  {{ i <= item.rating ? '★' : '☆' }}
                </span>
              </div>
              <p class="text-[10px] text-gray-500 line-clamp-2">
                {{ item.comment }}
              </p>
              <div class="flex gap-3 mt-2 text-gray-700">
                <i class="far fa-heart text-xs" />
                <i class="far fa-comment text-xs" />
                <i class="far fa-bookmark text-xs ml-auto" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  bestReviews: {
    type: Array,
    default: () => []
  },
  recentReviews: {
    type: Array,
    default: () => []
  }
});

const activeIndex = ref(0);
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>