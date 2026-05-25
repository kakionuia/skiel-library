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
      <div class="best-seller-carousel relative">
        <div class="carousel-track flex items-center justify-center">
          <div
            v-for="(book, index) in duplicatedBooks"
            :key="`${book.title}-${index}`"
            class="carousel-slide flex-shrink-0 transition-all duration-800 ease-out px-2 md:px-4"
            :class="getSlideClass(index)"
            :style="{ width: slideWidth + 'px' }"
          >
            <div class="book-card-wrapper relative group/card perspective-1000">
              <!-- Glassmorphism Badge -->
              <div class="absolute -top-4 -right-4 z-20 bg-white/80 backdrop-blur-md border border-white/20 shadow-lg w-12 h-12 rounded-full flex items-center justify-center font-bold text-[#E67E22]">
                #{{ (index % books.length) + 1 }}
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
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2 md:px-4 pointer-events-none z-30">
        <button @click="prevSlide" class="btn-prev pointer-events-auto w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-white shadow-2xl text-[#E67E22] opacity-0 -translate-x-10 group-hover/container:opacity-100 group-hover/container:translate-x-0 transition-all duration-500 hover:bg-[#E67E22] hover:text-white border border-gray-100">
          <i class="fa-solid fa-arrow-left" />
        </button>

        <button @click="nextSlide" class="btn-next pointer-events-auto w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-full bg-white shadow-2xl text-[#E67E22] opacity-0 translate-x-10 group-hover/container:opacity-100 group-hover/container:translate-x-0 transition-all duration-500 hover:bg-[#E67E22] hover:text-white border border-gray-100">
          <i class="fa-solid fa-arrow-right" />
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  books: {
    type: Array,
    required: true
  }
});

// State
const currentIndex = ref(0);
const slideWidth = ref(340);
let autoplayInterval = null;

// Duplicate books for seamless looping (3x untuk memastikan loop mulus)
const duplicatedBooks = computed(() => {
  const booksArray = props.books;
  return [...booksArray, ...booksArray, ...booksArray];
});

// Total original books
const totalSlides = computed(() => props.books.length);

// Get slide class based on position
function getSlideClass(index) {
  const normalizedIndex = index % (duplicatedBooks.value.length);
  const activeIndex = currentIndex.value % totalSlides.value + totalSlides.value;
  
  if (normalizedIndex === activeIndex) {
    return 'slide-active';
  } else if (normalizedIndex === activeIndex - 1 || normalizedIndex === activeIndex + 1) {
    return 'slide-prev-next';
  } else if (normalizedIndex === activeIndex - 2 || normalizedIndex === activeIndex + 2) {
    return 'slide-far';
  }
  return 'slide-hidden';
}

// Navigation
function nextSlide() {
  currentIndex.value++;
  checkBoundary();
  resetAutoplay();
}

function prevSlide() {
  currentIndex.value--;
  checkBoundary();
  resetAutoplay();
}

function checkBoundary() {
  if (currentIndex.value >= totalSlides.value * 2) {
    currentIndex.value = totalSlides.value;
  } else if (currentIndex.value < totalSlides.value) {
    currentIndex.value = totalSlides.value * 2 - 1;
  }
}

// Autoplay
function startAutoplay() {
  stopAutoplay();
  autoplayInterval = setInterval(() => {
    nextSlide();
  }, 4000);
}

function stopAutoplay() {
  if (autoplayInterval) {
    clearInterval(autoplayInterval);
  }
}

function resetAutoplay() {
  stopAutoplay();
  startAutoplay();
}

// Update slide width on resize
function updateSlideWidth() {
  if (window.innerWidth < 640) {
    slideWidth.value = 220;
  } else if (window.innerWidth < 768) {
    slideWidth.value = 280;
  } else {
    slideWidth.value = 340;
  }
}

onMounted(() => {
  // Start from middle set for seamless looping
  currentIndex.value = totalSlides.value;
  updateSlideWidth();
  window.addEventListener('resize', updateSlideWidth);
  startAutoplay();
});

onBeforeUnmount(() => {
  stopAutoplay();
  window.removeEventListener('resize', updateSlideWidth);
});
</script>

<style scoped>
.perspective-1000 {
  perspective: 1000px;
}

.best-seller-carousel {
  position: relative;
  overflow: hidden;
}

.carousel-track {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
}

.carousel-slide {
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

/* Slide active (tengah) */
.slide-active .book-card-wrapper {
  transform: scale(1.15);
  z-index: 10;
  position: relative;
}

.slide-active {
  filter: saturate(1) blur(0);
  opacity: 1;
  z-index: 10;
}

/* Slide prev/next (kiri kanan) */
.slide-prev-next .book-card-wrapper {
  transform: scale(0.9);
}

.slide-prev-next {
  filter: saturate(0.7) blur(0.5px);
  opacity: 0.7;
}

/* Slide far (jauh dari tengah) */
.slide-far .book-card-wrapper {
  transform: scale(0.75);
}

.slide-far {
  filter: saturate(0.3) blur(2px);
  opacity: 0.3;
}

/* Slide hidden */
.slide-hidden {
  opacity: 0;
  pointer-events: none;
}

.book-card-wrapper {
  width: 100%;
  margin: 0 auto;
  transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effect */
.carousel-slide:hover {
  cursor: grab;
}

.carousel-slide:active {
  cursor: grabbing;
}
</style>