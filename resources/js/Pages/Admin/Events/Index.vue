<template>
  <AdminLayout title="Kelola Event">
    <div class="space-y-8">
      <div class="flex flex-col m-10 gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs font-black uppercase tracking-[0.24em] text-orange-500">Event Literasi</p>
          <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Kelola Event</h1>
          <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-slate-500">
            Buat event poin atau karya, pantau partisipasi, dan tentukan pemenang dari satu halaman yang rapi.
          </p>
        </div>
        <button
          @click="showCreate = true"
          class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600"
        >
          Tambah Event
        </button>
      </div>

      <div class="grid gap-6 lg:grid-cols-[1.7fr_1fr] ms-6">
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
          <div class="mb-5 flex items-center justify-between gap-4">
            <div>
              <h2 class="text-lg font-black text-slate-950">Daftar Event</h2>
              <p class="text-sm font-medium text-slate-500">{{ events.length }} event tercatat</p>
            </div>
          </div>

          <div v-if="events.length === 0" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center">
            <p class="font-bold text-slate-700">Belum ada event literasi.</p>
          </div>

          <div v-else class="grid gap-4 md:grid-cols-2">
            <article v-for="event in events" :key="event.id" class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl">
              <div class="relative h-40 bg-orange-100">
                <img v-if="event.image_url" :src="event.image_url" :alt="event.title" class="h-full w-full object-cover">
                <div v-else class="flex h-full items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100 text-sm font-black text-orange-600">
                  Event Literasi
                </div>
                <div class="absolute left-4 top-4 flex gap-2">
                  <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-black text-orange-600 shadow-sm">{{ event.category_label }}</span>
                  <span :class="statusClass(event)" class="rounded-full px-3 py-1 text-xs font-black shadow-sm">{{ event.status_label }}</span>
                </div>
              </div>
              <div class="space-y-4 p-5">
                <div>
                  <h3 class="line-clamp-2 text-lg font-black leading-snug text-slate-950">{{ event.title }}</h3>
                  <p class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-400">{{ formatDate(event.starts_at) }} - {{ formatDate(event.ends_at) }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="rounded-xl bg-orange-50 p-3">
                    <p class="text-xs font-bold text-orange-700">Peserta</p>
                    <p class="mt-1 text-2xl font-black text-orange-600">{{ event.participants_count }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs font-bold text-slate-500">Pemenang</p>
                    <p class="mt-1 truncate text-sm font-black text-slate-800">{{ event.winner?.name || '-' }}</p>
                  </div>
                </div>
                <div class="flex gap-2">
                  <Link :href="route('admin.events.show', event.id)" class="flex-1 rounded-xl bg-slate-950 px-4 py-2.5 text-center text-xs font-black text-white transition hover:bg-orange-600">
                    Detail
                  </Link>
                  <button @click="deleteEvent(event)" class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-2.5 text-xs font-black text-rose-600 transition hover:bg-rose-100">
                    Cabut
                  </button>
                </div>
              </div>
            </article>
          </div>
        </section>

        <aside class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
          <div class="mb-5">
            <h2 class="text-lg font-black text-slate-950">Leaderboard Poin User</h2>
            <p class="text-sm font-medium text-slate-500">Setiap 1 buku bernilai {{ pointPerBook }} poin.</p>
          </div>
          <div class="space-y-3">
            <div v-if="leaderboard.length === 0" class="rounded-xl bg-slate-50 p-5 text-center text-sm font-semibold text-slate-500">
              Belum ada poin pengguna.
            </div>
            <div v-for="(user, index) in leaderboard" :key="user.id" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 text-xs font-black text-orange-600">{{ index + 1 }}</div>
              <img :src="avatarFor(user)" :alt="user.name" class="h-10 w-10 rounded-xl object-cover">
              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-black text-slate-900">{{ user.name }}</p>
                <p class="truncate text-xs font-semibold text-slate-400">{{ user.email }}</p>
              </div>
              <p class="text-sm font-black text-orange-600">{{ user.points }}</p>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
      <form @submit.prevent="submitCreate" class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-6 flex items-start justify-between gap-4">
          <div>
            <h2 class="text-2xl font-black text-slate-950">Tambah Event</h2>
            <p class="text-sm font-medium text-slate-500">Lengkapi detail event yang akan tampil di kategori Event.</p>
          </div>
          <button type="button" @click="showCreate = false" class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-500 hover:bg-slate-200">Tutup</button>
        </div>

        <div class="grid gap-4">
          <input v-model="form.title" required placeholder="Judul event" class="input">
          <textarea v-model="form.description" required rows="4" placeholder="Deskripsi event" class="input"></textarea>
          <textarea v-model="form.terms" rows="4" placeholder="Ketentuan event" class="input"></textarea>
          <div class="grid gap-4 md:grid-cols-3">
            <select v-model="form.category" required class="input">
              <option value="point">Poin</option>
              <option value="karya">Karya</option>
            </select>
            <input v-model="form.starts_at" required type="date" class="input">
            <input v-model="form.ends_at" required type="date" class="input">
          </div>
          <label class="rounded-2xl border-2 border-dashed border-orange-200 bg-orange-50/40 p-6 text-center text-sm font-bold text-orange-700 transition hover:border-orange-400">
            <input type="file" accept="image/*" class="hidden" @change="handleImage">
            {{ form.image ? form.image.name : 'Pilih gambar event' }}
          </label>
          <div v-if="Object.keys(form.errors).length" class="rounded-xl bg-rose-50 p-4 text-sm font-semibold text-rose-600">
            Periksa kembali data event yang diisi.
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button type="button" @click="showCreate = false" class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-black text-slate-600 hover:bg-slate-200">Batal</button>
          <button type="submit" :disabled="form.processing" class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-black text-white hover:bg-orange-600 disabled:opacity-60">
            {{ form.processing ? 'Menyimpan...' : 'Simpan Event' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
  events: { type: Array, default: () => [] },
  leaderboard: { type: Array, default: () => [] },
  pointPerBook: { type: Number, default: 10 },
});

const showCreate = ref(false);
const form = useForm({
  title: '',
  description: '',
  terms: '',
  category: 'point',
  starts_at: '',
  ends_at: '',
  image: null,
});

function handleImage(event) {
  form.image = event.target.files?.[0] || null;
}

function submitCreate() {
  form.post(route('admin.events.store'), {
    forceFormData: true,
    onSuccess: () => {
      form.reset();
      showCreate.value = false;
      Swal.fire('Berhasil', 'Event literasi berhasil dibuat.', 'success');
    },
  });
}

async function deleteEvent(event) {
  const result = await Swal.fire({
    title: 'Cabut event?',
    text: `${event.title} akan dihapus permanen dari sistem.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonText: 'Batal',
    confirmButtonText: 'Ya, cabut',
  });

  if (result.isConfirmed) {
    router.delete(route('admin.events.destroy', event.id));
  }
}

function avatarFor(user) {
  return user.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'User')}&background=f97316&color=fff`;
}

function formatDate(date) {
  if (!date) return '-';
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(date));
}

function statusClass(event) {
  return event.status_label === 'Aktif'
    ? 'bg-emerald-50 text-emerald-700'
    : 'bg-slate-50 text-slate-600';
}
</script>

<style scoped>
.input {
  @apply w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-100;
}
</style>
