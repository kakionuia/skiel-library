<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    recentActivities: {
        type: Array,
        default: () => [],
    },
    recentBooks: {
        type: Array,
        default: () => [],
    },
});

const user = usePage().props.auth.user;
const isAnggota = user.role === 'anggota'; // sesuaikan dengan nama role di sistem Anda
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout :showSidebar="false">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Profile Summary Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <!-- Avatar with initials -->
                            <div class="shrink-0">
                                <span class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-2xl font-bold text-indigo-700">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
                                <div class="mt-2 flex flex-wrap justify-center sm:justify-start gap-x-6 gap-y-1 text-sm text-gray-600">
                                    <div v-if="user.kelas" class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Kelas {{ user.kelas }}</span>
                                    </div>
                                    <div v-if="user.jurusan" class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                        <span>{{ user.jurusan }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="truncate">{{ user.email }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Role badge -->
                            <div class="shrink-0">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700': isAnggota,
                                        'bg-blue-100 text-blue-700': !isAnggota
                                    }"
                                >
                                    {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Books grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recent Activities -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Aktivitas Terbaru
                        </h3>
                        <div v-if="recentActivities.length === 0" class="text-sm text-gray-500 italic">
                            Belum ada aktivitas terbaru.
                        </div>
                        <ul v-else class="space-y-4">
                            <li v-for="activity in recentActivities" :key="activity.id" class="flex gap-4">
                                <div class="relative">
                                    <div class="h-2 w-2 mt-2 rounded-full bg-indigo-400"></div>
                                    <div class="absolute left-0.5 top-3 -bottom-5 w-px bg-gray-200 last:hidden" aria-hidden="true"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ activity.description }}</p>
                                    <p class="text-xs text-gray-500">{{ activity.created_at }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Recent Books (only for anggota) -->
                    <div v-if="isAnggota" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Buku Terakhir Dibaca
                        </h3>
                        <div v-if="recentBooks.length === 0" class="text-sm text-gray-500 italic">
                            Tidak ada buku yang pernah dibaca.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="book in recentBooks" :key="book.id" class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="h-12 w-10 bg-indigo-100 rounded flex items-center justify-center text-xs font-bold text-indigo-800">
                                    {{ book.judul?.charAt(0) || '📖' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ book.judul }}</p>
                                    <p class="text-xs text-gray-500">Terakhir dibaca {{ book.tanggal_baca }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jika bukan anggota, kolom kanan bisa diisi card berguna, misalnya ringkasan statistik -->
                    <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8 flex items-center justify-center">
                        <div class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm">Panel Admin / Petugas</p>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Password & Delete -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>