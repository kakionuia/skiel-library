<template>
  <div class="min-h-screen bg-[#fff8f2] font-sans text-slate-900">
    <nav class="sticky top-0 z-40 border-b border-orange-100 bg-white">
      <div class="flex h-16 items-center justify-between px-4 md:px-8">
        <Link href="/dashboard" class="flex items-center gap-3">
          <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-600 text-sm font-black text-white">S</span>
          <span class="text-base font-bold tracking-tight">SKIEL Library</span>
        </Link>

        <div class="flex items-center gap-2 text-sm font-semibold">
          <Link href="/dashboard" class="hidden rounded-lg px-3 py-2 text-slate-600 transition hover:bg-orange-50 hover:text-orange-700 sm:inline-flex">Katalog</Link>
          <Link href="/my-loans" class="rounded-lg bg-orange-50 px-3 py-2 text-orange-700 ring-1 ring-orange-100">Rak Buku</Link>
          <button @click="logout" class="rounded-lg p-2 text-slate-500 transition hover:bg-orange-50 hover:text-orange-700" title="Logout">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </div>
      </div>
    </nav>

    <div class="grid min-h-[calc(100vh-4rem)] lg:grid-cols-[272px_1fr]">
      <aside class="hidden border-r border-orange-100 bg-white lg:block">
        <div class="sticky top-16 flex h-[calc(100vh-4rem)] flex-col">
          <div class="border-b border-orange-100 p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Menu Peminjaman</p>
            <h1 class="mt-2 text-xl font-black tracking-tight text-slate-950">Rak Buku Saya</h1>
          </div>

          <div class="flex-1 space-y-1 p-3">
            <button
              v-for="tab in tabs"
              :key="tab.key"
              @click="activeTab = tab.key"
              class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-left text-sm font-bold transition"
              :class="activeTab === tab.key ? 'bg-orange-600 text-white' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-700'"
            >
              <span>{{ tab.label }}</span>
              <span v-if="tab.count !== null" class="rounded-md px-2 py-0.5 text-xs" :class="activeTab === tab.key ? 'bg-white/15 text-white' : 'bg-orange-50 text-orange-700 ring-1 ring-orange-100'">
                {{ tab.count }}
              </span>
            </button>
          </div>

          <div class="border-t border-orange-100 p-3">
            <Link href="/my-queue" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-slate-600 transition hover:bg-orange-50 hover:text-orange-700">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              ⏳ Antrian Buku
            </Link>
            <Link href="/profile" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-slate-600 transition hover:bg-orange-50 hover:text-orange-700">
              <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Profil Saya
            </Link>
          </div>
        </div>
      </aside>

      <main class="min-w-0 px-4 py-6 md:px-8">
        <section class="mb-6">
          <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
              <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600 lg:hidden">Rak Buku Saya</p>
              <h2 class="text-2xl font-black tracking-tight text-slate-950">{{ currentTab.title }}</h2>
              <p class="mt-1 text-sm text-slate-500">{{ currentTab.description }}</p>
            </div>
            <Link href="/dashboard" class="inline-flex w-fit items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2.5 text-sm font-bold text-orange-700 transition hover:bg-orange-50">
              Jelajahi Katalog
            </Link>
          </div>

          <div class="mb-5 grid gap-3 sm:grid-cols-4 lg:hidden">
            <button
              v-for="tab in tabs"
              :key="`mobile-${tab.key}`"
              @click="activeTab = tab.key"
              class="rounded-lg border px-3 py-3 text-left transition"
              :class="activeTab === tab.key ? 'border-orange-600 bg-orange-600 text-white' : 'border-orange-100 bg-white text-slate-700 hover:bg-orange-50'"
            >
              <span class="block text-xs font-bold">{{ tab.label }}</span>
              <span class="mt-1 block text-xl font-black">{{ tab.count }}</span>
            </button>
          </div>

          <div class="mb-6 grid gap-3 md:grid-cols-3">
            <div class="rounded-lg border border-orange-100 bg-white p-4">
              <p class="text-xs font-bold uppercase tracking-wide text-orange-700">Aktif</p>
              <p class="mt-2 text-2xl font-black text-slate-950">{{ stats.total_active }}</p>
            </div>
            <div class="rounded-lg border border-orange-100 bg-white p-4">
              <p class="text-xs font-bold uppercase tracking-wide text-orange-700">Menunggu</p>
              <p class="mt-2 text-2xl font-black text-slate-950">{{ stats.total_pending }}</p>
            </div>
            <div class="rounded-lg border border-orange-100 bg-orange-600 p-4 text-white">
              <p class="text-xs font-bold uppercase tracking-wide text-orange-100">Total Denda</p>
              <p class="mt-2 text-2xl font-black">Rp {{ formatNumber(stats.total_fines) }}</p>
            </div>
          </div>

          <div v-if="visibleLoans.length === 0" class="rounded-lg border border-orange-100 bg-white p-10 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <p class="text-base font-bold text-slate-900">{{ currentTab.emptyTitle }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ currentTab.emptyDescription }}</p>
          </div>

          <div v-else class="space-y-3">
            <LoanCard
              v-for="loan in visibleLoans"
              :key="loan.id"
              :loan="loan"
              @extend="extendLoan"
              @cancel="cancelLoan"
              @pay-fine="payFine"
            />
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import LoanCard from '@/Pages/User/Rak/Components/LoanCard.vue';

