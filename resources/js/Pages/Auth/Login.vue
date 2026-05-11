<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <template #logo>
            <img src="/image/logo-smk.png" alt="Logo SKIE" class="h-12 w-auto object-contain" />
            <div class="text-left">
                <h1 class="text-xl font-bold leading-none text-slate-900 tracking-tight">SKIE-Library</h1>
                <p class="text-xs text-slate-500 font-medium">Online Library Administration</p>
            </div>
        </template>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-slate-950">Selamat Datang Kembali!</h2>
            <p class="text-xs font-semibold text-slate-800 mt-1">Silakan masuk menggunakan akun terdaftar Anda</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 p-3 rounded-xl text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email Siswa / Admin" class="text-sm font-bold text-slate-900 mb-1" />
                <input 
                    id="email" 
                    type="email" 
                    placeholder="siswaskiel@example.com" 
                    v-model="form.email"
                    required
                    autofocus
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <InputLabel for="password" value="Password" class="text-sm font-bold text-slate-900" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-[#e47d05] hover:underline font-semibold"
                    >
                        Lupa Password?
                    </Link>
                </div>
                <input 
                    id="password" 
                    type="password" 
                    placeholder="**********" 
                    v-model="form.password"
                    required
                    class="w-full rounded-xl border border-orange-400 px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all outline-none placeholder:text-gray-300"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" class="rounded text-[#e47d05] focus:ring-[#e47d05]" />
                <span class="ms-2 text-xs font-medium text-slate-700">Ingat saya di perangkat ini</span>
            </div>

            <button 
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-[#e47d05] py-3 text-center text-sm font-bold text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all mt-4 cursor-pointer disabled:opacity-50"
            >
                {{ form.processing ? 'Memasuki...' : 'Masuk' }}
            </button>
        </form>

        <div class="mt-6 flex flex-col items-center space-y-4">
            <div class="text-xs font-semibold text-slate-800">
                Belum terdaftar? 
                <Link :href="route('register')" class="text-[#e47d05] hover:underline font-bold">
                    Buat akun baru
                </Link>
            </div>

            <span class="text-xs font-medium text-slate-400">Atau masuk dengan</span>
            <div class="flex gap-4">
                <button class="h-10 w-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer border border-gray-200" title="Google SSO"></button>
                <button class="h-10 w-10 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer border border-gray-200" title="Facebook SSO"></button>
            </div>
        </div>
    </GuestLayout>
</template>