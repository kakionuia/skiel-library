<template>
  <AdminLayout title="Manajemen Artikel">
    <template #header>
      <div class="flex flex-col sm:flex-row justify-around items-center sm:items-center gap-4 px-8 py-4">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Manajemen Artikel & Berita</h2>
          <p class="text-sm text-slate-500 mt-1">Kelola publikasi, kategori, dan status persetujuan artikel.</p>
        </div>
        <Link 
          :href="route('admin.articles.create')" 
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-600 to-amber-500 text-white font-semibold rounded-xl shadow-md shadow-orange-500/10 hover:opacity-95 transition-all active:scale-[0.98]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Buat Artikel Baru
        </Link>
      </div>
    </template>

    <div class="space-y-6 mb-6 ms-6">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <form @submit.prevent="filterArticles" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-2">Cari Artikel</label>
              <div class="relative">
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Judul atau isi..."
                  class="w-full pl-4 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-2">Status</label>
              <select 
                v-model="filters.status" 
                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm appearance-none"
              >
                <option value="">Semua Status</option>
                <option value="pending">Menunggu Persetujuan</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
                <option value="draft">Draft</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-2">Kategori</label>
              <select 
                v-model="filters.category" 
                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm appearance-none"
              >
                <option value="">Semua Kategori</option>
                <option value="artikel">Artikel</option>
                <option value="berita">Berita</option>
                <option value="event">Event</option>
              </select>
            </div>

            <div class="flex items-end">
              <button 
                type="submit" 
                class="w-full px-5 py-2.5 bg-slate-900 text-white font-semibold rounded-xl hover:bg-slate-800 transition-all active:scale-[0.98] text-sm"
              >
                Terapkan Filter
              </button>
            </div>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div v-if="articles.data.length > 0" class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/70">
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 tracking-wider uppercase">Judul</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 tracking-wider uppercase">Kategori</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 tracking-wider uppercase">Penulis</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 tracking-wider uppercase">Status</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 tracking-wider uppercase">Dibuat</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 tracking-wider uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="article in articles.data" :key="article.id" class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 max-w-md">
                  <div class="font-semibold text-slate-900 line-clamp-1">{{ article.title }}</div>
                  <div class="text-xs text-slate-400 mt-1 line-clamp-1">{{ article.excerpt }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold tracking-wide uppercase bg-orange-50 text-orange-700 rounded-lg">
                    {{ getCategoryText(article.category) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600">{{ article.author?.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex items-center px-2.5 py-1 text-xs font-bold tracking-wide uppercase rounded-lg',
                    getStatusClass(article.status)
                  ]">
                    {{ getStatusText(article.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                  {{ new Date(article.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-1.5">
                    <Link :href="route('admin.articles.edit', article.id)" class="px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                      Edit
                    </Link>
                    <button
                      v-if="article.status === 'pending'"
                      @click="showApprovalModal(article)"
                      class="px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition"
                    >
                      Setujui
                    </button>
                    <button
                      v-if="article.status === 'pending'"
                      @click="showRejectModal(article)"
                      class="px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 rounded-lg hover:bg-rose-100 transition"
                    >
                      Tolak
                    </button>
                    <button
                      @click="deleteArticle(article.id)"
                      class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-slate-500 font-medium">Tidak ada artikel ditemukan</p>
        </div>

        <div v-if="articles.links" class="px-6 py-4 flex justify-center border-t border-slate-100 bg-slate-50/30">
          <div class="flex gap-1.5">
            <template v-for="link in articles.links" :key="link.label">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all',
                  link.active
                    ? 'bg-orange-600 text-white shadow-md shadow-orange-600/10'
                    : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
                ]"
                v-html="link.label"
              />
              <span v-else class="px-3.5 py-1.5 text-xs text-slate-400 bg-slate-50 border border-slate-100 rounded-xl cursor-not-allowed" v-html="link.label" />
            </template>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showApproval" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-xl p-6 w-full max-w-md transform transition-all">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mb-4">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Setujui Artikel</h3>
        <p class="text-sm text-slate-500 mb-4">Apakah Anda yakin ingin mempublikasikan artikel ini?</p>
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-medium text-slate-700 mb-6">
          {{ selectedArticle?.title }}
        </div>
        <div class="flex gap-3">
          <button
            @click="approveArticle(selectedArticle.id)"
            class="flex-1 px-4 py-2.5 bg-orange-600 text-white rounded-xl font-semibold text-sm hover:opacity-95 transition"
          >
            Ya, Setujui
          </button>
          <button
            @click="showApproval = false"
            class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-200 transition"
          >
            Batal
          </button>
        </div>
      </div>
    </div>

    <div v-if="showReject" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-xl p-6 w-full max-w-md transform transition-all">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Tolak Artikel</h3>
        <p class="text-sm text-slate-500 mb-4">Berikan feedback penolakan untuk penulis.</p>
        
        <div class="mb-5">
          <label class="block text-xs font-bold text-slate-700 tracking-wider uppercase mb-2">Alasan Penolakan</label>
          <textarea
            v-model="rejectionReason"
            placeholder="Jelaskan secara rinci alasan penolakan..."
            rows="4"
            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all text-sm"
          />
        </div>
        <div class="flex gap-3">
          <button
            @click="rejectArticle(selectedArticle.id)"
            class="flex-1 px-4 py-2.5 bg-rose-600 text-white rounded-xl font-semibold text-sm hover:opacity-95 transition"
          >
            Tolak Artikel
          </button>
          <button
            @click="showReject = false"
            class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-200 transition"
          >
            Batal
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  articles: Object,
  filters: Object,
})

const showApproval = ref(false)
const showReject = ref(false)
const selectedArticle = ref(null)
const rejectionReason = ref('')

const filters = reactive({
  search: props.filters?.search || '',
  status: props.filters?.status || '',
  category: props.filters?.category || '',
})

const filterArticles = () => {
  router.get(route('admin.articles'), filters, {
    preserveScroll: true,
  })
}

const showApprovalModal = (article) => {
  selectedArticle.value = article
  showApproval.value = true
}

const showRejectModal = (article) => {
  selectedArticle.value = article
  rejectionReason.value = ''
  showReject.value = true
}

const approveArticle = async (articleId) => {
  const form = useForm({})
  form.post(route('admin.articles.approve', articleId), {
    onSuccess: () => {
      showApproval.value = false
      Swal.fire({
        title: 'Berhasil!',
        text: 'Artikel berhasil disetujui',
        icon: 'success',
        timer: 2000
      })
    }
  })
}

const rejectArticle = async (articleId) => {
  if (!rejectionReason.value.trim()) {
    Swal.fire({
      title: 'Error',
      text: 'Silakan masukkan alasan penolakan',
      icon: 'error'
    })
    return
  }

  const form = useForm({
    rejection_reason: rejectionReason.value
  })
  form.post(route('admin.articles.reject', articleId), {
    onSuccess: () => {
      showReject.value = false
      Swal.fire({
        title: 'Berhasil!',
        text: 'Artikel berhasil ditolak',
        icon: 'success',
        timer: 2000
      })
    }
  })
}

const deleteArticle = async (articleId) => {
  const result = await Swal.fire({
    title: 'Hapus Artikel?',
    text: 'Tindakan ini tidak dapat dibatalkan',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#ea580c',
  })

  if (result.isConfirmed) {
    const form = useForm({})
    form.delete(route('admin.articles.destroy', articleId), {
      onSuccess: () => {
        Swal.fire({
          title: 'Berhasil!',
          text: 'Artikel berhasil dihapus',
          icon: 'success',
          timer: 2000
        })
      }
    })
  }
}

const getStatusText = (status) => {
  const statuses = {
    'pending': 'Menunggu Persetujuan',
    'approved': 'Disetujui',
    'rejected': 'Ditolak',
    'draft': 'Draft'
  }
  return statuses[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-amber-50 text-amber-700 border border-amber-100',
    'approved': 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    'rejected': 'bg-rose-50 text-rose-700 border border-rose-100',
    'draft': 'bg-slate-50 text-slate-600 border border-slate-100'
  }
  return classes[status] || 'bg-slate-50 text-slate-600 border border-slate-100'
}

const getCategoryText = (category) => {
  const categories = {
    artikel: 'Artikel',
    berita: 'Berita',
    event: 'Event',
  }
  return categories[category] || category
}
</script>
