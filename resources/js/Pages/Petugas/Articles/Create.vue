<template>
  <AuthenticatedLayout title="Buat Artikel">
    <div class="max-w-4xl mx-auto">
      <div class="mb-8">
        <Link :href="route('petugas.articles.my')" class="text-blue-600 hover:text-blue-800 text-sm font-semibold mb-4 inline-block">
          ← Kembali ke Artikel Saya
        </Link>
        <h1 class="text-3xl font-bold text-gray-900">Buat Artikel Baru</h1>
        <p class="text-gray-600 mt-2">Tulis artikel atau berita untuk perpustakaan. Artikel Anda akan menunggu persetujuan admin sebelum dipublikasikan.</p>
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Judul -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Artikel *</label>
          <input
            v-model="form.title"
            type="text"
            placeholder="Masukkan judul artikel..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</p>
        </div>

        <!-- Kategori -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori *</label>
          <select v-model="form.category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="artikel">Artikel</option>
            <option value="berita">Berita</option>
          </select>
          <p v-if="form.errors.category" class="text-red-600 text-sm mt-1">{{ form.errors.category }}</p>
        </div>

        <!-- Ringkasan -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Ringkasan Singkat *</label>
          <textarea
            v-model="form.excerpt"
            placeholder="Ringkasan artikel (max 500 karakter)..."
            rows="3"
            maxlength="500"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <p class="text-sm text-gray-500 mt-1">{{ form.excerpt?.length || 0 }}/500 karakter</p>
          <p v-if="form.errors.excerpt" class="text-red-600 text-sm mt-1">{{ form.errors.excerpt }}</p>
        </div>

        <!-- Konten -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Konten Artikel *</label>
          <textarea
            v-model="form.content"
            placeholder="Tulis konten artikel di sini..."
            rows="10"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
            required
          />
          <p v-if="form.errors.content" class="text-red-600 text-sm mt-1">{{ form.errors.content }}</p>
        </div>

        <!-- Gambar Utama -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Utama</label>
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 transition" @click="$refs.imageInput.click()">
            <div v-if="!form.featured_image" class="space-y-2">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-12l-3.172-3.172a4 4 0 00-5.656 0L28 20M9 20l3.172-3.172a4 4 0 015.656 0L28 20m19-8a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <p class="text-sm text-gray-600">Klik untuk memilih gambar</p>
            </div>
            <div v-else class="text-sm text-gray-600">
              <p class="font-semibold text-green-600">✓ Gambar dipilih</p>
            </div>
          </div>
          <input ref="imageInput" type="file" class="hidden" accept="image/*" @change="handleImageUpload" />
          <p v-if="form.errors.featured_image" class="text-red-600 text-sm mt-1">{{ form.errors.featured_image }}</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
          <p class="text-sm text-blue-900">
            <strong>ℹ️ Catatan:</strong> Artikel Anda akan dikirim untuk persetujuan admin. Status akan menjadi "Pending" hingga admin mengecek dan menyetujuinya.
          </p>
        </div>

        <!-- Tombol Submit -->
        <div class="bg-white rounded-lg shadow-sm p-6 flex gap-3">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 font-semibold"
          >
            {{ form.processing ? 'Mengirim...' : 'Kirim untuk Persetujuan' }}
          </button>
          <Link :href="route('petugas.articles.my')" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold">
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
  featured_image: null
})

const handleImageUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.featured_image = file
  }
}

const submitForm = () => {
  form.post(route('petugas.articles.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script>
