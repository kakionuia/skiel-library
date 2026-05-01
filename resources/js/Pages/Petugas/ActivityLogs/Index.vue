<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Log Aktivitas Saya</h1>
            <p class="text-slate-500 text-sm mt-1">Riwayat semua tindakan yang telah Anda lakukan di sistem</p>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-[1400px] mx-auto space-y-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-wrap items-center gap-4">
          <div class="relative flex-1 min-w-[300px]">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input v-model="searchQuery" type="text" placeholder="Cari aktivitas..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-slate-200 transition-all text-sm">
          </div>
          <select v-model="selectedType" class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 focus:ring-2 focus:ring-slate-200 transition-all">
            <option value="">Semua Tipe</option>
            <option value="create">Buat</option>
            <option value="update">Perbarui</option>
            <option value="delete">Hapus</option>
            <option value="process">Proses</option>
          </select>
        </div>

        <div class="space-y-4">
          <div v-for="log in filteredLogs" :key="log.id" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-4">
                <div :class="{
                  'bg-orange-100 text-orange-600': log.action === 'create',
                  'bg-orange-100 text-orange-600': log.action === 'update',
                  'bg-red-100 text-red-600': log.action === 'delete',
                  'bg-emerald-100 text-emerald-600': log.action === 'process'
                }" class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm">
                  {{ getActionIcon(log.action) }}
                </div>
                <div>
                  <h4 class="text-sm font-bold text-slate-900">{{ log.description }}</h4>
                  <p class="text-xs text-slate-500 mt-1">{{ log.details }}</p>
                  <div class="flex items-center gap-4 mt-2">
                    <span class="text-[10px] text-slate-400 font-semibold uppercase">{{ formatDate(log.created_at) }}</span>
                    <span class="text-[10px] text-slate-400">{{ formatTime(log.created_at) }}</span>
                  </div>
                </div>
              </div>
              <span :class="{
                'bg-orange-100 text-orange-700': log.action === 'create',
                'bg-orange-100 text-orange-700': log.action === 'update',
                'bg-red-100 text-red-700': log.action === 'delete',
                'bg-emerald-100 text-emerald-700': log.action === 'process'
              }" class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                {{ formatAction(log.action) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { ref, computed } from 'vue';

const props = defineProps({
  activityLogs: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const selectedType = ref('');

const filteredLogs = computed(() => {
  return props.activityLogs.filter(log => {
    const matchesSearch = !searchQuery.value || log.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesType = !selectedType.value || log.action === selectedType.value;
    return matchesSearch && matchesType;
  });
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

const formatAction = (action) => {
  const actions = { 'create': 'Buat', 'update': 'Update', 'delete': 'Hapus', 'process': 'Proses' };
  return actions[action] || action;
};

const getActionIcon = (action) => {
  const icons = { 'create': '+', 'update': '✎', 'delete': '✕', 'process': '✓' };
  return icons[action] || '•';
};
</script>
