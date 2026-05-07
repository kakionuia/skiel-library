<template>
  <div class="min-h-screen bg-white font-sans text-gray-800 selection:bg-orange-100 overflow-x-hidden">
    <Navbar @open-auth="showAuthModal = true" />

    <!-- Hero -->
    <header class="relative pt-24 md:pt-40 pb-10 md:pb-20 px-4 md:px-12 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 md:gap-0">
      <div class="md:w-1/2" data-aos="fade-right">
        <h2 class="text-orange-500 font-bold text-lg md:text-xl mb-3 text-left">Selamat Datang di!</h2>
        <h1 class="text-3xl md:text-5xl font-black leading-tight mb-6 text-gray-900 text-left">
          Pengelolaan Perpustakaan <br>
          <span class="text-orange-500">SMKN 1 Gunungputri</span> Berbasis Teknologi Web
        </h1>
        <p class="text-gray-600 mb-8 text-base md:text-lg leading-relaxed max-w-lg text-left">
          Pengelolaan koleksi perpustakaan yang lebih mudah, cepat dan modern menggunakan Sistem Informasi berbasis Digital di SMKN 1 Gunungputri.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <button class="border-2 border-orange-500 text-orange-500 px-6 sm:px-8 py-3 rounded-full font-bold hover:bg-orange-50 transition-all text-sm sm:text-base">Mulai Sekarang</button>
          <button class="bg-orange-500 text-white px-6 sm:px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-orange-200 transition-all text-sm sm:text-base">Pelajari Fitur Dahulu</button>
        </div>
      </div>

      <div class="md:w-1/2 flex justify-center md:justify-end mt-8 md:mt-0" data-aos="zoom-in">
        <div class="relative w-64 h-64 md:w-[400px] md:h-[400px]">
          <div class="absolute -top-5 -right-5 w-40 h-40 md:w-64 md:h-64 bg-orange-500 rounded-full opacity-90"></div>
          <div class="relative z-10 w-full h-full rounded-full border-[6px] md:border-[10px] border-white shadow-2xl overflow-hidden bg-gray-100">
            <img src="/kepala-sekolah.png" class="w-full h-full object-cover" alt="Kepala Sekolah">
          </div>
          <div class="absolute bottom-2 md:bottom-5 left-0 z-20 bg-white/90 backdrop-blur-sm px-3 md:px-6 py-2 md:py-3 rounded-xl md:rounded-2xl shadow-xl border border-white">
            <p class="font-bold text-gray-900 text-xs md:text-sm">Usman Ilham Pratama, M.Pd</p>
            <p class="text-[10px] md:text-xs text-gray-500 font-medium">Kepala Sekolah SMKN 1 Gunungputri</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Intro -->
    <section class="max-w-7xl mx-auto text-center py-16 px-4">
      <div class="flex justify-center items-center p-4 rounded-full bg-orange-100 w-max mx-auto mb-6">
        <img src="/image/logo-smk.png" alt="Logo" class="w-20 h-20 object-contain" />
      </div>
      <h2 class="text-3xl font-bold mb-2">Web pengelolaan perpustakaan SMKN 1 Gunungputri</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Kunjungi halaman profil dan situs kami untuk melihat lebih banyak</p>
      <div class="flex justify-center gap-2 mt-8">
        <button class="bg-orange-500 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 transition">Web Sekolah</button>
        <button class="bg-orange-500 text-white px-4 py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 transition">          
          <font-awesome-icon :icon="['fab', 'youtube']" style="color: white;" />
        </button> 
        <button class="bg-orange-500 text-white px-4 py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 transition">          
          <font-awesome-icon :icon="['fab', 'youtube']" style="color: white;" />
        </button>
      </div>
    </section>

    <!-- Fitur Unggulan (sf) -->
    <section id="fitur" class="sf bg-orange-500">
      <div class="sf__bg-glow sf__bg-glow--a"></div>
      <div class="sf__bg-glow sf__bg-glow--b"></div>
      <svg class="sf__bg-grid" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
        <defs>
          <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
            <path d="M 48 0 L 0 0 0 48" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
      </svg>

      <div class="sf__inner">
        <header class="sf__head">
          <div class="sf__kicker">
            <span class="sf__kicker-pip"></span>
            Fitur Unggulan
            <span class="sf__kicker-pip"></span>
          </div>

          <h2 class="sf__h2">
            Semua yang kamu butuhkan<br>
            ada di <em>SKIE&#8209;Library</em>
          </h2>

          <p class="sf__sub">
            Perpustakaan digital SMKN 1 Gunungputri, dirancang untuk<br>
            pengalaman meminjam yang mudah, cepat, dan menyenangkan.
          </p>
        </header>

        <div class="sf__body">
          <div class="sf__vis-col">
            <transition name="vis" mode="out-in">
              <div :key="activeIndex" class="sf__vis">
                <template v-if="activeIndex === 0">
                  <div class="v v--scan">
                    <div class="v__scan-wrap">
                      <div class="v__scan-frame">
                        <div class="v__scan-laser"></div>
                        <span class="v__sc v__sc--tl"></span>
                        <span class="v__sc v__sc--tr"></span>
                        <span class="v__sc v__sc--bl"></span>
                        <span class="v__sc v__sc--br"></span>
                        <div class="v__bars">
                          <div v-for="n in 20" :key="n" class="v__bar"
                            :style="{ height: (Math.abs(Math.sin(n * 1.9)) * 40 + 28) + 'px', opacity: 0.7 + Math.abs(Math.sin(n * 0.7)) * 0.3 }">
                          </div>
                        </div>
                      </div>
                      <div class="v__scan-ping"></div>
                    </div>
                    <p class="v__label">Arahkan kamera → proses selesai dalam detik</p>
                  </div>
                </template>

                <template v-else-if="activeIndex === 1">
                  <div class="v v--data">
                    <div class="v__data-row" v-for="(r, i) in dataRows" :key="i"
                      :style="{ '--d': i * 0.1 + 's' }">
                      <span class="v__data-e">{{ r.e }}</span>
                      <span class="v__data-t">{{ r.t }}</span>
                      <span :class="['v__data-s', 'v__data-s--' + r.c]">{{ r.s }}</span>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 2">
                  <div class="v v--notif">
                    <div class="v__bell-wrap">
                      <span class="v__bell">🔔</span>
                      <span class="v__bell-ring"></span>
                    </div>
                    <div class="v__chips">
                      <span class="v__chip" v-for="(c, i) in notifChips" :key="i"
                        :style="{ '--d': i * 0.15 + 's' }">{{ c }}</span>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 3">
                  <div class="v v--history">
                    <div class="v__tl">
                      <div class="v__tl-track"></div>
                      <div class="v__tl-item" v-for="(h, i) in historyItems" :key="i"
                        :style="{ '--d': i * 0.12 + 's' }">
                        <div class="v__tl-dot"></div>
                        <div class="v__tl-text">
                          <span class="v__tl-date">{{ h.d }}</span>
                          <span class="v__tl-title">{{ h.t }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 4">
                  <div class="v v--points">
                    <div class="v__pts-ring">
                      <span class="v__pts-num">240</span>
                      <span class="v__pts-unit">poin</span>
                    </div>
                    <div class="v__badge-row">
                      <span class="v__badge" v-for="b in badges" :key="b">{{ b }}</span>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 5">
                  <div class="v v--forum">
                    <div class="v__review" v-for="(r, i) in reviewList" :key="i"
                      :style="{ '--d': i * 0.18 + 's' }">
                      <div class="v__review-stars">{{ r.s }}</div>
                      <div class="v__review-text">{{ r.t }}</div>
                    </div>
                    <div class="v__repo">📄 Repositori KTI tersedia</div>
                  </div>
                </template>
              </div>
            </transition>
          </div>

          <ul class="sf__list">
            <li
              v-for="(f, i) in fiturList"
              :key="i"
              class="sf__item"
              :class="{ 'is-active': activeIndex === i }"
              @click="select(i)"
            >
              <div class="sf__item-head">
                <span class="sf__no">{{ f.no }}</span>
                <span class="sf__title">{{ f.title }}</span>
                <span class="sf__arrow">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
              </div>
              <div class="sf__desc">
                <p>{{ f.desc }}</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </section>

<section class="py-20 md:py-32 px-6 md:px-12 max-w-7xl mx-auto" data-aos="fade-up">
  <div class="flex flex-col md:flex-row items-center gap-16 lg:gap-24">
    
    <!-- Bagian Teks Carousel (Kiri) -->
    <div class="w-full md:w-1/2 flex flex-col justify-center">
      <div class="mb-6 inline-flex items-center gap-2">
        <span class="w-12 h-[2px] bg-orange-500"></span>
        <span class="text-orange-500 font-bold tracking-widest uppercase text-xs">Profil Perpustakaan</span>
      </div>

      <div class="relative min-h-[350px] md:min-h-[400px]">
        <transition name="slide-up" mode="out-in">
          <div :key="activeProfileSlide" class="space-y-6">
            <h2 class="text-4xl md:text-6xl font-black text-gray-900 leading-[1.1]">
              {{ profileSlides[activeProfileSlide].title }}
            </h2>
            
            <div class="pt-4">
              <template v-if="typeof profileSlides[activeProfileSlide].content === 'string'">
                <p class="text-gray-500 text-lg md:text-xl leading-relaxed max-w-md">
                  {{ profileSlides[activeProfileSlide].content }}
                </p>
              </template>
              <ul v-else class="grid grid-cols-1 gap-4">
                <li v-for="(item, idx) in profileSlides[activeProfileSlide].content" 
                    :key="idx" 
                    class="flex items-center gap-4 text-gray-600 text-lg font-semibold">
                  <span class="flex-shrink-0 w-2 h-2 bg-orange-500 rounded-full shadow-[0_0_10px_rgba(249,115,22,0.5)]"></span>
                  {{ item }}
                </li>
              </ul>
            </div>
          </div>
        </transition>
      </div>

      <!-- Dots Indicator - Gaya Modern Pill -->
      <div class="flex items-center gap-3 mt-10">
        <button
          v-for="(slide, index) in profileSlides"
          :key="index"
          @click="goToSlide(index)"
          class="h-2 rounded-full transition-all duration-500"
          :class="activeProfileSlide === index ? 'w-10 bg-orange-500' : 'w-2 bg-gray-200 hover:bg-orange-200'"
          :aria-label="'Slide ' + (index + 1)"
        ></button>
      </div>
    </div>

    <!-- Bagian Gambar Statis (Kanan) -->
    <div class="w-full md:w-1/2 relative">
      <!-- Ornamen Dekoratif Oranye -->
      <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-100 rounded-full blur-3xl opacity-60"></div>
      <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-orange-50 rounded-full blur-3xl opacity-80"></div>
      
      <div class="relative z-10">
        <!-- Frame Gambar dengan Border Oranye Tipis -->
        <div class="absolute inset-0 border-2 border-orange-500/20 translate-x-4 translate-y-4 rounded-[3rem] -z-10"></div>
        
        <img
          src="/gedung.jpg" 
          alt="Gedung Perpustakaan"
          class="rounded-[3rem] w-full h-[450px] md:h-[600px] object-cover shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] transform transition-transform duration-700 hover:scale-[1.01]"
        />

        <!-- Floating Badge (Opsional, Menambah Kesan Pro) -->
        <div class="absolute -bottom-6 right-8 bg-white p-6 rounded-2xl shadow-xl border border-gray-50 flex items-center gap-4">
          <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center text-white font-bold text-xl">
            P
          </div>
          <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">SMKN 1 Gunungputri</p>
            <p class="text-sm font-black text-gray-900">Perpustakaan Digital</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

    <!-- ===== SECTION: PETUGAS PERPUSTAKAAN ===== -->
<!-- ===== PETUGAS PERPUSTAKAAN ===== -->
<section class="relative py-24 md:py-32 px-4 bg-white overflow-hidden">
  <!-- Dekorasi latar -->
  <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-50 rounded-full blur-3xl opacity-60"></div>
    <div class="absolute -bottom-32 -right-32 w-[30rem] h-[30rem] bg-orange-50 rounded-full blur-3xl opacity-60"></div>
  </div>

  <div class="relative max-w-7xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-16 md:mb-24">
      <span class="text-orange-500 font-bold text-xs tracking-[0.25em] uppercase block mb-4">Di Balik Layanan Prima</span>
      <h2 class="text-4xl md:text-4xl lg:text-5xl font-black text-gray-900 leading-tight">
          Petugas Perpustakaan
      </h2>
      <div class="w-20 h-1 bg-orange-500 mx-auto mt-8 rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-6 lg:gap-8">
      <div
        v-for="(staff, i) in staffList"
        :key="i"
        class="group relative bg-white rounded-3xl shadow-md hover:shadow-2xl transition-all duration-700 ease-out backface-hidden overflow-hidden flex flex-col"
        data-aos="fade-up"
        :data-aos-delay="i * 150"
      >
        <div class="relative w-full h-72 sm:h-80 overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10"></div>
          
          <img
            :src="staff.photo"
            :alt="staff.name"
            class="absolute inset-0 w-full h-full object-cover transform scale-100 group-hover:scale-105 transition-all duration-700 ease-out"
          >
          
          <div class="absolute bottom-4 left-4 right-4 flex justify-center gap-3 z-20 translate-y-6 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
            <a href="#" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-orange-500 transition-colors duration-300">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white hover:bg-orange-500 transition-colors duration-300">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .867-.157 1.696-.44 2.465l-4.211-4.21a2.5 2.5 0 00-3.536 0L4.527 12.32A6 6 0 0010 16a6 6 0 004.134-10.134 6 6 0 00-4.134-1.866c.627.001 1.257.104 1.854.292l1.561 1.561a.5.5 0 01-.146.854l-2 1a.5.5 0 01-.598-.133l-1.293-1.724A6.001 6.001 0 0114 10z" clip-rule="evenodd"/></svg>
            </a>
          </div>
        </div>

        <div class="relative p-6 flex flex-col flex-1">
          <div class="w-10 h-0.5 bg-orange-500 mb-4 group-hover:w-16 transition-all duration-500"></div>
          
          <h3 class="text-xl font-bold text-gray-900 tracking-tight">{{ staff.name }}</h3>
          <p class="text-orange-500 font-semibold text-sm mt-1">{{ staff.role }}</p>
          
          <p class="text-gray-400 text-xs leading-relaxed mt-3 line-clamp-3">{{ staff.bio }}</p>
          
          <div class="mt-auto pt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <span class="text-[10px] uppercase tracking-widest font-bold text-gray-300">Bidang Keahlian</span>
            <p class="text-xs text-gray-500 mt-1">Katalogisasi, Layanan Digital</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

    <!-- Repositori -->
  <section class="bg-orange-500 py-16 md:py-24 px-6 md:px-12 text-white overflow-hidden">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12">
      
      <!-- Bagian Teks (Kiri) -->
      <div class="w-full lg:w-5/12 text-left space-y-6 z-10 bg-orange-500 p-10">
        <h3 class="text-2xl md:text-4xl font-bold leading-tight">
          Mau lihat hasil karya tulis milik siswa maupun guru? Bisa! Kami menyediakan repositori karya SMK 1 Gunungputri.
        </h3>
        
        <button class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold flex items-center gap-3 text-sm md:text-base shadow-lg hover:shadow-2xl transition-all group">
          Cari Tahu Repository Karya 
          <span class="group-hover:translate-x-2 transition-transform">→</span>
        </button>
      </div>

      <!-- Bagian Slider Polaroid (Kanan) -->
      <div class="w-full lg:w-7/12 z-0">
        <swiper
          :modules="modules"
          :slides-per-view="2"
          :centered-slides="true"
          :loop="true"
          :space-between="30"
          :autoplay="{
            delay: 2500,
            disableOnInteraction: false,
          }"
          :breakpoints="{
            '768': {
              slidesPerView: 3,
              spaceBetween: 40
            }
          }"
          class="repo-swiper"
        >
          <swiper-slide v-for="(img, i) in bookCovers" :key="i" v-slot="{ isActive }">
            <!-- Container Polaroid -->
            <div 
              class="transition-all duration-700 ease-in-out transform"
              :class="isActive ? 'scale-110 rotate-0 opacity-100' : 'scale-75 -rotate-3 opacity-60'"
            >
              <div class="bg-white p-3 pb-10 shadow-2xl">
                <img 
                  :src="img" 
                  class="w-full h-48 md:h-80 object-cover border border-gray-100" 
                  alt="Karya Tulis"
                >
                <!-- Area Putih Bawah Polaroid -->
                <div class="mt-4 h-2 bg-gray-50 rounded-full w-3/4 mx-auto"></div>
              </div>
            </div>
          </swiper-slide>
        </swiper>
      </div>

    </div>
  </section>

    <!-- Artikel & Event -->
    <section class="max-w-7xl mx-auto px-4 py-12 font-sans">
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Artikel dan Event Kami</h2>
        <a href="#" class="bg-orange-500 text-white px-6 py-2 rounded-full flex items-center gap-2 hover:bg-orange-600 transition shadow-md">
          More <span class="text-xl">→</span>
        </a>
      </div>

      <div class="flex gap-4 mb-8">
        <button
          @click="activeTab = 'artikel'"
          :class="activeTab === 'artikel' ? 'border-orange-500 text-orange-500 font-bold shadow-sm' : 'border-gray-200 text-gray-400'"
          class="px-10 py-2 rounded-full border-2 transition-all duration-300 transform active:scale-95"
        >
          Artikel
        </button>
        <button
          @click="activeTab = 'event'"
          :class="activeTab === 'event' ? 'border-orange-500 text-orange-500 font-bold shadow-sm' : 'border-gray-200 text-gray-400'"
          class="px-10 py-2 rounded-full border-2 transition-all duration-300 transform active:scale-95"
        >
          Event
        </button>
      </div>

      <div class="relative group">
        <div
          ref="slider"
          class="flex gap-6 overflow-x-auto snap-x snap-mandatory no-scrollbar scroll-smooth pb-8"
          @scroll="handleScroll"
        >
          <div
            v-for="(item, index) in filteredItems"
            :key="index"
            class="min-w-[320px] md:min-w-[400px] bg-white rounded-2xl shadow-xl border border-gray-100 p-6 snap-start flex-shrink-0 hover:shadow-2xl transition-shadow"
          >
            <div class="relative mb-5 overflow-hidden rounded-xl border-2 border-orange-200">
              <img src="https://via.placeholder.com/400x240" class="w-full h-48 object-cover hover:scale-105 transition duration-500" alt="Artikel">
            </div>
            <span class="bg-orange-500 text-white text-[10px] px-4 py-1.5 rounded-full uppercase font-black tracking-wider">
              {{ item.category }}
            </span>
            <h3 class="mt-4 font-bold text-gray-900 leading-snug text-lg">
              {{ item.title }}
            </h3>
            <p class="mt-3 text-sm text-gray-500 line-clamp-3 leading-relaxed">
              {{ item.desc }}
            </p>
            <a href="#" class="inline-block mt-5 text-orange-500 font-extrabold text-sm hover:underline">
              Klik untuk baca >>
            </a>
          </div>
        </div>

        <div class="flex justify-center gap-3 mt-6">
          <button
            v-for="(_, i) in filteredItems"
            :key="i"
            @click="scrollToIndex(i)"
            class="w-3.5 h-3.5 rounded-full border-2 border-orange-500 transition-all duration-300"
            :class="currentIndex === i ? 'bg-orange-500 scale-110' : 'bg-transparent'"
          ></button>
        </div>
      </div>

      <!-- CTA Formulir -->
      <div class="mt-24 bg-orange-500 rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center justify-between relative overflow-hidden shadow-2xl">
        <div class="z-10 text-center md:text-left">
          <h2 class="text-3xl md:text-4xl font-black text-white mb-8 leading-tight">
            Punya pertanyaan dan saran<br class="hidden md:block"> untuk kami kedepannya?
          </h2>
          <a href="#" class="bg-white text-gray-800 px-10 py-4 rounded-full font-black flex items-center gap-4 w-fit mx-auto md:mx-0 hover:shadow-lg transition transform hover:-translate-y-1">
            BUAT FORMULIR
            <span class="bg-orange-500 text-white rounded-full w-7 h-7 flex items-center justify-center">→</span>
          </a>
        </div>

        <div class="mt-12 md:mt-0 relative group">
          <div class="bg-blue-300 w-56 h-40 rounded-2xl rotate-[-8deg] flex items-center justify-center shadow-2xl transform group-hover:rotate-0 transition duration-500">
            <div class="w-12 h-1.5 bg-blue-400 rounded-full"></div>
          </div>
          <span class="absolute -top-10 -right-4 text-yellow-300 text-6xl font-black opacity-90 drop-shadow-md">??</span>
        </div>
      </div>
    </section>

    <!-- Footer -->
        <FooterSection />


    <AuthModal :show="showAuthModal" @close="showAuthModal = false" />
  </div>
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import AuthModal from '@/Components/AuthModal.vue';
import FooterSection from '@/Components/FooterSection.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faYoutube } from '@fortawesome/free-brands-svg-icons';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay } from 'swiper/modules';

