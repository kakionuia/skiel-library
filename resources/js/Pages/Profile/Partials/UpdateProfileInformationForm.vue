<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const photoPreview = ref(user.profile_photo_url || null);
const photoInput = ref(null);

const form = useForm({
    name: user.name,
    email: user.email,
    profile_photo: null,
});

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    
    if (!photo) return;
    
    form.profile_photo = photo;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const deleteProfilePhoto = () => {
    if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        form.delete(route('profile.photo.destroy'), {
            preserveScroll: true,
            onSuccess: () => {
                photoPreview.value = null;
                form.profile_photo = null;
            },
        });
    }
};

const submitForm = () => {
    if (form.profile_photo) {
        form.post(route('profile.photo.update'), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                form.profile_photo = null;
                form.clearErrors();
                // Reload page to get updated auth user with new photo URL
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            },
            onError: () => {
                console.error('Photo upload error:', form.errors);
            },
        });
    } else {
        form.patch(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => form.clearErrors(),
        });
    }
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-gray-100 pb-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2.5">
                <span class="p-2 bg-orange-50 text-orange-600 rounded-xl">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </span>
                Informasi Profil
            </h2>
            <p class="mt-2 text-sm text-gray-500">
                Perbarui informasi profil akun, foto, dan alamat email Anda.
            </p>
        </header>

        <!-- Profile Photo Section -->
        <div class="bg-gradient-to-br from-slate-50 to-orange-50/30 rounded-2xl p-6 sm:p-8 border border-slate-200/80">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-5">Foto Profil</h3>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <!-- Current Photo / Preview -->
                <div class="relative group">
                    <div class="h-28 w-28 sm:h-32 sm:w-32 rounded-2xl overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300 border-2 border-slate-200 shadow-lg">
                        <!-- Show Uploaded Photo -->
                        <img 
                            v-if="photoPreview" 
                            :src="photoPreview" 
                            alt="Profile Photo" 
                            class="h-full w-full object-cover"
                        >
                        <!-- Show Initial Avatar -->
                        <div 
                            v-else 
                            class="h-full w-full flex items-center justify-center bg-gradient-to-br from-orange-500 to-amber-500"
                        >
                            <span class="text-4xl sm:text-5xl font-black text-white drop-shadow-lg">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Upload Overlay -->
                    <button 
                        type="button"
                        @click="selectNewPhoto"
                        class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center cursor-pointer"
                    >
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800">Upload foto baru</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            Format JPG, PNG atau GIF. Maksimal ukuran 2MB.
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <button 
                            type="button"
                            @click="selectNewPhoto"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-orange-50 hover:border-orange-300 hover:text-orange-600 transition-all shadow-sm active:scale-95"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Pilih Foto
                        </button>
                        
                        <button 
                            v-if="photoPreview && user.profile_photo_path"
                            type="button"
                            @click="deleteProfilePhoto"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-red-200 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 hover:border-red-300 transition-all shadow-sm active:scale-95"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Foto
                        </button>
                    </div>
                    
                    <input 
                        ref="photoInput" 
                        type="file" 
                        class="hidden"
                        accept="image/*"
                        @change="updatePhotoPreview"
                    >
                    
                    <InputError :message="form.errors.profile_photo" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Profile Information Form -->
        <form @submit.prevent="submitForm" class="space-y-5">
            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-xs font-bold uppercase tracking-wider text-gray-700" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition duration-200"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Masukkan nama lengkap"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Alamat Email" class="text-xs font-bold uppercase tracking-wider text-gray-700" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full rounded-xl border-gray-200 shadow-sm focus:border-orange-500 focus:ring focus:ring-orange-200 focus:ring-opacity-50 transition duration-200"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Masukkan alamat email"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="bg-amber-50/50 border border-amber-200 rounded-xl p-4 mt-4">
                <p class="text-sm text-amber-800 flex items-start gap-2">
                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>
                        Alamat email Anda belum diverifikasi.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="block mt-1 font-semibold underline text-amber-700 hover:text-amber-900 focus:outline-none"
                        >
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </Link>
                    </span>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-3 text-sm font-medium text-green-600 flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:from-orange-700 active:to-amber-700 text-white font-bold px-7 py-3 rounded-xl border-0 shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 transition-all duration-200 disabled:opacity-50"
                >
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else>Simpan Perubahan</span>
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-x-2"
                    leave-active-class="transition ease-in duration-200"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm font-semibold text-green-600 flex items-center gap-1.5">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Berhasil disimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>