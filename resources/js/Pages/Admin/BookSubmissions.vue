<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
  submissions: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const selectedStatus = ref('pending');

const filteredSubmissions = computed(() => {
  return props.submissions.filter(sub => {
    const matchesSearch = sub.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         sub.author?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = selectedStatus.value ? sub.status === selectedStatus.value : true;
    return matchesSearch && matchesStatus;
  });
});

async function approveSubmission(book) {
  const result = await Swal.fire({
    title: 'Setujui Pengajuan?',
    text: `Setujui pengajuan buku "${book.title}"?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Setujui',
    cancelButtonText: 'Batal',
  });

  if (result.isConfirmed) {
    router.post(route('admin.book-submissions.approve', book.id), {}, {
      onSuccess: () => {
        Swal.fire('Berhasil!', 'Pengajuan buku telah disetujui.', 'success');
      },
      onError: () => {
        Swal.fire('Gagal!', 'Gagal menyetujui pengajuan.', 'error');
      }
    });
  }
}

async function rejectSubmission(book) {
  const result = await Swal.fire({
    title: 'Tolak Pengajuan?',
    input: 'textarea',
    inputPlaceholder: 'Masukkan alasan penolakan...',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Tolak',
    cancelButtonText: 'Batal',
  });

  if (result.isConfirmed) {
    router.post(route('admin.book-submissions.reject', book.id), {
      rejection_reason: result.value || 'Tidak diberikan',
    }, {
      onSuccess: () => {
        Swal.fire('Berhasil!', 'Pengajuan buku telah ditolak.', 'success');
      },
      onError: () => {
        Swal.fire('Gagal!', 'Gagal menolak pengajuan.', 'error');
      }
    });
  }
}
</script>

<template>
  <AdminLayout title="Pengajuan Buku">
    <div class="min-h-screen">
      <div class="max-w-7xl mx-auto px-4 py-8 ms-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-slate-900">Pengajuan Buku</h1>
          <p class="text-slate-600 mt-2">Kelola pengajuan buku dari petugas perpustakaan</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
          <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
              <input 
                v-model="searchQuery"
                type="text"
                placeholder="🔍 Cari judul atau penulis..."
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="w-full md:w-48">
              <select 
                v-model="selectedStatus"
                class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Submissions List -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">Buku</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">Penulis</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">Diajukan Oleh</th>
                  <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-600">Status</th>
                  <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-600">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="submission in filteredSubmissions" :key="submission.id" class="hover:bg-slate-50">
                  <td class="px-6 py-4">
                    <Link 
                      :href="route('admin.book-submissions.show', submission.id)"
                      class="text-blue-600 hover:text-blue-800 font-semibold"
                    >
                      {{ submission.title }}
                    </Link>
                  </td>
                  <td class="px-6 py-4">{{ submission.author }}</td>
                  <td class="px-6 py-4">{{ submission.created_by_user?.name || 'Unknown' }}</td>
                  <td class="px-6 py-4">
                    <span 
                      :class="{
                        'bg-yellow-100 text-yellow-800': submission.status === 'pending',
                        'bg-green-100 text-green-800': submission.status === 'approved',
                        'bg-red-100 text-red-800': submission.status === 'rejected',
                      }"
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                    >
                      {{ submission.status === 'pending' ? 'Menunggu' : submission.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex gap-2 justify-center">
                      <Link 
                        :href="route('admin.book-submissions.show', submission.id)"
                        class="px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100"
                      >
                        Lihat
                      </Link>
                      <button 
                        v-if="submission.status === 'pending'"
                        @click="approveSubmission(submission)"
                        class="px-3 py-1 text-xs font-semibold text-green-600 bg-green-50 rounded-lg hover:bg-green-100"
                      >
                        Setujui
                      </button>
                      <button 
                        v-if="submission.status === 'pending'"
                        @click="rejectSubmission(submission)"
                        class="px-3 py-1 text-xs font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100"
                      >
                        Tolak
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredSubmissions.length === 0">
                  <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                    Tidak ada pengajuan ditemukan
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
