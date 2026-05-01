<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import { useForm } from '@inertiajs/vue3';
import { dangerPrompt, infoAlert } from '@/Utils/sweetAlert';

const form = useForm({
    password: '',
});

const confirmUserDeletion = async () => {
    const password = await dangerPrompt({
        title: 'Hapus akun?',
        text: 'Masukkan password untuk menghapus akun secara permanen.',
        input: 'password',
        inputPlaceholder: 'Password akun',
        confirmButtonText: 'Ya, hapus akun',
        required: true,
    });

    if (password !== null) {
        form.password = password;
        deleteUser();
    }
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onError: () => {
            infoAlert({
                title: 'Gagal menghapus akun',
                text: 'Password tidak sesuai atau permintaan tidak valid.',
            });
        },
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

    </section>
</template>
