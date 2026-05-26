<template>
  <div class="flex h-screen bg-slate-50 font-sans text-slate-900 antialiased selection:bg-orange-100 overflow-hidden">
    
    <transition
      enter-active-class="transition-opacity duration-300 ease-out"
      leave-active-class="transition-opacity duration-200 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isSidebarOpen" 
        @click="isSidebarOpen = false" 
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs lg:hidden"
      ></div>
    </transition>

    <aside 
      class="fixed inset-y-0 left-0 z-50 lg:static flex flex-col bg-white border-r border-slate-200/80 h-full shrink-0 transition-transform duration-300 ease-in-out lg:translate-x-0"
      :class="[
        isSidebarOpen ? 'translate-x-0 w-[280px]' : '-translate-x-full lg:translate-x-0',
        isSidebarCollapsed ? 'lg:w-[76px]' : 'lg:w-[280px]'
      ]"
    >
      <div class="h-20 flex items-center px-6 border-b border-slate-100 justify-between shrink-0">
        <Link href="/dashboard" class="flex items-center gap-3 group overflow-hidden">
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-black text-white transition-colors">
            <img src="/image/logo-smk.png" alt="Logo" class="h-5 w-5" onerror="this.style.display='none'">
          </span>
          <div v-show="!isSidebarCollapsed || isSidebarOpen" class="transition-opacity duration-200 whitespace-nowrap">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none mb-1">SKIE-LIBRARY</p>
            <span class="text-sm font-extrabold tracking-tight text-slate-900">Sirkulasi</span>
          </div>
        </Link>
        
        <button @click="isSidebarOpen = false" class="lg:hidden p-2 rounded-xl hover:bg-slate-100 text-slate-500 active:scale-95 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="flex-1 overflow-y-auto p-4 space-y-6 scrollbar-none">
        
        <div class="space-y-1">
          <p v-show="!isSidebarCollapsed || isSidebarOpen" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 px-3 py-2 leading-none">Menu Utama</p>
          
          <Link 
            href="/dashboard" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:bg-orange-50 hover:text-orange-500 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Dashboard Overview"
          >
            <i class="fas fa-th-large text-sm w-5 text-center"></i>
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Dashboard</span>
          </Link>

          <Link 
            href="/my-loans" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-500 border border-orange-100/50 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Rak Buku Saya"
          >
            <i class="fas fa-book-open text-sm w-5 text-center"></i>
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Rak Buku Saya</span>
          </Link>

          <Link 
            href="/my-queue" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:bg-orange-50 hover:text-orange-500 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Antrian Buku"
          >
            <i class="fas fa-hourglass-half text-sm w-5 text-center"></i>
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Antrian Buku</span>
          </Link>
        </div>

        <div class="border-t border-slate-100 my-4"></div>

        <div class="hidden lg:block space-y-1">
          <p v-show="!isSidebarCollapsed" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 px-3 py-2 leading-none">Filter Status</p>
          
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            class="flex w-full items-center rounded-xl p-3 text-left transition duration-150 group"
            :class="[
              activeTab === tab.key ? 'bg-orange-500 text-white shadow-xs' : 'text-slate-500 hover:bg-orange-50 hover:text-orange-600',
              isSidebarCollapsed ? 'justify-center' : 'justify-between'
            ]"
            :title="tab.label"
          >
            <div class="flex items-center gap-3">
              <span class="text-sm filter brightness-90 group-hover:brightness-110 flex items-center justify-center w-5">
                <i :class="getTabIcon(tab.key)"></i>
              </span>
              <span v-show="!isSidebarCollapsed" class="text-xs font-bold uppercase tracking-wider">{{ tab.label }}</span>
            </div>
            <span 
              v-if="tab.count !== null && !isSidebarCollapsed" 
              class="rounded-md px-1.5 py-0.5 text-[9px] font-black" 
              :class="activeTab === tab.key ? 'bg-white/20 text-white' : 'bg-orange-50 text-orange-600 border border-orange-100/40'"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>
      </div>

      <div class="p-4 border-t border-slate-100 bg-slate-50/50 shrink-0">
        <div class="flex items-center justify-between" :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }">
          <Link href="/profile" class="flex items-center gap-3 overflow-hidden group">
            <div class="h-9 w-9 rounded-full bg-orange-50 shrink-0 border border-orange-200 flex items-center justify-center font-bold text-orange-600">
              {{ stats.user_initials }}
            </div>
            <div v-show="!isSidebarCollapsed || isSidebarOpen" class="text-left whitespace-nowrap">
              <p class="text-xs font-bold text-slate-900 truncate max-w-[120px] group-hover:text-orange-600 transition-colors">{{ stats.user_name }}</p>
              <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate max-w-[120px] mt-0.5">{{ stats.user_role }}</p>
            </div>
          </Link>
          
          <button 
            v-show="!isSidebarCollapsed || isSidebarOpen"
            @click="handleLogout" 
            class="rounded-lg p-1.5 text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition"
            title="Keluar System"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
      
      <header class="sticky top-0 z-30 h-16 lg:h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
          <button 
            @click="toggleSidebar" 
            class="rounded-xl p-2 text-slate-500 hover:text-orange-600 hover:bg-orange-50 transition active:scale-95"
            aria-label="Toggle Menu"
          >
            <svg class="h-6 w-6 stroke-[2.3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <div class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-slate-400">
            <span class="hidden sm:inline">Sirkulasi</span>
            <span class="hidden sm:inline">/</span>
            <span class="text-slate-900 font-bold">Console Rak Saya</span>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden sm:flex items-center gap-6 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200/50">
            <div class="text-right">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Aktif Dipinjam</p>
              <p class="text-sm font-black text-slate-900 leading-none mt-0.5">{{ stats.total_active }} Item</p>
            </div>
            <div class="h-6 w-px bg-slate-200"></div>
            <div class="text-right">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Outstanding Denda</p>
              <p class="text-sm font-black text-rose-600 leading-none mt-0.5">Rp {{ formatNumber(stats.total_fines) }}</p>
            </div>
          </div>
          
          <Link 
            :href="route('profile.edit')" 
            class="h-8 w-8 rounded-full bg-orange-50 border border-orange-200 flex items-center justify-center lg:hidden font-bold text-orange-600 text-xs hover:bg-orange-100 transition-colors"
            title="Edit Profil"
          >
            {{ stats.user_initials }}
          </Link>
        </div>
      </header>

      <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 sm:py-10 max-w-5xl w-full mx-auto space-y-6 sm:space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 sm:pb-6 border-b border-slate-200">
          <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">{{ currentTab.title }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ currentTab.description }}</p>
          </div>
          <Link href="/dashboard" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 bg-white hover:bg-orange-50 border border-slate-200 rounded-xl text-xs font-black uppercase tracking-widest text-slate-700 hover:text-orange-600 transition shadow-3xs shrink-0">
            Kembali ke Dashboard
          </Link>
        </div>

        <div class="lg:hidden flex gap-2 pb-2 overflow-x-auto scrollbar-none border-b border-slate-100">
          <button
            v-for="tab in tabs"
            :key="`mobile-${tab.key}`"
            @click="activeTab = tab.key"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider whitespace-nowrap shrink-0 transition-all duration-200"
            :class="activeTab === tab.key ? 'bg-orange-600 text-white shadow-sm' : 'bg-white text-slate-500 border border-slate-200'"
          >
            <span>{{ tab.label }}</span>
            <span 
              class="px-1.5 py-0.5 rounded text-[10px]" 
              :class="activeTab === tab.key ? 'bg-white/20 text-white' : 'bg-orange-50 text-orange-600'"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>

        <div class="space-y-6">
          <div v-if="visibleLoans.length === 0" class="py-20 text-center bg-white rounded-2xl border border-slate-200/60 p-6 shadow-3xs">
            <div class="h-16 w-16 mx-auto bg-orange-50 border border-orange-100/50 rounded-2xl flex items-center justify-center text-xl text-orange-500 mb-4">
              <i class="fas fa-folder-open"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ currentTab.emptyTitle }}</h3>
            <p class="mt-1.5 text-xs font-medium text-slate-400 max-w-sm mx-auto leading-relaxed">{{ currentTab.emptyDescription }}</p>
          </div>

          <div v-else class="space-y-4">
            <LoanCard
              v-for="loan in visibleLoans"
              :key="loan.id"
              :loan="loan"
              @extend="extendLoan"
              @cancel="cancelLoan"
              @pay-fine="payFine"
            />
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import LoanCard from '@/Pages/User/Rak/Components/LoanCard.vue';