// Import CSS Swiper
import 'swiper/css';

const modules = [Autoplay];

// Data gambar mockup
const bookCovers = [
  '/image/ily.jpeg',
  '/image/ht.jpeg',
  '/image/nb.jpg',
  '/image/tk.jpg',
  '/image/sp.jpeg'
];

// Masukkan ikon yang akan dipakai
library.add(faYoutube);
const showAuthModal = ref(false);

const features = [
  { title: 'Scan Barcode', desc: 'Pinjam buku cepat dengan scan dari HP.' },
  { title: 'Notifikasi', desc: 'Dapatkan info ketersediaan buku real-time.' },
  { title: 'Sistem Poin', desc: 'Kumpulkan poin setiap kali membaca.' }
];

onMounted(() => {
  AOS.init({ duration: 1000, once: true });
});

const activeIndex = ref(0)

const reviewList = [
  { s: '⭐⭐⭐⭐⭐', t: '"Luar biasa, sangat inspiratif!"' },
  { s: '⭐⭐⭐⭐',   t: '"Wajib baca semua pelajar."' },
]

const dataRows = [
  { e: '📚', t: 'Matematika Diskrit', s: 'Dipinjam',     c: 'orange' },
  { e: '✅', t: 'Bumi — Tere Liye',   s: 'Dikembalikan', c: 'green'  },
  { e: '⏳', t: 'Clean Code',          s: '2 hari lagi',  c: 'warn'   },
]

