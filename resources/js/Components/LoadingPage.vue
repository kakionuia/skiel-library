<template>
  <div class="fixed inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm z-50">
    <div class="text-center">
      <!-- Spinner Animation -->
      <div class="relative w-20 h-20 mx-auto mb-6">
        <div class="absolute inset-0 border-4 border-orange-100 rounded-full animate-spin border-t-orange-600"></div>
        <div class="absolute inset-2 border-4 border-transparent rounded-full animate-spin border-r-orange-500" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
      </div>
      
      <!-- Loading Text -->
      <h2 class="text-2xl font-bold text-slate-900 mb-2">Memuat...</h2>
      <p class="text-slate-600 mb-4">{{ message }}</p>
      
      <!-- Progress Bar -->
      <div class="w-64 h-1 bg-orange-100 rounded-full overflow-hidden">
        <div class="h-full bg-gradient-to-r from-orange-500 to-orange-600 rounded-full" style="animation: slideIn 1.5s ease-in-out infinite;"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Silakan tunggu sebentar...'
  }
});
</script>

<style scoped>
@keyframes slideIn {
  0% {
    width: 0%;
  }
  50% {
    width: 100%;
  }
  100% {
    width: 0%;
  }
}
</style>
