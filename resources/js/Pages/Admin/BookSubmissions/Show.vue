<template>
  <AuthenticatedLayout title="Detail Pengajuan Buku">
    <div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        
        <div class="mb-6">
          <Link 
            :href="route('admin.book-submissions')" 
            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 hover:shadow-sm transition-all"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar Pengajuan
          </Link>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-8">
          
          <div class="pb-6 border-b border-slate-100 mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-orange-600">Lembar Peninjauan Berkas</p>
              <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">{{ book.title }}</h1>
              <p class="text-sm text-slate-500 mt-0.5">Karya Utama / Penulis: <span class="font-semibold text-slate-700">{{ book.author }}</span></p>
            </div>
            <div>
              <span :class="{
                'bg-orange-50 border-orange-200 text-orange-600': book.status === 'pending',
                'bg-slate-50 border-slate-200 text-slate-600': book.status === 'approved',
                'bg-white border-slate-200 text-slate-400': book.status === 'rejected',
              }" class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border">
                <span v-if="book.status === 'pending'" class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>
                Status: {{ book.status }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="md:col-span-1">
              <div v-if="book.cover_image" class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <img :src="book.cover_image" :alt="book.title" class="w-full h-auto object-cover">
              </div>
              <div v-else class="bg-slate-50 border border-dashed border-slate-200 rounded-xl h-60 flex flex-col items-center justify-center text-center p-4">
                <svg class="w-8 h-8 text-slate-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-xs font-medium text-slate-400">Tidak ada gambar sampul</span>
              </div>
            </div>

            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
              <div class="sm:col-span-2 border-b border-slate-50 pb-3">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor Standardisasi (ISBN)</h3>
                <p class="text-base font-mono text-slate-800 font-semibold bg-slate-50 px-2 py-1 rounded border inline-block">{{ book.isbn || '-' }}</p>
              </div>
              
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Penerbit Publikasi</h3>
                <p class="text-sm font-semibold text-slate-800">{{ book.publisher || '-' }}</p>
              </div>
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Tahun Terbit</h3>
                <p class="text-sm font-semibold text-slate-800">{{ book.year || '-' }}</p>
              </div>
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Kategori / Rak</h3>
                <p class="text-sm font-semibold text-slate-800">{{ book.category || '-' }}</p>
              </div>
              <div>
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-0.5">Volume Alokasi</h3>
                <p class="text-sm font-bold text-slate-800">{{ book.quantity }} <span class="text-xs text-slate-400 font-normal">eksemplar</span></p>
              </div>
            </div>
          </div>

          <div class="mb-8 pb-6 border-b border-slate-100">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Ringkas</h3>
            <p class="text-sm text-slate-600 leading-relaxed bg-slate-50/50 border border-slate-100 rounded-xl p-4">{{ book.description || 'Tidak ada catatan deskripsi tambahan untuk buku ini.' }}</p>
          </div>

          <div class="bg-slate-50/50 border border-slate-200/60 rounded-xl p-6 mb-8">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Informasi Log Pengajuan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
              <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Diajukan Oleh</p>
                <p class="font-bold text-slate-800 mt-0.5">{{ createdBy?.name || '-' }}</p>
                <p class="text-xs text-slate-500 font-medium">{{ createdBy?.email }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1 bg-slate-200/50 px-1.5 py-0.5 rounded inline-block">{{ createdBy?.role }}</p>
              </div>
              <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Waktu Masuk Sistem</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ formatDate(book.created_at) }}</p>
              </div>
              <div v-if="book.approved_at" class="sm:col-span-1 border-t border-slate-200/50 pt-3 mt-1">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Diproses Oleh</p>
                <p class="font-semibold text-slate-800 mt-0.5">{{ book.approved_by_user?.name || '-' }}</p>
              </div>
              <div v-if="book.approved_at" class="sm:col-span-1 border-t border-slate-200/50 pt-3 mt-1">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Waktu Eksekusi</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ formatDate(book.approved_at) }}</p>
              </div>
            </div>
          </div>

          <div v-if="book.status === 'pending'" class="flex flex-col sm:flex-row gap-3">
            <button @click="approveSubmission" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all text-sm">
              Setujui Dokumen Pengajuan
            </button>
            <button @click="rejectSubmission" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all text-sm">
              Tolak Pengajuan Berkas
            </button>
          </div>
          <div v-else class="p-3.5 bg-slate-100 border border-slate-200/60 rounded-xl text-center text-xs font-semibold text-slate-500">
            Berkas pengajuan ini telah selesai diproses oleh otoritas manajemen.
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