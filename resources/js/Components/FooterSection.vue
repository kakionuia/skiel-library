<template>
  <footer class="bg-orange-500 text-white pt-12 pb-6 px-4 md:px-12 font-sans">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
      
      <div class="flex flex-col gap-4">
        <div class="flex items-center gap-3">
          <img :src="$page.props.logoUrl" alt="Logo" class="w-16 h-16 object-contain">
          <div>
            <h2 class="text-3xl font-bold leading-none">SKIE-Library</h2>
            <p class="text-sm font-medium text-slate-100/90 mt-1">Administrasi perpus digital</p>
          </div>
        </div>
        <p class="text-sm leading-relaxed max-w-sm text-slate-100">
          Kami menyediakan beragam informasi untuk warga SMK 1 Gunungputri. Terus update kegiatan kami di media sosial.
        </p>
        <div class="flex gap-3 mt-2">
          <!-- Menggunakan tag <i> standar FontAwesome dengan utility text size Tailwind -->
          <a href="#" class="w-9 h-9 bg-white rounded-full flex items-center justify-center text-[#FF0000] text-xl hover:opacity-80 transition shadow-sm">    
            <i class="fab fa-youtube"></i>
          </a>
          <a href="#" class="w-9 h-9 bg-white rounded-full flex items-center justify-center text-[#C13584] text-xl hover:opacity-80 transition shadow-sm">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="w-9 h-9 bg-white rounded-full flex items-center justify-center text-[#000000] text-lg hover:opacity-80 transition shadow-sm">
            <i class="fab fa-tiktok"></i>
          </a>
        </div>
      </div>

      <div class="md:pl-20">
        <h3 class="text-xl font-bold mb-6">Peta Situs</h3>
        <ul class="space-y-3 text-sm font-medium text-slate-100">
          <li><a href="#" class="hover:underline">Beranda</a></li>
          <li><a href="#" class="hover:underline">Fitur</a></li>
          <li><a href="#" class="hover:underline">Layanan</a></li>
          <li><a href="#" class="hover:underline">Tentang Kami</a></li>
        </ul>
      </div>

      <div class="flex flex-col items-start md:items-end md:text-right">
        <h3 class="text-xl font-bold mb-4">Hubungi Kami</h3>
        <div class="flex gap-3 mb-6">
          <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#f97322] text-xl hover:opacity-80 transition shadow-sm">
            <i class="fas fa-envelope"></i>
          </a>
          <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#f97322] text-xl hover:opacity-80 transition shadow-sm">
            <i class="fas fa-phone"></i>
          </a>
        </div>
        <div class="text-sm space-y-1 text-slate-100">
          <p>Jl. Barokah No. 06, Desa Wanaherang, Kec.</p>
          <p>Gunungputri, Kab. Bogor, Jawa Barat 16965</p>
          <p class="pt-2"><span class="font-bold text-white">Telepon:</span> 0283232939232</p>
          <p><span class="font-bold text-white">Email:</span> denufurferrejdf@gmail.com</p>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto mt-12 pt-6 border-t border-white/20">
      <p class="text-center text-xs font-medium tracking-wide text-slate-100/80">
        © WellDesign for SKIE-Library 2026. Semua hak cipta dilindungi
      </p>
    </div>
  </footer>
</template>