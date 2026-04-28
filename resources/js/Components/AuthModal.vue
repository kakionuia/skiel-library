<template>
  <Transition name="fade">
    <div v-if="show" class="fixed inset-0 z-[110] flex items-center justify-center px-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('close')"></div>
      
      <div class="relative bg-white w-full max-w-md rounded-[32px] overflow-hidden shadow-2xl transform transition-all">
        <div class="p-8">
          <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-black text-gray-900">{{ isLogin ? 'Masuk ke Akun' : 'Daftar Akun Baru' }}</h2>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div v-if="!isLogin" class="space-y-1">
              <label class="text-sm font-bold text-gray-700 ml-1">Nama Lengkap</label>
              <input type="text" placeholder="Aurel Filza" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all">
            </div>

            <div class="space-y-1">
              <label class="text-sm font-bold text-gray-700 ml-1">Email Sekolah</label>
              <input type="email" placeholder="example@smkn1gputri.sch.id" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all">
            </div>

            <div class="space-y-1">
              <label class="text-sm font-bold text-gray-700 ml-1">Password</label>
              <input type="password" placeholder="••••••••" class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-200 transition-all active:scale-95">
              {{ isLogin ? 'Masuk Sekarang' : 'Buat Akun' }}
            </button>
          </form>

          <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm">
              {{ isLogin ? 'Belum punya akun?' : 'Sudah punya akun?' }}
              <button @click="isLogin = !isLogin" class="text-orange-600 font-bold hover:underline ml-1">
                {{ isLogin ? 'Daftar di sini' : 'Masuk di sini' }}
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';

defineProps(['show']);
defineEmits(['close']);

const isLogin = ref(true);

const handleSubmit = () => {
  // Nantinya di sini panggil API Laravel 12 kamu
  alert(isLogin.value ? 'Proses Login...' : 'Proses Pendaftaran...');
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>