const notifChips = ['📬 Buku tersedia!', '⏰ Jatuh tempo besok', '🎉 Event literasi baru']

const historyItems = [
  { d: '12 Mar 2025', t: 'Harry Potter & The Chamber' },
  { d: '5 Feb 2025',  t: 'Bumi — Tere Liye' },
  { d: '20 Jan 2025', t: 'Atomic Habits' },
]

const badges = ['🎖 Pembaca Aktif', '🏅 Tepat Waktu', '🎟 Event Access']

const fiturList = [
  { no: '01', title: 'Scan barcode buku',                  desc: 'Foto dan scan barcode yang ada di buku dengan kamera handphone untuk peminjaman buku secara cepat tanpa antre di meja petugas.' },
  { no: '02', title: 'Pendataan peminjaman & pengembalian', desc: 'Seluruh data peminjaman dan pengembalian buku tercatat otomatis dalam sistem, mudah dicek kapan saja dan di mana saja.' },
  { no: '03', title: 'Notifikasi buku tersedia',            desc: 'Tandai buku yang sedang tidak tersedia, dan kamu akan mendapat notifikasi segera setelah buku itu bisa dipinjam kembali.' },
  { no: '04', title: 'Riwayat & tenggat peminjaman',        desc: 'Pantau semua buku yang pernah kamu pinjam, lengkap dengan tanggal peminjaman dan tenggat pengembalian agar tidak terlambat.' },
  { no: '05', title: 'Sistem poin & event',                 desc: 'Dapatkan poin setiap kali meminjam buku tepat waktu. Tukarkan poin untuk reward menarik atau ikuti event literasi eksklusif.' },
  { no: '06', title: 'Forum review & repositori karya',     desc: 'Bagikan ulasanmu tentang buku yang sudah dibaca dan temukan repositori karya tulis ilmiah maupun kreatif dari seluruh siswa.' },
]

