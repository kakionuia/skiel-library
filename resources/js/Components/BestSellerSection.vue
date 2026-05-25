<template>
  <section v-if="books.length > 0" class="relative py-20 bg-[#FDFCFB] overflow-hidden group/container">
    <div class="absolute top-0 left-0 w-64 h-64 bg-[#E67E22]/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl pointer-events-none" />
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#2C3E50]/5 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl pointer-events-none" />

    <div class="relative text-center mb-16 px-4">
      <span class="text-[#E67E22] font-semibold tracking-widest uppercase text-sm mb-3 block">
        Top Picks
      </span>
      <h2 class="text-[#2C3E50] text-3xl md:text-4xl font-black uppercase tracking-tight mb-4">
        Best Seller <span class="text-[#E67E22]">Buku Terpopuler</span>
      </h2>
      <div class="w-20 h-1.5 bg-[#E67E22] mx-auto rounded-full" />
    </div>

    <div class="relative max-w-[1600px] mx-auto px-4 md:px-12">
      <Swiper
        :modules="modules"
        :slides-per-view="5"
        :centered-slides="true"
        :loop="books.length > 3"
        :speed="800"
        :space-between="30"
        :grab-cursor="true"
        :autoplay="{
          delay: 4000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true
        }"
        :navigation="{
          nextEl: '.btn-next',
          prevEl: '.btn-prev'
        }"
        :breakpoints="{
          320: {
            slidesPerView: 1.2,
            spaceBetween: 15
          },
          640: {
            slidesPerView: 2,
            spaceBetween: 20
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 30
          },
          1280: {
            slidesPerView: 5,
            spaceBetween: 30
          }
        }"
        class="best-seller-swiper !pb-24 !pt-10"
      >
        <SwiperSlide
          v-for="(book, index) in displayBooks"
          :key="`${index}-${book.id}`"
        >
          <div class="book-card-wrapper relative group/card perspective-1000">
            <div class="absolute -top-4 -right-4 z-20 bg-white/80 backdrop-blur-md border border-white/20 shadow-lg w-12 h-12 rounded-full flex items-center justify-center font-bold text-[#E67E22]">
              #{{ (index % totalUniqueBooks) + 1 }}
            </div>

            <Link
              :href="book.detail_url || `/books/${book.source_id || book.id}`"
              class="relative overflow-hidden rounded-2xl shadow-xl transition-all duration-500 group-hover/card:shadow-[0_20px_50px_rgba(230,126,34,0.3)] block"
            >
              <img
                :src="book.cover"
                :alt="book.title"
                class="w-full aspect-[2/3] object-cover transition-transform duration-700 group-hover/card:scale-110"
              />

              <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover/card:opacity-100 transition-all duration-500 flex flex-col justify-end p-6 text-white translate-y-2 group-hover/card:translate-y-0">
                <h3 class="font-bold text-base leading-tight line-clamp-2">{{ book.title }}</h3>
                <p class="text-xs text-gray-300 mt-2">{{ book.totalBorrowed || 0 }} kali dipinjam</p>
                <span class="mt-4 py-2 px-4 bg-[#E67E22] hover:bg-[#d35400] text-white text-xs font-bold rounded-lg transition-colors shadow-md inline-flex">
                  Detail Buku
                </span>
              </div>
            </Link>
          </div>
        </SwiperSlide>
      </Swiper>

      <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2 md:px-4 pointer-events-none z-30">
        <button class="btn-prev pointer-events-auto w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-white shadow-2xl text-[#E67E22] opacity-0 -translate-x-10 group-hover/container:opacity-100 group-hover/container:translate-x-0 transition-all duration-500 hover:bg-[#E67E22] hover:text-white border border-gray-100">
          <i class="fa-solid fa-arrow-left" />
        </button>

        <button class="btn-next pointer-events-auto w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-white shadow-2xl text-[#E67E22] opacity-0 translate-x-10 group-hover/container:opacity-100 group-hover/container:translate-x-0 transition-all duration-500 hover:bg-[#E67E22] hover:text-white border border-gray-100">
          <i class="fa-solid fa-arrow-right" />
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';

const props = defineProps({
  books: {
    type: Array,
    required: true
  }
});

const modules = [Autoplay, Navigation];

const totalUniqueBooks = computed(() => props.books.length);

const displayBooks = computed(() => {
  if (!props.books || props.books.length === 0) return [];
  
  return props.books.map((book, index) => ({
    ...book,
    source_id: book.id,
    id: `${book.id || index}-${index}`
  }));
});
</script>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}

/* Styling untuk slide tidak aktif (sejauh 2 slide ke kiri/kanan dari pusat) */
.best-seller-swiper .swiper-slide {
  transform: scale(0.75);
  opacity: 0.4;
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  filter: saturate(0.5) blur(1.5px);
}

/* Styling untuk slide aktif tepat di tengah */
.best-seller-swiper .swiper-slide-active {
  transform: scale(1.15) !important;
  opacity: 1 !important;
  z-index: 10 !important;
  filter: saturate(1) blur(0) !important;
}

/* Styling untuk slide 1 tingkat sebelum & sesudah slide aktif */
.best-seller-swiper .swiper-slide-prev,
.best-seller-swiper .swiper-slide-next {
  transform: scale(0.85) !important;
  opacity: 0.65 !important;
  filter: saturate(0.7) blur(0.5px) !important;
}

/* Styling tambahan untuk slide 2 tingkat dari slide aktif (kiri-kanan ujung) */
.best-seller-swiper .swiper-slide-prev-prev,
.best-seller-swiper .swiper-slide-next-next {
  transform: scale(0.7) !important;
  opacity: 0.3 !important;
  filter: saturate(0.5) blur(2px) !important;
}

.best-seller-swiper {
  cursor: grab;
  overflow: visible !important; /* Mencegah card berukuran besar terpotong di sisi slide */
}

.best-seller-swiper:active {
  cursor: grabbing;
}

/* Menjaga proporsi agar pas saat ada 5 card berjajar */
.book-card-wrapper {
  width: 100%;
  max-width: 280px;
  margin: 0 auto;
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Penyesuaian ukuran card responsif */
@media (min-width: 1280px) {
  .book-card-wrapper {
    max-width: 290px;
  }
}

@media (max-width: 640px) {
  .book-card-wrapper {
    max-width: 260px;
  }
}
</style>
