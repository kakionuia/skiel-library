Berikut adalah kode lengkap untuk file antrian buku (`myqueue.vue` / sejenisnya). Kode ini telah diperbarui agar komponen **Sidebar** dan **Header** memiliki visualisasi, tata letak, logika responsive (*resize listener*), serta dekorasi ikon Font Awesome yang **identik dan sinkron** dengan halaman sirkulasi/peminjaman sebelumnya (`myloans.vue`):

```vue
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
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-500 text-sm font-black text-white group-hover:bg-orange-700 transition-colors">
            <img src="/image/logo-smk.png" alt="Logo" class="h-5 w-5" onerror="this.style.display='none'">
          </span>
          <div v-show="!isSidebarCollapsed || isSidebarOpen" class="transition-opacity duration-200 whitespace-nowrap">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 leading-none mb-1">SKIE LIBRARY</p>
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
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:bg-orange-50 hover:text-orange-500 transition"
            :class="{ 'justify-center': isSidebarCollapsed && !isSidebarOpen }"
            title="Rak Buku Saya"
          >
            <i class="fas fa-book-open text-sm w-5 text-center"></i>
            <span v-show="!isSidebarCollapsed || isSidebarOpen">Rak Buku Saya</span>
          </Link>

          <Link 
            href="/my-queue" 
            class="flex items-center gap-3 rounded-xl px-3 py-3 text-xs font-black uppercase tracking-wider bg-orange-50 text-orange-500 border border-orange-100/50 transition"
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
                <i :class="{
                  'fas fa-clock': tab.key === 'waiting',
                  'fas fa-check-circle': tab.key === 'notified'
                }"></i>
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
            <div class="h-9 w-9 rounded-full bg-orange-50 shrink-0 border border-orange-200 flex items-center justify-center font-bold text-orange-600 text-xs uppercase">
              {{ userInitials }}
            </div>
            <div v-show="!isSidebarCollapsed || isSidebarOpen" class="text-left whitespace-nowrap">
              <p class="text-xs font-bold text-slate-900 truncate max-w-[120px] group-hover:text-orange-600 transition-colors">{{ user.name }}</p>
              <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate max-w-[120px] mt-0.5">{{ user.role || 'Siswa' }}</p>
            </div>
          </Link>
          
          <button 
            v-show="!isSidebarCollapsed || isSidebarOpen"
            @click="logout" 
            class="rounded-lg p-1.5 text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition"
            title="Keluar"
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
            aria-label="Toggle Navigation Menu"
          >
            <svg class="h-6 w-6 stroke-[2.3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <div class="flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-slate-400">
            <span class="hidden sm:inline">Sirkulasi</span>
            <span class="hidden sm:inline">/</span>
            <span class="text-slate-900 font-bold">Antrian Buku</span>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden sm:flex items-center gap-6 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200/50">
            <div class="text-right">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Sedang Antri</p>
              <p class="text-sm font-black text-slate-900 leading-none mt-0.5">{{ stats.waitingCount ?? 0 }} Buku</p>
            </div>
            <div class="h-6 w-px bg-slate-200"></div>
            <div class="text-right">
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Siap Diambil</p>
              <p class="text-sm font-black text-orange-600 leading-none mt-0.5">{{ stats.notifiedCount ?? 0 }} Buku</p>
            </div>
          </div>
          <Link 
            :href="route('profile.edit')" 
            class="h-8 w-8 rounded-full bg-orange-50 border border-orange-200 flex items-center justify-center lg:hidden font-bold text-orange-600 text-xs hover:bg-orange-100 hover:border-orange-300 transition-colors uppercase"
            title="Edit Profil"
          >
            {{ userInitials }}
          </Link>
        </div>
      </header>

      <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 sm:py-10 max-w-5xl w-full mx-auto space-y-6 sm:space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 sm:pb-6 border-b border-slate-200">
          <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">Daftar Antrian Buku</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Pantau dan kelola antrian buku reservasi yang Anda ajukan.</p>
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
          
          <div v-if="activeTab === 'waiting'" class="space-y-4">
            <div v-if="queuedBooks.filter(q => !q.notified_at).length === 0" class="py-20 text-center bg-white rounded-2xl border border-slate-200/60 p-6 shadow-3xs">
              <div class="h-16 w-16 mx-auto bg-orange-50 border border-orange-100/50 rounded-2xl flex items-center justify-center text-xl text-orange-500 mb-4">
                <i class="fas fa-hourglass-half"></i>
              </div>
              <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Tidak ada antrian berjalan</h3>
              <p class="mt-1.5 text-xs font-medium text-slate-400 max-w-sm mx-auto leading-relaxed">Saat ini Anda tidak memiliki antrian buku aktif.</p>
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="queue in queuedBooks.filter(q => !q.notified_at)" 
                :key="queue.id" 
                class="group relative flex flex-col md:flex-row gap-6 p-5 bg-white border border-slate-200/60 rounded-2xl shadow-3xs hover:shadow-md transition-all duration-300 overflow-hidden"
              >
                <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-amber-500 transition-all duration-300 group-hover:w-[6px]"></div>

                <div class="w-20 aspect-[3/4.2] shrink-0 overflow-hidden rounded-xl border border-slate-100 p-1 bg-slate-50 shadow-xs mx-auto md:mx-0">
                  <img 
                    :src="queue.book?.cover_image || 'https://placehold.co/120x180/f1f5f9/94a3b8?text=No+Cover'" 
                    alt="Book cover"
                    class="h-full w-full object-cover rounded-lg"
                  />
                </div>

                <div class="flex-1 flex flex-col justify-between min-w-0 py-1">
                  <div class="space-y-1 text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                      <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-md text-[10px] font-extrabold uppercase tracking-wider">
                        Menunggu Giliran
                      </span>
                      <span class="px-2.5 py-1 bg-orange-50 text-orange-700 border border-orange-200/60 rounded-md text-[10px] font-black uppercase tracking-wider">
                        Antrian ke #{{ queue.position }}
                      </span>
                    </div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight leading-snug group-hover:text-orange-600 transition-colors mt-2">
                      {{ queue.book?.title }}
                    </h3>
                    <p class="text-xs font-bold text-slate-500">Ditulis oleh {{ queue.book?.author }}</p>
                  </div>

                  <p class="text-[10px] text-center md:text-left font-bold text-slate-400 uppercase tracking-wider pt-4 border-t border-slate-100 mt-4 md:mt-0">
                    Masuk Daftar: {{ formatDate(queue.created_at) }}
                  </p>
                </div>

                <div class="flex flex-row md:flex-col justify-end items-center gap-3 w-full md:w-auto self-stretch md:self-auto pt-4 md:pt-0 border-t border-slate-100 md:border-t-0 md:pl-6 md:border-l md:border-slate-100">
                  <button 
                    @click="cancelQueue(queue)"
                    class="w-full md:w-auto px-5 py-2.5 bg-white hover:bg-orange-50 border border-slate-200 hover:border-orange-200 text-slate-500 hover:text-orange-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 active:scale-95 shadow-3xs"
                  >
                    Batal Antri
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="activeTab === 'notified'" class="space-y-4">
            <div v-if="queuedBooks.filter(q => q.notified_at).length === 0" class="py-20 text-center bg-white rounded-2xl border border-slate-200/60 p-6 shadow-3xs">
              <div class="h-16 w-16 mx-auto bg-orange-50 border border-orange-100/50 rounded-2xl flex items-center justify-center text-xl text-orange-500 mb-4">
                <i class="fas fa-check-circle"></i>
              </div>
              <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Belum ada buku tersedia</h3>
              <p class="mt-1.5 text-xs font-medium text-slate-400 max-w-sm mx-auto leading-relaxed">Kami akan memberi tahu Anda di sini jika ada buku yang siap diambil.</p>
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="queue in queuedBooks.filter(q => q.notified_at)" 
                :key="queue.id" 
                class="group relative flex flex-col md:flex-row gap-6 p-5 bg-white border border-slate-200/60 rounded-2xl shadow-3xs hover:shadow-md transition-all duration-300 overflow-hidden"
              >
                <div class="absolute left-0 top-0 bottom-0 w-[4px] bg-emerald-500 transition-all duration-300 group-hover:w-[6px]"></div>

                <div class="w-20 aspect-[3/4.2] shrink-0 overflow-hidden rounded-xl border border-slate-100 p-1 bg-slate-50 shadow-xs mx-auto md:mx-0">
                  <img 
                    :src="queue.book?.cover_image || 'https://placehold.co/120x180/f1f5f9/94a3b8?text=No+Cover'" 
                    alt="Book cover"
                    class="h-full w-full object-cover rounded-lg"
                  />
                </div>

                <div class="flex-1 flex flex-col justify-between min-w-0 py-1">
                  <div class="space-y-1 text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                      <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-md text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Siap Diambil
                      </span>
                    </div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight leading-snug group-hover:text-orange-600 transition-colors mt-2">
                      {{ queue.book?.title }}
                    </h3>
                    <p class="text-xs font-bold text-slate-500">Ditulis oleh {{ queue.book?.author }}</p>
                  </div>

                  <p class="text-[10px] text-center md:text-left font-bold text-emerald-600 uppercase tracking-wider pt-4 border-t border-slate-100 mt-4 md:mt-0">
                    Tersedia Sejak: {{ formatDate(queue.notified_at) }}
                  </p>
                </div>

                <div class="flex flex-col sm:flex-row md:flex-col justify-end items-center gap-3 w-full md:w-auto self-stretch md:self-auto pt-4 md:pt-0 border-t border-slate-100 md:border-t-0 md:pl-6 md:border-l md:border-slate-100">
                  <Link 
                    :href="`/books/${queue.book?.id}`"
                    class="w-full text-center px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 shadow-3xs active:scale-95"
                  >
                    Pinjam Sekarang
                  </Link>
                  <button 
                    @click="cancelQueue(queue)"
                    class="w-full px-5 py-2.5 bg-white hover:bg-orange-50 border border-slate-200 hover:border-orange-200 text-slate-500 hover:text-orange-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 active:scale-95 shadow-3xs"
                  >
                    Batalkan
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

      </main>
    </div>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { dangerConfirm, successAlert } from '@/Utils/sweetAlert';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  queuedBooks: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({}) },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Guest', role: 'Siswa' });

const userInitials = computed(() => {
  if (user.value.initials) return user.value.initials;
  return user.value.name
    ? user.value.name.split(' ').map(n => n[0]).slice(0, 2).join('')
    : 'U';
});

const activeTab = ref('waiting');

const isSidebarOpen = ref(false); 
const isSidebarCollapsed = ref(false); 

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
          confirmButtonColor: '#ea580c'
        });
      }
    });
  }
}

function logout() {
  router.post(route('logout'));
}

const tabs = computed(() => [
  { key: 'waiting', label: 'Menunggu', count: props.stats.waitingCount ?? 0 },
  { key: 'notified', label: 'Tersedia', count: props.stats.notifiedCount ?? 0 },
]);
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

```