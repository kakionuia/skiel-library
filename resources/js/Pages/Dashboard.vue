<template>
  <UserLayout>
    <div class="space-y-8">
      <section class="overflow-hidden rounded-3xl border border-orange-100 bg-white shadow-sm">
        <div class="grid gap-6 p-8 lg:grid-cols-[1.5fr_1fr]">
          <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-600">Dashboard Siswa</p>
            <h1 class="mt-3 text-3xl font-black leading-tight text-slate-950">
              Selamat Datang, {{ user.name }}!
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-500">
              Pantau peminjaman, jelajahi koleksi buku, kirim ulasan, keluhan, dan saran langsung dari satu halaman.
            </p>
          </div>

          <div class="rounded-2xl bg-orange-50 p-5">
            <div class="flex items-center gap-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-600 text-lg font-black text-white">
                {{ user.name?.charAt(0)?.toUpperCase() || 'S' }}
              </div>
              <div>
                <p class="text-sm font-black text-slate-900">{{ user.name }}</p>
                <p class="text-xs text-slate-500">{{ user.email }}</p>
              </div>
            </div>
            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
              <div class="rounded-xl bg-white p-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">NISN</p>
                <p class="mt-1 font-bold text-slate-800">{{ user.nisn || '-' }}</p>
              </div>
              <div class="rounded-xl bg-white p-3">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas</p>
                <p class="mt-1 font-bold text-slate-800">{{ user.kelas || '-' }}</p>
              </div>
            </div>
            <Link href="/profile" class="mt-4 block rounded-xl bg-orange-600 px-4 py-3 text-center text-sm font-black text-white transition hover:bg-orange-700">
              Edit Profil
            </Link>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div v-for="stat in statCards" :key="stat.label" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ stat.label }}</p>
          <p class="mt-3 text-3xl font-black" :class="stat.color">{{ stat.value }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ stat.caption }}</p>
        </div>
      </section>

      <section v-if="user.borrow_restricted" class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
        <p class="text-sm font-black text-rose-900">Peminjaman Anda sedang dibatasi.</p>
        <p class="mt-1 text-sm text-rose-700">Hubungi petugas perpustakaan untuk informasi lebih lanjut.</p>
      </section>

      <section class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
              <h2 class="text-lg font-black text-slate-950">Katalog Buku Tersedia</h2>
              <p class="text-sm text-slate-500">Data buku approved dari database perpustakaan.</p>
            </div>
            <div class="flex gap-3">
              <input v-model="bookSearch" type="text" placeholder="Cari buku..." class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 md:w-56">
              <select v-model="activeCategory" class="rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500">
                <option value="">Semua</option>
                <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
              </select>
            </div>
          </div>

          <div class="mt-6 grid gap-4 md:grid-cols-2">
            <article v-for="book in filteredBooks" :key="book.id" class="rounded-2xl border border-slate-100 bg-slate-50 p-4 transition hover:border-orange-200 hover:bg-orange-50/40">
              <div class="flex gap-4">
                <div class="h-28 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-white">
                  <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center bg-orange-100 text-xl font-black text-orange-600">
                    {{ book.title?.charAt(0) || 'B' }}
                  </div>
                </div>
                <div class="min-w-0 flex-1">
                  <p class="truncate text-sm font-black text-slate-900">{{ book.title }}</p>
                  <p class="mt-1 truncate text-xs text-slate-500">{{ book.author }}</p>
                  <p class="mt-2 text-[11px] font-bold uppercase tracking-wider text-orange-600">{{ book.category }}</p>
                  <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-bold">
                    <span class="rounded-full bg-white px-2.5 py-1 text-slate-600">{{ book.available_quantity }}/{{ book.quantity }} tersedia</span>
                    <span class="rounded-full bg-white px-2.5 py-1 text-slate-600">Rating {{ book.average_rating || '0.0' }}</span>
                  </div>
                </div>
              </div>
            </article>
          </div>

          <div v-if="filteredBooks.length === 0" class="py-12 text-center text-sm text-slate-500">
            Belum ada buku yang cocok dengan pencarian.
          </div>
        </div>

        <aside class="space-y-6">
          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Buku Populer</h2>
            <div class="mt-5 space-y-3">
              <div v-for="book in popularBooks" :key="book.id" class="flex items-center justify-between rounded-2xl bg-slate-50 p-4">
                <div class="min-w-0">
                  <p class="truncate text-sm font-bold text-slate-900">{{ book.title }}</p>
                  <p class="text-xs text-slate-500">{{ book.author }}</p>
                </div>
                <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-black text-orange-700">{{ book.borrows_count }}x</span>
              </div>
              <p v-if="popularBooks.length === 0" class="text-sm text-slate-500">Belum ada data peminjaman.</p>
            </div>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Kebijakan Peminjaman</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
              <li>Maksimal {{ maxBooksPerUser }} buku per siswa.</li>
              <li>Durasi peminjaman {{ maxLoanDays }} hari.</li>
              <li>Denda keterlambatan {{ formatCurrency(finePerDay) }} per hari.</li>
              <li>Peminjaman dan pengembalian diproses oleh petugas.</li>
            </ul>
          </div>
        </aside>
      </section>

      <section class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Buku Sedang Dipinjam</h2>
          <div class="mt-5 space-y-3">
            <div v-for="borrow in activeBorrows" :key="borrow.id" class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-bold text-slate-900">{{ borrow.book?.title || 'Buku' }}</p>
                  <p class="text-sm text-slate-500">{{ borrow.book?.author || '-' }}</p>
                  <p class="mt-2 text-xs text-slate-500">Jatuh tempo: {{ formatDate(borrow.due_date) }}</p>
                </div>
                <span :class="dueBadgeClass(borrow.due_date)" class="rounded-full px-3 py-1 text-[10px] font-black uppercase">
                  {{ dueText(borrow.due_date) }}
                </span>
              </div>
            </div>
            <p v-if="activeBorrows.length === 0" class="py-8 text-center text-sm text-slate-500">Tidak ada buku yang sedang dipinjam.</p>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Riwayat Peminjaman</h2>
          <div class="mt-5 overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                  <th class="py-3 pr-4">Buku</th>
                  <th class="py-3 pr-4">Pinjam</th>
                  <th class="py-3 pr-4">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="borrow in recentBorrows" :key="borrow.id">
                  <td class="py-4 pr-4">
                    <p class="font-bold text-slate-900">{{ borrow.book?.title || 'Buku' }}</p>
                    <p class="text-xs text-slate-500">{{ borrow.book?.author || '-' }}</p>
                  </td>
                  <td class="py-4 pr-4 text-slate-500">{{ formatDate(borrow.borrow_date) }}</td>
                  <td class="py-4 pr-4">
                    <span :class="borrowStatusClass(borrow.status)" class="rounded-full px-3 py-1 text-[10px] font-black uppercase">
                      {{ borrowStatusText(borrow.status) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-if="recentBorrows.length === 0" class="py-8 text-center text-sm text-slate-500">Belum ada riwayat peminjaman.</p>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        <form @submit.prevent="submitReview" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Beri Ulasan Buku</h2>
          <div class="mt-5 space-y-4">
            <select v-model="reviewForm.book_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" required>
              <option value="">Pilih buku</option>
              <option v-for="book in availableBooks" :key="book.id" :value="book.id">{{ book.title }}</option>
            </select>
            <select v-model="reviewForm.rating" class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" required>
              <option v-for="rating in [5,4,3,2,1]" :key="rating" :value="rating">{{ rating }} bintang</option>
            </select>
            <textarea v-model="reviewForm.comment" rows="4" placeholder="Tulis ulasan..." class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
            <button :disabled="reviewForm.processing" class="w-full rounded-xl bg-orange-600 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-700 disabled:opacity-50">
              Simpan Ulasan
            </button>
          </div>
        </form>

        <form @submit.prevent="submitComplaint" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Kirim Keluhan</h2>
          <div class="mt-5 space-y-4">
            <input v-model="complaintForm.subject" type="text" placeholder="Subjek keluhan" class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" required>
            <textarea v-model="complaintForm.description" rows="5" placeholder="Ceritakan kendala yang dialami..." class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" required></textarea>
            <button :disabled="complaintForm.processing" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600 disabled:opacity-50">
              Kirim Keluhan
            </button>
          </div>
        </form>

        <form @submit.prevent="submitSuggestion" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Kirim Saran</h2>
          <div class="mt-5 space-y-4">
            <textarea v-model="suggestionForm.content" rows="7" placeholder="Tulis saran untuk perpustakaan..." class="w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" required></textarea>
            <button :disabled="suggestionForm.processing" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-black text-white transition hover:bg-orange-600 disabled:opacity-50">
              Kirim Saran
            </button>
          </div>
        </form>
      </section>

      <section class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Ulasan Terbaru</h2>
          <div class="mt-5 space-y-4">
            <div v-for="review in latestReviews" :key="review.id" class="border-b border-slate-100 pb-4 last:border-0 last:pb-0">
              <p class="text-sm font-bold text-slate-900">{{ review.book?.title || 'Buku' }}</p>
              <p class="mt-1 text-xs text-orange-600">{{ review.rating }} bintang oleh {{ review.user?.name || 'Siswa' }}</p>
              <p class="mt-2 text-sm text-slate-500">{{ review.comment || 'Tanpa komentar.' }}</p>
            </div>
            <p v-if="latestReviews.length === 0" class="text-sm text-slate-500">Belum ada ulasan.</p>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Keluhan Saya</h2>
          <div class="mt-5 space-y-3">
            <div v-for="complaint in userComplaints" :key="complaint.id" class="rounded-2xl bg-slate-50 p-4">
              <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-bold text-slate-900">{{ complaint.subject }}</p>
                <span :class="complaintStatusClass(complaint.status)" class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase">{{ complaint.status }}</span>
              </div>
              <p class="mt-2 line-clamp-2 text-xs text-slate-500">{{ complaint.description }}</p>
            </div>
            <p v-if="userComplaints.length === 0" class="text-sm text-slate-500">Belum ada keluhan.</p>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-black text-slate-950">Saran Saya</h2>
          <div class="mt-5 space-y-3">
            <div v-for="suggestion in userSuggestions" :key="suggestion.id" class="rounded-2xl bg-slate-50 p-4">
              <div class="flex items-start justify-between gap-3">
                <p class="line-clamp-2 text-sm font-bold text-slate-900">{{ suggestion.content }}</p>
                <span :class="suggestionStatusClass(suggestion.status)" class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase">{{ suggestion.status }}</span>
              </div>
              <p v-if="suggestion.admin_note" class="mt-2 text-xs text-slate-500">{{ suggestion.admin_note }}</p>
            </div>
            <p v-if="userSuggestions.length === 0" class="text-sm text-slate-500">Belum ada saran.</p>
          </div>
        </div>
      </section>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { successAlert } from '@/Utils/sweetAlert';

const props = defineProps({
  user: { type: Object, required: true },
  activeBorrows: { type: Array, default: () => [] },
  recentBorrows: { type: Array, default: () => [] },
  overdueCount: { type: Number, default: 0 },
  totalBorrows: { type: Number, default: 0 },
  totalFines: { type: Number, default: 0 },
  maxBooksPerUser: { type: Number, default: 3 },
  maxLoanDays: { type: Number, default: 7 },
  finePerDay: { type: Number, default: 5000 },
  availableBooks: { type: Array, default: () => [] },
  popularBooks: { type: Array, default: () => [] },
  latestReviews: { type: Array, default: () => [] },
  userReviews: { type: Array, default: () => [] },
  userComplaints: { type: Array, default: () => [] },
  userSuggestions: { type: Array, default: () => [] },
  dashboardStats: { type: Object, default: () => ({}) },
});

const bookSearch = ref('');
const activeCategory = ref('');

const reviewForm = useForm({ book_id: '', rating: 5, comment: '' });
const complaintForm = useForm({ subject: '', description: '' });
const suggestionForm = useForm({ content: '' });

const categories = computed(() => {
  return [...new Set(props.availableBooks.map((book) => book.category).filter(Boolean))].sort();
});

const filteredBooks = computed(() => {
  const search = bookSearch.value.toLowerCase();
  return props.availableBooks.filter((book) => {
    const matchesSearch = !search
      || book.title?.toLowerCase().includes(search)
      || book.author?.toLowerCase().includes(search)
      || book.isbn?.toLowerCase().includes(search);
    const matchesCategory = !activeCategory.value || book.category === activeCategory.value;
    return matchesSearch && matchesCategory;
  });
});

const statCards = computed(() => [
  { label: 'Judul Buku', value: props.dashboardStats.totalTitles ?? 0, caption: 'koleksi approved', color: 'text-orange-600' },
  { label: 'Eksemplar Tersedia', value: props.dashboardStats.availableCopies ?? 0, caption: 'siap dipinjam via petugas', color: 'text-emerald-600' },
  { label: 'Sedang Dipinjam', value: props.activeBorrows.length, caption: `${props.maxBooksPerUser} buku maksimal`, color: 'text-slate-900' },
  { label: 'Denda Aktif', value: formatCurrency(props.totalFines), caption: `${props.overdueCount} buku terlambat`, color: 'text-rose-600' },
]);

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatCurrency = (value) => {
  return Number(value || 0).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
};

const daysUntilDue = (dueDate) => {
  if (!dueDate) return 0;
  const now = new Date();
  const due = new Date(dueDate);
  return Math.ceil((due - now) / (1000 * 60 * 60 * 24));
};

const dueText = (dueDate) => {
  const days = daysUntilDue(dueDate);
  if (days > 3) return `${days} hari`;
  if (days > 0) return `${days} hari lagi`;
  return 'Terlambat';
};

const dueBadgeClass = (dueDate) => {
  const days = daysUntilDue(dueDate);
  if (days > 3) return 'bg-emerald-100 text-emerald-700';
  if (days > 0) return 'bg-amber-100 text-amber-700';
  return 'bg-rose-100 text-rose-700';
};

const borrowStatusText = (status) => ({
  returned: 'Dikembalikan',
  borrowed: 'Aktif',
  overdue: 'Terlambat',
  lost: 'Hilang',
}[status] || status);

const borrowStatusClass = (status) => ({
  returned: 'bg-emerald-100 text-emerald-700',
  borrowed: 'bg-orange-100 text-orange-700',
  overdue: 'bg-rose-100 text-rose-700',
  lost: 'bg-slate-200 text-slate-700',
}[status] || 'bg-slate-100 text-slate-700');

const complaintStatusClass = (status) => ({
  pending: 'bg-amber-100 text-amber-700',
  reviewed: 'bg-orange-100 text-orange-700',
  resolved: 'bg-emerald-100 text-emerald-700',
  rejected: 'bg-rose-100 text-rose-700',
}[status] || 'bg-slate-100 text-slate-700');

const suggestionStatusClass = (status) => ({
  pending: 'bg-amber-100 text-amber-700',
  reviewed: 'bg-orange-100 text-orange-700',
  implemented: 'bg-emerald-100 text-emerald-700',
  rejected: 'bg-rose-100 text-rose-700',
}[status] || 'bg-slate-100 text-slate-700');

const submitReview = () => {
  reviewForm.post(route('dashboard.reviews.store'), {
    preserveScroll: true,
    onSuccess: () => {
      reviewForm.reset('book_id', 'comment');
      reviewForm.rating = 5;
      successAlert({ title: 'Ulasan disimpan', text: 'Terima kasih sudah memberi ulasan.' });
    },
  });
};

const submitComplaint = () => {
  complaintForm.post(route('dashboard.complaints.store'), {
    preserveScroll: true,
    onSuccess: () => {
      complaintForm.reset();
      successAlert({ title: 'Keluhan terkirim', text: 'Admin akan meninjau keluhan Anda.' });
    },
  });
};

const submitSuggestion = () => {
  suggestionForm.post(route('dashboard.suggestions.store'), {
    preserveScroll: true,
    onSuccess: () => {
      suggestionForm.reset();
      successAlert({ title: 'Saran terkirim', text: 'Terima kasih atas masukannya.' });
    },
  });
};
</script>
