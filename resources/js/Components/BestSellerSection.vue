<template>
  <section class="relative py-20 bg-[#FDFCFB] overflow-hidden group/container">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-[#E67E22]/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl pointer-events-none" />
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-[#2C3E50]/5 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl pointer-events-none" />

    <!-- Header -->
    <div class="relative text-center mb-16 px-4">
      <span class="text-[#E67E22] font-semibold tracking-widest uppercase text-sm mb-3 block">
        Top Picks
      </span>
      <h2 class="text-[#2C3E50] text-3xl md:text-4xl font-black uppercase tracking-tight mb-4">
        Best Seller <span class="text-[#E67E22]">Buku Terpopuler</span>
      </h2>
      <div class="w-20 h-1.5 bg-[#E67E22] mx-auto rounded-full" />
    </div>

    <!-- Carousel -->
    <div class="relative max-w-[1500px] mx-auto px-4 md:px-12">
      <Swiper
        :modules="modules"
        effect="coverflow"
        :grab-cursor="true"
        :centered-slides="true"
        slides-per-view="auto"
        :loop="true"
        :speed="1000"
        :coverflow-effect="{
          rotate: 15,
          stretch: 0,
          depth: 200,
          modifier: 1,
          slideShadows: false
        }"
        :autoplay="{
          delay: 4000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true
        }"
        :navigation="{
          nextEl: '.btn-next',
          prevEl: '.btn-prev'
        }"
        class="book-carousel-swiper !pb-24 !pt-10"
      >
        <SwiperSlide
          v-for="(book, index) in books"
          :key="index"
          class="!w-[220px] sm:!w-[280px] md:!w-[340px] transition-all duration-700 ease-out"
        >
          <div class="book-card-wrapper relative group/card perspective-1000">
            <!-- Glassmorphism Badge -->
            <div class="absolute -top-4 -right-4 z-20 bg-white/80 backdrop-blur-md border border-white/20 shadow-lg w-12 h-12 rounded-full flex items-center justify-center font-bold text-[#E67E22]">
              #{{ index + 1 }}
            </div>

            <div class="relative overflow-hidden rounded-2xl shadow-xl transition-all duration-500 group-hover/card:shadow-[0_20px_50px_rgba(230,126,34,0.3)]">
              <img
                :src="book.cover"
                :alt="book.title"
                class="w-full aspect-[2/3] object-cover transition-transform duration-700 group-hover/card:scale-110"
              />

              <!-- Info Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 text-white">
                <h3 class="font-bold text-lg leading-tight">{{ book.title }}</h3>
                <p class="text-sm text-gray-300 mt-2">Tersedia untuk dipinjam</p>
                <button class="mt-4 py-2 px-4 bg-[#E67E22] hover:bg-[#d35400] text-white text-xs font-bold rounded-lg transition-colors">
                  Detail Buku
                </button>
              </div>
            </div>
          </div>
        </SwiperSlide>
      </Swiper>

      <!-- Navigation Buttons -->
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
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation, EffectCoverflow } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/effect-coverflow';

defineProps({
  books: {
    type: Array,
    required: true
  }
});

const modules = [Autoplay, Navigation, EffectCoverflow];
</script>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}

.swiper-slide {
  filter: saturate(0.5) blur(1px);
  transform: scale(0.85);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.swiper-slide-active {
  filter: saturate(1) blur(0);
  transform: scale(1.05);
  z-index: 10 !important;
}

.book-carousel-swiper {
  cursor: grab;
}

.book-carousel-swiper:active {
  cursor: grabbing;
}

.swiper-slide-shadow-left,
.swiper-slide-shadow-right {
  border-radius: 1rem;
}
</style>