<template>
  <AuthenticatedLayout title="Detail Petugas">
    <div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto">
        
        <div class="mb-8 flex items-center gap-3">
          <Link 
            href="/admin/accounts" 
            class="p-2 bg-white border border-slate-200 text-slate-500 hover:text-slate-800 rounded-xl hover:shadow-sm transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Manajemen Akun</p>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Detail Profil Petugas</h1>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
          
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
              <div class="flex flex-col items-center text-center">
                
                <div class="w-20 h-20 rounded-full bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center font-bold text-2xl shadow-inner">
                  {{ petugas.name ? petugas.name.charAt(0).toUpperCase() : 'P' }}
                </div>
                
                <h2 class="mt-4 text-lg font-bold text-slate-900 tracking-tight">{{ petugas.name }}</h2>
                <p class="text-sm text-slate-500 font-medium break-all">{{ petugas.email }}</p>
                
                <div class="mt-6 w-full space-y-3.5 border-t border-slate-100 pt-5">
                  <div class="flex justify-between items-center py-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Hak Akses</span>
                    <span class="text-xs font-bold uppercase px-2 py-1 bg-slate-100 text-slate-600 rounded-md">Petugas</span>
                  </div>
                  
                  <div class="flex justify-between items-center py-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Akun</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase px-2 py-1 bg-white border border-orange-200 text-orange-600 rounded-md">
                      <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                      Aktif
                    </span>
                  </div>
                  
                  <div class="flex justify-between items-center py-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Terdaftar</span>
                    <span class="text-sm font-medium text-slate-700">{{ petugas.created_at }}</span>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
              <div class="mb-5">
                <h3 class="text-base font-bold tracking-tight text-slate-900">Log Aktivitas Terbaru</h3>
                <p class="text-xs text-slate-400">Rekam jejak tindakan operasi yang dilakukan oleh petugas ini</p>
              </div>

              <div class="relative pl-4 border-l-2 border-slate-100 space-y-6 my-2">
                <div 
                  v-for="activity in activities" 
                  :key="activity.id" 
                  class="relative group"
                >
                  <div class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-orange-500 ring-4 ring-white group-hover:border-orange-600 transition-colors"></div>
                  
                  <div class="bg-white hover:bg-slate-50/50 p-4 rounded-xl border border-slate-100 transition-all">
                    <p class="text-sm font-medium text-slate-800 leading-relaxed">{{ activity.description }}</p>
                    <div class="mt-2 flex items-center gap-1.5 text-xs font-medium text-slate-400">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ activity.created_at }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="activities.length === 0" class="text-center py-12 border border-dashed border-slate-200 rounded-xl bg-slate-50/30">
                  <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p class="text-sm font-medium text-slate-400">Belum ada rekaman aktivitas.</p>
                </div>
              </div>

            </div>
          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  petugas: { type: Object, default: () => ({}) },
  activities: { type: Array, default: () => [] },
});
</script>