function select(i) { activeIndex.value = i }

const activeTab = ref('artikel');
const slider = ref(null);
const currentIndex = ref(0);

const data = {
  artikel: [
    { title: 'SMKN 1 Gunungputri rubuh, siswa diliburkan sementara waktu', category: 'Sekolah', desc: 'Is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard.' },
    { title: 'Update Kurikulum PPLG 2026', category: 'Kurikulum', desc: 'Is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been.' },
    { title: 'Lomba UI/UX antar SMK se-Bogor', category: 'Lomba', desc: 'Is simply dummy text of the printing and typesetting industry.' },
    { title: 'Kunjungan Industri ke Google Indonesia', category: 'Industri', desc: 'Siswa SMK melihat langsung ekosistem kerja profesional.' },
  ],
  event: [
    { title: 'Grand Opening RR.CO Waste Center', category: 'Event', desc: 'Peresmian pusat pengelolaan limbah digital daerah Gunungputri.' },
    { title: 'Webinar Flutter & Dart SMKN 1', category: 'Edukasi', desc: 'Belajar mobile development bersama expert.' },
  ]
};

const filteredItems = computed(() => data[activeTab.value]);

// Logika Scroll untuk indikator
const handleScroll = (e) => {
  const scrollLeft = e.target.scrollLeft;
  const cardWidth = 424; // Lebar kartu + gap
  currentIndex.value = Math.round(scrollLeft / cardWidth);
};

