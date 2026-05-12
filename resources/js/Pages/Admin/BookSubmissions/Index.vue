<template>
  <AuthenticatedLayout title="Pengajuan Buku">
    <div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Manajemen Koleksi</p>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Pengajuan Buku Petugas</h1>
          <p class="mt-2 text-sm text-slate-500 max-w-2xl">Tinjau, verifikasi, dan kelola usulan penambahan koleksi buku baru yang diajukan oleh staf petugas perpustakaan.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50/70 border-b border-slate-200">
                <tr>
                  <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">Informasi Buku</th>
                  <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">Staf Pengaju</th>
                  <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah</th>
                  <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                  <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Input</th>
                  <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Opsi Tindakan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="book in submissions.data" :key="book.id" class="hover:bg-slate-50/40 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-slate-900">{{ book.title }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ book.author }} • {{ book.year || '-' }}</div>
                    <div class="text-[11px] font-mono text-slate-400 mt-1 bg-slate-50 px-1.5 py-0.5 rounded inline-block border border-slate-100">{{ book.isbn || 'No ISBN' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-slate-800">{{ book.petugas?.name || '-' }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">{{ book.petugas?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-slate-800">{{ book.quantity }} <span class="text-xs text-slate-400 font-normal">eks.</span></div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-orange-50 border-orange-200 text-orange-600': book.status === 'pending',
                      'bg-slate-50 border-slate-200 text-slate-600': book.status === 'approved',
                      'bg-white border-slate-200 text-slate-400': book.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide border">
                      <span v-if="book.status === 'pending'" class="w-1 h-1 bg-orange-500 rounded-full mr-1.5"></span>
                      {{ book.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-slate-500 font-medium">{{ formatDate(book.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="book.status === 'pending'" class="flex justify-end gap-2">
                      <Link :href="route('admin.book-submissions.show', book.id)" class="bg-white border border-slate-200 text-slate-700 hover:text-slate-900 px-3 py-1.5 rounded-lg text-xs font-semibold hover:shadow-sm transition-all">
                        Detail
                      </Link>
                      <button @click="approveSubmission(book)" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:shadow-sm transition-all">
                        Setuju
                      </button>
                      <button @click="rejectSubmission(book)" class="bg-slate-800 hover:bg-slate-900 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:shadow-sm transition-all">
                        Tolak
                      </button>
                    </div>
                    <Link v-else :href="route('admin.book-submissions.show', book.id)" class="text-xs text-orange-600 hover:text-orange-700 font-bold transition-colors">
                      Lihat Detail →
                    </Link>
                  </td>
                </tr>
                <tr v-if="submissions.data?.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-sm font-medium">Tidak ada berkas pengajuan buku yang tersedia.</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="submissions.data?.length" class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
            <p class="text-xs font-medium text-slate-400">Menampilkan {{ submissions.data.length }} dari {{ submissions.total }} entri data</p>
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

defineProps({
  submissions: { type: Object, default: () => ({ data: [] }) },
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const approveSubmission = async (book) => {
  if (await confirmDialog({
    title: 'Setujui pengajuan buku?',
    text: `"${book.title}" dari ${book.petugas?.name || 'petugas'} akan ditambahkan ke perpustakaan.`,
    confirmButtonText: 'Ya, setujui',
  })) {
    router.post(route('admin.book-submissions.approve', book.id));
  }
};

const rejectSubmission = async (book) => {
  const reason = await promptDialog({
    title: 'Tolak pengajuan buku?',
    text: `Berikan alasan penolakan untuk "${book.title}".`,
    inputPlaceholder: 'Contoh: ISBN atau data buku belum valid.',
    confirmButtonText: 'Tolak pengajuan',
    required: true,
  });

  if (reason !== null) {
    router.post(route('admin.book-submissions.reject', book.id), { reason });
  }
};
</script>