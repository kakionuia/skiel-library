<template>
  <div class="flex h-screen bg-slate-50 font-sans text-slate-900 antialiased selection:bg-orange-100 overflow-hidden">
    
    <!-- Mobile overlay -->
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
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
      ></div>
    </transition>

    <!-- Sidebar -->
    <aside 
      class="fixed inset-y-0 left-0 z-50 lg:static flex flex-col bg-white border-r border-slate-200/80 h-full shrink-0 transition-transform duration-300 ease-in-out lg:translate-x-0"
      :class="[
        isSidebarOpen ? 'translate-x-0 w-[280px]' : '-translate-x-full lg:translate-x-0',
        isSidebarCollapsed ? 'lg:w-[76px]' : 'lg:w-[280px]'
      ]"
    >
      <!-- Sidebar Header -->
      <div class="h-20 flex items-center px-6 border-b border-slate-100 justify-between shrink-0">
        <Link href="/dashboard" class="flex items-center gap-3 group overflow-hidden">
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-orange-600 text-sm font-black text-white group-hover:bg-orange-700 transition-colors">S</span>
          <div v-show="!isSidebarCollapsed || isSidebarOpen" class="transition-opacity duration-200 whitespace-nowrap">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none mb-1">SKIEL LIBRARY</p>
            <span class="text-sm font-extrabold tracking-tight text-slate-900">Sirkulasi</span>
          </div>
        </Link>
        
        <!-- Close button for mobile -->
        <button @click="isSidebarOpen = false" class="lg:hidden p-2 rounded-xl hover:bg-slate-100 text-slate-500 active:scale-95 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Sidebar Navigation -->
      <div class="flex-1 overflow-y-auto p-4 space-y-6 scrollbar-none">
        
        <!-- Main Menu -->
        <div class="space-y-1">
          <p v-show="!isSidebarCollapsed || isSidebarOpen" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 px-3 py-2 leading-none">Menu Utama</p>
          
          <Link 
            href="/dashboard" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:bg-orange-50 hover:text-orange-600 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Dashboard"
          >
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Dashboard</span>
          </Link>

          <Link 
            href="/my-loans" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-100/50 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Rak Buku Saya"
          >
            <span class="text-lg shrink-0">📖</span>
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Rak Buku Saya</span>
          </Link>

          <Link 
            href="/my-queue" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:bg-orange-50 hover:text-orange-600 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Antrian Buku"
          >
            <span class="text-lg shrink-0">⏳</span>
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Antrian Buku</span>
          </Link>
        </div>

        <!-- Divider -->
        <div class="border-t border-slate-100"></div>

        <!-- Filter Tabs (hidden on mobile sidebar, shown in main content) -->
        <div class="hidden lg:block space-y-1">
          <p v-show="!isSidebarCollapsed" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 px-3 py-2 leading-none">Filter Status</p>
          
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            class="flex w-full items-center rounded-xl p-3 text-left transition duration-150 group"
            :class="[
              activeTab === tab.key ? 'bg-orange-600 text-white shadow-sm' : 'text-slate-500 hover:bg-orange-50 hover:text-orange-600',
              isSidebarCollapsed ? 'justify-center' : 'justify-between'
            ]"
            :title="isSidebarCollapsed ? tab.label : ''"
          >
            <div class="flex items-center gap-3">
              <span class="text-base shrink-0">{{ getTabIcon(tab.key) }}</span>
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

      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-slate-100 bg-slate-50/50 shrink-0">
        <div class="flex items-center" :class="(isSidebarCollapsed && !isSidebarOpen) ? 'justify-center' : 'justify-between'">
          <Link href="/profile" class="flex items-center gap-3 overflow-hidden group">
            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 shrink-0 flex items-center justify-center font-bold text-white text-sm">
              {{ stats.user_initials || 'U' }}
            </div>
            <div v-show="!isSidebarCollapsed || isSidebarOpen" class="text-left whitespace-nowrap">
              <p class="text-xs font-bold text-slate-900 truncate max-w-[120px]">{{ stats.user_name || 'User' }}</p>
              <p class="text-[10px] text-slate-400 font-semibold truncate max-w-[120px]">{{ stats.user_role || 'Anggota' }}</p>
            </div>
          </Link>
          
          <button 
            v-show="!isSidebarCollapsed || isSidebarOpen"
            @click="handleLogout" 
            class="rounded-lg p-1.5 text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition shrink-0"
            title="Keluar"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
      
      <!-- Top Header -->
      <header class="sticky top-0 z-30 h-16 lg:h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
          <button 
            @click="toggleSidebar" 
            class="rounded-xl p-2 text-slate-500 hover:text-orange-600 hover:bg-orange-50 transition active:scale-95"
            aria-label="Toggle Navigation Menu"
          >
            <svg class="h-6 w-6 stroke-[2.3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <div class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-slate-400">
            <span class="hidden sm:inline">Sirkulasi</span>
            <span class="hidden sm:inline">/</span>
            <span class="text-slate-900 font-bold">Rak Buku Saya</span>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden md:flex items-center gap-6">
            <div class="text-right">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Buku Aktif</p>
              <p class="text-sm font-black text-slate-900 leading-none mt-0.5">{{ stats.total_active }} Buku</p>
            </div>
            <div class="h-6 w-px bg-slate-200"></div>
            <div class="text-right">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Denda</p>
              <p class="text-sm font-black text-orange-600 leading-none mt-0.5">Rp {{ formatNumber(stats.total_fines) }}</p>
            </div>
          </div>
