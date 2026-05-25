<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
  parameters: Array
});

const parameters = ref(props.parameters);
const editingId = ref(null);
const editingValues = ref({});

function startEdit(parameter) {
  editingId.value = parameter.id;
  editingValues.value[parameter.id] = parameter.value;
}

function cancelEdit() {
  editingId.value = null;
  editingValues.value = {};
}

function saveEdit(parameter) {
  const newValue = editingValues.value[parameter.id];

  Swal.fire({
    title: 'Perbarui Parameter?',
    html: `
      <div class="text-left bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2 mt-2">
        <p class="text-sm text-slate-600"><strong>Parameter:</strong> <span class="text-slate-900 uppercase tracking-wider font-mono text-xs">${parameter.key}</span></p>
        <p class="text-sm text-slate-600"><strong>Nilai Lama:</strong> <span class="text-slate-900 font-semibold">${parameter.value}</span></p>
        <p class="text-sm text-slate-600"><strong>Nilai Baru:</strong> <span class="text-orange-500 font-bold">${newValue}</span></p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#64748b',
    confirmButtonText: 'Ya, Perbarui',
    cancelButtonText: 'Batal',
    customClass: {
      popup: 'rounded-xl',
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/admin/settings/system-parameters/${parameter.id}`, {
        value: newValue
      }, {
        onSuccess: () => {
          const index = parameters.value.findIndex(p => p.id === parameter.id);
          if (index !== -1) {
            parameters.value[index].value = newValue.toString();
          }
          editingId.value = null;
          Swal.fire({
            title: 'Berhasil!',
            text: 'Parameter berhasil diperbarui.',
            icon: 'success',
            confirmButtonColor: '#f97316',
            customClass: { popup: 'rounded-xl' }
          });
        },
        onError: (error) => {
          console.error('Error:', error);
          Swal.fire({
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat memperbarui parameter.',
            icon: 'error',
            confirmButtonColor: '#f97316',
            customClass: { popup: 'rounded-xl' }
          });
        }
      });
    }
  });
}

function getParameterDescription(key) {
  const descriptions = {
    max_borrow_days: 'Batas maksimal durasi peminjaman buku untuk setiap anggota.',
    fine_per_day: 'Besaran denda akumulatif per hari untuk keterlambatan pengembalian.',
    max_extensions: 'Batasan berapa kali anggota diizinkan memperpanjang masa pinjam.',
    extension_days: 'Durasi tambahan hari yang diberikan dalam satu kali ekstensi.'
  };
  return descriptions[key] || 'Konfigurasi global aturan sirkulasi sistem.';
}

function getParameterIcon(key) {
  const icons = {
    max_borrow_days: '📅',
    fine_per_day: '💰',
    max_extensions: '🔄',
    extension_days: '⏱️'
  };
  return icons[key] || '⚙️';
}
</script>

