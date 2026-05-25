<template>
  <AuthenticatedLayout title="Keluhan & Saran">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Keluhan & Saran</h1>
          <p class="mt-2 text-sm text-gray-500">Kelola keluhan dan saran yang disampaikan oleh anggota.</p>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 flex gap-3 border-b border-gray-200">
          <button
            @click="activeTab = 'complaints'"
            :class="[
              'px-6 py-3 font-medium text-sm border-b-2 transition-colors',
              activeTab === 'complaints'
                ? 'border-orange-500 text-orange-600'
                : 'border-transparent text-gray-600 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
              <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
            </svg>
            Keluhan ({{ complaintCount }})
          </button>
          <button
            @click="activeTab = 'suggestions'"
            :class="[
              'px-6 py-3 font-medium text-sm border-b-2 transition-colors',
              activeTab === 'suggestions'
                ? 'border-orange-500 text-orange-600'
                : 'border-transparent text-gray-600 hover:text-gray-900'
            ]"
          >
            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
            Saran ({{ suggestionCount }})
          </button>
        </div>

        <!-- Complaints Table -->
        <div v-if="activeTab === 'complaints'" class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Subjek</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Deskripsi</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="complaint in complaints" :key="complaint.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ complaint.user?.name || 'Anonim' }}</div>
                    <p class="text-xs text-gray-500">{{ complaint.user?.email }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ complaint.subject }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ complaint.description }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700': complaint.status === 'pending',
                      'bg-blue-100 text-blue-700': complaint.status === 'reviewed',
                      'bg-green-100 text-green-700': complaint.status === 'resolved',
                      'bg-red-100 text-red-700': complaint.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase">
                      {{ complaint.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ formatDateTime(complaint.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <Link :href="`/admin/complaints/${complaint.id}`" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                      Tangani →
                    </Link>
                  </td>
                </tr>
                <tr v-if="complaints.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    Belum ada keluhan.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Suggestion Modal -->
        <div v-if="showSuggestionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
          <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-96 overflow-y-auto">
            <div class="p-8">
              <!-- Header -->
              <div class="flex items-start justify-between mb-6">
                <div>
                  <h2 class="text-2xl font-bold text-gray-900">Saran dari {{ selectedSuggestion?.user?.name || 'Anonim' }}</h2>
                  <p class="text-sm text-gray-500 mt-1">{{ formatDateTime(selectedSuggestion?.created_at) }}</p>
                </div>
                <button 
                  @click="showSuggestionModal = false"
                  class="text-gray-400 hover:text-gray-600 transition"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Content -->
              <div class="space-y-4">
                <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                  <p class="text-gray-900 whitespace-pre-wrap break-words">{{ selectedSuggestion?.content }}</p>
                </div>
              </div>

              <!-- Close Button -->
              <div class="mt-6 flex justify-end">
                <button 
                  @click="showSuggestionModal = false"
                  class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg font-medium transition"
                >
                  Tutup
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Suggestions Table -->
        <div v-if="activeTab === 'suggestions'" class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Anggota</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Saran</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Status</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Catatan Admin</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="suggestion in suggestions" :key="suggestion.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ suggestion.user?.name || 'Anonim' }}</div>
                    <p class="text-xs text-gray-500">{{ suggestion.user?.email }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-sm">{{ suggestion.content }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="{
                      'bg-amber-100 text-amber-700': suggestion.status === 'pending',
                      'bg-green-100 text-green-700': suggestion.status === 'reviewed' || suggestion.status === 'implemented',
                      'bg-red-100 text-red-700': suggestion.status === 'rejected',
                    }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase">
                      {{ suggestion.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-gray-600 max-w-xs truncate">{{ suggestion.admin_note || '-' }}</p>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ formatDateTime(suggestion.created_at) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <button @click="openSuggestionModal(suggestion)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                      Proses →
                    </button>
                  </td>
                </tr>
                <tr v-if="suggestions.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5h.01" />
                    </svg>
                    Belum ada saran.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  complaints: {
    type: Array,
    default: () => [],
  },
  suggestions: {
    type: Array,
    default: () => [],
  },
});

const activeTab = ref('complaints');
const showSuggestionModal = ref(false);
const selectedSuggestion = ref(null);

const complaintCount = computed(() => props.complaints?.length || 0);
const suggestionCount = computed(() => props.suggestions?.length || 0);

const formatDateTime = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  if (Number.isNaN(date.getTime())) return '-';

  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Jakarta',
    timeZoneName: 'short',
  }).format(date);
};

const openSuggestionModal = (suggestion) => {
  selectedSuggestion.value = suggestion;
  showSuggestionModal.value = true;
};
</script>
