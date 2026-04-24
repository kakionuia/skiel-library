<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Petugas</h1>
    <p class="mt-2 text-gray-600">Selamat datang di panel petugas perpustakaan.</p>

    <div class="mt-8 grid gap-6 md:grid-cols-3">
      <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
        <div class="text-sm font-medium text-gray-500">Buku Tersedia</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">0</div>
      </div>
      <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
        <div class="text-sm font-medium text-gray-500">Buku Dipinjam</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">0</div>
      </div>
      <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
        <div class="text-sm font-medium text-gray-500">User Aktif</div>
        <div class="mt-2 text-3xl font-bold text-gray-900">0</div>
      </div>
    </div>

    <div class="mt-8 rounded-lg bg-white p-6 shadow-sm border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Menu Petugas</h2>
      <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 p-4">
          <div class="font-medium text-gray-900">Kelola Buku</div>
          <div class="mt-1 text-sm text-gray-500">Tambah, edit, hapus data buku</div>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
          <div class="font-medium text-gray-900">Peminjaman</div>
          <div class="mt-1 text-sm text-gray-500">Kelola peminjaman dan pengembalian buku</div>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
          <div class="font-medium text-gray-900">Data Anggota</div>
          <div class="mt-1 text-sm text-gray-500">Lihat data anggota perpustakaan</div>
        </div>
      </div>
    </div>
  </div>
</template>