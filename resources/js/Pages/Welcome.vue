<template>
  <div class="min-h-screen bg-white font-sans text-gray-800 selection:bg-orange-100">
 <Navbar @open-auth="showAuthModal = true" />
    <header class="relative pt-40 pb-20 px-12 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between">
      <div class="md:w-1/2" data-aos="fade-right">
        <h2 class="text-orange-500 font-bold text-xl mb-3 text-left">Selamat Datang di!</h2>
        <h1 class="text-5xl font-black leading-tight mb-6 text-gray-900 text-left">
          Pengelolaan Perpustakaan <br>
          <span class="text-orange-500">SMKN 1 Gunungputri</span> Berbasis Teknologi Web
        </h1>
        <p class="text-gray-600 mb-8 text-lg leading-relaxed max-w-lg text-left">
          Pengelolaan koleksi perpustakaan yang lebih mudah, cepat dan modern menggunakan Sistem Informasi berbasis Digital di SMKN 1 Gunungputri.
        </p>
        <div class="flex gap-4">
          <button class="border-2 border-orange-500 text-orange-500 px-8 py-3 rounded-full font-bold hover:bg-orange-50 transition-all">Mulai Sekarang</button>
          <button class="bg-orange-500 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-orange-200 transition-all">Pelajari Fitur Dahulu</button>
        </div>
      </div>
      
      <div class="md:w-1/2 flex justify-center md:justify-end mt-16 md:mt-0" data-aos="zoom-in">
        <div class="relative w-[400px] h-[400px]">
          <div class="absolute -top-5 -right-5 w-64 h-64 bg-orange-500 rounded-full opacity-90"></div>
          <div class="relative z-10 w-full h-full rounded-full border-[10px] border-white shadow-2xl overflow-hidden bg-gray-100">
            <img src="/kepala-sekolah.png" class="w-full h-full object-cover">
          </div>
          <div class="absolute bottom-5 left-0 z-20 bg-white/90 backdrop-blur-sm px-6 py-3 rounded-2xl shadow-xl border border-white">
            <p class="font-bold text-gray-900 text-sm">Usman Ilham Pratama, M.Pd</p>
            <p class="text-[10px] text-gray-500 font-medium">Kepala Sekolah SMKN 1 Gunungputri</p>
          </div>
        </div>
      </div>
    </header>

    <section class="bg-orange-500 py-24 px-12 text-white relative overflow-hidden">
      <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center relative z-10">
        <div data-aos="fade-right" class="bg-white/10 p-10 rounded-[40px] border border-white/20 backdrop-blur-sm">
          <div class="bg-white p-12 rounded-xl text-center">
            <div class="border-4 border-blue-400 p-6 inline-block rounded-lg mb-4">
              <img src="/qr-code.png" class="w-40 h-40">
            </div>
            <p class="text-blue-500 font-black text-3xl tracking-widest">SCAN ME!</p>
          </div>
          <p class="mt-8 text-center text-xl font-bold">Scan barcode buku untuk pinjam</p>
        </div>
        <div data-aos="fade-left" class="text-left">
          <p class="text-lg font-medium mb-2">— Fitur yang Kami Tawarkan —</p>
          <h3 class="text-4xl font-black mb-10 leading-tight">Semua yang dibutuhkan perpustakaan online ada di sini</h3>
          <div class="space-y-6">
            <div v-for="f in features" :key="f.title" class="flex gap-5 border-l-4 border-white/30 pl-4 hover:border-white transition-all">
              <div>
                <h4 class="text-xl font-bold mb-1">{{ f.title }}</h4>
                <p class="text-orange-100 text-sm">{{ f.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-24 px-12 max-w-7xl mx-auto text-center" data-aos="fade-up">
      <h3 class="text-3xl font-black mb-16 underline decoration-orange-500 underline-offset-8">Profil Perpustakaan</h3>
      <div class="flex flex-col md:flex-row gap-12 text-left mb-20">
        <div class="md:w-1/2">
          <img src="/gedung.jpg" class="rounded-3xl shadow-xl w-full h-[350px] object-cover">
        </div>
        <div class="md:w-1/2">
          <h4 class="text-2xl font-bold mb-4">Sejarah Singkat</h4>
          <p class="text-gray-600 leading-relaxed">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text...</p>
        </div>
      </div>
    </section>

    <section class="bg-orange-500 py-20 px-12 text-white">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2 text-left" data-aos="fade-right">
          <h3 class="text-3xl font-black mb-8 leading-snug">Repositori karya tulis siswa & guru SMK 1 Gunungputri.</h3>
          <button class="bg-white text-orange-600 px-8 py-4 rounded-full font-bold flex items-center gap-3">
            Lihat Repository <span>→</span>
          </button>
        </div>
        <div class="md:w-1/2 grid grid-cols-3 gap-4" data-aos="fade-left">
          <div v-for="i in 3" :key="i" class="bg-white p-2 rounded-lg shadow-2xl transform rotate-3 hover:rotate-0 transition-all">
            <img src="/book-sample.jpg" class="w-full h-60 object-cover rounded shadow">
          </div>
        </div>
      </div>
    </section>

    <footer class="bg-orange-600 text-white py-16 px-12 border-t border-white/10">
      <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-12 text-left">
        <div>
          <h2 class="text-2xl font-black mb-4">SKIE-Library</h2>
          <p class="text-orange-100 text-sm">Update kegiatan kami di media sosial.</p>
        </div>
        <div>
          <h4 class="font-bold mb-4">Peta Situs</h4>
          <ul class="text-sm space-y-2 opacity-80">
            <li>Beranda</li><li>Fitur</li><li>Tentang Kami</li>
          </ul>
        </div>
        <div class="text-right">
          <h4 class="font-bold mb-4">Hubungi Kami</h4>
          <p class="text-sm opacity-80">Jl. Barokah No. 06, Gunungputri, Bogor</p>
        </div>
      </div>
    </footer>
    <AuthModal :show="showAuthModal" @close="showAuthModal = false" />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { ref } from 'vue';
import AuthModal from '@/Components/AuthModal.vue';

const showAuthModal = ref(false);

const features = [
  { title: 'Scan Barcode', desc: 'Pinjam buku cepat dengan scan dari HP.' },
  { title: 'Notifikasi', desc: 'Dapatkan info ketersediaan buku real-time.' },
  { title: 'Sistem Poin', desc: 'Kumpulkan poin setiap kali membaca.' }
];

onMounted(() => {
  AOS.init({ duration: 1000, once: true });
});
</script>