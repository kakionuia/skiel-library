<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Form diinisialisasi persis dengan data/field yang dikirim oleh AuthModal
const form = useForm({
    name: '',
    nisn: '',
    kelas: '',
    jurusan: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        preserveScroll: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <template #logo>
            <img src="/image/logo-smk.png" alt="Logo SKIE" class="h-12 w-auto object-contain" />
            <div class="text-left">
                <h1 class="text-xl font-bold leading-none text-slate-900 tracking-tight">SKIE-Library</h1>
                <p class="text-xs text-slate-500 font-medium">Online Library Administration</p>
            </div>
        </template>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-950 tracking-tight">Bergabung dengan SKIE-Library</h2>
            <p class="text-xs font-semibold text-slate-800 mt-1.5">Buat akun untuk akses penuh layanan digital</p>
            <p class="text-[10px] text-slate-500 mt-2 leading-relaxed px-2">
                Data yang kamu kirim akan kami tinjau dahulu untuk konfirmasi kamu siswa SMKN 1 Gunungputri atau bukan
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-1">
                <InputLabel for="name" value="Nama Lengkap" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                <input 
                    id="name" 
                    type="text" 
                    placeholder="Nama Lengkap Anda" 
                    v-model="form.name"
                    required
                    autofocus
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1 ml-1" :message="form.errors.name" />
            </div>

            <div class="space-y-1">
                <InputLabel for="nisn" value="NISN" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                <input 
                    id="nisn" 
                    type="text" 
                    placeholder="ex: 2425*******" 
                    v-model="form.nisn"
                    required
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1 ml-1" :message="form.errors.nisn" />
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1 space-y-1">
                    <InputLabel for="kelas" value="Kelas" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                    <select 
                        id="kelas" 
                        v-model="form.kelas"
                        required
                        class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                    >
                        <option value="" disabled selected hidden>Kelas</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                    <InputError class="mt-1 ml-1" :message="form.errors.kelas" />
                </div>

                <div class="col-span-2 space-y-1">
                    <InputLabel for="jurusan" value="Jurusan" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                    <select 
                        id="jurusan" 
                        v-model="form.jurusan"
                        required
                        class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                    >
                        <option value="" disabled selected hidden>Pilih Jurusan</option>
                        <option value="Elektro 1">Elektro 1</option>
                        <option value="Elektro 2">Elektro 2</option>
                        <option value="Elektro 3">Elektro 3</option>
                        <option value="Elektro 4">Elektro 4</option>
                        <option value="Mesin 1">Mesin 1</option>
                        <option value="Mesin 2">Mesin 2</option>
                        <option value="Mesin 3">Mesin 3</option>
                        <option value="Kimia 1">Kimia 1</option>
                        <option value="Kimia 2">Kimia 2</option>
                        <option value="Kimia 3">Kimia 3</option>
                        <option value="Las 1">Las 1</option>
                        <option value="Las 2">Las 2</option>
                        <option value="Las 3">Las 3</option>
                        <option value="RPL 1">RPL 1</option>
                        <option value="RPL 2">RPL 2</option>
                        <option value="RPL 3">RPL 3</option>
                        <option value="RPL 4">RPL 4</option>
                    </select>
                    <InputError class="mt-1 ml-1" :message="form.errors.jurusan" />
                </div>
            </div>

            <div class="space-y-1">
                <InputLabel for="email" value="Email Sekolah" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                <input 
                    id="email" 
                    type="email" 
                    placeholder="name@smkn1gputri.sch.id" 
                    v-model="form.email"
                    required
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1 ml-1" :message="form.errors.email" />
            </div>

            <div class="space-y-1">
                <InputLabel for="password" value="Kata Sandi" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                <input 
                    id="password" 
                    type="password" 
                    placeholder="••••••••" 
                    v-model="form.password"
                    required
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1 ml-1" :message="form.errors.password" />
            </div>

            <div class="space-y-1">
                <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em] ml-1" />
                <input 
                    id="password_confirmation" 
                    type="password" 
                    placeholder="••••••••" 
                    v-model="form.password_confirmation"
                    required
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1 ml-1" :message="form.errors.password_confirmation" />
            </div>

            <button 
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-[#e47d05] py-3 text-center text-sm font-bold text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all mt-4 cursor-pointer disabled:opacity-50"
            >
                <span v-if="!form.processing">Daftar</span>
                <div v-else class="w-5 h-5 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            </button>
        </form>

        <div class="mt-8 flex flex-col items-center space-y-4 text-center">
            <p class="text-xs text-gray-400 font-medium">
                Sudah punya akun?
                <Link :href="route('login')" class="text-orange-600 font-bold ml-1 hover:underline underline-offset-4">
                    Masuk di sini
                </Link>
            </p>
            
            <span class="text-xs font-medium text-slate-400">Atau masuk dengan</span>
            <div class="flex gap-4">
                <button class="h-10 w-10 rounded-full bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer border border-gray-100" title="Google SSO"></button>
                <button class="h-10 w-10 rounded-full bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer border border-gray-100" title="Facebook SSO"></button>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Menyamakan gaya input dengan class @apply auth-input yang ada di modal */
.auth-input-style {
    @apply w-full px-5 py-3.5 rounded-2xl bg-gray-50 border border-gray-100
           text-sm font-semibold text-gray-900 outline-none transition-all duration-300
           placeholder:text-gray-300 focus:bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/5;
}

/* Menyamakan gaya tombol submit dengan class @apply submit-btn yang ada di modal */
.submit-btn-style {
    @apply w-full bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-lg 
           shadow-orange-100 transition-all duration-300 flex items-center justify-center
           hover:bg-orange-700 hover:shadow-orange-200 active:scale-[0.98] mt-6
           disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none disabled:cursor-not-allowed;
}
</style>