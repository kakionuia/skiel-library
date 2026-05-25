<script setup>
import { ref, computed } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePage, Link } from '@inertiajs/vue3';

defineProps({
    showSidebar: { type: Boolean, default: true },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const dashboardRoute = computed(() => {
    const role = user.value.role;
    if (role === 'admin') return 'admin.dashboard';
    if (role === 'petugas') return 'petugas.dashboard';
    return 'dashboard';
});

const isOnDashboard = computed(() => {
    return route().current(dashboardRoute.value);
});

const isOnProfile = computed(() => {
    return route().current('profile.edit');
});

// Helper untuk mendapatkan URL foto profil
const profilePhotoUrl = computed(() => {
    return user.value.profile_photo_url || null;
});
</script>

<template>
    <div class="min-h-screen bg-slate-50/50 dark:bg-slate-950 flex text-slate-800 dark:text-slate-100">
        <!-- Sidebar Component -->
        <Sidebar v-if="showSidebar" />
        
        <!-- Main Content Area -->
        <div :class="['flex-1', 'flex', 'flex-col', showSidebar ? 'ml-72' : 'ml-0']">
            
            <!-- Premium Navbar (Authenticated Version) -->
            <header class="h-20 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-700/80 sticky top-0 z-[90] px-8 flex items-center justify-between transition-all duration-300">
                
                <!-- Left Side: Dynamic Logo / Title Hybrid -->
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <div class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-md shadow-orange-500/10 group-hover:scale-105 transition-transform duration-300">
                            <img :src="$page.props.logoUrl" alt="Logo" class="w-8 h-8">
                        </div>
                        <div class="flex flex-col hidden sm:flex">
                            <h1 class="font-black text-sm tracking-tight text-slate-900 dark:text-white leading-none">
                                SKIE-Library
                            </h1>
                            <p class="text-[9px] uppercase font-semibold tracking-wider text-orange-600 dark:text-orange-400 mt-0.5">
                                Online Administration
                            </p>
                        </div>
                    </div>

                    <span class="h-5 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block"></span>

                    <div class="animate-fade-in">
                        <slot name="header" />
                    </div>
                </div>

                <!-- Right Side: Profile Action & Dropdown -->
                <div class="flex items-center gap-4">
                    <Dropdown align="right" width="56">
                        <template #trigger>
                            <button class="flex items-center gap-3 pl-2 pr-4 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-orange-50 dark:hover:bg-orange-900/20 border border-slate-200/60 dark:border-slate-700/60 hover:border-orange-200 dark:hover:border-orange-900/40 transition-all duration-300 group text-left">
                                <!-- Avatar Frame with Photo -->
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-xs font-black text-white shadow-sm ring-2 ring-white dark:ring-slate-800 group-hover:ring-orange-100 dark:group-hover:ring-orange-900/30 transition-all overflow-hidden">
                                    <!-- Show Profile Photo -->
                                    <img 
                                        v-if="profilePhotoUrl" 
                                        :src="profilePhotoUrl" 
                                        alt="Profile" 
                                        class="w-full h-full object-cover"
                                    >
                                    <!-- Fallback Initial -->
                                    <span v-else class="text-xs font-black">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <!-- User Information Short -->
                                <div class="hidden md:flex flex-col pr-1">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors line-clamp-1 max-w-[120px] leading-tight">
                                        {{ user.name }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 capitalize font-medium leading-none mt-0.5">
                                        {{ user.role }}
                                    </span>
                                </div>
                                <!-- Chevron -->
                                <svg class="h-3.5 w-3.5 text-slate-400 dark:text-slate-500 group-hover:text-orange-500 dark:group-hover:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </template>
                        
                        <template #content>
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 rounded-t-lg">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar in Dropdown -->
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-sm font-black text-white overflow-hidden">
                                        <img 
                                            v-if="profilePhotoUrl" 
                                            :src="profilePhotoUrl" 
                                            alt="Profile" 
                                            class="w-full h-full object-cover"
                                        >
                                        <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ user.name }}</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ user.email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-1 space-y-0.5">
                                <DropdownLink 
                                    v-if="!isOnDashboard" 
                                    :href="route(dashboardRoute)"
                                    class="flex items-center gap-2 px-3 py-2 rounded-md text-slate-600 dark:text-slate-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 font-medium transition"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </DropdownLink>
                                
                                <DropdownLink 
                                    v-if="!isOnProfile" 
                                    :href="route('profile.edit')"
                                    class="flex items-center gap-2 px-3 py-2 rounded-md text-slate-600 dark:text-slate-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 font-medium transition"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Edit Profil
                                </DropdownLink>
                                
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>

                                <DropdownLink 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button"
                                    class="w-full flex items-center gap-2 px-3 py-2 rounded-md text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold transition"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </DropdownLink>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Main Workspace Slot -->
            <main class="p-8 flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
