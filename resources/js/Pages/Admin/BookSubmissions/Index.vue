<template>
  <AuthenticatedLayout title="Pengajuan Buku">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengajuan Buku Petugas</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola pengajuan penambahan buku dari petugas. Tinjau dan setujui untuk menambahkan buku ke perpustakaan.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Buku</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Petugas</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Jumlah</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="book in submissions.data" :key="book.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-gray-900">{{ book.title }}</div>
                    <div class="text-xs text-gray-500">{{ book.author }} • {{ book.year || '-' }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ book.isbn || 'No ISBN' }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ book.petugas?.name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ book.petugas?.email }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ book.quantity }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700 ring-1 ring-amber-200': book.status === 'pending',
                      'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200': book.status === 'approved',
                      'bg-rose-100 text-rose-700 ring-1 ring-rose-200': book.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight">
                      {{ book.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ formatDate(book.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <div v-if="book.status === 'pending'" class="flex justify-end gap-2">
                      <Link :href="route('admin.book-submissions.show', book.id)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                        Detail
                      </Link>
                      <button @click="approveSubmission(book)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                        Setuju
                      </button>
                      <button @click="rejectSubmission(book)" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                        Tolak
                      </button>
                    </div>
                    <Link v-else :href="route('admin.book-submissions.show', book.id)" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                      Lihat Detail
                    </Link>
                  </td>
                </tr>
                <tr v-if="submissions.data?.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada pengajuan buku yang tertunda.</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="submissions.data?.length" class="px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Menampilkan {{ submissions.data.length }} dari {{ submissions.total }} data</p>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

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

const approveSubmission = (book) => {
  if (confirm(`Setujui pengajuan buku "${book.title}" dari ${book.petugas?.name}? Buku akan ditambahkan ke perpustakaan.`)) {
    router.post(route('admin.book-submissions.approve', book.id));
  }
};

const rejectSubmission = (book) => {
  const reason = prompt('Masukkan alasan penolakan:');
  if (reason !== null) {
    router.post(route('admin.book-submissions.reject', book.id), { reason });
  }
};
</script>