const scrollToIndex = (index) => {
  const cardWidth = 424;
  slider.value.scrollTo({ left: index * cardWidth, behavior: 'smooth' });
};

const staffList = [
  {
    name: 'Ahmad Fauzi',
    role: 'Kepala Perpustakaan',
    bio: 'Memimpin layanan perpustakaan dengan pengalaman lebih dari 10 tahun.',
    photo: '/image/j.jpeg'
  },
  {
    name: 'Siti Nurhaliza',
    role: 'Pustakawan Ahli',
    bio: 'Ahli dalam katalogisasi, referensi, dan pengembangan koleksi.',
    photo: '/image/o.jpeg'
  },
  {
    name: 'Budi Santoso',
    role: 'Teknisi Sistem',
    bio: 'Mengelola sistem informasi perpustakaan dan layanan digital.',
    photo: '/image/j.jpeg'
  },
  {
    name: 'Dewi Lestari',
    role: 'Layanan Sirkulasi',
    bio: 'Menjamin kelancaran peminjaman dan pengembalian buku.',
    photo: '/image/o.jpeg'
  }
];

// --- Carousel Profil ---

const activeProfileSlide = ref(0)
let profileInterval = null

const profileSlides = [
  {
    title: 'Sejarah Singkat',
    content: 'Perpustakaan SMKN 1 Gunungputri hadir sejak tahun 2005 untuk mendukung visi sekolah dalam mencetak generasi cerdas dan inovatif melalui ketersediaan literasi berkualitas.'
  },
  {
    title: 'Visi & Misi Kami',
    content: [
      'Pusat literasi digital SMK terbaik',
      'Akses koleksi fisik & digital 24/7',
      'Pengembangan minat bakat siswa',
      'Sistem peminjaman berbasis barcode'
    ]
  },
  {
    title: 'Tatanan Pengurus',
    content: [
      'Kepala: Ahmad Fauzi, M.Pd',
      'Pustakawan: Siti Nurhaliza',
      'Teknisi: Budi Santoso',
      'Sirkulasi: Dewi Lestari'
    ]
  }
]

