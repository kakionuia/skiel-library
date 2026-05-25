<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
  feedback_type: 'suggestion', // 'suggestion' atau 'complaint'
  subject: '',
  message: '',
});

const isLoading = ref(false);

const handleSubmit = async () => {
  isLoading.value = true;
  
  form.post(route('feedback.store'), {
    preserveScroll: false,
    onError: () => {
      isLoading.value = false;
    },
  });
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 font-sans">
    <Head title="Formulir Feedback" />
    
    <!-- Navbar Sederhana -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-slate-200">
      <div class="max-w-2xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between">
        <Link :href="route('home')" class="flex items-center gap-3 group">
          <img src="/image/logo-smk.png" alt="Logo" class="h-8 w-auto object-contain" />
          <div class="text-left">
            <h1 class="text-sm font-bold text-slate-900">SKIE-Library</h1>
            <p class="text-[10px] text-slate-500">Feedback Form</p>
          </div>
        </Link>
        <Link :href="route('home')" class="text-slate-600 hover:text-orange-600 transition font-medium text-sm">Kembali</Link>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-2xl mx-auto px-4 md:px-6 py-12 md:py-20">
      
      <!-- Header -->
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-3">Sampaikan Feedback Anda</h2>
        <p class="text-slate-600 max-w-xl mx-auto text-sm">
          Kami menghargai setiap masukan, saran, dan keluhan Anda untuk meningkatkan kualitas layanan SKIE-Library.
        </p>
      </div>

      <!-- Form -->
      <form v-if="true" @submit.prevent="handleSubmit" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-8 md:p-10 space-y-6">
        
        <!-- Feedback Type Selection -->
        <div>
          <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-4">Tipe Feedback</label>
          <div class="grid grid-cols-2 gap-4">
            <!-- Suggestion Option -->
            <button
              type="button"
              @click="form.feedback_type = 'suggestion'"
              :class="[
                'p-4 rounded-xl border transition-all duration-200 cursor-pointer text-left',
                form.feedback_type === 'suggestion'
                  ? 'border-orange-600 bg-orange-50/50'
                  : 'border-slate-200 bg-white hover:border-slate-300'
              ]"
            >
              <div class="flex items-center gap-3">
                <div :class="[
                  'w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0',
                  form.feedback_type === 'suggestion'
                    ? 'border-orange-600 bg-orange-600'
                    : 'border-slate-300 bg-white'
                ]">
                  <div v-if="form.feedback_type === 'suggestion'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                </div>
                <div>
                  <div class="font-bold text-slate-900 text-sm">Saran</div>
                  <div class="text-[10px] text-slate-500">Ide untuk peningkatan</div>
                </div>
              </div>
            </button>

            <!-- Complaint Option -->
            <button
              type="button"
              @click="form.feedback_type = 'complaint'"
              :class="[
                'p-4 rounded-xl border transition-all duration-200 cursor-pointer text-left',
                form.feedback_type === 'complaint'
                  ? 'border-orange-600 bg-orange-50/50'
                  : 'border-slate-200 bg-white hover:border-slate-300'
              ]"
            >
              <div class="flex items-center gap-3">
                <div :class="[
                  'w-4 h-4 rounded-full border flex items-center justify-center flex-shrink-0',
                  form.feedback_type === 'complaint'
                    ? 'border-orange-600 bg-orange-600'
                    : 'border-slate-300 bg-white'
                ]">
                  <div v-if="form.feedback_type === 'complaint'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                </div>
                <div>
                  <div class="font-bold text-slate-900 text-sm">Keluhan</div>
                  <div class="text-[10px] text-slate-500">Masalah operasional</div>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- Subject -->
        <div>
          <InputLabel for="subject" :value="`Subjek ${form.feedback_type === 'complaint' ? 'Keluhan' : 'Saran'}`" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider" />
          <input 
            id="subject"
            v-model="form.subject"
            type="text"
            placeholder="Ringkas topik feedback Anda dalam satu kalimat"
            required
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-orange-600 focus:ring-1 focus:ring-orange-600 transition-all outline-none placeholder:text-slate-400"
            :class="form.errors.subject ? 'border-slate-400' : ''"
          />
          <InputError :message="form.errors.subject" class="mt-1" />
        </div>

        <!-- Message -->
        <div>
          <InputLabel for="message" value="Pesan Lengkap" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider" />
          <textarea 
            id="message"
            v-model="form.message"
            placeholder="Jelaskan detail feedback Anda dengan jelas dan spesifik..."
            required
            rows="8"
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-orange-600 focus:ring-1 focus:ring-orange-600 transition-all outline-none placeholder:text-slate-400 resize-none"
            :class="form.errors.message ? 'border-slate-400' : ''"
          />
          <InputError :message="form.errors.message" class="mt-1" />
          <p class="text-[10px] text-slate-500 mt-2">{{ form.message.length }}/5000 karakter</p>
        </div>

        <!-- Info Box -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
          <div class="flex gap-3">
            <svg class="w-5 h-5 text-slate-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-slate-700">
              <p class="font-bold text-slate-900">Catatan Penting</p>
              <p class="text-xs mt-1 text-slate-500">Feedback Anda akan ditinjau oleh tim admin. Pastikan data Anda sudah login agar kami bisa menghubungi kembali jika diperlukan.</p>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-3 pt-2">
          <Link 
            :href="route('home')"
            class="flex-1 text-center border border-slate-300 text-slate-700 px-6 py-3 rounded-xl font-bold hover:bg-slate-50 transition-all text-sm"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="isLoading || !form.subject || !form.message"
            class="flex-1 bg-orange-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-orange-600/10 hover:bg-orange-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm"
          >
            <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isLoading ? 'Mengirim...' : 'Kirim Feedback' }}</span>
          </button>
        </div>
      </form>

      <!-- Back Home Link -->
      <div class="mt-8 text-center">
        <Link 
          :href="route('home')"
          class="text-orange-600 hover:text-orange-700 font-semibold text-sm"
        >
          Kembali ke Beranda
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
textarea::-webkit-scrollbar {
  width: 6px;
}

textarea::-webkit-scrollbar-track {
  background: transparent;
}

textarea::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>