<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8 px-4">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <Link href="/my-loans" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Rak Buku
        </Link>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">📋 Laporan Kondisi Buku</h1>
        <p class="text-slate-500">Laporkan kondisi buku sebelum melakukan pembayaran denda</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-slate-200/60 overflow-hidden mb-8">
        <!-- Book Info -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 border-b border-orange-200/50 p-8">
          <div class="flex gap-6">
            <div class="w-24 h-32 rounded-xl overflow-hidden bg-slate-100 border-2 border-white shadow-lg shrink-0">
              <div class="w-full h-full bg-gradient-to-br from-orange-200 to-orange-400 flex items-center justify-center text-3xl">
                📚
              </div>
            </div>
            <div class="flex-1">
              <h2 class="text-lg font-bold text-slate-900 mb-1">{{ borrow.book?.title }}</h2>
              <p class="text-sm text-slate-600 mb-4">{{ borrow.book?.author }}</p>
              <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                  <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hari Terlambat</p>
                  <p class="text-2xl font-black text-orange-600 mt-1">{{ borrow.late_days }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Denda</p>
                  <p class="text-2xl font-black text-red-600 mt-1">Rp {{ formatNumber(borrow.late_fee) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Damage Report Form -->
        <div class="p-8">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6">Form Laporan Kondisi Buku</h3>
          
          <form @submit.prevent="submitReport" class="space-y-6">
            <!-- Apakah buku mengalami kerusakan? -->
            <div class="space-y-4">
              <label class="block text-sm font-bold text-slate-900">Apakah buku mengalami kerusakan?</label>
              <div class="flex gap-4">
                <label 
                  class="flex-1 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                  :class="form.is_damaged === true 
                    ? 'border-red-500 bg-red-50' 
                    : 'border-slate-200 bg-white hover:border-red-200'"
                >
                  <input 
                    type="radio" 
                    v-model="form.is_damaged" 
                    :value="true" 
                    class="sr-only"
                  >
                  <div class="flex items-center gap-3">
                    <div 
                      class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                      :class="form.is_damaged === true ? 'border-red-500 bg-red-500' : 'border-slate-300'"
                    >
                      <svg v-if="form.is_damaged === true" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900">Ya, Mengalami Kerusakan</p>
                      <p class="text-xs text-slate-600 mt-0.5">Buku hilang, basah, sobek, atau ternoda</p>
                    </div>
                  </div>
                </label>

                <label 
                  class="flex-1 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                  :class="form.is_damaged === false 
                    ? 'border-green-500 bg-green-50' 
                    : 'border-slate-200 bg-white hover:border-green-200'"
                >
                  <input 
                    type="radio" 
                    v-model="form.is_damaged" 
                    :value="false" 
                    class="sr-only"
                  >
                  <div class="flex items-center gap-3">
                    <div 
                      class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                      :class="form.is_damaged === false ? 'border-green-500 bg-green-500' : 'border-slate-300'"
                    >
                      <svg v-if="form.is_damaged === false" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-bold text-slate-900">Tidak, Kondisi Baik</p>
                      <p class="text-xs text-slate-600 mt-0.5">Buku dalam kondisi normal</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Upload Foto (Required) -->
            <div class="space-y-4">
              <label class="block text-sm font-bold text-slate-900">
                Foto Kondisi Buku <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input 
                  type="file" 
                  ref="fileInput"
                  @change="handleFileUpload"
                  accept="image/*"
                  multiple
                  class="sr-only"
                >
                <div 
                  @click="$refs.fileInput.click()"
                  class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center cursor-pointer hover:border-orange-400 transition-all duration-200"
                  :class="previewImages.length > 0 ? 'border-orange-400 bg-orange-50/50' : ''"
                >
                  <div v-if="previewImages.length === 0">
                    <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm font-medium text-slate-600">Klik untuk upload foto buku</p>
                    <p class="text-xs text-slate-400 mt-1">Maksimal 3 foto (JPG, PNG, WebP)</p>
                  </div>
                  <div v-else class="grid grid-cols-3 gap-4">
                    <div v-for="(preview, index) in previewImages" :key="index" class="relative group">
                      <img :src="preview" class="w-full h-32 object-cover rounded-xl" />
                      <button 
                        @click.stop="removeImage(index)"
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <p v-if="photoError" class="text-xs text-red-500">{{ photoError }}</p>
            </div>

            <!-- Damage Details (if "Ya") -->
            <div v-if="form.is_damaged === true" class="space-y-4 p-6 bg-red-50 rounded-2xl border border-red-200">
              <h4 class="text-sm font-bold text-red-900 mb-4">Detail Kerusakan</h4>
              
              <!-- Damage Type -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-900">Jenis Kerusakan <span class="text-red-500">*</span></label>
                <select 
                  v-model="form.damage_type"
                  class="w-full px-4 py-3 rounded-xl border-2 border-red-200 bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all"
                >
                  <option value="">Pilih jenis kerusakan</option>
                  <option value="lost">Hilang</option>
                  <option value="wet">Basah</option>
                  <option value="torn">Sobek</option>
                  <option value="stained">Tertimpa Noda</option>
                  <option value="other">Lainnya</option>
                </select>
              </div>

              <!-- Damage Description -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-900">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
                <textarea 
                  v-model="form.damage_description"
                  rows="4"
                  placeholder="Ceritakan detail kerusakan buku..."
                  class="w-full px-4 py-3 rounded-xl border-2 border-red-200 bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none resize-none transition-all"
                ></textarea>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 pt-4">
              <Link 
                href="/my-loans"
                class="flex-1 py-4 px-6 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold text-center hover:bg-slate-50 transition-all"
              >
                Batal
              </Link>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="flex-1 py-4 px-6 rounded-2xl text-white font-bold text-center transition-all flex items-center justify-center gap-2"
                :class="isSubmitting ? 'bg-orange-300 cursor-not-allowed' : 'bg-orange-600 hover:bg-orange-700'"
              >
                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ isSubmitting ? 'Mengirim...' : 'Kirim Laporan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  borrow: { type: Object, required: true }
});

const form = ref({
  is_damaged: null,
  damage_type: '',
  damage_description: '',
  images: []
});

const previewImages = ref([]);
const photoError = ref('');
const isSubmitting = ref(false);

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
}

