<template>
  <AdminLayout title="Detail Event">
    <div class="space-y-8 ms-8">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <Link :href="route('admin.events.index')" class="text-sm font-black text-orange-600 hover:text-orange-700">Kembali ke Kelola Event</Link>
          <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ event.title }}</h1>
          <p class="mt-2 text-sm font-bold uppercase tracking-wider text-slate-400">{{ event.category_label }} · {{ event.status_label }}</p>
        </div>
        <div v-if="event.winner" class="flex items-center gap-3 rounded-2xl border border-orange-100 bg-orange-50 p-4">
          <img :src="avatarFor(event.winner)" :alt="event.winner.name" class="h-14 w-14 rounded-xl object-cover">
          <div>
            <p class="text-xs font-black uppercase tracking-wider text-orange-500">Pemenang</p>
            <p class="font-black text-slate-950">{{ event.winner.name }}</p>
          </div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-[1fr_1.1fr]">
        <form @submit.prevent="submitUpdate" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
          <h2 class="mb-5 text-lg font-black text-slate-950">Edit Detail Event</h2>
          <div class="space-y-4">
            <input v-model="form.title" required class="input">
            <textarea v-model="form.description" required rows="5" class="input"></textarea>
            <textarea v-model="form.terms" rows="5" placeholder="Ketentuan event" class="input"></textarea>
            <div class="grid gap-4 md:grid-cols-3">
              <select v-model="form.category" required class="input" :disabled="event.winner_user_id">
                <option value="point">Poin</option>
                <option value="karya">Karya</option>
              </select>
              <input v-model="form.starts_at" required type="date" class="input">
              <input v-model="form.ends_at" required type="date" class="input">
            </div>
            <div v-if="event.image_url" class="overflow-hidden rounded-2xl border border-slate-100">
              <img :src="event.image_url" :alt="event.title" class="h-56 w-full object-cover">
            </div>
            <label class="block rounded-2xl border-2 border-dashed border-orange-200 bg-orange-50/40 p-5 text-center text-sm font-black text-orange-700">
              <input type="file" accept="image/*" class="hidden" @change="handleImage">
              {{ form.image ? form.image.name : 'Ganti gambar event' }}
            </label>
          </div>
          <div class="mt-6 flex justify-end">
            <button type="submit" :disabled="form.processing" class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-black text-white hover:bg-orange-600 disabled:opacity-60">
              {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>

        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
          <div class="mb-5 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-orange-50 p-4">
              <p class="text-xs font-black uppercase tracking-wider text-orange-600">Peserta</p>
              <p class="mt-2 text-3xl font-black text-orange-600">{{ participants.length }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
              <p class="text-xs font-black uppercase tracking-wider text-slate-500">Periode</p>
              <p class="mt-2 text-sm font-black text-slate-900">{{ formatDate(event.starts_at) }} - {{ formatDate(event.ends_at) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-950 p-4">
              <p class="text-xs font-black uppercase tracking-wider text-orange-200">Nilai Buku</p>
              <p class="mt-2 text-2xl font-black text-white">{{ pointPerBook }} poin</p>
            </div>
          </div>

          <div class="mb-4 flex items-center justify-between">
            <div>
              <h2 class="text-lg font-black text-slate-950">User Berpartisipasi</h2>
              <p class="text-sm font-medium text-slate-500">
                {{ event.category === 'point' ? 'Diurutkan dari poin event terbesar.' : 'Periksa karya PDF satu per satu sebelum memilih pemenang.' }}
              </p>
            </div>
          </div>

          <div v-if="participants.length === 0" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center">
            <p class="font-bold text-slate-600">Belum ada user yang berpartisipasi.</p>
          </div>

          <div v-else class="overflow-hidden rounded-2xl border border-slate-100">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-500">
                <tr>
                  <th class="px-4 py-3">User</th>
                  <th class="px-4 py-3">Poin</th>
                  <th class="px-4 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="participant in participants" :key="participant.id" class="bg-white">
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <img :src="avatarFor(participant.user)" :alt="participant.user?.name" class="h-10 w-10 rounded-xl object-cover">
                      <div class="min-w-0">
                        <p class="truncate font-black text-slate-900">{{ participant.user?.name }}</p>
                        <p class="truncate text-xs font-semibold text-slate-400">{{ participant.user?.email }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-4">
                    <span class="font-black text-orange-600">{{ participant.event_points || 0 }}</span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex flex-wrap gap-2">
                      <a
                        v-if="event.category === 'karya' && participant.submission_url"
                        :href="route('admin.events.submissions.show', [event.id, participant.id])"
                        target="_blank"
                        class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-200"
                      >
                        Lihat Karya
                      </a>
                      <button
                        @click="chooseWinner(participant)"
                        :disabled="Boolean(event.winner_user_id)"
                        class="rounded-lg bg-orange-500 px-3 py-2 text-xs font-black text-white hover:bg-orange-600 disabled:cursor-not-allowed disabled:bg-slate-300"
                      >
                        Pemenang
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  event: { type: Object, required: true },
  participants: { type: Array, default: () => [] },
  pointPerBook: { type: Number, default: 10 },
});

const form = useForm({
  title: props.event.title,
  description: props.event.description,
  terms: props.event.terms || '',
  category: props.event.category,
  starts_at: props.event.starts_at?.slice(0, 10),
  ends_at: props.event.ends_at?.slice(0, 10),
  image: null,
});

function handleImage(event) {
  form.image = event.target.files?.[0] || null;
}

function submitUpdate() {
  form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.events.update', props.event.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.image = null;
      Swal.fire('Berhasil', 'Event berhasil diperbarui.', 'success');
    },
  });
}

async function chooseWinner(participant) {
  const result = await Swal.fire({
    title: 'Tetapkan pemenang?',
    text: `${participant.user?.name} akan menjadi pemenang dan event berubah menjadi tidak aktif.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonText: 'Batal',
    confirmButtonText: 'Tetapkan',
  });

  if (result.isConfirmed) {
    router.post(route('admin.events.winner', [props.event.id, participant.user_id]), {}, { preserveScroll: true });
  }
}

function avatarFor(user) {
  return user?.profile_photo_url
    || (user?.profile_photo_path ? `/storage/${user.profile_photo_path}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&background=f97316&color=fff`);
}

function formatDate(date) {
  if (!date) return '-';
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(date));
}
</script>

<style scoped>
.input {
  @apply w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400;
}
</style>
