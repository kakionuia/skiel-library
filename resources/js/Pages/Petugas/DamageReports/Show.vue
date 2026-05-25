<template>
  <AuthenticatedLayout title="Detail Laporan Kerusakan">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8">
          <Link 
            href="/petugas/damage-reports" 
            class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 mb-4"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar
          </Link>
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">📋 Detail Laporan Kerusakan</h1>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Left: Damage Info & Images -->
          <div class="lg:col-span-2 space-y-6">
            
            <!-- User & Book Info -->
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
              <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Pengguna & Buku</h2>
              
              <div class="space-y-4">
                <div class="flex items-start justify-between p-4 bg-gray-50 rounded-xl">
                  <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ report.user?.name }}</p>
                    <p class="text-sm text-gray-600">{{ report.user?.email }}</p>
                  </div>
                </div>

                <div class="flex items-start justify-between p-4 bg-gray-50 rounded-xl">
                  <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Buku</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ report.book?.title }}</p>
                    <p class="text-sm text-gray-600">{{ report.book?.author }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Damage Details -->
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
              <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Detail Kerusakan</h2>
              
              <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Kerusakan</p>
                    <p class="text-lg font-bold text-gray-900 mt-2">{{ getDamageTypeLabel(report.damage_type) }}</p>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hari Terlambat</p>
                    <p class="text-lg font-bold text-gray-900 mt-2">{{ report.late_days }} hari</p>
                  </div>
                </div>

                <div class="p-4 bg-orange-50 rounded-xl border border-orange-200">
                  <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Denda Keterlambatan</p>
                  <p class="text-2xl font-black text-orange-600 mt-2">Rp {{ formatNumber(report.late_fee) }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-xl">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Kerusakan</p>
                  <p class="text-gray-700">{{ report.damage_description }}</p>
                </div>
              </div>
            </div>

            <!-- Damage Images -->
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
              <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Foto Kondisi Buku</h2>
              
              <div v-if="report.damage_images && report.damage_images.length > 0" class="grid grid-cols-2 gap-4">
                <div v-for="(image, index) in report.damage_images" :key="index" class="bg-gray-100 rounded-xl overflow-hidden">
                  <img :src="image" :alt="`Foto kerusakan ${index + 1}`" class="w-full h-48 object-cover">
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Tidak ada foto
              </div>
            </div>
          </div>

          <!-- Right: Response Form -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6 sticky top-20">
              <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Respons Laporan</h2>
              
              <form @submit.prevent="submitResponse" class="space-y-4">
                
                <!-- Additional Fine -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Denda Tambahan (Rp) <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model.number="form.additional_fine"
                    type="number"
                    min="0"
                    step="1000"
                    placeholder="0"
                    class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all"
                  />
                  <p class="text-xs text-gray-500 mt-1">Total yang harus dibayar: Rp {{ formatNumber(report.late_fee + (form.additional_fine || 0)) }}</p>
                </div>

                <!-- Staff Comment -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Komentar <span class="text-red-500">*</span>
                  </label>
                  <textarea
                    v-model="form.staff_comment"
                    rows="4"
                    placeholder="Jelaskan pertimbangan denda dan instruksi pembayaran..."
                    maxlength="500"
                    class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none resize-none transition-all"
                  ></textarea>
                  <p class="text-xs text-gray-500 mt-1">{{ form.staff_comment.length }}/500</p>
                </div>

                <!-- Error Messages -->
                <div v-if="Object.keys(errors).length > 0" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                  <p class="text-xs font-semibold text-red-600">Terdapat kesalahan:</p>
                  <ul class="text-xs text-red-600 mt-1 space-y-1">
                    <li v-for="(error, field) in errors" :key="field">• {{ error }}</li>
                  </ul>
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="w-full py-3 px-4 bg-orange-600 hover:bg-orange-700 disabled:bg-orange-400 text-white font-bold rounded-lg transition-all"
                >
                  <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Mengirim...
                  </span>
                  <span v-else>Kirim Respons</span>
                </button>
              </form>

              <!-- Info Box -->
              <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-700">
                  ℹ️ Setelah mengirim respons, pengguna akan menerima notifikasi dan dapat melakukan pembayaran. Tombol "Tandai Selesai" akan muncul setelah pengguna membayar.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  report: { type: Object, required: true }
});

const form = ref({
  additional_fine: 0,
  staff_comment: ''
});

const errors = ref({});
const isSubmitting = ref(false);

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};

const getDamageTypeLabel = (type) => {
  const labels = {
    lost: '🗑️ Hilang',
    wet: '💧 Basah',
    torn: '✂️ Sobek',
    stained: '🔴 Ternoda',
    other: '❓ Lainnya'
  };
  return labels[type] || type;
};

const submitResponse = () => {
  errors.value = {};
  
  // Validate
  if (form.value.additional_fine === null || form.value.additional_fine === undefined || form.value.additional_fine === '') {
    errors.value.additional_fine = 'Denda tambahan wajib diisi.';
  }
  if (!form.value.staff_comment || form.value.staff_comment.trim().length === 0) {
    errors.value.staff_comment = 'Komentar wajib diisi.';
  } else if (form.value.staff_comment.length < 10) {
    errors.value.staff_comment = 'Komentar minimal 10 karakter.';
  }

  if (Object.keys(errors.value).length > 0) {
    return;
  }

  isSubmitting.value = true;

  router.post(`/petugas/damage-reports/${props.report.id}/respond`, {
    additional_fine: form.value.additional_fine,
    staff_comment: form.value.staff_comment
  }, {
    onSuccess: () => {
      isSubmitting.value = false;
      Swal.fire({
        title: 'Respons Dikirim!',
        text: `Respons laporan kerusakan telah dikirim kepada ${props.report.user.name}. Pengguna sekarang dapat melakukan pembayaran.`,
        icon: 'success',
        confirmButtonColor: '#ea580c'
      }).then(() => {
        router.visit('/petugas/damage-reports');
      });
    },
    onError: (error) => {
      isSubmitting.value = false;
      Swal.fire({
        title: 'Gagal!',
        text: error.message || 'Terjadi kesalahan saat mengirim respons.',
        icon: 'error',
        confirmButtonColor: '#ea580c'
      });
    }
  });
};
</script>