<Link 
  :href="route('profile.edit')" 
  class="h-8 w-8 rounded-full bg-orange-50 border border-orange-200 flex items-center justify-center lg:hidden font-bold text-orange-600 text-xs hover:bg-orange-100 hover:border-orange-300 transition-colors"
  title="Edit Profil"
>
  {{ stats.user_initials }}
</Link>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 sm:py-10 max-w-5xl w-full mx-auto space-y-6 sm:space-y-8">
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 sm:pb-6 border-b border-slate-200">
          <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">{{ currentTab.title }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ currentTab.description }}</p>
          </div>
          <Link href="/dashboard" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2.5 bg-white hover:bg-orange-50 border border-slate-200 rounded-xl text-xs font-black uppercase tracking-widest text-slate-700 hover:text-orange-600 transition shadow-sm shrink-0">
            Dashboard
          </Link>
        </div>

        <!-- Mobile Filter Tabs -->
        <div class="lg:hidden flex gap-2 pb-2 overflow-x-auto scrollbar-none border-b border-slate-100">
          <button
            v-for="tab in tabs"
            :key="`mobile-${tab.key}`"
            @click="activeTab = tab.key"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider whitespace-nowrap shrink-0 transition-all duration-200"
            :class="activeTab === tab.key ? 'bg-orange-600 text-white shadow-sm' : 'bg-white text-slate-500 border border-slate-200 hover:bg-orange-50'"
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

        <!-- Loan Cards List -->
        <div class="space-y-4">
          <!-- Empty State -->
          <div v-if="visibleLoans.length === 0" class="py-20 text-center bg-white rounded-2xl border border-slate-200/60 p-6">
            <div class="h-16 w-16 mx-auto bg-orange-50 border border-orange-100/50 rounded-2xl flex items-center justify-center text-2xl text-orange-500 mb-4">📚</div>
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">{{ currentTab.emptyTitle }}</h3>
            <p class="mt-1.5 text-xs font-medium text-slate-400 max-w-sm mx-auto leading-relaxed">{{ currentTab.emptyDescription }}</p>
          </div>

          <!-- Loan Cards -->
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

// Responsive sidebar state
const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);

