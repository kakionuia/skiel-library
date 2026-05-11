<script setup>
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { dangerConfirm, successAlert } from '@/Utils/sweetAlert';
import { ref, computed } from 'vue';

const props = defineProps({
  queuedBooks: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({}) },
});

const activeTab = ref('waiting');

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

async function cancelQueue(queue) {
  const result = await dangerConfirm({
    title: 'Batalkan Antrian?',
    html: `<p class="text-sm mb-3">Batalkan antrian untuk buku <strong>${queue.book?.title}</strong>?</p><p class="text-xs text-slate-500">Posisi: #${queue.position}</p>`,
    confirmButtonText: 'Ya, Batalkan',
  });

  if (result) {
    router.delete(route('queue.cancel', queue.id), {
      onSuccess: () => {
        successAlert({
          title: 'Berhasil!',
          text: 'Antrian berhasil dibatalkan.',
        });
      },
      onError: (errors) => {
        Swal.fire({
          title: 'Gagal!',
          text: errors.error || 'Gagal membatalkan antrian.',
          icon: 'error',
        });
      }
    });
  }
}

function logout() {
  router.post(route('logout'));
}

const tabs = computed(() => [
  { key: 'waiting', label: '⏳ Menunggu', count: props.stats.waitingCount ?? 0 },
  { key: 'notified', label: '✅ Tersedia', count: props.stats.notifiedCount ?? 0 },
]);
</script>

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
          <Link href="/my-loans" class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-orange-50 hover:text-orange-700">Rak Buku</Link>
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
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Menu Antrian</p>
            <h1 class="mt-2 text-xl font-black tracking-tight text-slate-950">Antrian Buku</h1>
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
            <Link href="/my-loans" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-slate-600 transition hover:bg-orange-50 hover:text-orange-700">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              📚 Peminjaman Saya
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

      <main class="flex flex-col">
        <div class="flex-1 p-4 md:p-8">
          <!-- Stats Cards -->
          <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-orange-100 bg-gradient-to-br from-orange-50 to-amber-50 p-6">
              <p class="text-xs font-bold uppercase text-orange-600">⏳ Menunggu Giliran</p>
              <p class="mt-2 text-3xl font-black text-slate-900">{{ stats.waitingCount ?? 0 }}</p>
              <p class="mt-1 text-sm text-orange-600">Antrian aktif saat ini</p>
            </div>
            <div class="rounded-2xl border border-green-100 bg-gradient-to-br from-green-50 to-emerald-50 p-6">
              <p class="text-xs font-bold uppercase text-green-600">✅ Buku Tersedia</p>
              <p class="mt-2 text-3xl font-black text-slate-900">{{ stats.notifiedCount ?? 0 }}</p>
              <p class="mt-1 text-sm text-green-600">Siap untuk dipinjam</p>
            </div>
          </div>

          <!-- Content -->
          <div class="space-y-4">
            <!-- Tab: Menunggu -->
            <div v-if="activeTab === 'waiting'">
              <div v-if="queuedBooks.filter(q => !q.notified_at).length === 0" class="rounded-2xl border border-slate-200/60 bg-white p-12 text-center shadow-sm">
                <div class="text-6xl mb-4 opacity-50">⏳</div>
                <p class="text-lg font-bold text-slate-600">Tidak ada antrian buku</p>
                <p class="mt-2 text-sm text-slate-500">Kunjungi katalog untuk memulai antrian buku favorit Anda</p>
                <Link href="/dashboard" class="mt-6 inline-block rounded-lg bg-orange-600 px-6 py-2.5 font-semibold text-white hover:bg-orange-700 transition">
                  🔍 Jelajahi Katalog
                </Link>
              </div>

              <div v-else class="space-y-4">
                <div v-for="queue in queuedBooks.filter(q => !q.notified_at)" :key="queue.id" class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <img 
                      :src="queue.book?.cover_image || 'https://placehold.co/100x150/E67E22/white?text=Book'" 
                      alt="Book cover"
                      class="w-full md:w-24 md:h-36 h-auto object-cover rounded-xl shadow-md"
                    />
                    <div class="md:col-span-3 flex flex-col justify-between">
                      <div>
                        <div class="flex justify-between items-start gap-4 mb-3">
                          <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ queue.book?.title }}</h3>
                            <p class="text-sm text-slate-600 mt-1">✍️ {{ queue.book?.author }}</p>
                          </div>
                          <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold whitespace-nowrap">
                            ⏳ Posisi #{{ queue.position }}
                          </span>
                        </div>
                        <p class="text-sm text-slate-500 mt-3">📅 Ditambahkan: {{ formatDate(queue.created_at) }}</p>
                      </div>

                      <div class="flex gap-3 mt-4">
                        <button 
                          @click="cancelQueue(queue)"
                          class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-semibold hover:bg-red-100 transition border border-red-200"
                        >
                          ❌ Batalkan Antrian
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab: Tersedia -->
            <div v-if="activeTab === 'notified'">
              <div v-if="queuedBooks.filter(q => q.notified_at).length === 0" class="rounded-2xl border border-slate-200/60 bg-white p-12 text-center shadow-sm">
                <div class="text-6xl mb-4 opacity-50">✅</div>
                <p class="text-lg font-bold text-slate-600">Belum ada buku yang tersedia</p>
                <p class="mt-2 text-sm text-slate-500">Anda akan menerima notifikasi ketika ada buku yang siap dipinjam</p>
              </div>

              <div v-else class="space-y-4">
                <div v-for="queue in queuedBooks.filter(q => q.notified_at)" :key="queue.id" class="rounded-2xl border border-green-200 bg-green-50 p-6 shadow-sm hover:shadow-md transition-shadow">
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <img 
                      :src="queue.book?.cover_image || 'https://placehold.co/100x150/E67E22/white?text=Book'" 
                      alt="Book cover"
                      class="w-full md:w-24 md:h-36 h-auto object-cover rounded-xl shadow-md"
                    />
                    <div class="md:col-span-3 flex flex-col justify-between">
                      <div>
                        <div class="flex justify-between items-start gap-4 mb-3">
                          <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ queue.book?.title }}</h3>
                            <p class="text-sm text-slate-600 mt-1">✍️ {{ queue.book?.author }}</p>
                          </div>
                          <span class="px-3 py-1 bg-green-200 text-green-700 rounded-full text-xs font-bold whitespace-nowrap">
                            ✅ Siap Dipinjam
                          </span>
                        </div>
                        <p class="text-sm text-green-700 font-semibold mt-3">🎉 Buku sudah tersedia! Segera lakukan peminjaman sebelum orang lain mengambilnya.</p>
                        <p class="text-xs text-slate-500 mt-2">📨 Diberitahu: {{ formatDate(queue.notified_at) }}</p>
                      </div>

                      <div class="flex gap-3 mt-4">
                        <Link 
                          :href="`/books/${queue.book?.id}`"
                          class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition"
                        >
                          📖 Pinjam Sekarang
                        </Link>
                        <button 
                          @click="cancelQueue(queue)"
                          class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-200 transition"
                        >
                          ❌ Batalkan
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-slate-200 rounded-full;
}
</style>
