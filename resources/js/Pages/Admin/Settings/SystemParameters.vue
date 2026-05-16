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
      <div class="text-left">
        <p class="text-sm"><strong>Parameter:</strong> ${parameter.key}</p>
        <p class="text-sm"><strong>Nilai Lama:</strong> ${parameter.value}</p>
        <p class="text-sm"><strong>Nilai Baru:</strong> ${newValue}</p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ea580c',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Perbarui',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/admin/settings/system-parameters/${parameter.id}`, {
        value: newValue
      }, {
        onSuccess: () => {
          // Update local state to reflect the change immediately
          const index = parameters.value.findIndex(p => p.id === parameter.id);
          if (index !== -1) {
            parameters.value[index].value = newValue.toString();
          }
          editingId.value = null;
          Swal.fire('Berhasil!', 'Parameter berhasil diperbarui.', 'success');
        },
        onError: (error) => {
          console.error('Error:', error);
          Swal.fire('Gagal!', 'Terjadi kesalahan saat memperbarui parameter.', 'error');
        }
      });
    }
  });
}

function getParameterDescription(key) {
  const descriptions = {
    max_borrow_days: 'Jumlah hari maksimal peminjaman buku',
    fine_per_day: 'Nominal denda per hari keterlambatan (dalam Rupiah)',
    max_extensions: 'Jumlah maksimal perpanjangan peminjaman',
    extension_days: 'Jumlah hari yang dapat diperpanjang per permintaan'
  };
  return descriptions[key] || 'Parameter sistem';
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
    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">⚙️ Pengaturan Parameter Sistem</h1>
          <p class="text-gray-600 text-sm mt-1">Kelola konfigurasi sistem perpustakaan</p>
        </div>

        <div class="divide-y divide-gray-200">
          <div
            v-for="parameter in parameters"
            :key="parameter.id"
            class="px-6 py-4 hover:bg-gray-50 transition"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <span class="text-2xl">{{ getParameterIcon(parameter.key) }}</span>
                  <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase">{{ parameter.key }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ getParameterDescription(parameter.key) }}</p>
                  </div>
                </div>
              </div>

              <div v-if="editingId === parameter.id" class="flex items-center gap-2 w-64">
                <input
                  v-model.number="editingValues[parameter.id]"
                  :type="parameter.type === 'integer' ? 'number' : 'text'"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                  @keyup.enter="saveEdit(parameter)"
                />
                <button
                  @click="saveEdit(parameter)"
                  class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold"
                >
                  Simpan
                </button>
                <button
                  @click="cancelEdit"
                  class="px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg text-xs font-semibold"
                >
                  Batal
                </button>
              </div>

              <div v-else class="flex items-center gap-3">
                <div class="text-right">
                  <div class="text-lg font-bold text-orange-600">
                    {{ parameter.value }}
                    <span v-if="parameter.key === 'fine_per_day'" class="text-sm text-gray-600 ml-1">/hari</span>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ parameter.type === 'integer' ? 'Angka' : parameter.type === 'boolean' ? 'Boolean' : 'Teks' }}
                  </p>
                </div>
                <button
                  @click="startEdit(parameter)"
                  class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-semibold"
                >
                  Edit
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Info Box -->
        <div class="px-6 py-4 bg-blue-50 border-t border-blue-200">
          <p class="text-sm text-blue-800">
            <strong>💡 Informasi:</strong> Perubahan parameter akan berlaku segera untuk semua transaksi baru. Pastikan nilai yang dimasukkan sudah benar sebelum menyimpan.
          </p>
        </div>
      </div>

      <!-- Summary Card -->
      <div class="mt-6 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-6 border border-orange-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4">📋 Ringkasan Parameter</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="param in parameters" :key="param.key" class="bg-white rounded-lg p-3 shadow-xs">
            <p class="text-xs font-semibold text-gray-600 uppercase">{{ param.key }}</p>
            <p class="text-xl font-bold text-orange-600 mt-1">{{ param.value }}</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
