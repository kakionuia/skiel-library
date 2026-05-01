<template>
  <AuthenticatedLayout title="Detail Pengajuan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        
        <!-- Back Button -->
        <div class="mb-6">
          <Link :href="route('admin.book-submissions')" class="text-orange-600 hover:text-orange-700 font-medium text-sm flex items-center gap-1">
            ← Kembali ke Pengajuan
          </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden p-8">
          
          <!-- Header -->
          <div class="pb-6 border-b border-gray-200 mb-8">
            <div class="flex items-start justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ book.title }}</h1>
                <p class="mt-2 text-gray-600">Oleh <strong>{{ book.author }}</strong></p>
              </div>
              <span :class="{
                'bg-amber-100 text-amber-700 ring-1 ring-amber-200': book.status === 'pending',
                'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200': book.status === 'approved',
                'bg-rose-100 text-rose-700 ring-1 ring-rose-200': book.status === 'rejected',
              }" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide">
                {{ book.status }}
              </span>
            </div>
          </div>

          <!-- Cover Image and Basic Info -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Cover Image -->
            <div class="md:col-span-1">
              <div v-if="book.cover_image" class="bg-gray-100 rounded-lg overflow-hidden">
                <img :src="book.cover_image" :alt="book.title" class="w-full h-auto object-cover">
              </div>
              <div v-else class="bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg h-64 flex items-center justify-center">
                <span class="text-gray-400 text-center">
                  <div class="text-4xl mb-2">📕</div>
                  Tidak ada sampul
                </span>
              </div>
            </div>

            <!-- Basic Information -->
            <div class="md:col-span-2 space-y-6">
              <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">ISBN</h3>
                <p class="text-lg text-gray-900">{{ book.isbn || '-' }}</p>
              </div>
              
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Penerbit</h3>
                  <p class="text-lg text-gray-900">{{ book.publisher || '-' }}</p>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Tahun</h3>
                  <p class="text-lg text-gray-900">{{ book.year || '-' }}</p>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategori</h3>
                  <p class="text-lg text-gray-900">{{ book.category || '-' }}</p>
                </div>
              </div>

              <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Jumlah Buku</h3>
                <p class="text-lg font-bold text-gray-900">{{ book.quantity }} <span class="text-sm text-gray-500">eksemplar</span></p>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="mb-8 pb-8 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Deskripsi</h3>
            <p class="text-gray-700 leading-relaxed">{{ book.description || 'Tidak ada deskripsi.' }}</p>
          </div>

          <!-- Submission Info -->
          <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Pengajuan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Diajukan oleh</p>
                <p class="text-lg font-semibold text-gray-900">{{ createdBy?.name || '-' }}</p>
                <p class="text-sm text-gray-600">{{ createdBy?.email }}</p>
                <p class="text-xs text-gray-500 mt-1">Role: <strong>{{ createdBy?.role }}</strong></p>
              </div>
              <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Pengajuan</p>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(book.created_at) }}</p>
              </div>
              <div v-if="book.approved_at">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Diproses oleh</p>
                <p class="text-lg font-semibold text-gray-900">{{ book.approved_by_user?.name || '-' }}</p>
              </div>
              <div v-if="book.approved_at">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Proses</p>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(book.approved_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div v-if="book.status === 'pending'" class="flex gap-3">
            <button @click="approveSubmission" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">
              ✓ Setujui Pengajuan
            </button>
            <button @click="rejectSubmission" class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 px-6 rounded-lg transition-colors">
              ✗ Tolak Pengajuan
            </button>
          </div>
          <div v-else class="p-4 bg-gray-100 rounded-lg text-center text-gray-600">
            <p>Pengajuan ini sudah diproses.</p>
          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { confirmDialog, promptDialog } from '@/Utils/sweetAlert';

const props = defineProps({
  book: { type: Object, required: true },
  createdBy: { type: Object, default: () => ({}) },
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const approveSubmission = async () => {
  if (await confirmDialog({
    title: 'Setujui pengajuan buku?',
    text: `"${props.book.title}" akan ditambahkan ke perpustakaan dan dapat diakses pengguna.`,
    confirmButtonText: 'Ya, setujui',
  })) {
    router.post(route('admin.book-submissions.approve', props.book.id));
  }
};

const rejectSubmission = async () => {
  const reason = await promptDialog({
    title: 'Tolak pengajuan buku?',
    text: `Tambahkan alasan penolakan untuk "${props.book.title}".`,
    inputPlaceholder: 'Alasan penolakan (opsional)',
    confirmButtonText: 'Tolak pengajuan',
  });

  if (reason !== null) {
    router.post(route('admin.book-submissions.reject', props.book.id), { reason });
  }
};
</script>