const startAutoSlide = () => {
  stopAutoSlide()
  profileInterval = setInterval(() => {
    activeProfileSlide.value = (activeProfileSlide.value + 1) % profileSlides.length
  }, 7000) // 7 detik agar user sempat membaca
}

const stopAutoSlide = () => {
  if (profileInterval) clearInterval(profileInterval)
}

const goToSlide = (index) => {
  activeProfileSlide.value = index
  startAutoSlide() // Reset timer setelah klik manual
}

onMounted(() => {
  startAutoSlide()
})

onBeforeUnmount(() => {
  stopAutoSlide()
});

</script>

<style>
*, *::before, *::after { box-sizing: border-box; }

.sf {
  --bg:        #d47a3c;
  --surface:   #EA580C;
  --orange:    #FFFFFF;
  --orange-lo: rgba(255,255,255,.15);
  --orange-md: rgba(255,255,255,.35);
  --teal:      #FFE0B5;
  --green:     #D4E6B3;
  --warn:      #FFF0CC;
  --text:      #FFFFFF;
  --muted:     #FFF2E0;
  --line:      rgba(255,255,255,.2);
}

.sf {
  margin: 0;
  padding: 60px 16px 120px;
  position: relative;
  font-family: 'Syne', sans-serif;
  overflow: hidden;
  color: var(--text);
}

@media (min-width: 768px) {
  .sf {
    padding: 80px 48px 140px;
  }
}

.sf__bg-glow {
  position: absolute; border-radius: 50%;
  filter: blur(100px); pointer-events: none; z-index: 0;
}
.sf__bg-glow--a {
  width: 520px; height: 520px;
  top: -160px; left: -80px;
  background: radial-gradient(circle, rgba(255,255,255,.25) 0%, transparent 70%);
}
.sf__bg-glow--b {
  width: 380px; height: 380px;
  bottom: -100px; right: 5%;
  background: radial-gradient(circle, rgba(255,245,220,.2) 0%, transparent 70%);
}

.sf__bg-grid {
  position: absolute; inset: 0; z-index: 0; pointer-events: none;
}

.sf__inner {
  position: relative; z-index: 1;
  max-width: 1280px; margin: 0 auto;
  padding: 0 16px;
}

@media (min-width: 768px) {
  .sf__inner {
    padding: 0 24px;
  }
}

.sf__head {
  margin-bottom: 60px;
}

@media (min-width: 768px) {
  .sf__head {
    margin-bottom: 80px;
  }
}

.sf__kicker {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 10px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase;
  color: var(--text);
  margin-bottom: 28px;
  opacity: 0.85;
}

.sf__kicker-pip {
  display: block; width: 20px; height: 1px; background: var(--text);
}

