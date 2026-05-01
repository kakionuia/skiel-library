<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-[100] transition-all duration-500 px-8 py-4 flex items-center justify-between',
      isScrolled 
        ? 'bg-orange-600 shadow-2xl py-3 border-b border-orange-400' 
        : 'bg-transparent py-6'
    ]"
  >
    <div class="flex items-center gap-3 group cursor-pointer">
      <div class="bg-white p-1.5 rounded-xl shadow-sm group-hover:scale-110 transition-transform duration-300">
        <img src="/logo-smk.png" alt="Logo" class="w-8 h-8">
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

    <div :class="['hidden md:flex gap-10 font-bold text-sm transition-colors duration-500', isScrolled ? 'text-white' : 'text-gray-700']">
      <a v-for="item in menuItems" :key="item" href="#" class="relative group">
        {{ item }}
        <span :class="['absolute -bottom-1 left-0 w-0 h-0.5 transition-all duration-300 group-hover:w-full', isScrolled ? 'bg-white' : 'bg-orange-500']"></span>
      </a>
    </div>

    <div class="flex items-center">
      <Link 
        v-if="$page.props.auth.user"
        :href="getDashboardRoute()"
        :class="[
          'flex items-center gap-3 px-5 py-2 rounded-full font-bold transition-all duration-500 border-2',
          isScrolled 
            ? 'bg-white text-orange-600 border-white hover:bg-orange-50' 
            : 'bg-orange-500 text-white border-orange-500 hover:bg-orange-600 shadow-lg shadow-orange-200'
        ]"
      >
        <span class="text-xs tracking-wide uppercase">Dashboard</span>
        <div :class="['w-7 h-7 rounded-full flex items-center justify-center overflow-hidden border', isScrolled ? 'border-orange-100' : 'border-white/30']">
            <span class="text-[10px]">{{ $page.props.auth.user.name.charAt(0) }}</span>
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

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const isScrolled = ref(false);
const menuItems = ['Beranda', 'Fitur', 'Layanan', 'Tentang Kami'];
const page = usePage();

const getDashboardRoute = () => {
  const user = page.props.auth.user;
  if (!user) return route('dashboard');
  
  if (user.role === 'admin') {
    return route('admin.dashboard');
  } else if (user.role === 'petugas') {
    return route('petugas.dashboard');
  }
  
  return route('dashboard');
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