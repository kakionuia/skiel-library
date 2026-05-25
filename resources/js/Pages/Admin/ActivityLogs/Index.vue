<template>
  <AuthenticatedLayout title="Log Aktivitas">
    <div class="py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Log Aktivitas</h1>
          <p class="mt-2 text-sm text-gray-500">Riwayat aktivitas pengguna sistem.</p>
        </div>

        <!-- Filter Buttons -->
        <div class="flex gap-2 mb-6">
          <Link v-for="role in ['anggota', 'petugas', 'admin']" :key="role"
            :href="route('admin.activity-logs', {role})"
            :class="currentRole === role ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
            class="px-4 py-2 rounded-lg text-sm font-medium ring-1 ring-gray-200">
            {{ role === 'anggota' ? 'Siswa' : role.charAt(0).toUpperCase() + role.slice(1) }}
          </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50/50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Pengguna</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Aktivitas</th>
                  <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400">Waktu</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ activity.user?.name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ activity.user?.role }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <span :class="{
                        'bg-orange-100 text-orange-700': activity.type === 'login',
                        'bg-green-100 text-green-700': activity.type === 'borrow',
                        'bg-orange-100 text-orange-700': activity.type === 'return',
                        'bg-orange-100 text-orange-700': activity.type === 'create',
                        'bg-red-100 text-red-700': activity.type === 'delete',
                        'bg-amber-100 text-amber-700': activity.type === 'update',
                      }" class="px-2 py-1 rounded text-[10px] font-black uppercase">
                        {{ activity.type }}
                      </span>
                      <span class="text-sm text-gray-600">{{ activity.description }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-xs text-gray-500">{{ activity.created_at }}</div>
                  </td>
                </tr>
                <tr v-if="!activities.data?.length">
                  <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada aktivitas.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  activities: { type: Object, default: () => ({ data: [] }) },
  currentRole: { type: String, default: 'anggota' },
});
</script>