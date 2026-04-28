<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-bold leading-tight text-gray-800">Manajemen Akun</h2>
    </template>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto space-y-8">
        
        <transition enter-active-class="transition ease-out duration-300" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100">
          <div v-if="$page.props.flash?.success" class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="text-sm font-medium">{{ $page.props.flash.success }}</span>
          </div>
        </transition>

        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="p-6 border-b border-gray-50 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">Tambah Petugas Baru</h3>
            <p class="text-sm text-gray-500">Petugas yang ditambahkan akan memiliki akses untuk mengelola data harian.</p>
          </div>
          <div class="p-6">
            <form @submit.prevent="submitPetugas" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
              <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Nama Lengkap</label>
                <input v-model="petugasForm.name" type="text" placeholder="Masukkan nama..." class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm" required />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Alamat Email</label>
                <input v-model="petugasForm.email" type="email" placeholder="nama@email.com" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm" required />
              </div>
              <div>
                <label class="block text-xs font-bold uppercase text-gray-500 mb-1 ml-1">Password</label>
                <input v-model="petugasForm.password" type="text" placeholder="Kosongkan untuk auto" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all text-sm" />
              </div>
              <button type="submit" :disabled="petugasForm.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl transition-all shadow-sm shadow-indigo-200 disabled:opacity-50 flex items-center justify-center gap-2">
                <svg v-if="!petugasForm.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                {{ petugasForm.processing ? 'Memproses...' : 'Simpan Petugas' }}
              </button>
            </form>
          </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
          
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
              <h3 class="font-bold text-gray-900">Daftar Petugas</h3>
              <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">{{ petugas.length }} Orang</span>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                  <tr>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-wider text-gray-400">Identitas</th>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="person in petugas" :key="person.id" class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase">{{ person.name.charAt(0) }}</div>
                        <div>
                          <div class="text-sm font-semibold text-gray-900">{{ person.name }}</div>
                          <div class="text-xs text-gray-500">{{ person.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex justify-end gap-2">
                        <Link :href="`/admin/accounts/petugas/${person.id}`" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Lihat Detail">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </Link>
                        <button @click="deleteUser(person)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
              <h3 class="font-bold text-gray-900">Validasi Anggota</h3>
              <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold">Total: {{ anggota.length }}</span>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black uppercase tracking-wider text-gray-400">
                  <tr>
                    <th class="px-6 py-3">Nama & Sekolah</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="user in anggota" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                      <div class="font-semibold text-gray-900">{{ user.name }}</div>
                      <div class="text-[11px] text-gray-500 uppercase">{{ user.kelas || '-' }} • {{ user.nisn || '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <span :class="{
                        'bg-amber-100 text-amber-700 ring-1 ring-amber-200': user.status === 'pending',
                        'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200': user.status === 'approved',
                        'bg-rose-100 text-rose-700 ring-1 ring-rose-200': user.status === 'rejected',
                      }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight">
                        {{ user.status === 'pending' ? 'Waiting' : user.status === 'approved' ? 'Active' : 'Rejected' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                      <div v-if="user.status === 'pending'" class="flex justify-end gap-2">
                        <button @click="approveUser(user)" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded-lg shadow-sm transition-all" title="Setuju">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </button>
                        <button @click="rejectUser(user)" class="bg-rose-500 hover:bg-rose-600 text-white p-1.5 rounded-lg shadow-sm transition-all" title="Tolak">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                      </div>
                      <div v-else class="flex justify-end gap-2">
                        <Link :href="`/admin/accounts/anggota/${user.id}`" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Lihat Detail">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </Link>
                        <button @click="deleteUser(user)" class="p-2 text-gray-300 hover:text-rose-600 transition-all" title="Hapus">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; 
import { useForm, router, Link } from '@inertiajs/vue3';

defineProps({
  petugas: { type: Array, default: () => [] },
  anggota: { type: Array, default: () => [] },
});

const petugasForm = useForm({
  name: '',
  email: '',
  password: '',
});

const submitPetugas = () => {
  petugasForm.post('/admin/accounts/petugas', {
    onSuccess: () => {
      petugasForm.reset();
    },
  });
};

const approveUser = (user) => {
  if (confirm('Verifikasi akun ini?')) {
    router.post(`/admin/accounts/${user.id}/approve`);
  }
};

const rejectUser = (user) => {
  if (confirm('Tolak akses akun ini?')) {
    router.post(`/admin/accounts/${user.id}/reject`);
  }
};

const deleteUser = (user) => {
  if (confirm('Hapus akun ini secara permanen?')) {
    router.delete(`/admin/accounts/${user.id}`);
  }
};
</script>