<template>
  <AdminLayout>
    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
      
      <!-- Top Header Area -->
      <div class="mb-8 border-b border-slate-100 pb-6">
        <div class="flex items-center gap-3.5">
          <div class="p-2.5 bg-orange-50 rounded-xl text-orange-500">
            ⚙️
          </div>
          <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Parameter Sistem</h1>
            <p class="text-slate-500 text-sm mt-0.5">Atur ambang batas, aturan sirkulasi, dan kalkulasi denda perpustakaan</p>
          </div>
        </div>
      </div>

      <!-- Main Layout Split -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Left Side: Section Context Info -->
        <div class="space-y-4">
          <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 tracking-wide uppercase">Aturan Sirkulasi</h2>
            <p class="text-xs text-slate-500 leading-relaxed mt-2">
              Konfigurasi di samping menentukan alur kerja otomatisasi pengembalian, perpanjangan, dan sanksi finansial pada modul sirkulasi.
            </p>
            <div class="mt-4 pt-4 border-t border-slate-100 flex gap-2">
              <span class="text-orange-500 text-xs mt-0.5">⚠️</span>
              <p class="text-xxs text-slate-400 leading-normal">
                Perubahan nilai akan langsung berdampak pada kalkulasi transaksi baru yang berjalan setelah penyimpanan selesai.
              </p>
            </div>
          </div>
        </div>

        <!-- Right Side: Parameters Settings Form Box -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 shadow-xs divide-y divide-slate-100">
          <div
            v-for="parameter in parameters"
            :key="parameter.id"
            class="p-6 transition-colors duration-150 hover:bg-slate-50/40"
          >
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
              
              <!-- Left Item: Metadata -->
              <div class="flex gap-4 flex-1">
                <div class="w-10 h-10 shrink-0 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-lg">
                  {{ getParameterIcon(parameter.key) }}
                </div>
                <div class="space-y-1">
                  <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest font-mono">
                    {{ parameter.key.replace(/_/g, ' ') }}
                  </h3>
                  <p class="text-sm text-slate-500 leading-normal max-w-md">
                    {{ getParameterDescription(parameter.key) }}
                  </p>
                </div>
              </div>

              <!-- Right Item: Dynamic Form State -->
              <div class="w-full md:w-64 shrink-0 flex items-center justify-end">
                
                <!-- Active Editing View -->
                <div v-if="editingId === parameter.id" class="flex flex-col gap-2 w-full">
                  <div class="relative w-full rounded-xl shadow-xs">
                    <span v-if="parameter.key === 'fine_per_day'" class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm font-semibold pointer-events-none">
                      Rp
                    </span>
                    <input
                      v-model.number="editingValues[parameter.id]"
                      :type="parameter.type === 'integer' ? 'number' : 'text'"
                      :class="[
                        'w-full py-2 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500/10 focus:border-orange-500 focus:bg-white transition-all',
                        parameter.key === 'fine_per_day' ? 'pl-9' : 'pl-3'
                      ]"
                      @keyup.enter="saveEdit(parameter)"
                      autofocus
                    />
                  </div>
                  <div class="flex items-center gap-2 justify-end">
                    <button
                      @click="cancelEdit"
                      class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-bold transition-all"
                    >
                      Batal
                    </button>
                    <button
                      @click="saveEdit(parameter)"
                      class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-bold transition-all shadow-sm shadow-orange-500/10"
                    >
                      Simpan
                    </button>
                  </div>
                </div>

                <!-- Static Parameter View -->
                <div v-else class="flex items-center justify-between w-full md:justify-end md:gap-6">
                  <div class="text-left md:text-right">
                    <div class="text-base font-bold text-slate-900 tracking-tight">
                      {{ parameter.key === 'fine_per_day' ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(parameter.value) : parameter.value }}
                      <span v-if="parameter.key === 'fine_per_day'" class="text-xs font-medium text-slate-400 ml-0.5">/hari</span>
                    </div>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xxs font-semibold bg-slate-100 text-slate-500 uppercase tracking-wider mt-1">
                      {{ parameter.type === 'integer' ? 'Angka' : 'Teks' }}
                    </span>
                  </div>
                  <button
                    @click="startEdit(parameter)"
                    class="px-3.5 py-1.5 border border-slate-200 text-slate-700 hover:border-orange-500 hover:text-orange-500 bg-white rounded-xl text-xs font-bold transition-all duration-150 shadow-xs"
                  >
                    Ubah
                  </button>
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Quick Metrics Overview Section -->
      <div class="mt-12 pt-6 border-t border-slate-100">
        <h3 class="text-xxs font-bold text-slate-400 uppercase tracking-widest mb-4">Pratinjau Ringkas</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div 
            v-for="param in parameters" 
            :key="param.key" 
            class="bg-white rounded-xl p-4 border border-slate-100 shadow-xs flex flex-col justify-between min-h-[90px]"
          >
            <p class="text-xxs font-bold text-slate-400 uppercase tracking-wider truncate" :title="param.key">
              {{ param.key.replace(/_/g, ' ') }}
            </p>
            <p class="text-lg font-bold text-slate-900 mt-2 tracking-tight">
              {{ param.key === 'fine_per_day' ? 'Rp' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(param.value) : param.value }}
            </p>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
.text-xxs {
  font-size: 0.65rem;
}
</style>