function handleFileUpload(event) {
  const files = event.target.files;
  photoError.value = '';
  
  if (files.length + form.value.images.length > 3) {
    photoError.value = 'Maksimal 3 foto';
    return;
  }

  for (let file of files) {
    if (file.size > 2048 * 1024) {
      photoError.value = 'Ukuran foto maksimal 2MB';
      return;
    }
    
    form.value.images.push(file);
    
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImages.value.push(e.target.result);
    };
    reader.readAsDataURL(file);
  }
}

function removeImage(index) {
  form.value.images.splice(index, 1);
  previewImages.value.splice(index, 1);
}

// Di ReportDamage.vue, ganti fungsi submitReport

async function submitReport() {
  // Validasi
  if (form.value.is_damaged === null) {
    Swal.fire({
      title: 'Peringatan',
      text: 'Pilih kondisi buku terlebih dahulu.',
      icon: 'warning',
      confirmButtonColor: '#ea580c'
    });
    return;
  }

  if (previewImages.value.length === 0) {
    Swal.fire({
      title: 'Peringatan',
      text: 'Foto kondisi buku wajib diupload.',
      icon: 'warning',
      confirmButtonColor: '#ea580c'
    });
    return;
  }

  if (form.value.is_damaged === true) {
    if (!form.value.damage_type) {
      Swal.fire({
        title: 'Peringatan',
        text: 'Pilih jenis kerusakan.',
        icon: 'warning',
        confirmButtonColor: '#ea580c'
      });
      return;
    }
    if (!form.value.damage_description || form.value.damage_description.trim() === '') {
      Swal.fire({
        title: 'Peringatan',
        text: 'Deskripsi kerusakan wajib diisi.',
        icon: 'warning',
        confirmButtonColor: '#ea580c'
      });
      return;
    }
  }

  isSubmitting.value = true;

  const formData = new FormData();
  // Kirim is_damaged sebagai string agar backend bisa konversi
  formData.append('is_damaged', form.value.is_damaged ? '1' : '0');
  
  // Jika kerusakan, tambahkan detail
  if (form.value.is_damaged) {
    formData.append('damage_type', form.value.damage_type);
    formData.append('damage_description', form.value.damage_description);
  }
  
  // Append semua images
  form.value.images.forEach((image, index) => {
    formData.append(`images[${index}]`, image);
  });

  try {
    const response = await fetch(`/borrow/${props.borrow.id}/report-damage`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      isSubmitting.value = false;
      
      Swal.fire({
        title: 'Validasi Gagal',
        html: Object.values(errorData.errors || {}).flat().join('<br>'),
        icon: 'error',
        confirmButtonColor: '#ea580c'
      });
      return;
    }

    const data = await response.json();
    isSubmitting.value = false;
    
    Swal.fire({
      title: 'Berhasil!',
      text: form.value.is_damaged 
        ? 'Laporan kerusakan dikirim. Petugas akan review.' 
        : 'Laporan kondisi baik dikirim. Lanjut ke pembayaran.',
      icon: 'success',
      confirmButtonColor: '#ea580c',
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
      if (result.isConfirmed || result.isDismissed) {
        window.location.href = form.value.is_damaged
          ? '/my-loans'
          : `/fines/${props.borrow.id}/pay`;
      }
    });
    
  } catch (error) {
    isSubmitting.value = false;
    console.error('Error:', error);
    Swal.fire({
      title: 'Gagal',
      text: 'Terjadi kesalahan. Coba lagi.',
      icon: 'error',
      confirmButtonColor: '#ea580c'
    });
  }
}
</script>
