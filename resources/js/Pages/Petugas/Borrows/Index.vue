<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen pb-12 relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Kelola Peminjaman</h1>
            <p class="text-slate-500 text-sm">Sistem manajemen sirkulasi buku perpustakaan</p>
          </div>
          <div class="flex items-center gap-6">
            <div class="text-right border-r pr-6 border-slate-200 hidden sm:block">
              <p class="text-sm font-bold text-slate-700">{{ formattedDate }}</p>
              <p class="text-[10px] text-slate-400 uppercase tracking-[0.2em] font-black">Waktu Server</p>
            </div>
            <button @click="showLoanModal = true" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              Peminjaman Baru
            </button>
          </div>
        </div>
      </header>

      <div class="px-10 py-8 max-w-[1400px] mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Peminjaman Aktif</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.activeCount }}</h3>
              <div class="p-3 rounded-2xl bg-indigo-50 text-indigo-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Terindikasi Terlambat</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.overdueCount }}</h3>
              <div class="p-3 rounded-2xl bg-orange-50 text-orange-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Durasi Rata-rata</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.avgDuration }} <span class="text-xs font-medium text-slate-400">hari</span></h3>
              <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-all group">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] mb-3">Total Peminjam</p>
            <div class="flex items-end justify-between">
              <h3 class="text-3xl font-black text-slate-800">{{ stats.totalBorrowers }}</h3>
              <div class="p-3 rounded-2xl bg-rose-50 text-rose-600 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292M15 21H3a6 6 0 016-6h6a6 6 0 016 6v0"/></svg>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/60 p-4 mb-6 flex flex-wrap items-center gap-4 shadow-sm">
          <div class="relative flex-1 min-w-[300px]">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input v-model="searchQuery" type="text" placeholder="Cari peminjam atau judul buku..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-slate-200 transition-all text-sm">
          </div>
          <select v-model="selectedStatus" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 focus:ring-2 focus:ring-slate-200 transition-all">
            <option value="">Semua Status</option>
            <option value="borrowed">Aktif</option>
            <option value="returned">Kembali</option>
            <option value="overdue">Terlambat</option>
          </select>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Peminjam</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Informasi Buku</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Batas Waktu</th>
                  <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                  <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="loan in filteredBorrows" :key="loan.id" class="hover:bg-slate-50/50 transition-colors group">
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xs border border-indigo-100">
                        {{ loan.member_name?.[0]?.toUpperCase() }}
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-800">{{ loan.member_name }}</p>
                        <p class="text-[11px] text-slate-400">{{ loan.member_id }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <p class="text-sm font-bold text-slate-700">{{ loan.book_title }}</p>
                    <p class="text-xs text-slate-400">{{ loan.book_isbn }}</p>
                  </td>
                  <td class="px-8 py-5 text-sm font-medium text-slate-600">
                    {{ formatDate(loan.due_date) }}
                  </td>
                  <td class="px-8 py-5">
                    <span :class="getStatusClass(loan.status)" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider">
                      {{ getStatusText(loan.status) }}
                    </span>
                  </td>
                  <td class="px-8 py-5 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button v-if="loan.status === 'borrowed'" @click="returnBook(loan)" class="p-2 hover:bg-emerald-50 text-emerald-600 rounded-xl transition-all border border-transparent hover:border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </button>
                      <button class="p-2 hover:bg-slate-100 text-slate-400 rounded-xl transition-all border border-transparent hover:border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>

    <Transition name="fade">
      <div v-if="showLoanModal" class="fixed inset-0 z-[60] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200">
          <div class="px-10 pt-10 pb-4">
            <h3 class="text-xl font-black text-slate-900">Peminjaman Baru</h3>
            <p class="text-sm text-slate-500 mt-1">Lengkapi data sirkulasi buku</p>
          </div>
          <form @submit.prevent="createLoan" class="p-10 space-y-6">
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Anggota Perpustakaan</label>
              <select v-model="loanForm.user_id" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Pilih Anggota</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Koleksi Buku</label>
              <select v-model="loanForm.book_id" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-indigo-500/20">
                <option value="">Cari Judul Buku</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Pengembalian</label>
              <input type="date" v-model="loanForm.due_date" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div class="flex gap-4 pt-4">
              <button type="button" @click="showLoanModal = false" class="flex-1 px-6 py-4 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-2xl transition-all">Batal</button>
              <button type="submit" class="flex-1 px-6 py-4 text-sm font-bold text-white bg-slate-900 hover:bg-indigo-600 rounded-2xl transition-all shadow-xl shadow-slate-200">Simpan Data</button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
  stats: { type: Object, default: () => ({}) },
  borrows: { type: Array, default: () => [] },
});

const showLoanModal = ref(false);
const searchQuery = ref('');
const selectedStatus = ref('');
const formattedDate = ref('');
const loanForm = ref({ user_id: '', book_id: '', due_date: '' });

const filteredBorrows = computed(() => {
  return props.borrows.filter(loan => {
    const matchesSearch = loan.member_name?.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                         loan.book_title?.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = selectedStatus.value ? loan.status === selectedStatus.value : true;
    return matchesSearch && matchesStatus;
  });
});

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const getStatusClass = (status) => {
  switch(status) {
    case 'borrowed': return 'bg-indigo-50 text-indigo-600 border border-indigo-100';
    case 'returned': return 'bg-emerald-50 text-emerald-600 border border-emerald-100';
    case 'overdue': return 'bg-rose-50 text-rose-600 border border-rose-100';
    default: return 'bg-slate-50 text-slate-600';
  }
};

const getStatusText = (status) => {
  switch(status) {
    case 'borrowed': return 'Aktif';
    case 'returned': return 'Kembali';
    case 'overdue': return 'Terlambat';
    default: return status;
  }
};

const createLoan = () => {
  console.log('Create loan:', loanForm.value);
  showLoanModal.value = false;
};

const returnBook = (loan) => {
  console.log('Return book:', loan);
};

onMounted(() => {
  formattedDate.value = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
