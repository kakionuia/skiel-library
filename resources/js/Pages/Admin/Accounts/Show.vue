<template>
  <AuthenticatedLayout title="Detail Anggota">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex items-center gap-4">
          <Link href="/admin/accounts" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Detail Anggota</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Profile Info -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                  {{ user.name.charAt(0) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ user.name }}</h2>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
                
                <div class="mt-4 w-full space-y-3">
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">NISN</span>
                    <span class="text-sm font-medium">{{ user.nisn || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">Kelas</span>
                    <span class="text-sm font-medium">{{ user.kelas || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">No. HP</span>
                    <span class="text-sm font-medium">{{ user.phone || '-' }}</span>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-500">Status</span>
                    <span :class="{
                      'bg-green-100 text-green-800': user.status === 'approved',
                      'bg-yellow-100 text-yellow-800': user.status === 'pending',
                      'bg-red-100 text-red-800': user.status === 'rejected',
                    }" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ user.status }}
                    </span>
                  </div>
                  <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-500">Dibatasi</span>
                    <span :class="user.borrow_restricted ? 'text-red-600' : 'text-green-600'" class="text-sm font-medium">
                      {{ user.borrow_restricted ? 'Ya' : 'Tidak' }}
                    </span>
                  </div>
                </div>

                <div class="mt-6 w-full flex flex-col gap-2">
                  <button v-if="user.status === 'approved' && user.borrow_restricted" @click="unrestrictUser" class="w-full py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Cabut Pembatasan
                  </button>
                  <button v-if="user.status === 'approved' && !user.borrow_restricted" @click="restrictUser" class="w-full py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Batasi Peminjaman
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Borrows -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Peminjaman (3 Buku Terbaru)</h3>
              <div class="space-y-4">
                <div v-for="borrow in recentBorrows" :key="borrow.id" class="flex items-center justify-between p-4 border rounded-lg">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ borrow.book?.title || 'Buku' }}</p>
                      <p class="text-sm text-gray-500">Dipinjam: {{ borrow.borrow_date }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <span :class="{
                      'bg-green-100 text-green-800': borrow.status === 'returned',
                      'bg-yellow-100 text-yellow-800': borrow.status === 'borrowed',
                      'bg-red-100 text-red-800': borrow.status === 'overdue',
                    }" class="px-2 py-1 rounded-full text-xs font-medium">
                      {{ borrow.status }}
                    </span>
                    <p v-if="borrow.late_fee > 0" class="text-sm text-red-600 mt-1">Denda: Rp {{ borrow.late_fee }}</p>
                  </div>
                </div>
                <div v-if="recentBorrows.length === 0" class="text-center py-8 text-gray-500">
                  Belum ada peminjaman.
                </div>
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

defineProps({
  user: { type: Object, default: () => ({}) },
  recentBorrows: { type: Array, default: () => [] },
});

const restrictUser = () => {
  if (confirm('Batasi peminjaman akun ini?')) {
    router.post(`/admin/accounts/${props.user.id}/restrict`);
  }
};

const unrestrictUser = () => {
  if (confirm('Cabut pembatasan peminjaman?')) {
    router.post(`/admin/accounts/${props.user.id}/unrestrict`);
  }
};

const props = defineProps({
  user: Object,
  recentBorrows: Array
});
</script>