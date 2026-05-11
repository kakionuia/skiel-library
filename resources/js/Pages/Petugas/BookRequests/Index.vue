<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen pb-12">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Permintaan Buku Member</h1>
            <p class="text-slate-500 text-sm">Kelola pengajuan koleksi baru dari anggota perpustakaan</p>
          </div>
          <div class="px-4 py-2 bg-orange-50 rounded-xl border border-orange-100">
            <p class="text-xs font-bold text-orange-600 uppercase tracking-wider">{{ pendingCount }} Menunggu Approval</p>
          </div>
        </div>
      </header>

      <div class="px-10 py-8 max-w-[1400px] mx-auto">
        <div class="flex gap-3 mb-8">
          <button @click="filterStatus = ''" :class="filterButtonClass('')" class="px-6 py-3 rounded-xl font-bold text-sm transition-all">
            <span v-if="filterStatus === ''" class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1H3zm0 4a1 1 0 011-1h12a1 1 0 011 1H3zm0 4a1 1 0 011-1h12a1 1 0 011 1H3zm0 4a1 1 0 011-1h12a1 1 0 011 1H3z"/></svg>
              Semua
            </span>
            <span v-else>Semua</span>
          </button>
          <button @click="filterStatus = 'pending'" :class="filterButtonClass('pending')" class="px-6 py-3 rounded-xl font-bold text-sm transition-all">
            <span v-if="filterStatus === 'pending'" class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
              Menunggu
            </span>
            <span v-else>Menunggu</span>
          </button>
          <button @click="filterStatus = 'approved'" :class="filterButtonClass('approved')" class="px-6 py-3 rounded-xl font-bold text-sm transition-all">
            <span v-if="filterStatus === 'approved'" class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
              Disetujui
            </span>
            <span v-else>Disetujui</span>
          </button>
        </div>

        <div class="flex items-center gap-3 mb-8 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
          <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input v-model="searchQuery" type="text" placeholder="Cari nama member atau judul buku..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-slate-200 text-sm">
          </div>
        </div>

        <div class="space-y-3">
          <div v-for="request in filteredRequests" :key="request.id" class="bg-white rounded-2xl border border-slate-200 p-7 shadow-sm hover:shadow-md transition-all group">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
              <div class="md:col-span-5 flex gap-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center font-bold text-sm border border-orange-100 flex-shrink-0">
                  {{ request.user_name?.[0]?.toUpperCase() }}
                </div>
                <div class="flex-1">
                  <h3 class="text-sm font-bold text-slate-800">{{ request.user_name }}</h3>
                  <p class="text-[11px] text-slate-400">{{ formatDate(request.created_at) }}</p>
                  <p class="text-xs text-slate-600 mt-2 italic">
                    <span class="font-semibold text-slate-700">"{{ request.reason }}"</span>
                  </p>
                </div>
              </div>

              <div class="md:col-span-4">
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Informasi Buku</p>
                  <p class="text-sm font-bold text-slate-800">{{ request.title }}</p>
                  <p class="text-xs text-slate-500">{{ request.author }}</p>
                  <p class="text-[11px] text-slate-400 mt-2 font-mono">{{ request.isbn || 'ISBN tidak tersedia' }}</p>
                </div>
              </div>

              <div class="md:col-span-3 flex flex-col gap-3 justify-between">
                <div class="text-right">
                  <span :class="getStatusBadgeClass(request.status)" class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider inline-block">
                    {{ getStatusText(request.status) }}
                  </span>
                </div>

                <div class="flex gap-2">
                  <button v-if="request.status === 'pending'" @click="approveRequest(request)" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                    Setujui
                  </button>
                  <button v-if="request.status === 'pending'" @click="rejectRequest(request)" class="px-4 py-2.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg hover:bg-rose-100 hover:text-rose-600 transition-all">
                    Tolak
                  </button>
                  <button v-if="request.status === 'approved'" class="flex-1 px-4 py-2.5 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition-all shadow-lg shadow-orange-100">
                    Tandai Sudah Dibeli
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="filteredRequests.length === 0" class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-slate-500 text-sm font-medium">Tidak ada permintaan buku untuk ditampilkan</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { confirmDialog, successAlert } from '@/Utils/sweetAlert';
import { ref, computed } from 'vue';

const props = defineProps({
  bookRequests: { type: Array, default: () => [] },
});

const filterStatus = ref('');
const searchQuery = ref('');

const filteredRequests = computed(() => {
  return props.bookRequests.filter(request => {
    const matchesSearch = request.user_name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         request.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         request.author?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = filterStatus.value ? request.status === filterStatus.value : true;
    return matchesSearch && matchesStatus;
  });
});

const pendingCount = computed(() => {
  return props.bookRequests.filter(r => r.status === 'pending').length;
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'pending': return 'bg-orange-50 text-orange-600 border border-orange-100';
    case 'approved': return 'bg-emerald-50 text-emerald-600 border border-emerald-100';
    case 'rejected': return 'bg-rose-50 text-rose-600 border border-rose-100';
    default: return 'bg-slate-50 text-slate-600';
  }
};

const getStatusText = (status) => {
  switch(status) {
    case 'pending': return 'Menunggu';
    case 'approved': return 'Disetujui';
    case 'rejected': return 'Ditolak';
    default: return status;
  }
};

const filterButtonClass = (status) => {
  return filterStatus.value === status
    ? 'bg-slate-900 text-white border border-slate-700 shadow-lg shadow-slate-200'
    : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100';
};

async function approveRequest(request) {
  const result = await confirmDialog({
    title: 'Setujui Permintaan Buku?',
    html: `<p class="text-sm mb-3">Permintaan buku <strong>${request.title}</strong> dari <strong>${request.user_name}</strong> akan disetujui.</p>`,
    confirmButtonText: 'Ya, Setujui',
  });

  if (result) {
    router.post(route('petugas.book-requests.approve', request.id), {}, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Permintaan berhasil disetujui.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menyetujui permintaan.',
          icon: 'error',
        });
      }
    });
  }
}

async function rejectRequest(request) {
  const result = await confirmDialog({
    title: 'Tolak Permintaan Buku?',
    html: `<p class="text-sm">Permintaan buku <strong>${request.title}</strong> dari <strong>${request.user_name}</strong> akan ditolak.</p>`,
    confirmButtonText: 'Ya, Tolak',
  });

  if (result) {
    router.post(route('petugas.book-requests.reject', request.id), {}, {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Permintaan berhasil ditolak.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal menolak permintaan.',
          icon: 'error',
        });
      }
    });
  }
}
</script>
