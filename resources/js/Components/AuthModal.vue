<template>
  <Transition name="modal">
    <div v-if="show" class="fixed inset-0 z-[120] flex items-center justify-center p-6">
      <div class="absolute inset-0 bg-gray-950/30 backdrop-blur-md" @click="handleClose"></div>
      
      <div class="relative bg-white w-full max-w-[440px] max-h-[92vh] rounded-[32px] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden transform transition-all border border-gray-100 flex flex-col">
        
        <div class="px-10 py-12 overflow-y-auto custom-scroll">
          <div class="flex justify-center mb-8">
            <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-200">
              <img src="/image/logo-smk.png" class="w-7 h-7" alt="Logo">
            </div>
          </div>

          <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
              {{ isLogin ? 'Selamat Datang Kembali' : 'Bergabung dengan SKIE-Library' }}
            </h2>
            <p class="text-gray-400 mt-1.5 text-sm font-medium">
              {{ isLogin ? 'Silakan masuk ke akun perpustakaan Anda' : 'Buat akun untuk akses penuh layanan digital' }}
            </p>
          </div>

          <Transition name="fade">
            <div v-if="formError" class="mb-6 px-4 py-3 bg-red-50 border border-red-100 rounded-xl">
              <p class="text-[11px] font-bold text-red-600 uppercase tracking-widest text-center">{{ formError }}</p>
            </div>
          </Transition>

          <form @submit.prevent="handleSubmit" class="space-y-5 overflow-auto">
            <div v-if="!isLogin" class="space-y-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1">Nama Lengkap</label>
              <input v-model="form.name" type="text" placeholder="Nama Lengkap Anda" class="auth-input" :disabled="isSubmitting">
              <p v-if="errors.name" class="error-msg">{{ errors.name }}</p>
            </div>

            <div class="space-y-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1">NISN</label>
              <input v-model="form.nisn" type="text" placeholder="ex: 2425*******" class="auth-input" :disabled="isSubmitting">
              <p v-if="errors.nisn" class="error-msg">{{ errors.nisn }}</p>
            </div>

            <!-- KELAS & JURUSAN (hanya saat daftar) -->
            <div v-if="!isLogin" class="space-y-2">
              <div class="flex gap-3">
                <!-- Pilih Kelas -->
                <div class="flex-1">
                  <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1 mb-1">Kelas</label>
                  <select v-model="form.kelas" class="auth-input" :disabled="isSubmitting">
                    <option value="" disabled selected hidden>Pilih Kelas</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                  </select>
                  <p v-if="errors.kelas" class="error-msg">{{ errors.kelas }}</p>
                </div>

                <!-- Pilih Jurusan -->
                <div class="flex-[2]">
                  <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1 mb-1">Jurusan</label>
                  <select v-model="form.jurusan" class="auth-input" :disabled="isSubmitting">
                    <option value="" disabled selected hidden>Pilih Jurusan</option>
                    <option value="Elektro 1">Elektro 1</option>
                    <option value="Elektro 2">Elektro 2</option>
                    <option value="Elektro 3">Elektro 3</option>
                    <option value="Elektro 4">Elektro 4</option>
                    <option value="Mesin 1">Mesin 1</option>
                    <option value="Mesin 2">Mesin 2</option>
                    <option value="Mesin 3">Mesin 3</option>
                    <option value="Kimia 1">Kimia 1</option>
                    <option value="Kimia 2">Kimia 2</option>
                    <option value="Kimia 3">Kimia 3</option>
                    <option value="Las 1">Las 1</option>
                    <option value="Las 2">Las 2</option>
                    <option value="Las 3">Las 3</option>
                    <option value="RPL 1">RPL 1</option>
                    <option value="RPL 2">RPL 2</option>
                    <option value="RPL 3">RPL 3</option>
                    <option value="RPL 4">RPL 4</option>
                  </select>
                  <p v-if="errors.jurusan" class="error-msg">{{ errors.jurusan }}</p>
                </div>
              </div>
            </div>
            <!-- END KELAS & JURUSAN -->

           <div class="space-y-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1">Email Sekolah</label>
              <input v-model="form.email" type="email" placeholder="name@smkn1gputri.sch.id" class="auth-input" :disabled="isSubmitting">
              <p v-if="errors.email" class="error-msg">{{ errors.email }}</p>
           </div>

            <div v-if="!isLogin" class="space-y-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1">Nomor Handphone</label>
              <input v-model="form.phone" type="tel" placeholder="08xxxxxxxxxx" class="auth-input" :disabled="isSubmitting">
              <p v-if="errors.phone" class="error-msg">{{ errors.phone }}</p>
            </div>

            <div class="space-y-2">
              <div class="flex justify-between items-center ml-1">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">Kata Sandi</label>
                <button v-if="isLogin" type="button" class="text-[10px] font-bold text-orange-600 hover:underline">Lupa Sandi?</button>
              </div>
              <input v-model="form.password" type="password" placeholder="••••••••" class="auth-input" :disabled="isSubmitting">
              <p v-if="errors.password" class="error-msg">{{ errors.password }}</p>
            </div>

            <div v-if="!isLogin" class="space-y-2">
              <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1">Konfirmasi Kata Sandi</label>
              <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="auth-input" :disabled="isSubmitting">
              <p v-if="errors.password_confirmation" class="error-msg">{{ errors.password_confirmation }}</p>
            </div>

            <button type="submit" :disabled="isSubmitting" class="submit-btn">
              <span v-if="!isSubmitting">{{ isLogin ? 'Masuk' : 'Buat Akun' }}</span>
              <div v-else class="w-5 h-5 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            </button>
          </form>

          <div class="mt-8 text-center">
            <p class="text-xs text-gray-400 font-medium">
              {{ isLogin ? 'Belum terdaftar?' : 'Sudah punya akun?' }}
              <button @click="toggleMode" class="text-orange-600 font-bold ml-1 hover:underline underline-offset-4">
                {{ isLogin ? 'Daftar sekarang' : 'Masuk di sini' }}
              </button>
            </p>
          </div>
        </div>

        <button @click="handleClose" class="absolute top-6 right-6 text-gray-300 hover:text-gray-900 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ show: Boolean });
