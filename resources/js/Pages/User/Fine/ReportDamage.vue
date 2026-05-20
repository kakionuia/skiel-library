<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100/50 py-12 px-4 antialiased">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <Link href="/my-loans" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-500 hover:text-orange-600 transition-colors duration-200 mb-4 group">
          <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Rak Buku
        </Link>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight sm:text-4xl">📋 Laporan Kondisi Buku</h1>
        <p class="text-slate-500 mt-2 text-sm sm:text-base">Verifikasi kondisi fisik buku sebelum melanjutkan prosedur penutupan administrasi pinjaman.</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-3xl shadow-xl shadow-slate-100 border border-slate-200/60 overflow-hidden mb-8">
        
        <!-- Book Info Section -->
        <div class="bg-gradient-to-br from-orange-50/60 via-orange-50/30 to-transparent border-b border-slate-100 p-6 sm:p-8">
          <div class="flex flex-col sm:flex-row gap-6">
            <!-- Book Cover -->
            <div class="w-24 h-32 rounded-2xl overflow-hidden bg-gradient-to-br from-orange-400 to-orange-500 shadow-md shadow-orange-500/20 flex items-center justify-center text-4xl shrink-0 mx-auto sm:mx-0">
              📚
            </div>

            <!-- Book Details -->
            <div class="flex-1 text-center sm:text-left flex flex-col justify-center">
              <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 w-max mx-auto sm:mx-0 mb-2">Buku yang Dipinjam</span>
              <h2 class="text-xl font-bold text-slate-900 leading-snug mb-1">{{ borrow.book?.title || 'Judul Buku' }}</h2>
              <p class="text-sm text-slate-500 font-medium">{{ borrow.book?.author || 'Penulis' }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-200/60">
            <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl border border-slate-100 text-center">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Masa Keterlambatan</p>
              <p class="text-2xl font-black text-slate-800 mt-1">{{ borrow.late_days }} <span class="text-xs font-bold text-slate-400">hari</span></p>
            </div>
            <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl border border-slate-100 text-center">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Denda Keterlambatan</p>
              <p class="text-2xl font-black text-orange-500 mt-1"><span class="text-xs font-bold text-orange-400">Rp</span> {{ formatNumber(borrow.late_fee) }}</p>
            </div>
          </div>
        </div>

        <!-- Form Section -->
        <div class="p-6 sm:p-8">
          <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-5">Instrumen Evaluasi Fisik</h3>
          
          <form @submit.prevent="submitReport" class="space-y-6">
            <!-- Question: Is Damaged? -->
            <div class="space-y-3">
              <label class="block text-sm font-bold text-slate-800">Bagaimana kondisi fisik buku saat ini?</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Option: Fine / Normal -->
                <label 
                  class="p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex items-start gap-4 text-left group relative overflow-hidden"
                  :class="form.is_damaged === false 
                    ? 'border-orange-500 bg-orange-50/30 ring-4 ring-orange-500/5' 
                    : 'border-slate-200/80 bg-white hover:border-orange-200 hover:bg-orange-50/10'"
                >
                  <input type="radio" v-model="form.is_damaged" :value="false" class="sr-only">
                  <div :class="[
                    'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all duration-200',
                    form.is_damaged === false ? 'border-orange-500 bg-orange-500' : 'border-slate-300'
                  ]">
                    <svg v-if="form.is_damaged === false" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-bold text-slate-800 text-sm sm:text-base">Utuh / Layak</p>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Buku kembali dalam keadaan normal, bersih, dan tidak cacat struktural.</p>
                  </div>
                </label>

                <!-- Option: Damaged -->
                <label 
                  class="p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex items-start gap-4 text-left group relative overflow-hidden"
                  :class="form.is_damaged === true 
                    ? 'border-orange-500 bg-orange-50/30 ring-4 ring-orange-500/5' 
                    : 'border-slate-200/80 bg-white hover:border-orange-200 hover:bg-orange-50/10'"
                >
                  <input type="radio" v-model="form.is_damaged" :value="true" class="sr-only">
                  <div :class="[
                    'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition-all duration-200',
                    form.is_damaged === true ? 'border-orange-500 bg-orange-500' : 'border-slate-300'
                  ]">
                    <svg v-if="form.is_damaged === true" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-bold text-slate-800 text-sm sm:text-base">Rusak / Cacat</p>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">Buku mengalami robek, terkena cairan, coretan permanen, atau hilang.</p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Upload Photo Section -->
            <div class="space-y-3">
              <label class="block text-sm font-bold text-slate-800">
                Dokumentasi Bukti Fisik <span class="text-orange-500">*</span>
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
                  class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center cursor-pointer hover:border-orange-400 hover:bg-orange-50/10 transition-all duration-200"
                  :class="previewImages.length > 0 ? 'border-orange-400 bg-slate-50/50' : 'bg-slate-50/30'"
                >
                  <!-- Empty State -->
                  <div v-if="previewImages.length === 0" class="py-4">
                    <div class="p-3 bg-white border border-slate-100 rounded-2xl shadow-sm w-max mx-auto mb-3 text-slate-400">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                      </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Unggah Foto Kondisi Aktual</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Maksimal 3 berkas gambar dalam format JPG, PNG, atau WebP (Maks. 2MB/foto).</p>
                  </div>
                  
                  <!-- Grid Preview Pictures -->
                  <div v-else class="grid grid-cols-3 gap-3">
                    <div v-for="(preview, index) in previewImages" :key="index" class="relative group aspect-square rounded-xl overflow-hidden shadow-sm border border-slate-200">
                      <img :src="preview" class="w-full h-full object-cover" />
                      <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <button 
                          @click.stop="removeImage(index)"
                          class="w-8 h-8 bg-red-500 text-white rounded-xl flex items-center justify-center shadow-lg transform scale-90 group-hover:scale-100 transition-transform duration-150 hover:bg-red-600"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </div>
                    </div>
                    <!-- Add button inside grid if slot remaining -->
                    <div v-if="previewImages.length < 3" class="border border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center bg-white text-slate-400 hover:text-orange-500 hover:border-orange-400 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
              <p v-if="photoError" class="text-xs font-semibold text-red-500 flex items-center gap-1.5 mt-1">
                ⚠️ {{ photoError }}
              </p>
            </div>

            <!-- Conditional Damage Specification (Smooth Transition View) -->
            <div v-if="form.is_damaged === true" class="space-y-4 p-5 sm:p-6 bg-slate-50 border border-slate-200/60 rounded-2xl animate-fadeIn">
              <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Spesifikasi Kerusakan</h4>
              </div>
              
              <!-- Damage Type Selection -->
              <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Kategori Kerusakan <span class="text-orange-500">*</span></label>
                <div class="relative">
                  <select 
                    v-model="form.damage_type"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/5 outline-none transition-all appearance-none text-sm font-medium text-slate-800"
                  >
                    <option value="">Pilih jenis kerusakan</option>
                    <option value="lost">📚 Buku Hilang / Tidak Dikembalikan</option>
                    <option value="wet">💧 Terkena Air / Lembab / Jamur</option>
                    <option value="torn">✂️ Lembaran Robek / Hilang Sebagian</option>
                    <option value="stained">🔴 Coretan / Noda Kopi / Kotoran</option>
                    <option value="other">❓ Kerusakan Struktural Lainnya</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Damage Description Details -->
              <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide">Deskripsi Kronologi & Detail Fisik <span class="text-orange-500">*</span></label>
                <textarea 
                  v-model="form.damage_description"
                  rows="4"
                  placeholder="Mohon jabarkan secara terperinci kondisi bagian buku yang rusak atau kronologi singkatnya..."
                  class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/5 outline-none resize-none transition-all text-slate-800 placeholder-slate-400 leading-relaxed"
                ></textarea>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-4 border-t border-slate-100">
              <Link 
                href="/my-loans"
                class="order-2 sm:order-1 flex-1 py-4 px-6 rounded-2xl border border-slate-200 text-slate-600 font-bold text-center bg-white hover:bg-slate-50 hover:text-slate-800 active:scale-[0.99] transition-all duration-150 shadow-sm"
              >
                Batal
              </Link>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="order-1 sm:order-2 flex-1 py-4 px-6 rounded-2xl bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300 text-white font-bold text-center shadow-md shadow-orange-500/10 active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
              >
                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isSubmitting ? 'Memproses Berkas...' : 'Kirim Laporan Resmi' }}
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
    photoError.value = 'Batas maksimal unggahan adalah 3 berkas foto.';
    return;
  }

  for (let file of files) {
    if (file.size > 2048 * 1024) {
      photoError.value = 'Ukuran berkas terlalu besar. Maksimal resolusi per foto adalah 2MB.';
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

async function submitReport() {
  if (form.value.is_damaged === null) {
    Swal.fire({
      title: 'Peringatan',
      text: 'Silakan tentukan indikator kondisi kelayakan buku terlebih dahulu.',
      icon: 'warning',
      confirmButtonColor: '#f97316'
    });
    return;
  }

  if (previewImages.value.length === 0) {
    Swal.fire({
      title: 'Dokumentasi Wajib',
      text: 'Mohon unggah minimal 1 foto kondisi fisik buku untuk validasi bukti.',
      icon: 'warning',
      confirmButtonColor: '#f97316'
    });
    return;
  }

  if (form.value.is_damaged === true) {
    if (!form.value.damage_type) {
      Swal.fire({
        title: 'Kategori Kosong',
        text: 'Silakan pilih klasifikasi jenis kerusakan buku.',
        icon: 'warning',
        confirmButtonColor: '#f97316'
      });
      return;
    }
    if (!form.value.damage_description || form.value.damage_description.trim() === '') {
      Swal.fire({
        title: 'Deskripsi Wajib',
        text: 'Mohon isi rincian penjelasan kerusakan buku Anda.',
        icon: 'warning',
        confirmButtonColor: '#f97316'
      });
      return;
    }
  }

  isSubmitting.value = true;

  const formData = new FormData();
  formData.append('is_damaged', form.value.is_damaged ? '1' : '0');
  
  if (form.value.is_damaged) {
    formData.append('damage_type', form.value.damage_type);
    formData.append('damage_description', form.value.damage_description);
  }
  
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
        confirmButtonColor: '#f97316'
      });
      return;
    }

    const data = await response.json();
    isSubmitting.value = false;
    
    Swal.fire({
      title: 'Laporan Diterima!',
      text: form.value.is_damaged 
        ? 'Data kerusakan berhasil diajukan. Mohon tunggu proses telaah kalkulasi denda dari tim pustakawan.' 
        : 'Verifikasi kondisi baik selesai. Sistem mengarahkan Anda ke halaman tagihan.',
      icon: 'success',
      confirmButtonColor: '#f97316',
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
      title: 'Gangguan Sistem',
      text: 'Gagal mengirim data akibat masalah koneksi jaringan. Silakan dicoba beberapa saat lagi.',
      icon: 'error',
      confirmButtonColor: '#f97316'
    });
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.25s ease-out forwards;
}
</style>