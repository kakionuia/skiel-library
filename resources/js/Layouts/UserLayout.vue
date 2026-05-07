<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
      <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">
            📚
          </div>
          <div>
            <h1 class="text-lg font-bold text-slate-900">SKIE-Library</h1>
            <p class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Perpustakaan Digital</p>
          </div>
        </div>

        <div class="flex items-center gap-6">
          <button class="relative p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>

          <div class="border-l border-slate-200 pl-6">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex items-center gap-3 hover:bg-slate-50 px-3 py-2 rounded-lg transition-all">
                  <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">
                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="text-left hidden sm:block">
                    <p class="text-xs font-bold text-slate-900">{{ $page.props.auth.user.name }}</p>
                    <p class="text-[10px] text-slate-500">Anggota</p>
                  </div>
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('profile.edit')">Edit Profil</DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">Keluar</DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-6">
      <div class="max-w-7xl mx-auto px-6">
        <div class="text-center text-sm text-slate-500">
          <p>&copy; 2026 LibraSync Perpustakaan Digital. Semua hak dilindungi.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
</script>
