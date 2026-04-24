<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900">Kelola Akun</h1>
    <p class="mt-2 text-gray-600">Kelola akun petugas dan user di sini.</p>

    <!-- Success Message -->
    <div v-if="$page.props.success" class="mt-4 rounded-lg bg-green-50 p-4 text-green-800">
      {{ $page.props.success }}
    </div>

    <!-- Error Message -->
    <div v-if="$page.props.error" class="mt-4 rounded-lg bg-red-50 p-4 text-red-800">
      {{ $page.props.error }}
    </div>

    <!-- Add Petugas Form -->
    <div class="mt-6 rounded-lg bg-white p-6 shadow-sm border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Tambah Akun Petugas</h2>
      <p class="mt-1 text-sm text-gray-500">Buat akun petugas baru. Password akan dibuat otomatis.</p>
      
      <form @submit.prevent="submitPetugas" class="mt-4 flex gap-4">
        <input
          v-model="petugasForm.name"
          type="text"
          placeholder="Nama Petugas"
          class="flex-1 rounded-lg border border-gray-300 px-4 py-2"
          required
        />
        <input
          v-model="petugasForm.email"
          type="email"
          placeholder="Email Petugas"
          class="flex-1 rounded-lg border border-gray-300 px-4 py-2"
          required
        />
        <button
          type="submit"
          class="rounded-lg bg-gray-900 px-6 py-2 font-medium text-white hover:bg-gray-800"
          :disabled="petugasForm.processing"
        >
          {{ petugasForm.processing ? 'Menyimpan...' : 'Tambah Petugas' }}
        </button>
      </form>
    </div>

    <!-- Petugas List -->
    <div class="mt-6 rounded-lg bg-white p-6 shadow-sm border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Daftar Petugas</h2>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-left text-sm text-gray-700">
          <thead class="border-b bg-gray-50 text-gray-500">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="person in petugas" :key="person.id">
              <td class="px-4 py-3 font-medium">{{ person.name }}</td>
              <td class="px-4 py-3">{{ person.email }}</td>
              <td class="px-4 py-3">
                <button
                  @click="deleteUser(person)"
                  class="text-red-600 hover:text-red-800"
                >
                  Hapus
                </button>
              </td>
            </tr>
            <tr v-if="petugas.length === 0">
              <td colspan="3" class="px-4 py-4 text-center text-gray-500">Belum ada petugas.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- User List -->
    <div class="mt-6 rounded-lg bg-white p-6 shadow-sm border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Daftar User (Siswa)</h2>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-left text-sm text-gray-700">
          <thead class="border-b bg-gray-50 text-gray-500">
            <tr>
              <th class="px-4 py-3">Nama</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">NISN</th>
              <th class="px-4 py-3">Kelas</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id">
              <td class="px-4 py-3 font-medium">{{ user.name }}</td>
              <td class="px-4 py-3">{{ user.email }}</td>
              <td class="px-4 py-3">{{ user.nisn || '-' }}</td>
              <td class="px-4 py-3">{{ user.kelas || '-' }}</td>
              <td class="px-4 py-3">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800': user.status === 'pending',
                    'bg-green-100 text-green-800': user.status === 'approved',
                    'bg-red-100 text-red-800': user.status === 'rejected',
                  }"
                  class="rounded-full px-2 py-1 text-xs font-medium"
                >
                  {{ user.status === 'pending' ? 'Menunggu' : user.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div v-if="user.status === 'pending'" class="flex gap-2">
                  <button
                    @click="approveUser(user)"
                    class="text-green-600 hover:text-green-800"
                  >
                    Setuju
                  </button>
                  <button
                    @click="rejectUser(user)"
                    class="text-red-600 hover:text-red-800"
                  >
                    Tolak
                  </button>
                </div>
                <button
                  v-else
                  @click="deleteUser(user)"
                  class="text-red-600 hover:text-red-800"
                >
                  Hapus
                </button>
              </td>
            </tr>
            <tr v-if="users.length === 0">
              <td colspan="6" class="px-4 py-4 text-center text-gray-500">Belum ada user.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
  petugas: { type: Array, default: () => [] },
  users: { type: Array, default: () => [] },
});

const petugasForm = useForm({
  name: '',
  email: '',
});

const submitPetugas = () => {
  petugasForm.post('/admin/accounts/petugas', {
    onSuccess: () => {
      petugasForm.reset();
    },
  });
};

const approveUser = (user) => {
  if (confirm('Setuju kan akun ini?')) {
    router.post(`/admin/accounts/${user.id}/approve`);
  }
};

const rejectUser = (user) => {
  if (confirm('Tolak akun ini?')) {
    router.post(`/admin/accounts/${user.id}/reject`);
  }
};

const deleteUser = (user) => {
  if (confirm('Hapus akun ini?')) {
    router.delete(`/admin/accounts/${user.id}`);
  }
};

import { router } from '@inertiajs/vue3';
</script>