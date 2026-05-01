<template>
  <div class="min-h-screen bg-white font-sans text-gray-800 selection:bg-orange-100 overflow-x-hidden">
    <Navbar @open-auth="showAuthModal = true" />
    <header class="relative pt-40 md:pt-40 pb-10 md:pb-20 px-4 md:px-12 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 md:gap-0">
      <div class="md:w-1/2" data-aos="fade-right">
        <h2 class="text-orange-500 font-bold text-xl mb-3 text-left">Selamat Datang di!</h2>
        <h1 class="text-3xl md:text-5xl font-black leading-tight mb-6 text-gray-900 text-left">
          Pengelolaan Perpustakaan <br>
          <span class="text-orange-500">SMKN 1 Gunungputri</span> Berbasis Teknologi Web
        </h1>
        <p class="text-gray-600 mb-8 text-lg leading-relaxed max-w-lg text-left">
          Pengelolaan koleksi perpustakaan yang lebih mudah, cepat dan modern menggunakan Sistem Informasi berbasis Digital di SMKN 1 Gunungputri.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <button class="border-2 border-orange-500 text-orange-500 px-6 sm:px-8 py-3 rounded-full font-bold hover:bg-orange-50 transition-all text-sm sm:text-base">Mulai Sekarang</button>
          <button class="bg-orange-500 text-white px-6 sm:px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-orange-200 transition-all text-sm sm:text-base">Pelajari Fitur Dahulu</button>
        </div>
      </div>
      
      <div class="md:w-1/2 flex justify-center md:justify-end mt-8 md:mt-0" data-aos="zoom-in">
        <div class="relative w-64 h-64 md:w-[400px] md:h-[400px]">
          <div class="absolute -top-5 -right-5 w-40 h-40 md:w-64 md:h-64 bg-orange-500 rounded-full opacity-90"></div>
          <div class="relative z-10 w-full h-full rounded-full border-[6px] md:border-[10px] border-white shadow-2xl overflow-hidden bg-gray-100">
            <img src="/kepala-sekolah.png" class="w-full h-full object-cover">
          </div>
          <div class="absolute bottom-2 md:bottom-5 left-0 z-20 bg-white/90 backdrop-blur-sm px-3 md:px-6 py-2 md:py-3 rounded-xl md:rounded-2xl shadow-xl border border-white">
            <p class="font-bold text-gray-900 text-xs md:text-sm">Usman Ilham Pratama, M.Pd</p>
            <p class="text-[8px] md:text-[10px] text-gray-500 font-medium">Kepala Sekolah SMKN 1 Gunungputri</p>
          </div>
        </div>
      </div>
    </header>

    <section>
      <div class="max-w-7xl mx-auto text-center py-16">
        <div class="flex justify-center items-center p-4 rounded-full bg-orange-400 w-max mx-auto mb-6">
          <img src="/image/logo-smk.png" alt="Logo" class="mx-auto w-20 h-20"/>      
        </div>
        <h2 class="text-3xl font-bold mb-2">Web ini merupakan web pengelolaan perpustakaan dari SMKN 1 Gunungputri</h2>
        <p class="text-gray-500">Kunjungi halaman profil dan situs kami untuk melihat lebih banyak</p>
        <div class="flex justify-center gap-4 mt-4">
          <button class="bg-[#F69351] text-white px-6 py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 transition">Mulai Sekarang</button>
        </div>
      </div>
    </section>

    <section id="fitur" class="sf">
      <div class="sf__bg-glow sf__bg-glow--a"></div>
      <div class="sf__bg-glow sf__bg-glow--b"></div>
      <svg class="sf__bg-grid" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
        <defs>
          <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
            <path d="M 48 0 L 0 0 0 48" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
      </svg>

      <div class="sf__inner">
        <header class="sf__head">
          <div class="sf__kicker">
            <span class="sf__kicker-pip"></span>
            Fitur Unggulan
            <span class="sf__kicker-pip"></span>
          </div>

          <h2 class="sf__h2">
            Semua yang kamu butuhkan<br>
            ada di <em>SKIE&#8209;Library</em>
          </h2>

          <p class="sf__sub">
            Perpustakaan digital SMKN 1 Gunungputri, dirancang untuk<br>
            pengalaman meminjam yang mudah, cepat, dan menyenangkan.
          </p>
        </header>

        <div class="sf__body">
          <div class="sf__vis-col">
            <transition name="vis" mode="out-in">
              <div :key="activeIndex" class="sf__vis">
                <template v-if="activeIndex === 0">
                  <div class="v v--scan">
                    <div class="v__scan-wrap">
                      <div class="v__scan-frame">
                        <div class="v__scan-laser"></div>
                        <span class="v__sc v__sc--tl"></span>
                        <span class="v__sc v__sc--tr"></span>
                        <span class="v__sc v__sc--bl"></span>
                        <span class="v__sc v__sc--br"></span>
                        <div class="v__bars">
                          <div v-for="n in 20" :key="n" class="v__bar"
                            :style="{ height: (Math.abs(Math.sin(n * 1.9)) * 40 + 28) + 'px', opacity: 0.7 + Math.abs(Math.sin(n * 0.7)) * 0.3 }">
                          </div>
                        </div>
                      </div>
                      <div class="v__scan-ping"></div>
                    </div>
                    <p class="v__label">Arahkan kamera → proses selesai dalam detik</p>
                  </div>
                </template>

                <template v-else-if="activeIndex === 1">
                  <div class="v v--data">
                    <div class="v__data-row" v-for="(r, i) in dataRows" :key="i"
                      :style="{ '--d': i * 0.1 + 's' }">
                      <span class="v__data-e">{{ r.e }}</span>
                      <span class="v__data-t">{{ r.t }}</span>
                      <span :class="['v__data-s', 'v__data-s--' + r.c]">{{ r.s }}</span>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 2">
                  <div class="v v--notif">
                    <div class="v__bell-wrap">
                      <span class="v__bell">🔔</span>
                      <span class="v__bell-ring"></span>
                    </div>
                    <div class="v__chips">
                      <span class="v__chip" v-for="(c, i) in notifChips" :key="i"
                        :style="{ '--d': i * 0.15 + 's' }">{{ c }}</span>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 3">
                  <div class="v v--history">
                    <div class="v__tl">
                      <div class="v__tl-track"></div>
                      <div class="v__tl-item" v-for="(h, i) in historyItems" :key="i"
                        :style="{ '--d': i * 0.12 + 's' }">
                        <div class="v__tl-dot"></div>
                        <div class="v__tl-text">
                          <span class="v__tl-date">{{ h.d }}</span>
                          <span class="v__tl-title">{{ h.t }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 4">
                  <div class="v v--points">
                    <div class="v__pts-ring">
                      <span class="v__pts-num">240</span>
                      <span class="v__pts-unit">poin</span>
                    </div>
                    <div class="v__badge-row">
                      <span class="v__badge" v-for="b in badges" :key="b">{{ b }}</span>
                    </div>
                  </div>
                </template>

                <template v-else-if="activeIndex === 5">
                  <div class="v v--forum">
                    <div class="v__review" v-for="(r, i) in reviewList" :key="i"
                      :style="{ '--d': i * 0.18 + 's' }">
                      <div class="v__review-stars">{{ r.s }}</div>
                      <div class="v__review-text">{{ r.t }}</div>
                    </div>
                    <div class="v__repo">📄 Repositori KTI tersedia</div>
                  </div>
                </template>
              </div>
            </transition>
          </div>

          <ul class="sf__list">
            <li
              v-for="(f, i) in fiturList"
              :key="i"
              class="sf__item"
              :class="{ 'is-active': activeIndex === i }"
              @click="select(i)"
            >
              <div class="sf__item-head">
                <span class="sf__no">{{ f.no }}</span>
                <span class="sf__title">{{ f.title }}</span>
                <span class="sf__arrow">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
              </div>
              <div class="sf__desc">
                <p>{{ f.desc }}</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <section class="py-12 md:py-24 px-4 md:px-12 max-w-7xl mx-auto text-center" data-aos="fade-up">
      <h3 class="text-2xl md:text-3xl font-black mb-8 md:mb-16 underline decoration-orange-500 underline-offset-8">Profil Perpustakaan</h3>
      <div class="flex flex-col md:flex-row gap-6 md:gap-12 text-left mb-10 md:mb-20">
        <div class="md:w-1/2">
          <img src="/gedung.jpg" class="rounded-2xl md:rounded-3xl shadow-xl w-full h-60 md:h-[350px] object-cover">
        </div>
        <div class="md:w-1/2">
          <h4 class="text-xl md:text-2xl font-bold mb-4">Sejarah Singkat</h4>
          <p class="text-gray-600 leading-relaxed text-sm md:text-base">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text...</p>
        </div>
      </div>
    </section>

    <section class="bg-orange-500 py-12 md:py-20 px-4 md:px-12 text-white">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-8 md:gap-12">
        <div class="md:w-1/2 text-left" data-aos="fade-right">
          <h3 class="text-2xl md:text-3xl font-black mb-6 md:mb-8 leading-snug">Repositori karya tulis siswa & guru SMK 1 Gunungputri.</h3>
          <button class="bg-white text-orange-600 px-6 md:px-8 py-3 md:py-4 rounded-full font-bold flex items-center gap-2 md:gap-3 text-sm md:text-base">
            Lihat Repository <span>→</span>
          </button>
        </div>
        <div class="md:w-1/2 grid grid-cols-3 gap-2 md:gap-4" data-aos="fade-left">
          <div v-for="i in 3" :key="i" class="bg-white p-1 md:p-2 rounded-lg shadow-2xl transform rotate-3 hover:rotate-0 transition-all">
            <img src="/book-sample.jpg" class="w-full h-32 md:h-60 object-cover rounded shadow">
          </div>
        </div>
      </div>
    </section>

    <footer class="bg-orange-600 text-white py-10 md:py-16 px-4 md:px-12 border-t border-white/10">
      <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-8 md:gap-12 text-left">
        <div>
          <h2 class="text-xl md:text-2xl font-black mb-4">SKIE-Library</h2>
          <p class="text-orange-100 text-xs md:text-sm">Update kegiatan kami di media sosial.</p>
        </div>
        <div>
          <h4 class="font-bold mb-4 text-sm md:text-base">Peta Situs</h4>
          <ul class="text-xs md:text-sm space-y-2 opacity-80">
            <li>Beranda</li><li>Fitur</li><li>Tentang Kami</li>
          </ul>
        </div>
        <div class="text-left md:text-right">
          <h4 class="font-bold mb-4 text-sm md:text-base">Hubungi Kami</h4>
          <p class="text-xs md:text-sm opacity-80">Jl. Barokah No. 06, Gunungputri, Bogor</p>
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

const activeIndex = ref(0)

const reviewList = [
  { s: '⭐⭐⭐⭐⭐', t: '"Luar biasa, sangat inspiratif!"' },
  { s: '⭐⭐⭐⭐',   t: '"Wajib baca semua pelajar."' },
]

const dataRows = [
  { e: '📚', t: 'Matematika Diskrit', s: 'Dipinjam',     c: 'orange' },
  { e: '✅', t: 'Bumi — Tere Liye',   s: 'Dikembalikan', c: 'green'  },
  { e: '⏳', t: 'Clean Code',          s: '2 hari lagi',  c: 'warn'   },
]

const notifChips = ['📬 Buku tersedia!', '⏰ Jatuh tempo besok', '🎉 Event literasi baru']

const historyItems = [
  { d: '12 Mar 2025', t: 'Harry Potter & The Chamber' },
  { d: '5 Feb 2025',  t: 'Bumi — Tere Liye' },
  { d: '20 Jan 2025', t: 'Atomic Habits' },
]

const badges = ['🎖 Pembaca Aktif', '🏅 Tepat Waktu', '🎟 Event Access']

const fiturList = [
  { no: '01', title: 'Scan barcode buku',                  desc: 'Foto dan scan barcode yang ada di buku dengan kamera handphone untuk peminjaman buku secara cepat tanpa antre di meja petugas.' },
  { no: '02', title: 'Pendataan peminjaman & pengembalian', desc: 'Seluruh data peminjaman dan pengembalian buku tercatat otomatis dalam sistem, mudah dicek kapan saja dan di mana saja.' },
  { no: '03', title: 'Notifikasi buku tersedia',            desc: 'Tandai buku yang sedang tidak tersedia, dan kamu akan mendapat notifikasi segera setelah buku itu bisa dipinjam kembali.' },
  { no: '04', title: 'Riwayat & tenggat peminjaman',        desc: 'Pantau semua buku yang pernah kamu pinjam, lengkap dengan tanggal peminjaman dan tenggat pengembalian agar tidak terlambat.' },
  { no: '05', title: 'Sistem poin & event',                 desc: 'Dapatkan poin setiap kali meminjam buku tepat waktu. Tukarkan poin untuk reward menarik atau ikuti event literasi eksklusif.' },
  { no: '06', title: 'Forum review & repositori karya',     desc: 'Bagikan ulasanmu tentang buku yang sudah dibaca dan temukan repositori karya tulis ilmiah maupun kreatif dari seluruh siswa.' },
]

function select(i) { activeIndex.value = i }
</script>

<style>
*, *::before, *::after { box-sizing: border-box; }

.sf {
  --bg:        #F97316;
  --surface:   #EA580C;
  --orange:    #FFFFFF;
  --orange-lo: rgba(255,255,255,.15);
  --orange-md: rgba(255,255,255,.35);
  --teal:      #FFE0B5;
  --green:     #D4E6B3;
  --warn:      #FFF0CC;
  --text:      #FFFFFF;
  --muted:     #FFF2E0;
  --line:      rgba(255,255,255,.2);
}

.sf {
  margin: 0;
  padding: 60px 16px 120px;
  background: var(--bg);
  position: relative;
  font-family: 'Syne', sans-serif;
  overflow: hidden;
  color: var(--text);
}

@media (min-width: 768px) {
  .sf {
    padding: 80px 48px 140px;
  }
}

.sf__bg-glow {
  position: absolute; border-radius: 50%;
  filter: blur(100px); pointer-events: none; z-index: 0;
}
.sf__bg-glow--a {
  width: 520px; height: 520px;
  top: -160px; left: -80px;
  background: radial-gradient(circle, rgba(255,255,255,.25) 0%, transparent 70%);
}
.sf__bg-glow--b {
  width: 380px; height: 380px;
  bottom: -100px; right: 5%;
  background: radial-gradient(circle, rgba(255,245,220,.2) 0%, transparent 70%);
}

.sf__bg-grid {
  position: absolute; inset: 0; z-index: 0; pointer-events: none;
}

.sf__inner {
  position: relative; z-index: 1;
  max-width: 1280px; margin: 0 auto;
  padding: 0 16px;
}

@media (min-width: 768px) {
  .sf__inner {
    padding: 0 24px;
  }
}

.sf__head {
  margin-bottom: 60px;
}

@media (min-width: 768px) {
  .sf__head {
    margin-bottom: 80px;
  }
}

.sf__kicker {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 10px; font-weight: 700; letter-spacing: 4px; text-transform: uppercase;
  color: var(--text);
  margin-bottom: 28px;
  opacity: 0.85;
}
.sf__kicker-pip {
  display: block; width: 20px; height: 1px; background: var(--text);
}

.sf__h2 {
  font-family: 'Fraunces', serif;
  font-size: clamp(36px, 5vw, 62px);
  font-weight: 300;
  line-height: 1.12;
  color: var(--text);
  margin-bottom: 20px;
}
.sf__h2 em {
  font-style: italic;
  color: #FFFFFF;
  font-weight: 600;
  text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.sf__sub {
  font-size: 15px;
  color: rgba(255,255,255,0.85);
  line-height: 1.8;
  font-weight: 400;
  max-width: 480px;
}

.sf__body {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0 48px;
  align-items: start;
}

@media (max-width: 1024px) {
  .sf__body {
    gap: 0 32px;
  }
}

.sf__vis-col {
  position: sticky; top: 48px;
  height: 400px;
  display: flex; align-items: center; justify-content: center;
}

.sf__vis {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
}

.vis-enter-active { transition: opacity .4s ease, transform .4s ease; }
.vis-leave-active { transition: opacity .2s ease, transform .2s ease; }
.vis-enter-from   { opacity: 0; transform: translateY(16px) scale(.98); }
.vis-leave-to     { opacity: 0; transform: translateY(-10px) scale(.98); }

.v { display: flex; flex-direction: column; align-items: center; gap: 24px; width: 100%; }

.v--scan {}
.v__scan-wrap { position: relative; display: inline-flex; }
.v__scan-frame {
  width: 160px; height: 160px;
  display: flex; align-items: flex-end; justify-content: center;
  gap: 3px;
  padding: 16px 20px 20px;
  position: relative;
}
.v__scan-laser {
  position: absolute; left: 20px; right: 20px; height: 2px;
  background: linear-gradient(90deg, transparent, var(--text), transparent);
  box-shadow: 0 0 12px var(--text);
  animation: laserMove 2s ease-in-out infinite;
  border-radius: 1px;
}
@keyframes laserMove {
  0%,100% { top: 20px; opacity: 0; }
  15%     { opacity: 1; }
  85%     { opacity: 1; }
  99%     { top: 130px; }
}
.v__sc {
  position: absolute; width: 16px; height: 16px;
  border-color: var(--text); border-style: solid;
}
.v__sc--tl { top: 0; left: 0;  border-width: 2px 0 0 2px; }
.v__sc--tr { top: 0; right: 0; border-width: 2px 2px 0 0; }
.v__sc--bl { bottom: 0; left: 0;  border-width: 0 0 2px 2px; }
.v__sc--br { bottom: 0; right: 0; border-width: 0 2px 2px 0; }
.v__bars { display: flex; align-items: flex-end; gap: 3px; height: 80px; }
.v__bar { width: 4px; background: var(--text); border-radius: 1px; }
.v__scan-ping {
  position: absolute; inset: -8px; border-radius: 4px;
  border: 1px solid var(--orange-md);
  animation: pingPulse 2.4s ease-out infinite;
}
@keyframes pingPulse {
  0%   { transform: scale(1); opacity: .6; }
  70%  { transform: scale(1.06); opacity: 0; }
  100% { transform: scale(1.06); opacity: 0; }
}
.v__label {
  font-size: 12px; color: var(--muted); letter-spacing: .4px; text-align: center;
}

.v--data { gap: 0; }
.v__data-row {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid var(--line);
  width: 100%; max-width: 320px;
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
}
.v__data-row:first-child { border-top: 1px solid var(--line); }
.v__data-e { font-size: 20px; }
.v__data-t { flex: 1; font-size: 13px; color: var(--text); font-weight: 600; }
.v__data-s {
  font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
  padding: 4px 10px; border-radius: 100px;
}
.v__data-s--orange { background: rgba(255,255,255,.2);  color: #FFFFFF; }
.v__data-s--green  { background: rgba(255,255,255,.2);  color: #FFFFFF;  }
.v__data-s--warn   { background: rgba(255,255,255,.2);  color: #FFFFFF;   }

.v--notif { gap: 20px; }
.v__bell-wrap { position: relative; width: 72px; height: 72px; display: flex; align-items: center; justify-content: center; }
.v__bell { font-size: 44px; animation: bellRock 2.8s ease infinite; display: block; position: relative; z-index: 1; }
@keyframes bellRock {
  0%,100%    { transform: rotate(0); }
  8%, 24%    { transform: rotate(-14deg); }
  16%, 32%   { transform: rotate(14deg); }
  40%        { transform: rotate(0); }
}
.v__bell-ring {
  position: absolute; inset: 0; border-radius: 50%;
  border: 1.5px solid var(--orange-md);
  animation: ringExpand 2.8s ease infinite;
}
@keyframes ringExpand {
  0%   { transform: scale(.8); opacity: 0; }
  40%  { transform: scale(1.5); opacity: .6; }
  80%  { transform: scale(2); opacity: 0; }
  100% { opacity: 0; }
}
.v__chips { display: flex; flex-direction: column; gap: 10px; align-items: center; }
.v__chip {
  padding: 8px 20px; border-radius: 100px;
  font-size: 12px; font-weight: 600; color: var(--text);
  border: 1px solid var(--line);
  background: rgba(255,255,255,.1);
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
  white-space: nowrap;
}

.v--history {}
.v__tl { position: relative; width: 100%; max-width: 280px; padding-left: 24px; }
.v__tl-track {
  position: absolute; left: 3px; top: 8px; bottom: 8px; width: 1px;
  background: linear-gradient(to bottom, var(--text), var(--teal));
}
.v__tl-item {
  display: flex; gap: 16px; padding: 10px 0;
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
}
.v__tl-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--text); margin-top: 4px; flex-shrink: 0;
  position: relative; left: -27px; margin-right: -8px;
  box-shadow: 0 0 0 3px var(--orange-lo);
}
.v__tl-text { display: flex; flex-direction: column; gap: 2px; }
.v__tl-date  { font-size: 10px; color: var(--muted); letter-spacing: .5px; }
.v__tl-title { font-size: 13px; color: var(--text); font-weight: 600; }

.v--points { gap: 24px; }
.v__pts-ring {
  width: 160px; height: 160px;
  border-radius: 50%;
  border: 1px solid var(--orange-md);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  position: relative;
  box-shadow: 0 0 40px var(--orange-lo), inset 0 0 40px rgba(255,255,255,.04);
}
.v__pts-ring::before {
  content: '';
  position: absolute; inset: 8px; border-radius: 50%;
  border: 1px dashed rgba(255,255,255,.3);
}
.v__pts-num {
  font-family: 'Fraunces', serif;
  font-size: 56px; font-weight: 600; color: var(--text);
  line-height: 1;
}
.v__pts-unit { font-size: 12px; color: var(--muted); font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-top: 4px; }
.v__badge-row { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
.v__badge {
  font-size: 11px; font-weight: 600; color: var(--muted);
  padding: 5px 14px; border-radius: 100px;
  border: 1px solid var(--line);
}

.v--forum { gap: 16px; align-items: flex-start; padding-left: 8px; }
.v__review {
  max-width: 300px; width: 100%;
  padding-left: 16px;
  border-left: 2px solid var(--text);
  animation: fadeUp .35s ease both;
  animation-delay: var(--d);
}
.v__review-stars { font-size: 13px; margin-bottom: 4px; }
.v__review-text  { font-size: 13px; color: var(--muted); font-style: italic; }
.v__repo {
  font-size: 11px; color: var(--teal); font-weight: 700;
  padding: 6px 16px; border: 1px solid rgba(255,255,255,.35); border-radius: 100px;
  letter-spacing: .5px;
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

.sf__list {
  list-style: none;
  border-top: 1px solid var(--line);
}

.sf__item {
  border-bottom: 1px solid var(--line);
  cursor: pointer;
  position: relative;
  transition: background-color 0.3s ease;
  padding: 0;
}

.sf__item:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.sf__item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 2px;
  background: var(--text);
  transform: scaleY(0);
  transform-origin: bottom;
  transition: transform 0.35s ease;
}

.sf__item.is-active::before {
  transform: scaleY(1);
}

.sf__item-head {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 0;
  user-select: none;
  position: relative;
  z-index: 1;
}

.sf__no {
  font-family: 'Fraunces', serif;
  font-size: 13px;
  font-style: italic;
  font-weight: 300;
  color: var(--muted);
  width: 30px;
  flex-shrink: 0;
  margin-right: 12px;
  text-align: right;
  transition: color 0.25s, font-weight 0.25s;
}

.sf__title {
  flex: 1;
  font-size: 15px;
  font-weight: 600;
  color: var(--text);
  transition: color 0.25s, font-weight 0.25s;
  letter-spacing: -0.01em;
}

.sf__arrow {
  color: var(--muted);
  transition: transform 0.3s ease, color 0.25s;
  display: flex;
  align-items: center;
}

.sf__item:hover .sf__title {
  color: #FFFFFF;
  text-decoration: underline;
}

.sf__item:hover .sf__arrow {
  transform: translateX(5px);
  color: var(--text);
}

.sf__item.is-active .sf__no {
  color: var(--text);
  font-weight: 600;
}

.sf__item.is-active .sf__title {
  color: var(--text);
  font-weight: 700;
}

.sf__item.is-active .sf__arrow {
  transform: translateX(6px);
  color: var(--text);
}

.sf__desc {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.5s ease, padding 0.4s ease, opacity 0.3s ease;
  opacity: 0;
  padding: 0 0 0 62px;
}

.sf__item.is-active .sf__desc {
  max-height: 150px;
  opacity: 1;
  padding: 0 20px 24px 62px;
}

.sf__desc p {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.8;
  margin: 0;
  transform: translateY(8px);
  transition: transform 0.4s ease 0.1s;
}

.sf__item.is-active .sf__desc p {
  transform: translateY(0);
}
</style>