const props = defineProps({
  loans: {
    type: Array,
    default: () => []
  }
});

const activeTab = ref('active');

const activeLoans = computed(() => props.loans.filter(loan => loan.status === 'approved' || loan.status === 'borrowed'));
const pendingLoans = computed(() => props.loans.filter(loan => loan.status === 'pending'));
const historyLoans = computed(() => props.loans.filter(loan => ['returned', 'lost', 'rejected'].includes(loan.status)));
const finesList = computed(() => props.loans.filter(loan => (loan.late_fee || 0) > 0 && loan.status !== 'returned'));

const stats = computed(() => ({
  total_active: activeLoans.value.length,
  total_pending: pendingLoans.value.length,
  total_fines: finesList.value.reduce((sum, loan) => sum + (loan.late_fee || 0), 0)
}));

const tabContent = {
  active: {
    title: 'Buku Aktif',
    description: 'Buku yang sudah disetujui atau sedang dipinjam.',
    emptyTitle: 'Belum ada buku aktif',
    emptyDescription: 'Buku yang disetujui akan tampil di bagian ini.'
  },
  pending: {
    title: 'Menunggu Persetujuan',
    description: 'Pengajuan peminjaman yang sedang diproses pustakawan.',
    emptyTitle: 'Tidak ada pengajuan menunggu',
    emptyDescription: 'Pengajuan baru akan tampil di bagian ini.'
  },
  history: {
    title: 'Riwayat Peminjaman',
    description: 'Catatan peminjaman yang sudah selesai atau ditolak.',
    emptyTitle: 'Riwayat masih kosong',
    emptyDescription: 'Riwayat akan terisi setelah peminjaman selesai.'
  },
  fines: {
    title: 'Denda & Tagihan',
    description: 'Tagihan keterlambatan yang masih perlu diselesaikan.',
    emptyTitle: 'Tidak ada denda',
    emptyDescription: 'Tidak ada tagihan keterlambatan yang aktif.'
  }
};

const tabs = computed(() => [
  { key: 'active', label: 'Aktif Dipinjam', count: stats.value.total_active },
  { key: 'pending', label: 'Menunggu', count: stats.value.total_pending },
  { key: 'history', label: 'Riwayat', count: historyLoans.value.length },
  { key: 'fines', label: 'Denda', count: finesList.value.length }
]);

const currentTab = computed(() => tabContent[activeTab.value]);
const visibleLoans = computed(() => {
  if (activeTab.value === 'pending') return pendingLoans.value;
  if (activeTab.value === 'history') return historyLoans.value;
  if (activeTab.value === 'fines') return finesList.value;
  return activeLoans.value;
});

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

function extendLoan(loan) {
  Swal.fire({
    title: 'Perpanjang Peminjaman?',
    html: `<p class="text-sm">Buku <strong>${loan.book?.title}</strong> akan diperpanjang selama 7 hari.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Perpanjang',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/borrow/${loan.id}/extend`, {}, {
        onSuccess: () => Swal.fire({ title: 'Berhasil', text: 'Peminjaman telah diperpanjang.', icon: 'success', confirmButtonColor: '#ea580c' }),
        onError: (error) => Swal.fire({ title: 'Gagal', text: error.message || 'Terjadi kesalahan saat perpanjangan.', icon: 'error', confirmButtonColor: '#ea580c' })
      });
    }
  });
}

function cancelLoan(loan) {
  Swal.fire({
    title: 'Batalkan Peminjaman?',
    html: `<p class="text-sm">Batalkan pengajuan <strong>${loan.book?.title}</strong>?</p>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Kembali'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/borrow/${loan.id}`, {
        onSuccess: () => Swal.fire({ title: 'Berhasil', text: 'Peminjaman telah dibatalkan.', icon: 'success', confirmButtonColor: '#ea580c' }),
        onError: (error) => Swal.fire({ title: 'Gagal', text: error.message || 'Terjadi kesalahan saat pembatalan.', icon: 'error', confirmButtonColor: '#ea580c' })
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
    if (result.isConfirmed) router.visit(`/fines/${fine.id}/pay`);
  });
}

function logout() {
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
    if (result.isConfirmed) router.post(route('logout'));
  });
}
</script>