const emits = defineEmits(['close']);

const isLogin = ref(true);
const formError = ref('');
const isSubmitting = ref(false);

const form = useForm({
  name: '',
  email: '',
  nisn: '',
  phone: '',
  password: '',
  password_confirmation: '',
  kelas: '',    // <-- tambahan field kelas
  jurusan: '',  // <-- tambahan field jurusan
  remember: false,
});

const errors = computed(() => form.errors);

const toggleMode = () => {
  isLogin.value = !isLogin.value;
  form.clearErrors();
  formError.value = '';
};

const handleClose = () => { if(!isSubmitting.value) emits('close'); };

const handleSubmit = () => {
  formError.value = '';
  isSubmitting.value = true;

  const endpoint = isLogin.value ? 'login' : 'register';

  form.post(route(endpoint), {
    preserveScroll: true,
    onSuccess: () => {
      isSubmitting.value = false;
      emits('close');
      form.reset();
    },
    onError: (err) => {
      isSubmitting.value = false;
      formError.value = Object.values(err)[0];
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};
</script>

<style scoped>
.auth-input {
  @apply w-full px-5 py-3.5 rounded-2xl bg-gray-50 border border-gray-100
         text-sm font-semibold text-gray-900 outline-none transition-all duration-300
         placeholder:text-gray-300 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/5;
}

.submit-btn {
  @apply w-full bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-lg 
         shadow-orange-100 transition-all duration-300 flex items-center justify-center
         hover:bg-orange-700 hover:shadow-orange-200 active:scale-[0.98]
         disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none disabled:cursor-not-allowed;
}

.error-msg {
  @apply text-[10px] text-red-500 font-bold mt-1 ml-1 tracking-wide;
}

.custom-scroll::-webkit-scrollbar {
  width: 6px;
}

.custom-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
  background: #fed7aa;
  border-radius: 999px;
}

/* Modal Smooth Animation */
.modal-enter-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-leave-active {
  transition: all 0.3s cubic-bezier(0.7, 0, 0.84, 0);
}
.modal-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}
.modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