.sf__h2 {
  font-family: 'Fraunces', serif;
  font-size: clamp(36px, 5vw, 62px);
  font-weight: 300;
  line-height: 1.12;
  color: var(--text);
  margin-bottom: 20px;
}
.sf__h2 em {
  font-style: italic;
  color: #FFFFFF;
  font-weight: 600;
  text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.sf__sub {
  font-size: 15px;
  color: rgba(255,255,255,0.85);
  line-height: 1.8;
  font-weight: 400;
  max-width: 480px;
}

.sf__body {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0 48px;
  align-items: start;
}

@media (max-width: 1024px) {
  .sf__body {
    gap: 0 32px;
  }
}

.sf__vis-col {
  position: sticky; top: 48px;
  height: 400px;
  display: flex; align-items: center; justify-content: center;
}

.sf__vis {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
}

.vis-enter-active { transition: opacity .4s ease, transform .4s ease; }
.vis-leave-active { transition: opacity .2s ease, transform .2s ease; }
.vis-enter-from   { opacity: 0; transform: translateY(16px) scale(.98); }
.vis-leave-to     { opacity: 0; transform: translateY(-10px) scale(.98); }

.v { display: flex; flex-direction: column; align-items: center; gap: 24px; width: 100%; }

.v--scan {

}

.v__scan-wrap { position: relative; display: inline-flex; }
.v__scan-frame {
  width: 160px; height: 160px;
  display: flex; align-items: flex-end; justify-content: center;
  gap: 3px;
  padding: 16px 20px 20px;
  position: relative;
}
.v__scan-laser {
  position: absolute; left: 20px; right: 20px; height: 2px;
  background: linear-gradient(90deg, transparent, var(--text), transparent);
  box-shadow: 0 0 12px var(--text);
  animation: laserMove 2s ease-in-out infinite;
  border-radius: 1px;
}
@keyframes laserMove {
  0%,100% { top: 20px; opacity: 0; }
  15%     { opacity: 1; }
  85%     { opacity: 1; }
  99%     { top: 130px; }
}
.v__sc {
  position: absolute; width: 16px; height: 16px;
  border-color: var(--text); border-style: solid;
}
.v__sc--tl { top: 0; left: 0;  border-width: 2px 0 0 2px; }
.v__sc--tr { top: 0; right: 0; border-width: 2px 2px 0 0; }
.v__sc--bl { bottom: 0; left: 0;  border-width: 0 0 2px 2px; }
.v__sc--br { bottom: 0; right: 0; border-width: 0 2px 2px 0; }
.v__bars { display: flex; align-items: flex-end; gap: 3px; height: 80px; }
.v__bar { width: 4px; background: var(--text); border-radius: 1px; }
.v__scan-ping {
  position: absolute; inset: -8px; border-radius: 4px;
  border: 1px solid var(--orange-md);
  animation: pingPulse 2.4s ease-out infinite;
}
@keyframes pingPulse {
  0%   { transform: scale(1); opacity: .6; }
  70%  { transform: scale(1.06); opacity: 0; }
  100% { transform: scale(1.06); opacity: 0; }
}
.v__label {
  font-size: 12px; color: var(--muted); letter-spacing: .4px; text-align: center;
}

.v--data { gap: 0; }
.v__data-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid var(--line);
  width: 100%; max-width: 320px;
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
}
.v__data-row:first-child { border-top: 1px solid var(--line); }
.v__data-e { font-size: 20px; }
.v__data-t { flex: 1; font-size: 13px; color: var(--text); font-weight: 600; }
.v__data-s {
  font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
  padding: 4px 10px; border-radius: 100px;
}
.v__data-s--orange { background: rgba(255,255,255,.2);  color: #FFFFFF; }
.v__data-s--green  { background: rgba(255,255,255,.2);  color: #FFFFFF;  }
.v__data-s--warn   { background: rgba(255,255,255,.2);  color: #FFFFFF;   }

.v--notif { gap: 20px; }
.v__bell-wrap { position: relative; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; }
.v__bell { font-size: 44px; animation: bellRock 2.8s ease infinite; display: block; position: relative; z-index: 1; }
@keyframes bellRock {
  0%,100%    { transform: rotate(0); }
  8%, 24%    { transform: rotate(-14deg); }
  16%, 32%   { transform: rotate(14deg); }
  40%        { transform: rotate(0); }
}
.v__bell-ring {
  position: absolute; inset: 0; border-radius: 50%;
  border: 1.5px solid var(--orange-md);
  animation: ringExpand 2.8s ease infinite;
}
@keyframes ringExpand {
  0%   { transform: scale(.8); opacity: 0; }
  40%  { transform: scale(1.5); opacity: .6; }
  80%  { transform: scale(2); opacity: 0; }
  100% { opacity: 0; }
}
.v__chips { display: flex; flex-direction: column; gap: 10px; align-items: center; }
.v__chip {
  padding: 8px 20px; border-radius: 100px;
  font-size: 12px; font-weight: 600; color: var(--text);
  border: 1px solid var(--line);
  background: rgba(255,255,255,.1);
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
  white-space: nowrap;
}

.v--history {}
.v__tl { position: relative; width: 100%; max-width: 280px; padding-left: 24px; }
.v__tl-track {
  position: absolute; left: 3px; top: 8px; bottom: 8px; width: 1px;
  background: linear-gradient(to bottom, var(--text), var(--teal));
}
.v__tl-item {
  display: flex; gap: 16px; padding: 10px 0;
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
}
.v__tl-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--text); margin-top: 4px; flex-shrink: 0;
  position: relative; left: -27px; margin-right: -8px;
  box-shadow: 0 0 0 3px var(--orange-lo);
}
.v__tl-text { display: flex; flex-direction: column; gap: 2px; }
.v__tl-date  { font-size: 10px; color: var(--muted); letter-spacing: .5px; }
.v__tl-title { font-size: 13px; color: var(--text); font-weight: 600; }

.v--points { gap: 24px; }
.v__pts-ring {
  width: 160px; height: 160px;
  border-radius: 50%;
  border: 1px solid var(--orange-md);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  position: relative;
  box-shadow: 0 0 40px var(--orange-lo), inset 0 0 40px rgba(255,255,255,.04);
}
.v__pts-ring::before {
  content: '';
  position: absolute; inset: 8px; border-radius: 50%;
  border: 1px dashed rgba(255,255,255,.3);
}
.v__pts-num {
  font-family: 'Fraunces', serif;
  font-size: 56px; font-weight: 600; color: var(--text);
  line-height: 1;
}
.v__pts-unit { font-size: 12px; color: var(--muted); font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-top: 4px; }
.v__badge-row { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
.v__badge {
  font-size: 11px; font-weight: 600; color: var(--muted);
  padding: 5px 14px; border-radius: 100px;
  border: 1px solid var(--line);
}

.v--forum { gap: 16px; align-items: flex-start; padding-left: 8px; }
.v__review {
  max-width: 300px; width: 100%;
  padding-left: 16px;
  border-left: 2px solid var(--text);
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
}
.v__review-stars { font-size: 13px; margin-bottom: 4px; }
.v__review-text  { font-size: 13px; color: var(--muted); font-style: italic; }
.v__repo {
  font-size: 11px; color: var(--teal); font-weight: 700;
  padding: 6px 16px; border: 1px solid rgba(255,255,255,.35); border-radius: 100px;
  letter-spacing: .5px;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

.sf__list {
  list-style: none;
  border-top: 1px solid var(--line);
}

.sf__item {
  border-bottom: 1px solid var(--line);
  cursor: pointer;
  position: relative;
  transition: background-color 0.3s ease;
  padding: 0;
}

.sf__item:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.sf__item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 2px;
  background: var(--text);
  transform: scaleY(0);
  transform-origin: bottom;
  transition: transform 0.35s ease;
}

.sf__item.is-active::before {
  transform: scaleY(1);
}

.sf__item-head {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 0;
  user-select: none;
  position: relative;
  z-index: 1;
}

.sf__no {
  font-family: 'Fraunces', serif;
  font-size: 13px;
  font-style: italic;
  font-weight: 300;
  color: var(--muted);
  width: 30px;
  flex-shrink: 0;
  margin-right: 12px;
  text-align: right;
  transition: color 0.25s, font-weight 0.25s;
}

.sf__title {
  flex: 1;
  font-size: 15px;
  font-weight: 600;
  color: var(--text);
  transition: color 0.25s, font-weight 0.25s;
  letter-spacing: -0.01em;
}

.sf__arrow {
  color: var(--muted);
  transition: transform 0.3s ease, color 0.25s;
  display: flex;
  align-items: center;
}

.sf__item:hover .sf__title {
  color: #FFFFFF;
  text-decoration: underline;
}

.sf__item:hover .sf__arrow {
  transform: translateX(5px);
  color: var(--text);
}

.sf__item.is-active .sf__no {
  color: var(--text);
  font-weight: 600;
}

.sf__item.is-active .sf__title {
  color: var(--text);
  font-weight: 700;
}

.sf__item.is-active .sf__arrow {
  transform: translateX(6px);
  color: var(--text);
}

.sf__desc {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.5s ease, padding 0.4s ease, opacity 0.3s ease;
  opacity: 0;
  padding: 0 0 0 62px;
}

.sf__item.is-active .sf__desc {
  max-height: 150px;
  opacity: 1;
  padding: 0 20px 24px 62px;
}

.sf__desc p {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.8;
  margin: 0;
  transform: translateY(8px);
  transition: transform 0.4s ease 0.1s;
}

.sf__item.is-active .sf__desc p {
  transform: translateY(0);
}

@media (max-width: 768px) {
  .sf__body {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}

.repo-swiper {
  width: 100%;
  padding: 50px 0;
  overflow: visible !important;
}

.swiper-slide {
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 0.5s ease;
}

/* Membuat efek bayangan polaroid lebih nyata */
.bg-white {
  backface-visibility: hidden;
  -webkit-font-smoothing: subpixel-antialiased;
}

/* Pastikan section tidak memiliki padding horizontal terlalu sempit di mobile */
@media (max-width: 640px) {
  .text-2xl {
    font-size: 1.5rem;
    line-height: 1.4;
  }
}
</style>