<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  borrows: Object
});

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number || 0);
}

function getPaymentMethodBadge(method) {
  const badges = {
    qris: { class: 'bg-blue-100 text-blue-800', text: '📱 QRIS' },
    cash: { class: 'bg-green-100 text-green-800', text: '💵 Tunai' },
    none: { class: 'bg-gray-100 text-gray-800', text: 'Tidak Ada' }
  };
  return badges[method] || badges['none'];
}

function confirmCashPayment(borrow) {
  Swal.fire({
    title: 'Konfirmasi Pembayaran Tunai',
    html: `
      <div class="text-left space-y-2">
        <p><strong>Pengguna:</strong> ${borrow.user.name}</p>
        <p><strong>Buku:</strong> ${borrow.book.title}</p>
        <p><strong>Nominal:</strong> Rp ${formatNumber(borrow.late_fee)}</p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Konfirmasi Pembayaran',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/payments/${borrow.id}/confirm-cash`, {}, {
        onSuccess: () => {
          Swal.fire('Berhasil!', 'Pembayaran denda tunai berhasil dikonfirmasi.', 'success');
        },
        onError: () => {
          Swal.fire('Gagal!', 'Terjadi kesalahan saat mengkonfirmasi pembayaran.', 'error');
        }
      });
    }
  });
}

function confirmQrisPayment(borrow) {
  Swal.fire({
    title: 'Konfirmasi Pembayaran QRIS',
    html: `
      <div class="text-left space-y-2">
        <p><strong>Pengguna:</strong> ${borrow.user.name}</p>
        <p><strong>Buku:</strong> ${borrow.book.title}</p>
        <p><strong>Nominal:</strong> Rp ${formatNumber((borrow.late_fee || 0) + (borrow.additional_fine || 0))}</p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2563eb',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Konfirmasi QRIS',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/payments/${borrow.id}/confirm-qris`, {}, {
        onSuccess: () => Swal.fire('Berhasil!', 'Pembayaran QRIS berhasil dikonfirmasi.', 'success'),
        onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan saat mengkonfirmasi pembayaran.', 'error')
      });
    }
  });
}
</script>

<template>
  <AdminLayout>
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">💳 Pembayaran Denda Tertunda</h1>
        <p class="text-gray-600 text-sm mt-1">Daftar pembayaran denda yang menunggu konfirmasi atau tindakan</p>
      </div>

      <div class="px-6 py-4">
        <!-- Filter Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-xs text-blue-600 uppercase font-semibold">QRIS (Menunggu)</p>
            <p class="text-2xl font-bold text-blue-900 mt-1">
              {{ borrows.data.filter(b => b.payment_method === 'qris' && b.payment_status === 'pending').length }}
            </p>
          </div>
          <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-xs text-green-600 uppercase font-semibold">Tunai (Menunggu)</p>
            <p class="text-2xl font-bold text-green-900 mt-1">
              {{ borrows.data.filter(b => b.payment_method === 'cash' && b.payment_status === 'pending').length }}
            </p>
          </div>
          <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <p class="text-xs text-purple-600 uppercase font-semibold">Total Denda</p>
            <p class="text-2xl font-bold text-purple-900 mt-1">
              Rp {{ formatNumber(borrows.data.reduce((sum, b) => sum + (b.late_fee || 0) + (b.additional_fine || 0), 0)) }}
            </p>
          </div>
        </div>

        <!-- Payments Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengguna</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="borrows.data.length === 0">
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pembayaran tertunda</td>
              </tr>
              <tr v-for="borrow in borrows.data" :key="borrow.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ borrow.user.name }}</div>
                  <div class="text-xs text-gray-500">{{ borrow.user.email }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ borrow.book.title }}</div>
                  <div class="text-xs text-gray-500">{{ borrow.book.author }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-bold text-red-600">Rp {{ formatNumber((borrow.late_fee || 0) + (borrow.additional_fine || 0)) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="px-3 py-1 text-xs font-semibold rounded-full"
                    :class="getPaymentMethodBadge(borrow.payment_method).class"
                  >
                    {{ getPaymentMethodBadge(borrow.payment_method).text }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="borrow.payment_status === 'pending'" class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    ⏳ Menunggu
                  </span>
                  <span v-else-if="borrow.payment_status === 'confirmed'" class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    ✓ Dikonfirmasi
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(borrow.updated_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <div v-if="borrow.payment_status === 'pending'" class="flex gap-2">
                    <button
                      v-if="borrow.payment_method === 'cash'"
                      @click="confirmCashPayment(borrow)"
                      class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium"
                    >
                      Konfirmasi Tunai
                    </button>
                    <button
                      v-if="borrow.payment_method === 'qris'"
                      @click="confirmQrisPayment(borrow)"
                      class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium"
                    >
                      Konfirmasi QRIS
                    </button>
                  </div>
                  <span v-else class="text-gray-500 text-xs">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="borrows.links" class="mt-6 flex items-center justify-between">
          <p class="text-sm text-gray-600">
            Menampilkan {{ borrows.from }} hingga {{ borrows.to }} dari {{ borrows.total }} hasil
          </p>
          <div class="flex gap-2">
            <Link
              v-for="link in borrows.links"
              :key="link.label"
              v-html="link.label"
              :href="link.url || '#'"
              :class="{
                'px-3 py-1 rounded border': true,
                'bg-orange-600 text-white border-orange-600': link.active,
                'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active,
                'opacity-50 cursor-not-allowed': !link.url
              }"
            />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