const props = defineProps({
  loans: {
    type: Array,
    default: () => []
  }
});

const page = usePage();
const activeTab = ref('active');

const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);

const finesList = computed(() => props.loans.filter(loan => {
  const fee = parseFloat(loan.late_fee || 0);
  return fee > 0 && loan.status !== 'returned';
}));

function handleResize() {
  if (window.innerWidth >= 1024) {
    isSidebarOpen.value = false;
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
  if (window.innerWidth < 1024) {
    isSidebarCollapsed.value = false;
  }
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

function toggleSidebar() {
  if (window.innerWidth >= 1024) {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
  } else {
    isSidebarOpen.value = !isSidebarOpen.value;
  }
}

function getTabIcon(key) {
  const icons = { 
    active: 'fas fa-folder', 
    pending: 'fas fa-clock', 
    history: 'fas fa-history', 
    fines: 'fas fa-credit-card' 
  };
  return icons[key] || 'fas fa-file-alt';
}

const activeLoans = computed(() => props.loans.filter(loan => loan.status === 'approved' || loan.status === 'borrowed'));
const pendingLoans = computed(() => props.loans.filter(loan => loan.status === 'pending'));
const historyLoans = computed(() => props.loans.filter(loan => ['returned', 'lost', 'rejected'].includes(loan.status)));

const stats = computed(() => {
  const user = page.props.auth?.user || {};
  return {
    total_active: activeLoans.value.length,
    total_pending: pendingLoans.value.length,
    total_fines: finesList.value.reduce((sum, loan) => {
      const fee = parseFloat(loan.late_fee);
      return sum + (isNaN(fee) ? 0 : fee);
    }, 0),
    user_name: user.name || 'Staff Admin',
    user_initials: user.name ? user.name.charAt(0).toUpperCase() : 'A',
    user_role: user.role || 'Pustakawan'
  };
});

const tabContent = {
  active: {
    title: 'Data Peminjaman Aktif',
    description: 'Daftar sirkulasi buku berstatus approved atau sedang dalam masa peminjaman fisik.',
    emptyTitle: 'Record aktif nihil',
    emptyDescription: 'Tidak mendeteksi peminjaman berjalan yang memerlukan monitoring berkala.'
  },
  pending: {
    title: 'Persetujuan Tertunda (Pending)',
    description: 'Antrian entri pengajuan sirkulasi masuk yang membutuhkan verifikasi pustakawan.',
    emptyTitle: 'Queue bersih',
    emptyDescription: 'Seluruh pengajuan sirkulasi tuntas diproses oleh sistem.'
  },
  history: {
    title: 'Arsip / Log Riwayat Sirkulasi',
    description: 'Seluruh jejak historis peminjaman buku yang telah selesai (Returned / Rejected).',
    emptyTitle: 'Log kosong',
    emptyDescription: 'Belum ada rekaman sirkulasi lampau yang diarsipkan ke dalam database.'
  },
  fines: {
    title: 'Manajemen Tagihan Denda',
    description: 'Daftar akumulasi denda tarif keterlambatan buku yang belum diselesaikan.',
    emptyTitle: 'Invoice nihil',
    emptyDescription: 'Tidak ditemukan tagihan denda menggantung pada akun ini.'
  }
};

const tabs = computed(() => [
  { key: 'active', label: 'Aktif Berjalan', count: stats.value.total_active },
  { key: 'pending', label: 'Menunggu Verifikasi', count: stats.value.total_pending },
  { key: 'history', label: 'Log Riwayat', count: historyLoans.value.length },
  { key: 'fines', label: 'Tagihan Denda', count: finesList.value.length }
]);

const currentTab = computed(() => tabContent[activeTab.value]);

const visibleLoans = computed(() => {
  switch (activeTab.value) {
    case 'pending': return pendingLoans.value;
    case 'history': return historyLoans.value;
    case 'fines': return finesList.value;
    default: return activeLoans.value;
  }
});

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

function extendLoan(loan) {
  Swal.fire({
    title: 'Ajukan Perpanjangan Peminjaman',
    html: `
      <div class="text-left space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Buku</label>
          <input type="text" value="${loan.book?.title || 'Unknown'}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-700 text-sm font-medium" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Permintaan Perpanjangan (Hari)</label>
          <select id="requestedDays" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-slate-700 text-sm">
            <option value="3">3 Hari</option>
            <option value="7" selected>7 Hari</option>
            <option value="14">14 Hari</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase mb-2">Alasan (Opsional)</label>
          <textarea id="reason" placeholder="Jelaskan alasan perpanjangan Anda..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-slate-700 text-sm h-20 resize-none"></textarea>
        </div>
      </div>
    `,
    icon: 'info',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Kirim Permintaan',
    cancelButtonText: 'Batal',
    didOpen: () => {
      const dialog = Swal.getHtmlContainer();
      if (dialog) {
        dialog.querySelector('#requestedDays').focus();
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const requestedDays = document.getElementById('requestedDays').value;
      const reason = document.getElementById('reason').value || '';
      
      router.post(`/borrow/${loan.id}/extend`, {
        requested_days: parseInt(requestedDays),
        reason: reason
      }, {
        onSuccess: () => {
          Swal.fire({ 
            title: 'Success', 
            text: 'Permintaan perpanjangan telah dikirim. Silakan tunggu persetujuan petugas.', 
            icon: 'success', 
            confirmButtonColor: '#f97316' 
          });
        },
        onError: (errors) => {
          Swal.fire({
            title: 'Gagal',
            text: Object.values(errors)[0] || 'Terjadi kesalahan saat mengirim permintaan',
            icon: 'error',
            confirmButtonColor: '#f97316'
          });
        }
      });
    }
  });
}

function cancelLoan(loan) {
  Swal.fire({
    title: 'Drop Pengajuan?',
    html: `<p class="text-xs text-slate-500">Batalkan antrian otorisasi buku <strong>${loan.book?.title || 'Unknown'}</strong>?</p>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Drop Request',
    cancelButtonText: 'Keep'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/borrow/${loan.id}`, {
        onSuccess: () => {
          Swal.fire({ title: 'Dropped', text: 'Pengajuan sukses dihapus.', icon: 'success', confirmButtonColor: '#ea580c' });
        }
      });
    }
  });
}

function payFine(fine) {
  if (fine.is_damaged === null || fine.is_damaged === undefined) {
    router.visit(`/fines/${fine.id}/report-damage`);
    return;
  }

  if (fine.payment_status === 'pending') {
    router.visit(`/fines/${fine.id}/waiting-payment`);
    return;
  }

  router.visit(`/fines/${fine.id}/pay`);
}

function handleLogout() {
  Swal.fire({
    title: 'Terminate Session?',
    text: 'Keluar dari hak akses konsol manajemen sirkulasi.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Sign Out',
    cancelButtonText: 'Stay'
  }).then((result) => {
    if (result.isConfirmed) router.post(route('logout'));
  });
}
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>