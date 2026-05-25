<template>
  <div class="flex min-h-screen bg-[#F8FAFC] font-sans text-slate-900">
    <PetugasSidebar class="fixed inset-y-0 left-0 w-80 z-50 shadow-xl shadow-slate-200/50" />

    <main class="flex-1 ml-80 min-h-screen relative">
      <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-slate-200 px-10 py-6">
        <div class="flex items-center justify-between max-w-[1400px] mx-auto">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Konfigurasi & Profil</h1>
            <p class="text-slate-500 text-sm mt-1">Personalisasi akun dan atur parameter sistem perpustakaan</p>
          </div>
          <div class="flex items-center gap-3">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sistem Online</span>
          </div>
        </div>
      </header>

      <div class="p-10 max-w-[1400px] mx-auto grid grid-cols-1 xl:grid-cols-12 gap-8">
        <div class="xl:col-span-8 space-y-8">
          <section class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-100">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Informasi Personal</h2>
              </div>
            </div>

            <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap Petugas</label>
                <input type="text" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500/20 text-sm font-medium transition-all">
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
                <input type="email" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500/20 text-sm font-medium transition-all">
              </div>
              <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp / HP</label>
                <input type="text" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-500/20 text-sm font-medium transition-all">
              </div>
              <div class="md:col-span-2 pt-4">
                <button class="px-8 py-3.5 bg-orange-500 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-orange-700 shadow-xl shadow-orange-100 transition-all">
                  Simpan Perubahan Profil
                </button>
              </div>
            </div>
          </section>

          <section class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Keamanan Akun</h2>
              </div>
            </div>

            <div class="p-10 space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password Saat Ini</label>
                  <input type="password" placeholder="••••••••" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500/20 text-sm transition-all">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password Baru</label>
                  <input type="password" placeholder="Min. 8 Karakter" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500/20 text-sm transition-all">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ulangi Password</label>
                  <input type="password" placeholder="Konfirmasi" class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-rose-500/20 text-sm transition-all">
                </div>
              </div>
              <button class="px-8 py-3.5 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-rose-600 transition-all">
                Perbarui Kata Sandi
              </button>
            </div>
          </section>
        </div>

        <div class="xl:col-span-4">
          <section class="bg-orange-500 rounded-[2.5rem] shadow-2xl shadow-orange-200 p-8 sticky top-32">
            <h2 class="text-white text-xl font-black mb-2 tracking-tight">Parameter Sistem</h2>
            <p class="text-orange-300 text-xs mb-8">Aturan baku peminjaman buku yang berlaku secara global.</p>
            
            <form @submit.prevent="updateParameters" class="space-y-6">
              <div class="bg-white/10 p-6 rounded-3xl border border-white/10 space-y-4">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-orange-200 uppercase tracking-[0.2em] ml-1">Maks. Hari Peminjaman</label>
                  <div class="relative">
                    <input v-model.number="settingsForm.max_borrow_days" type="number" min="1" class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 text-sm focus:ring-2 focus:ring-white/20 focus:border-transparent transition-all">
                    <span class="absolute right-4 top-3 text-[10px] font-bold text-orange-300">HARI</span>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-black text-orange-200 uppercase tracking-[0.2em] ml-1">Denda Keterlambatan</label>
                  <div class="relative">
                    <input v-model.number="settingsForm.late_fee_per_day" type="number" min="0" class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 text-sm focus:ring-2 focus:ring-white/20 focus:border-transparent transition-all">
                    <span class="absolute left-4 top-3 text-[10px] font-bold text-orange-300">RP</span>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-black text-orange-200 uppercase tracking-[0.2em] ml-1">Limit Buku per Member</label>
                  <input v-model.number="settingsForm.max_books_per_member" type="number" min="1" class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 text-sm focus:ring-2 focus:ring-white/20 focus:border-transparent transition-all">
                </div>
              </div>

              <button type="submit" class="w-full py-4 bg-white text-orange-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-400 hover:text-white transition-all shadow-lg">
                Update Parameter
              </button>
            </form>

            <div class="mt-10 p-5 bg-white/5 rounded-2xl border border-white/5">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center">
                  <svg class="w-4 h-4 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[10px] text-orange-200 leading-relaxed font-medium">Perubahan parameter akan langsung berdampak pada perhitungan denda otomatis sistem.</p>
              </div>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import PetugasSidebar from '@/Components/PetugasSidebar.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { successAlert } from '@/Utils/sweetAlert';

const props = defineProps({
  parameters: {
    type: Array,
    default: () => []
  }
});

const settingsForm = ref({
  max_borrow_days: 7,
  late_fee_per_day: 5000,
  max_books_per_member: 3,
});

onMounted(() => {
  // Load values from props (from database)
  if (props.parameters && props.parameters.length > 0) {
    props.parameters.forEach(param => {
      if (param.key === 'max_borrow_days') {
        settingsForm.value.max_borrow_days = parseInt(param.value);
      } else if (param.key === 'fine_per_day') {
        settingsForm.value.late_fee_per_day = parseInt(param.value);
      } else if (param.key === 'max_books_per_member') {
        settingsForm.value.max_books_per_member = parseInt(param.value);
      }
    });
  }
});

const updateParameters = () => {
  router.post(route('petugas.settings.update'), settingsForm.value, {
    onSuccess: () => {
      successAlert({
        title: 'Parameter diperbarui',
        text: 'Pengaturan sistem berhasil disimpan dan langsung berlaku.',
      });
    },
  });
};
</script>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
