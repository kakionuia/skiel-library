<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
    <p class="mt-2 text-gray-600">Selamat datang di panel administrasi.</p>

    <div class="mt-8 grid gap-6 md:grid-cols-3">
      <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
        <div class="text-sm font-medium text-gray-500">Total Petugas</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ petugasCount }}</div>
      </div>
      <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
        <div class="text-sm font-medium text-gray-500">Total User</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ userCount }}</div>
      </div>
      <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
        <div class="text-sm font-medium text-gray-500">Menunggu Persetujuan</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">{{ pendingCount }}</div>
      </div>
    </div>

    <div class="mt-8 rounded-lg bg-white p-6 shadow-sm border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Menu Admin</h2>
      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <Link href="/admin/accounts" class="rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
          <div class="font-medium text-gray-900">Kelola Akun</div>
          <div class="mt-1 text-sm text-gray-500">Lihat dan kelola akun petugas dan user</div>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  petugasCount: { type: Number, default: 0 },
  userCount: { type: Number, default: 0 },
  pendingCount: { type: Number, default: 0 },
});
</script>