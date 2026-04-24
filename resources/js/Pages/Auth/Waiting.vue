<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full p-8 bg-white rounded-lg shadow-sm border border-gray-200 text-center">
      <div class="mb-6">
        <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
          <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>

      <h1 class="text-xl font-bold text-gray-900">Menunggu Persetujuan</h1>
      <p class="mt-4 text-gray-600">
        Akun Anda sedang dalam proses verifikasi oleh admin. 
        Mohon tunggu beberapa saat hingga akun Anda disetujui.
      </p>
      <p class="mt-2 text-sm text-gray-500">
        Anda akan dapat login setelah admin menyetujui pendaftaran Anda.
      </p>

      <div class="mt-6 border-t pt-4">
        <p class="text-sm text-gray-500">Ingin keluar?</p>
        <form @submit.prevent="logout">
          <button
            type="submit"
            class="mt-2 text-sm text-gray-600 hover:text-gray-900 underline"
          >
            Keluar
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const logout = () => {
  router.post('/logout');
};
</script>