<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-100 flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-lg">
      <!-- Error Container -->
      <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-orange-100">
        
        <!-- Error Icon -->
        <div class="flex justify-center mb-8">
          <div class="relative">
            <div class="absolute inset-0 bg-orange-500/20 blur-2xl rounded-full"></div>
            <div class="relative w-24 h-24 flex items-center justify-center bg-orange-100 rounded-full">
              <svg v-if="icon === 'warning'" class="w-12 h-12 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              <svg v-else-if="icon === 'error'" class="w-12 h-12 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 15.572 8.026a.75.75 0 10-1.08.308A4.5 4.5 0 1113.949 13.5a.75.75 0 10-1.472.29z" clip-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-12 h-12 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Error Code -->
        <div class="text-center mb-4">
          <h1 class="text-5xl md:text-6xl font-black bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">
            {{ code }}
          </h1>
        </div>

        <!-- Error Title -->
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center mb-3">
          {{ title }}
        </h2>

        <!-- Error Message -->
        <p class="text-slate-600 text-center mb-8 leading-relaxed">
          {{ message }}
        </p>

        <!-- Error Details (if provided) -->
        <div v-if="details" class="bg-orange-50 border border-orange-200 rounded-2xl p-4 mb-8">
          <p class="text-sm text-slate-700 font-mono break-words">{{ details }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
          <button 
            @click="goBack"
            class="flex-1 px-6 py-3 bg-white border-2 border-orange-500 text-orange-600 rounded-xl font-bold hover:bg-orange-50 transition-colors duration-200"
          >
            ← Kembali
          </button>
          <Link 
            href="/"
            class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-orange-500/30 transition-all duration-200 text-center"
          >
            🏠 Beranda
          </Link>
        </div>

        <!-- Help Text -->
        <div class="mt-8 pt-8 border-t border-slate-200 text-center">
          <p class="text-sm text-slate-500 mb-3">Butuh bantuan?</p>
          <a href="mailto:support@skiel.local" class="text-orange-600 hover:text-orange-700 font-semibold text-sm">
            Hubungi Support →
          </a>
        </div>
      </div>

      <!-- Background Decorations -->
      <div class="absolute top-0 left-0 w-64 h-64 bg-orange-200/20 rounded-full blur-3xl -z-10"></div>
      <div class="absolute bottom-0 right-0 w-64 h-64 bg-orange-300/20 rounded-full blur-3xl -z-10"></div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  code: {
    type: [String, Number],
    default: '500'
  },
  title: {
    type: String,
    default: 'Terjadi Kesalahan'
  },
  message: {
    type: String,
    default: 'Maaf, sesuatu berjalan tidak sesuai rencana. Tim kami telah diberitahu dan sedang menangani masalah ini.'
  },
  details: {
    type: String,
    default: null
  },
  icon: {
    type: String,
    enum: ['error', 'warning', 'info'],
    default: 'error'
  }
});

const goBack = () => {
  window.history.back();
};
</script>
