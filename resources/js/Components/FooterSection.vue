<template>
  <footer class="bg-orange-500 text-white pt-12 pb-6 px-4 md:px-12 font-sans">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
      
      <!-- Kolom 1: Branding & Sosmed -->
      <div class="flex flex-col gap-4">
        <div class="flex items-center gap-3">
          <!-- Ganti src dengan path logo sekolah Anda -->
        <img :src="$page.props.logoUrl" alt="Logo" class="w-16 h-16">
          <div>
            <h2 class="text-3xl font-bold leading-none">SKIE-Library</h2>
            <p class="text-sm font-medium opacity-90">Administrasi perpus digital</p>
          </div>
        </div>
        <p class="text-sm leading-relaxed max-w-sm mt-2">
          Kami menyediakan beragam informasi untuk warga SMK 1 Gunungputri. Terus update kegiatan - kegiatan kami di media sosial.
        </p>
        <div class="flex gap-3 mt-2">
          <!-- Icon Sosmed Bulat Putih -->
          <a href="#" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:opacity-80 transition"></a>
          <a href="#" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:opacity-80 transition"></a>
          <a href="#" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:opacity-80 transition"></a>
        </div>
      </div>

      <!-- Kolom 2: Peta Situs -->
      <div class="md:pl-20">
        <h3 class="text-xl font-bold mb-6">Peta Situs</h3>
        <ul class="space-y-3 text-sm font-medium">
          <li><a href="#" class="hover:underline opacity-90">Beranda</a></li>
          <li><a href="#" class="hover:underline opacity-90">Fitur</a></li>
          <li><a href="#" class="hover:underline opacity-90">Layanan</a></li>
          <li><a href="#" class="hover:underline opacity-90">Tentang Kami</a></li>
        </ul>
      </div>

      <!-- Kolom 3: Hubungi Kami -->
      <div class="flex flex-col items-end text-right">
        <h3 class="text-xl font-bold mb-4">Hubungi Kami</h3>
        <div class="flex gap-3 mb-6">
          <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center hover:opacity-80 transition"></a>
          <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center hover:opacity-80 transition"></a>
        </div>
        <div class="text-sm space-y-1 opacity-90">
          <p>Jl. Barokah No. 06, Desa Wanaherang, Kec.</p>
          <p>Gunungputri, Kab. Bogor, Jawa Barat 16965</p>
          <p class="pt-2"><span class="font-bold">Telepon:</span> 0283232939232</p>
          <p><span class="font-bold">Email:</span> denufurferrejdf@gmail.com</p>
        </div>
      </div>
    </div>

    <!-- Garis Pemisah & Copyright -->
    <div class="max-w-7xl mx-auto mt-12 pt-6 border-t border-white/30">
      <p class="text-center text-xs font-medium tracking-wide opacity-80">
        © WellDesign for SKIE-Library 2025. Semua hak cipta dilindungi
      </p>
    </div>
  </footer>
</template>