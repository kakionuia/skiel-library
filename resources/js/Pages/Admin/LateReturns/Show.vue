<template>
  <AuthenticatedLayout title="Detail Penunggakan">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-6 flex items-center gap-4">
          <Link href="/admin/late-returns" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <h1 class="text-2xl font-bold text-gray-900">Detail Penunggakan</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- User Info -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-2xl">
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
                  <div class="flex justify-between py-2">
                    <span class="text-sm text-gray-500">Dibatasi</span>
                    <span :class="user.borrow_restricted ? 'text-red-600' : 'text-green-600'" class="text-sm font-medium">
                      {{ user.borrow_restricted ? 'Ya' : 'Tidak' }}
                    </span>
                  </div>
                </div>

                <div class="mt-6 w-full flex flex-col gap-2">
                  <button v-if="user.borrow_restricted" @click="unrestrictUser" class="w-full py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Cabut Pembatasan
                  </button>
                  <button v-else @click="restrictUser" class="w-full py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Batasi Peminjaman
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Late Books -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-gray-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Buku yang Ditunggakan</h3>
              <div class="space-y-4">
                <div v-for="borrow in lateBorrows" :key="borrow.id" class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                      <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ borrow.book?.title }}</p>
                      <p class="text-sm text-gray-500">Jatuh tempo: {{ borrow.due_date }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-red-600 font-medium">{{ borrow.late_days || 0 }} hari terlambat</p>
                    <p class="text-sm font-bold text-orange-600">Rp {{ formatNumber(borrow.late_fee) }}</p>
                  </div>
                </div>
                <div v-if="lateBorrows.length === 0" class="text-center py-8 text-gray-500">
                  Tidak ada penunggakan.
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
import { confirmDialog, dangerConfirm } from '@/Utils/sweetAlert';

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  lateBorrows: { type: Array, default: () => [] },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0);
};

const restrictUser = async () => {
  if (await dangerConfirm({
    title: 'Batasi peminjaman akun ini?',
    text: `${props.user.name} tidak akan bisa melakukan peminjaman baru.`,
    confirmButtonText: 'Ya, batasi',
  })) {
    router.post(`/admin/accounts/${props.user.id}/restrict`);
  }
};

const unrestrictUser = async () => {
  if (await confirmDialog({
    title: 'Cabut pembatasan peminjaman?',
    text: `${props.user.name} akan bisa melakukan peminjaman kembali.`,
    confirmButtonText: 'Ya, cabut',
  })) {
    router.post(`/admin/accounts/${props.user.id}/unrestrict`);
  }
};

</script>