// Watch window resize
function handleResize() {
  if (window.innerWidth >= 1024) {
    isSidebarOpen.value = false;
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
  // Initialize sidebar state based on screen size
  if (window.innerWidth < 1024) {
    isSidebarCollapsed.value = false;
  }
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

function toggleSidebar() {
  if (window.innerWidth >= 1024) {
    // Desktop: toggle collapse
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
  } else {
    // Mobile: toggle slide
    isSidebarOpen.value = !isSidebarOpen.value;
  }
}

function getTabIcon(key) {
  const icons = {
    active: '📖',
    pending: '⏳',
    history: '📜',
    fines: '💳'
  };
  return icons[key] || '📁';
}

// Computed loan lists
const activeLoans = computed(() => 
  props.loans.filter(loan => loan.status === 'approved' || loan.status === 'borrowed')
);

const pendingLoans = computed(() => 
  props.loans.filter(loan => loan.status === 'pending')
);

const historyLoans = computed(() => 
  props.loans.filter(loan => ['returned', 'lost', 'rejected'].includes(loan.status))
);

const finesList = computed(() => 
  props.loans.filter(loan => (loan.late_fee || 0) > 0 && loan.status !== 'returned')
);

// Stats with user info
const stats = computed(() => {
  const user = page.props.auth?.user || {};
  return {
    total_active: activeLoans.value.length,
    total_pending: pendingLoans.value.length,
    total_fines: finesList.value.reduce((sum, loan) => sum + (loan.late_fee || 0), 0),
    user_name: user.name || 'Pengguna',
    user_initials: user.name ? user.name.charAt(0).toUpperCase() : 'U',
    user_role: user.role || 'Anggota'
  };
});

// Tab content configuration
const tabContent = {
  active: {
    title: 'Buku Aktif',
    description: 'Buku yang sudah disetujui atau sedang Anda baca saat ini.',
    emptyTitle: 'Belum ada buku aktif',
    emptyDescription: 'Silakan lakukan peminjaman buku dari katalog perpustakaan.'
  },
  pending: {
    title: 'Menunggu Persetujuan',
    description: 'Daftar pengajuan sirkulasi buku yang sedang diproses oleh pustakawan.',
    emptyTitle: 'Tidak ada pengajuan aktif',
    emptyDescription: 'Pengajuan baru Anda akan segera tampil di halaman ini.'
  },
  history: {
    title: 'Riwayat Sirkulasi',
    description: 'Catatan jejak rekam peminjaman buku Anda yang telah selesai.',
    emptyTitle: 'Riwayat sirkulasi kosong',
    emptyDescription: 'Data riwayat akan otomatis tercatat setelah masa peminjaman selesai.'
  },
  fines: {
    title: 'Denda & Tagihan',
    description: 'Seluruh denda keterlambatan buku yang belum dilunasi.',
    emptyTitle: 'Bersih dari denda',
    emptyDescription: 'Hebat! Anda tidak memiliki tagihan denda berjalan saat ini.'
  }
};

const tabs = computed(() => [
  { key: 'active', label: 'Aktif Dipinjam', count: stats.value.total_active },
  { key: 'pending', label: 'Menunggu', count: stats.value.total_pending },
  { key: 'history', label: 'Riwayat', count: historyLoans.value.length },
  { key: 'fines', label: 'Tagihan Denda', count: finesList.value.length }
]);

const currentTab = computed(() => tabContent[activeTab.value]);

const visibleLoans = computed(() => {
  switch (activeTab.value) {
    case 'pending':
      return pendingLoans.value;
    case 'history':
      return historyLoans.value;
    case 'fines':
      return finesList.value;
    default:
      return activeLoans.value;
  }
});

// Utility functions
function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

// Loan actions
function extendLoan(loan) {
  Swal.fire({
    title: 'Perpanjang Peminjaman?',
    html: `<p class="text-sm">Buku <strong>${loan.book?.title || 'tidak diketahui'}</strong> akan diperpanjang selama 7 hari.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Perpanjang',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/borrow/${loan.id}/extend`, {}, {
        onSuccess: () => {
          Swal.fire({ 
            title: 'Berhasil', 
            text: 'Peminjaman telah diperpanjang.', 
            icon: 'success', 
            confirmButtonColor: '#ea580c' 
          });
        },
        onError: (error) => {
          Swal.fire({ 
            title: 'Gagal', 
            text: error?.message || 'Terjadi kesalahan saat perpanjangan.', 
            icon: 'error', 
            confirmButtonColor: '#ea580c' 
          });
        }
      });
    }
  });
}

function cancelLoan(loan) {
  Swal.fire({
    title: 'Batalkan Peminjaman?',
    html: `<p class="text-sm">Batalkan pengajuan sirkulasi untuk buku <strong>${loan.book?.title || 'tidak diketahui'}</strong>?</p>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Kembali'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/borrow/${loan.id}`, {
        onSuccess: () => {
          Swal.fire({ 
            title: 'Berhasil', 
            text: 'Peminjaman telah dibatalkan.', 
            icon: 'success', 
            confirmButtonColor: '#ea580c' 
          });
        },
        onError: (error) => {
          Swal.fire({ 
            title: 'Gagal', 
            text: error?.message || 'Terjadi kesalahan saat pembatalan.', 
            icon: 'error', 
            confirmButtonColor: '#ea580c' 
          });
        }
      });
    }
  });
}

function payFine(fine) {
  Swal.fire({
    title: 'Bayar Denda?',
    html: `<p class="text-sm">Total denda: <strong>Rp ${formatNumber(fine.late_fee)}</strong></p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Lanjut Bayar',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.visit(`/fines/${fine.id}/pay`);
    }
  });
}

function handleLogout() {
  Swal.fire({
    title: 'Keluar dari Akun?',
    text: 'Anda akan keluar dari sistem SKIEL Library.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Keluar',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('logout'));
    }
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