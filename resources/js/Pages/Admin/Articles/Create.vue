<template>
  <AuthenticatedLayout title="Buat Artikel">
    <div class="max-w-4xl mx-auto px-4 sm:px-0">
      <div class="mb-8">
        <Link :href="route('admin.articles')" class="inline-flex items-center gap-1.5 text-orange-600 hover:text-orange-700 text-sm font-bold tracking-wide transition mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Kembali ke Daftar Artikel
        </Link>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Buat Artikel Baru</h1>
        <p class="text-slate-500 mt-1.5 text-sm font-medium">Tambahkan artikel atau berita baru ke dalam sistem repositori publikasi.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-3">Judul Artikel <span class="text-orange-500">*</span></label>
          <input
            v-model="form.title"
            type="text"
            placeholder="Masukkan judul artikel..."
            class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium"
            required
          />
          <p v-if="form.errors.title" class="text-rose-600 text-xs mt-2 font-medium">{{ form.errors.title }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-3">Kategori <span class="text-orange-500">*</span></label>
            <select v-model="form.category" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium appearance-none" required>
              <option value="">-- Pilih Kategori --</option>
              <option value="artikel">Artikel</option>
              <option value="berita">Berita</option>
            </select>
            <p v-if="form.errors.category" class="text-rose-600 text-xs mt-2 font-medium">{{ form.errors.category }}</p>
          </div>

          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-3">Status <span class="text-orange-500">*</span></label>
            <select v-model="form.status" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium appearance-none" required>
              <option value="">-- Pilih Status --</option>
              <option value="draft">Draft</option>
              <option value="pending">Pending Persetujuan</option>
              <option value="approved">Langsung Dipublikasikan</option>
            </select>
            <p v-if="form.errors.status" class="text-rose-600 text-xs mt-2 font-medium">{{ form.errors.status }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <div class="flex justify-between items-center mb-3">
            <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase">Ringkasan Singkat <span class="text-orange-500">*</span></label>
            <span class="text-xs font-semibold px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md">{{ form.excerpt?.length || 0 }}/500</span>
          </div>
          <textarea
            v-model="form.excerpt"
            placeholder="Ringkasan artikel (max 500 karakter)..."
            rows="3"
            maxlength="500"
            class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm leading-relaxed"
            required
          />
          <p v-if="form.errors.excerpt" class="text-rose-600 text-xs mt-2 font-medium">{{ form.errors.excerpt }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-3">Konten Artikel <span class="text-orange-500">*</span></label>
          <textarea
            v-model="form.content"
            placeholder="Tulis konten artikel di sini..."
            rows="10"
            class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-mono text-sm leading-relaxed"
            required
          />
          <p v-if="form.errors.content" class="text-rose-600 text-xs mt-2 font-medium">{{ form.errors.content }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-3">Gambar Utama</label>
          
          <div 
            @click="$refs.imageInput.click()"
            class="border-2 border-dashed border-slate-200 hover:border-orange-400 rounded-2xl p-8 text-center cursor-pointer bg-slate-50/30 hover:bg-orange-50/10 transition-all group"
          >
            <div v-if="!form.featured_image" class="space-y-2">
              <svg class="mx-auto h-10 w-10 text-slate-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-sm text-slate-600 font-medium">Klik untuk memilih gambar utama</p>
              <p class="text-xs text-slate-400">Format PNG, JPG, JPEG maks 2MB</p>
            </div>
            <div v-else class="text-sm text-slate-600 flex items-center justify-center gap-1.5 font-bold text-orange-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
              Gambar berhasil dipilih dan siap diunggah
            </div>
          </div>
          <input ref="imageInput" type="file" class="hidden" accept="image/*" @change="handleImageUpload" />
          <p v-if="form.errors.featured_image" class="text-rose-600 text-xs mt-2 font-medium">{{ form.errors.featured_image }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-amber-500 text-white rounded-xl hover:opacity-95 disabled:from-slate-400 disabled:to-slate-400 font-bold text-sm tracking-wide shadow-md shadow-orange-500/10 transition-all active:scale-[0.98]"
          >
            {{ form.processing ? 'Menyimpan...' : 'Simpan Artikel' }}
          </button>
          <Link :href="route('admin.articles')" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 font-bold text-sm tracking-wide transition">
            Batal
          </Link>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

const form = useForm({
  title: '',
  content: '',
  excerpt: '',
  category: '',
  featured_image: null,
  status: 'pending'
})

const handleImageUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.featured_image = file
  }
}

const submitForm = () => {
  form.post(route('admin.articles.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>