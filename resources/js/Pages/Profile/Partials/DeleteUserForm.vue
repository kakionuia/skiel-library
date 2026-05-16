<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    
    setTimeout(() => {
        passwordInput.value?.focus();
    }, 100);
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingUserDeletion.value = false;
        },
        onError: () => {
            passwordInput.value?.focus();
            form.reset();
        },
        onFinish: () => {
            if (form.errors.password) {
                form.reset();
            }
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-red-100 pb-6">
            <h2 class="text-xl font-bold text-red-900 flex items-center gap-2.5">
                <span class="p-2 bg-red-50 text-red-600 rounded-xl">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </span>
                Hapus Akun
            </h2>
            <p class="mt-2 text-sm text-red-700/80">
                Tindakan ini bersifat permanen. Begitu akun dihapus, seluruh data dan aset di dalamnya akan langsung musnah dan tidak dapat dikembalikan.
            </p>
        </header>

        <div class="bg-red-50/50 border border-red-200/60 rounded-xl p-4 text-sm text-red-800">
            Sebelum menghapus, pastikan Anda telah mencadangkan data penting yang sekiranya masih dibutuhkan di masa mendatang.
        </div>

        <div class="pt-2">
            <DangerButton 
                @click="confirmUserDeletion"
                class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 active:from-red-700 active:to-rose-800 text-white font-bold px-7 py-3 rounded-xl border-0 shadow-lg shadow-red-500/10 transition-all duration-200"
            >
                Hapus Akun Secara Permanen
            </DangerButton>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="confirmingUserDeletion" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

                <!-- Modal -->
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 transform transition-all">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Konfirmasi Penghapusan</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Masukkan password Anda untuk mengkonfirmasi penghapusan akun secara permanen.
                        </p>
                    </div>

                    <div class="mt-6">
                        <div class="relative">
                            <input
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50 transition duration-200 px-4 py-3 text-sm"
                                placeholder="Password"
                                @keyup.enter="deleteUser"
                            />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3 justify-end">
                        <button
                            @click="closeModal"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            @click="deleteUser"
                            :disabled="form.processing"
                            class="px-5 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 rounded-xl shadow-lg shadow-red-500/20 transition-all duration-200 disabled:opacity-50"
                        >
                            <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menghapus...
                            </span>
                            <span v-else>Hapus Akun</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>