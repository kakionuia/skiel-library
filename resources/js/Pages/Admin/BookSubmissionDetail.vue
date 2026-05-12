<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  submission: { type: Object, required: true },
});

async function approveSubmission() {
  const result = await Swal.fire({
    title: 'Setujui Pengajuan?',
    text: `Setujui pengajuan buku "${props.submission.title}"?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Setujui',
    cancelButtonText: 'Batal',
  });

  if (result.isConfirmed) {
    router.post(route('admin.book-submissions.approve', props.submission.id), {}, {
      onSuccess: () => {
        Swal.fire('Berhasil!', 'Pengajuan buku telah disetujui.', 'success').then(() => {
          router.visit(route('admin.book-submissions'));
        });
      },
      onError: () => {
        Swal.fire('Gagal!', 'Gagal menyetujui pengajuan.', 'error');
      }
    });
  }
}

async function rejectSubmission() {
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
    router.post(route('admin.book-submissions.reject', props.submission.id), {
      rejection_reason: result.value || 'Tidak diberikan',
    }, {
      onSuccess: () => {
        Swal.fire('Berhasil!', 'Pengajuan buku telah ditolak.', 'success').then(() => {
          router.visit(route('admin.book-submissions'));
        });
      },
      onError: () => {
        Swal.fire('Gagal!', 'Gagal menolak pengajuan.', 'error');
      }
    });
  }
}
</script>

<template>
  <AdminLayout title="Detail Pengajuan Buku">
    <div class="min-h-screen">
      <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Back Link -->
        <Link 
          :href="route('admin.book-submissions')"
          class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-2 mb-6"
        >
          ← Kembali ke Daftar Pengajuan
        </Link>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
          <!-- Header -->
          <div class="flex justify-between items-start mb-8 pb-6 border-b border-slate-200">
            <div>
              <h1 class="text-3xl font-bold text-slate-900">{{ submission.title }}</h1>
              <p class="text-slate-600 mt-2">ISBN: {{ submission.isbn }}</p>
            </div>
            <span 
              :class="{
                'bg-yellow-100 text-yellow-800': submission.status === 'pending',
                'bg-green-100 text-green-800': submission.status === 'approved',
                'bg-red-100 text-red-800': submission.status === 'rejected',
              }"
              class="inline-block px-4 py-2 rounded-full font-semibold text-sm"
            >
              {{ submission.status === 'pending' ? 'Menunggu Persetujuan' : submission.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
            </span>
          </div>

          <!-- Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Left Column -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Penulis</label>
                <p class="text-lg text-slate-900">{{ submission.author }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Penerbit</label>
                <p class="text-lg text-slate-900">{{ submission.publisher || '-' }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Tahun Terbit</label>
                <p class="text-lg text-slate-900">{{ submission.year || '-' }}</p>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Kategori</label>
                <p class="text-lg text-slate-900">{{ submission.category }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Stok</label>
                <p class="text-lg text-slate-900">{{ submission.quantity }} buah</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Diajukan Oleh</label>
                <p class="text-lg text-slate-900">{{ submission.created_by_user?.name || 'Unknown' }}</p>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="mb-8">
            <label class="block text-sm font-semibold text-slate-600 mb-2">Deskripsi</label>
            <p class="text-slate-700 leading-relaxed">{{ submission.description || 'Tidak ada deskripsi' }}</p>
          </div>

          <!-- Rejection Reason (if rejected) -->
          <div v-if="submission.status === 'rejected' && submission.rejection_reason" class="mb-8 p-4 bg-red-50 rounded-lg border border-red-200">
            <label class="block text-sm font-semibold text-slate-600 mb-2">Alasan Penolakan</label>
            <p class="text-slate-700">{{ submission.rejection_reason }}</p>
          </div>

          <!-- Action Buttons -->
          <div v-if="submission.status === 'pending'" class="flex gap-4 pt-6 border-t border-slate-200">
            <button 
              @click="approveSubmission"
              class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition"
            >
              ✓ Setujui Pengajuan
            </button>
            <button 
              @click="rejectSubmission"
              class="px-8 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition"
            >
              ✕ Tolak Pengajuan
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
