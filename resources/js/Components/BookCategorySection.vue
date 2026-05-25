<template>
  <section class="mx-auto max-w-7xl px-4 py-12 md:py-20">
  <h2 class="mb-8 text-2xl font-black text-slate-900 md:text-3xl">Novel Populer</h2> <!-- bisa diganti judul sesuai kebutuhan -->
  <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
    <div v-for="(cat, idx) in groupedBooks" :key="idx"
         class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
      <h3 class="text-lg font-black text-slate-900">{{ cat.name }}</h3>
      <ul class="mt-6 space-y-5">
        <li v-for="book in cat.books" :key="book.id" class="flex items-start gap-4">
          <div class="h-14 w-11 flex-shrink-0 overflow-hidden rounded-lg bg-slate-100">
            <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="h-full w-full object-cover">
            <div v-else class="flex h-full w-full items-center justify-center bg-orange-100 text-sm font-black text-orange-600">
              {{ book.title?.charAt(0) || 'B' }}
            </div>
          </div>
          <div class="min-w-0 flex-1 pt-0.5">
            <p class="truncate text-sm font-bold text-slate-900">{{ book.title }}</p>
            <p class="truncate text-xs text-slate-500">{{ book.author }}</p>
          </div>
        </li>
      </ul>
      <p v-if="cat.books.length === 0" class="py-4 text-center text-sm text-slate-400">Belum ada buku</p>
    </div>
  </div>
</section>
</template>

<script setup>
defineProps({
  categories: {
    type: Array,
    required: true,
    // Format: [{ name: 'Buku Fiksi', books: [{ id, title, author, cover_image }] }]
  },
});
</script>