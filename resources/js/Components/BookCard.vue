<template>
  <article class="w-[140px] flex-shrink-0 bg-white shadow-sm border border-slate-100 flex flex-col transition-all hover:shadow-xl hover:-translate-y-2 overflow-hidden">
    <!-- Cover -->
    <div class="aspect-[3/4] w-full overflow-hidden relative bg-slate-100">
      <img
        v-if="book.cover_image && !hasImageError"
        :src="book.cover_image"
        class="h-full w-full object-cover"
        :alt="book.title"
        @error="$emit('imageError', book.id)"
      />
      <div
        v-else
        class="flex h-full w-full items-center justify-center bg-orange-50 text-orange-400 font-black text-4xl"
      >
        {{ bookInitial }}
      </div>

      <!-- Badge Stock Habis -->
      <div
        v-if="isOutOfStock"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center"
      >
        <span class="bg-red-500 text-white text-[10px] font-black px-3 py-1 uppercase tracking-widest">
          Habis
        </span>
      </div>
    </div>

    <!-- Content -->
    <div class="p-2 flex flex-col flex-1 text-center">
      <h3 class="text-[11px] font-black text-slate-900 line-clamp-1 mb-1">
        {{ book.title }}
      </h3>
      <p class="text-[9px] text-slate-500 font-bold mb-2 italic line-clamp-1">
        {{ book.author }}
      </p>
      <p class="text-[8px] text-orange-400 font-bold mb-1">
        {{ book.category }}
      </p>

      <button
        @click="$emit('borrow', book)"
        :disabled="isOutOfStock"
        class="mt-auto w-full py-1.5 rounded-full text-[9px] font-black uppercase tracking-wider transition-all shadow-md active:scale-95"
        :class="isOutOfStock
          ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
          : 'bg-orange-500 hover:bg-[#b5652d] text-white'"
      >
        Pinjam Buku
      </button>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  book: {
    type: Object,
    required: true
  },
  imgErrors: {
    type: Object,
    default: () => ({})
  }
});

defineEmits(['borrow', 'imageError']);

const hasImageError = computed(() => props.imgErrors[props.book.id]);
const isOutOfStock = computed(() => props.book.available_quantity === 0);
const bookInitial = computed(() => props.book.title?.charAt(0).toUpperCase() || '?');
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

article {
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
}
</style>