<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-[100] transition-all duration-500 px-8 py-4 flex items-center justify-between',
      isScrolled 
        ? 'bg-orange-500 shadow-2xl py-3 border-b border-orange-400' 
        : 'bg-transparent py-6'
    ]"
  >
    <div class="flex items-center gap-3">
      <div class="bg-white p-1.5 rounded-xl shadow-sm">
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

    <button 
      @click="$emit('openAuth')"
      :class="[
        'px-7 py-2.5 rounded-full font-bold transition-all duration-500 transform hover:scale-105 active:scale-95 shadow-lg',
        isScrolled ? 'bg-white text-orange-600' : 'bg-orange-500 text-white'
      ]"
    >
      Masuk/Daftar
    </button>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);
const menuItems = ['Beranda', 'Fitur', 'Layanan', 'Tentang Kami'];

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