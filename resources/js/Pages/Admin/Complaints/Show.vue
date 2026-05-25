<template>
  <AuthenticatedLayout title="Detail Keluhan">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex items-center gap-4">
          <Link href="/admin/complaints" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Detail Keluhan</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <div class="mb-4">
                <span :class="{
                  'bg-amber-100 text-amber-700': complaint.status === 'pending',
                  'bg-orange-100 text-orange-700': complaint.status === 'reviewed',
                  'bg-green-100 text-green-700': complaint.status === 'resolved',
                  'bg-red-100 text-red-700': complaint.status === 'rejected',
                }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ complaint.status }}
                </span>
              </div>
              <h2 class="text-xl font-bold text-gray-900">{{ complaint.subject }}</h2>
              <p class="mt-4 text-gray-600">{{ complaint.description }}</p>
              
              <div v-if="complaint.admin_response" class="mt-6 p-4 bg-green-50 rounded-lg">
                <p class="text-sm font-medium text-green-800">Tanggapan:</p>
                <p class="mt-2 text-gray-700">{{ complaint.admin_response }}</p>
              </div>

              <div v-if="complaint.status === 'pending'" class="mt-6 p-4 bg-amber-50 rounded-lg">
                <h4 class="text-sm font-bold text-amber-900 mb-4">Proses Keluhan</h4>

                <!-- Status Selection -->
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Status</label>
                  <select v-model="selectedStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="pending">Tertunda (Pending)</option>
                    <option value="reviewed">Ditinjau (Reviewed)</option>
                    <option value="resolved">Selesai (Resolved)</option>
                    <option value="rejected">Ditolak (Rejected)</option>
                  </select>
                </div>

                <!-- Response Form -->
                <form @submit.prevent="resolveComplaint">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Tanggapan</label>
                  <textarea v-model="response" placeholder="Tulis tanggapan admin..." class="w-full p-3 border border-gray-300 rounded-lg" rows="4" required></textarea>
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    {{ isSubmitting ? 'Mengirim...' : 'Kirim Tanggapan' }}
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <h3 class="text-sm font-medium text-gray-500">Pelapor</h3>
              <p class="mt-2 text-lg font-semibold text-gray-900">{{ complaint.user?.name }}</p>
              <p class="text-sm text-gray-500">{{ complaint.user?.email }}</p>
              
              <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-500">Dibuat pada</p>
                <p class="text-sm font-medium text-gray-900">{{ formatDateTime(complaint.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  complaint: { type: Object, default: () => ({}) },
});

const response = ref('');
const selectedStatus = ref(props.complaint.status);
const isSubmitting = ref(false);

const formatDateTime = (dateString) => {
  if (!dateString) return '-';

  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '-';

  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Jakarta',
    timeZoneName: 'short',
  }).format(date);
};

const resolveComplaint = async () => {
  if (!response.value.trim() || !selectedStatus.value) {
    alert('Silahkan isi tanggapan dan pilih status');
    return;
  }

  isSubmitting.value = true;
  router.post(`/admin/complaints/${props.complaint.id}/resolve`, {
    admin_response: response.value,
    status: selectedStatus.value
  }, {
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};
</script>
