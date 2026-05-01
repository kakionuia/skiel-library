<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full p-8 bg-white rounded-lg shadow-sm border border-gray-200 text-center">
      <!-- Header Icon -->
      <div class="mb-6">
        <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center animate-pulse"
          :class="user.status === 'rejected' ? 'bg-red-100' : 'bg-yellow-100'">
          <svg v-if="user.status === 'rejected'" class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2" />
          </svg>
          <svg v-else class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>

      <!-- Status Badge -->
      <div class="mb-4">
        <span v-if="user.status === 'pending'" class="inline-block px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
          Menunggu Persetujuan
        </span>
        <span v-else-if="user.status === 'rejected'" class="inline-block px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
          Akun Ditolak
        </span>
      </div>

      <!-- Title and Message -->
      <h1 class="text-xl font-bold text-gray-900 mb-2">
        <span v-if="user.status === 'rejected'">Akun Anda Ditolak</span>
        <span v-else>Menunggu Persetujuan</span>
      </h1>
      
      <div class="space-y-3 text-left mb-6 p-4 bg-gray-50 rounded-lg">
        <div>
          <p class="text-xs text-gray-500 font-medium">NAMA</p>
          <p class="text-gray-900 font-semibold">{{ user.name }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-500 font-medium">EMAIL</p>
          <p class="text-gray-900 font-semibold">{{ user.email }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-500 font-medium">STATUS AKUN</p>
          <p class="text-gray-900 font-semibold capitalize">
            <span v-if="user.status === 'rejected'" class="text-red-600">Ditolak</span>
            <span v-else class="text-yellow-600">Menunggu Verifikasi</span>
          </p>
        </div>
        <div v-if="user.created_at">
          <p class="text-xs text-gray-500 font-medium">TANGGAL DAFTAR</p>
          <p class="text-gray-900 font-semibold">{{ formatDate(user.created_at) }}</p>
        </div>
      </div>

      <!-- Description -->
      <p v-if="user.status === 'rejected'" class="text-red-600 text-sm mb-4">
        Maaf, aplikasi Anda untuk membuat akun telah ditolak oleh admin. Jika Anda ingin mencoba lagi, silakan buat akun baru.
      </p>
      <p v-else class="text-gray-600 text-sm mb-4">
        Akun Anda sedang dalam proses verifikasi oleh admin. Mohon tunggu beberapa saat hingga akun Anda disetujui.
      </p>

      <!-- Action Buttons -->
      <div class="mt-8 space-y-3">
        <button
          @click="checkStatus"
          :disabled="isChecking"
          class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 disabled:bg-gray-400 transition-colors"
        >
          <span v-if="!isChecking">Cek Status Akun</span>
          <span v-else class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengecek...
          </span>
        </button>

        <button
          @click="confirmDeleteAccount"
          :disabled="isDeleting"
          class="w-full px-4 py-2 text-red-700 border-2 border-red-300 rounded-lg hover:bg-red-50 transition-colors font-medium"
        >
          {{ isDeleting ? 'Menghapus...' : 'Hapus Akun' }}
        </button>

        <div v-if="statusMessage" :class="[
          'mt-4 p-3 rounded-lg text-sm font-medium',
          statusType === 'success' ? 'bg-green-100 text-green-800 border border-green-300' :
          statusType === 'error' ? 'bg-red-100 text-red-800 border border-red-300' :
          'bg-blue-100 text-blue-800 border border-blue-300'
        ]">
          {{ statusMessage }}
        </div>
      </div>

      <p class="text-xs text-gray-400 mt-6">
        Sistem akan otomatis mengecek setiap {{ autoCheckInterval / 1000 }} detik
      </p>
    </div>

  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { dangerConfirm } from '@/Utils/sweetAlert';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  }
});

const isChecking = ref(false);
const isDeleting = ref(false);
const statusMessage = ref('');
const statusType = ref('');
const autoCheckInterval = 30000; // 30 seconds
let autoCheckTimer = null;

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const checkStatus = async () => {
  isChecking.value = true;
  statusMessage.value = '';
  
  try {
    const response = await fetch('/api/check-approval-status', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
      }
    });
    
    if (!response.ok) {
      throw new Error('Failed to check status');
    }

    const data = await response.json();
    
    if (data.status === 'approved') {
      statusMessage.value = 'Selamat! Akun Anda sudah disetujui. Anda akan diarahkan ke dashboard...';
      statusType.value = 'success';
      
      setTimeout(() => {
        router.get('/dashboard');
      }, 1000);
    } else if (data.status === 'pending') {
      statusMessage.value = 'Akun Anda masih dalam proses verifikasi. Silakan coba lagi nanti.';
      statusType.value = 'info';
    } else {
      statusMessage.value = 'Status akun tidak berubah.';
      statusType.value = 'info';
    }
  } catch (error) {
    statusMessage.value = 'Gagal mengecek status. Silakan coba lagi.';
    statusType.value = 'error';
  } finally {
    isChecking.value = false;
  }
};

const confirmDeleteAccount = async () => {
  if (await dangerConfirm({
    title: 'Hapus akun?',
    text: 'Tindakan ini tidak dapat dibatalkan. Semua data akun Anda akan dihapus permanen.',
    confirmButtonText: 'Ya, hapus akun',
  })) {
    deleteAccount();
  }
};

const deleteAccount = async () => {
  isDeleting.value = true;
  
  try {
    router.post('/delete-account', {}, {
      onError: () => {
        statusMessage.value = 'Gagal menghapus akun. Silakan coba lagi.';
        statusType.value = 'error';
        isDeleting.value = false;
      }
    });
  } catch (error) {
    statusMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
    statusType.value = 'error';
    isDeleting.value = false;
  }
};

onMounted(() => {
  // Only auto-check for pending users, not rejected
  if (props.user.status === 'pending') {
    autoCheckTimer = setInterval(checkStatus, autoCheckInterval);
  }
});

onUnmounted(() => {
  if (autoCheckTimer) {
    clearInterval(autoCheckTimer);
  }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
