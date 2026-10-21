<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const isScrolled = ref(false);
const page = usePage();

// Ambil user dan profile photo
const user = computed(() => page.props.auth.user);
const profilePhotoUrl = computed(() => user.value?.profile_photo_url || null);

const getDashboardRoute = () => {
  const currentUser = user.value;
  if (!currentUser) return route('dashboard');
  
  if (currentUser.role === 'admin') {
    return route('admin.dashboard');
  } else if (currentUser.role === 'petugas') {
    return route('petugas.dashboard');
  }
  
  return route('dashboard');
};

const navigateTo = (section) => {
  const isHomePage = window.location.pathname === '/';
  
  if (section === 'home') {
    if (isHomePage) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      router.visit(route('home'));
    }
  } 
  else if (section === 'features') {
    if (isHomePage) {
      const featuresSection = document.getElementById('fitur');
      if (featuresSection) {
        featuresSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    } else {
      router.visit(route('home') + '#fitur');
    }
  }
  else if (section === 'about') {
    if (isHomePage) {
      const aboutSection = document.querySelector('[data-aos="fade-up"]');
      if (aboutSection) {
        aboutSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else {
        const profileText = Array.from(document.querySelectorAll('span')).find(
          el => el.textContent.includes('Profil Perpustakaan')
        );
        if (profileText) {
          profileText.closest('section')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    } else {
      router.visit(route('home'), {
        onSuccess: () => {
          setTimeout(() => {
            const aboutSection = document.querySelector('[data-aos="fade-up"]');
            if (aboutSection) {
              aboutSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          }, 500);
        }
      });
    }
  }
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

defineEmits(['openAuth']);
</script>

<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-[100] transition-all duration-500 px-8 py-4 flex items-center justify-between',
      isScrolled 
        ? 'bg-orange-500 shadow-xl shadow-neutral-350 py-3 border-b border-orange-400' 
        : 'bg-transparent py-6'
    ]"
  >
    <!-- Logo -->
    <div class="flex items-center gap-3 group cursor-pointer" @click="navigateTo('home')">
      <div class="bg-white p-1.5 rounded-full shadow-sm group-hover:scale-110 transition-transform duration-300">
        <img :src="$page.props.logoUrl" alt="Logo" class="h-8 w-8 md:h-10 md:w-10 object-contain" />
      </div>
      <div class="flex flex-col">
        <h1 :class="['font-black leading-none transition-colors duration-500', isScrolled ? 'text-white' : 'text-orange-600']">
          SKIE-Library
        </h1>
        <p :class="['text-[9px] uppercase tracking-tighter transition-colors duration-500', isScrolled ? 'text-orange-100' : 'text-gray-500']">
          of SMKN 1 Gunungputri
        </p>
      </div>
    </div>

    <!-- Desktop Menu -->
    <div :class="['hidden md:flex gap-10 font-bold text-sm transition-colors duration-500', isScrolled ? 'text-white' : 'text-gray-700']">
      <!-- Beranda -->
      <a 
        @click.prevent="navigateTo('home')" 
        href="#" 
        class="relative group cursor-pointer"
      >
        Beranda
        <span :class="['absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full', isScrolled ? 'bg-white' : 'bg-orange-500']"></span>
      </a>
      
      <!-- Fitur -->
      <a 
        @click.prevent="navigateTo('features')" 
        href="#fitur" 
        class="relative group cursor-pointer"
      >
        Fitur
        <span :class="['absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full', isScrolled ? 'bg-white' : 'bg-orange-500']"></span>
      </a>
      
      <!-- Layanan (Dropdown) -->
      <div class="relative group cursor-pointer">
        <span class="flex items-center gap-1">
          Layanan
          <svg :class="['w-3 h-3 transition-transform duration-300 group-hover:rotate-180', isScrolled ? 'text-white' : 'text-gray-700']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
          </svg>
        </span>
        <span :class="['absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full', isScrolled ? 'bg-white' : 'bg-orange-500']"></span>
        
        <!-- Dropdown Menu -->
        <div class="absolute top-full left-0 mt-3 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50">
          <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2">
            <Link 
              :href="route('articles.index')"
              class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
              <div>
                <span class="font-semibold text-sm">Artikel & Berita</span>
                <p class="text-xs text-gray-400">Baca artikel terbaru</p>
              </div>
            </Link>
            
            <a 
              href="#" 
              @click.prevent="navigateTo('home')"
              class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <div>
                <span class="font-semibold text-sm">Koleksi Buku</span>
                <p class="text-xs text-gray-400">Jelajahi koleksi kami</p>
              </div>
            </a>
            
            <Link 
              :href="route('articles.index', { category: 'event' })"
              class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <div>
                <span class="font-semibold text-sm">Event</span>
                <p class="text-xs text-gray-400">Event literasi seru</p>
              </div>
            </Link>
          </div>
        </div>
      </div>
      
      <!-- Tentang Kami -->
      <a 
        @click.prevent="navigateTo('about')" 
        href="#tentang-kami" 
        class="relative group cursor-pointer"
      >
        Tentang Kami
        <span :class="['absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full', isScrolled ? 'bg-white' : 'bg-orange-500']"></span>
      </a>
    </div>

    <!-- Right Side - PERBAIKAN: Tampilkan foto profil di navbar landing -->
    <div class="flex items-center">
      <Link 
        v-if="user"
        :href="getDashboardRoute()"
        :class="[
          'flex items-center gap-2 px-4 py-2 md:gap-3 md:px-5 md:py-2 rounded-full font-bold transition-all duration-500 border-2',
          isScrolled 
            ? 'bg-white text-orange-600 border-white hover:bg-orange-50' 
            : 'bg-orange-500 text-white border-orange-500 hover:bg-orange-600 shadow-lg shadow-orange-200'
        ]"
      >
        <span class="text-xs tracking-wide uppercase">Dashboard</span>
        <!-- Avatar dengan Foto Profil -->
        <div :class="['w-8 h-8 rounded-full flex items-center justify-center overflow-hidden border-2', isScrolled ? 'border-orange-100' : 'border-white/30']">
          <img 
            v-if="profilePhotoUrl" 
            :src="profilePhotoUrl" 
            alt="Profile" 
            class="w-full h-full object-cover"
          >
          <span v-else class="text-xs font-bold">
            {{ user.name.charAt(0).toUpperCase() }}
          </span>
        </div>
      </Link>

      <button 
        v-else
        @click="$emit('openAuth')"
        :class="[
          'px-8 py-2.5 rounded-full font-bold transition-all duration-500 transform hover:scale-105 active:scale-95 shadow-lg',
          isScrolled 
            ? 'bg-white text-orange-600 hover:bg-orange-50' 
            : 'bg-orange-500 text-white hover:bg-orange-600 shadow-orange-200'
        ]"
      >
        Masuk/Daftar
      </button>
    </div>
  </nav>
</template>
