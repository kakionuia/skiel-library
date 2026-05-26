<template>
  <div class="min-h-screen bg-white font-sans text-slate-800">
    <!-- ========== HERO SECTION ========== -->
    <section
      ref="heroSection"
      class="relative min-h-[100svh] lg:min-h-[600px] flex items-center overflow-hidden bg-gradient-to-br from-orange-700 to-orange-500 text-white justify-center"
    >
      <!-- Background Decorators -->
      <div class="absolute inset-0 z-0">
        <div
          class="absolute -left-20 -top-20 h-[300px] w-[300px] lg:h-[500px] lg:w-[500px] rounded-full bg-white/10 blur-[80px] lg:blur-[120px]"
        />
        <div
          class="absolute right-0 bottom-0 h-[250px] w-[250px] lg:h-[400px] lg:w-[400px] rounded-full bg-orange-400/20 blur-[70px] lg:blur-[100px]"
        />
        <div
          class="absolute inset-0 opacity-[0.05]"
          style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px"
        />
      </div>

      <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 py-12 lg:py-20 w-full">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
          <!-- Kolom Kiri: Informasi & Status -->
          <div
            class="space-y-6 lg:space-y-8 text-center lg:text-left flex flex-col items-center lg:items-start"
          >
            <!-- Badge Menu -->
            <div
              class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs font-bold tracking-wider uppercase backdrop-blur-md"
            >
              <span class="relative flex h-2 w-2">
                <span
                  class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"
                />
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white" />
              </span>
              SKIE-Library System
            </div>

            <!-- Ucapan Selamat & Nama -->
            <h1
              class="text-4xl sm:text-5xl font-extrabold tracking-tight lg:text-6xl leading-[1.1]"
            >
              Selamat Pagi, <br />
              <span class="text-amber-200 block sm:inline">{{ user.name }}</span>
            </h1>

            <p class="max-w-md text-base sm:text-lg text-orange-50 leading-relaxed">
              Tingkatkan literasi digital dan temukan referensi terbaik untuk mendukung
              perjalanan akademikmu.
            </p>

            <!-- Widget Jam & Status Operasional Perpustakaan -->
            <div
              class="w-full max-w-md rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-lg text-left"
            >
              <div>
                <div class="text-xs uppercase tracking-wider text-orange-200 font-bold">
                  Waktu Saat Ini
                </div>
                <div class="text-2xl font-mono font-bold text-white">
                  {{ currentTime }}
                </div>
              </div>
              <div
                class="w-full sm:w-auto flex items-center justify-center sm:justify-start gap-3 px-4 py-2.5 rounded-xl bg-black/10"
              >
                <!-- Indicator Bulat Dinamis -->
                <span class="relative flex h-3 w-3">
                  <span
                    :class="[
                      'animate-ping absolute inline-flex h-full w-full rounded-full opacity-75',
                      libraryStatus.color
                    ]"
                  />
                  <span
                    :class="[
                      'relative inline-flex rounded-full h-3 w-3',
                      libraryStatus.color
                    ]"
                  />
                </span>
                <div class="text-sm">
                  <span class="font-bold block text-white">
                    {{ libraryStatus.text }}
                  </span>
                  <span class="text-xs text-orange-100">
                    {{ libraryStatus.note }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Kolom Kanan: Grid Tombol (Sangat Ramah Mobile) -->
          <div class="grid grid-cols-2 gap-3 p-1 sm:gap-4 w-full max-w-2xl mx-auto">
  <button
    v-for="action in quickActions"
    :key="action.label"
    type="button"
    @click="handleQuickAction(action)"
    class="group relative flex flex-col justify-between items-start gap-4 rounded-2xl bg-white p-4 text-left border border-slate-100 shadow-sm transition-all duration-200 active:scale-95 active:bg-slate-50 tap-highlight-transparent overflow-hidden"
  >
    <span class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-gradient-to-br from-orange-500/10 to-transparent opacity-0 group-active:opacity-100 md:group-hover:opacity-100 transition-opacity duration-300"></span>

    <div
      class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600 transition-all duration-300 md:group-hover:bg-orange-600 md:group-hover:text-white"
    >
      <component :is="action.icon" class="h-5 w-5" />
    </div>

    <div class="w-full mt-2">
      <h3 class="text-sm font-semibold text-slate-800 tracking-tight line-clamp-1">
        {{ action.label }}
      </h3>
      <p
        class="text-xs text-slate-500 leading-normal line-clamp-2 mt-1"
      >
        {{ action.description }}
      </p>
    </div>
  </button>
</div>
        </div>
      </div>

      <!-- Wave Divider bawah -->
      <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none z-10">
        <svg
          class="relative block h-6 sm:h-12 w-full"
          viewBox="0 0 1200 120"
          preserveAspectRatio="none"
        >
          <path
            d="M1200 120L0 120L0 0C161.333 46.6667 361.333 70 600 70C838.667 70 1038.67 46.6667 1200 0V120Z"
            fill="#FFFFFF"
          />
        </svg>
      </div>
    </section>

    <!-- ========== NAVBAR ========== -->
    <nav
      ref="navbarRef"
      class="w-full z-40 bg-white border-b border-slate-100 transition-all duration-300"
      :class="{ 'sticky top-0 shadow-md': navbarSticky }"
    >
      <div
        class="mx-auto max-w-7xl px-4 md:px-8 py-4 flex justify-between items-center gap-4 md:gap-5"
      >
        <!-- Tombol Toggle Sidebar -->
        <button
          @click="toggleSidebar"
          class="text-orange-500 p-1.5 rounded-xl hover:bg-orange-50 transition-all active:scale-90"
          aria-label="Menu Utama"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-7 w-7 stroke-[2.5]"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>

        <!-- Desktop Search Section -->
        <div class="hidden md:flex items-center gap-2.5 w-full max-w-2xl">
          <button
            type="button"
            @click="openScanBorrow"
            class="shrink-0 bg-white border border-orange-500 text-orange-500 p-2.5 rounded-xl shadow-xs hover:bg-orange-50 transition-all active:scale-95"
            title="Scan barcode buku"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M4 5q-.825 0-1.412.587Q2 6.175 2 7v10q0 .825.588 1.413Q3.175 19 4 19h16q.825 0 1.413-.587Q22 17.825 22 17V7q0-.825-.587-1.413Q20.825 5 20 5h-3.15L15.45 3H8.55L7.15 5ZM12 17q-2.075 0-3.537-1.463Q7 14.075 7 12q0-2.075 1.463-3.537Q9.925 7 12 7q2.075 0 3.538 1.463Q17 9.925 17 12q0 2.075-1.462 3.537Q14.075 17 12 17Zm0-2q1.25 0 2.125-.875T15 12q0-1.25-.875-2.125T12 9q-1.25 0-2.125.875T9 12q0 1.25.875 2.125T12 15Z"
              />
            </svg>
          </button>

          <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
              <input
                v-model="bookSearch"
                type="text"
                placeholder="Cari buku, penulis, dan tahun terbit..."
                class="w-full border border-orange-500 rounded-xl py-2.5 px-4 shadow-3xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-hidden text-slate-800 placeholder:text-slate-400 transition"
              />
            </div>
            <button
              @click="triggerSearch"
              class="bg-orange-500 text-white px-5 rounded-xl shadow-xs hover:bg-orange-600 transition-all active:scale-95"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 stroke-[3]"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Search Button -->
        <button
          @click="toggleMobileSearch"
          class="md:hidden text-orange-500 p-2 rounded-xl hover:bg-orange-50 transition active:scale-95"
          aria-label="Cari buku"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 stroke-[2.5]"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </button>

        <!-- User Avatar / Profile Link -->
        <Link
          href="/profile"
          class="shrink-0 rounded-full focus:outline-hidden focus:ring-2 focus:ring-orange-500/40"
        >
          <img
            :src="userAvatar"
            class="h-10 w-10 md:h-11 md:w-11 rounded-full border-2 border-slate-100 object-cover hover:scale-105 transition duration-200 shadow-xs"
            alt="Profile"
          />
        </Link>
      </div>

      <!-- Mobile Search Bar (Dropdown) -->
      <div
        v-if="showMobileSearch"
        class="md:hidden px-4 pb-4 transition-all animate-fadeIn"
      >
        <div class="flex gap-2">
          <div class="relative flex-1">
            <input
              v-model="bookSearch"
              type="text"
              placeholder="Cari buku, penulis, atau kategori..."
              class="w-full border border-orange-500 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-hidden text-slate-800 placeholder:text-slate-400"
              autofocus
            />
          </div>
          <button
            @click="triggerSearch"
            class="bg-orange-500 text-white px-4 rounded-xl active:scale-95 transition"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 stroke-[2.5]"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Category Bar -->
      <div class="bg-orange-500 shadow-inner">
        <div class="mx-auto max-w-7xl px-4 md:px-8 py-2.5">
          <div
            class="flex items-center gap-3 overflow-x-auto whitespace-nowrap text-white font-bold text-xs tracking-wide no-scrollbar"
          >
            <button
              @click="setActiveCategory('')"
              class="transition duration-150 px-3 py-1.5 rounded-lg font-extrabold uppercase shrink-0"
              :class="
                activeCategory === ''
                  ? 'bg-white/20 text-white'
                  : 'opacity-80 hover:opacity-100 hover:bg-white/10'
              "
            >
              Semua Buku
            </button>
            <button
              v-for="category in allCategories"
              :key="category"
              @click="setActiveCategory(category)"
              class="transition duration-150 px-3 py-1.5 rounded-lg font-extrabold uppercase shrink-0"
              :class="
                activeCategory === category
                  ? 'bg-white/20 text-white'
                  : 'opacity-80 hover:opacity-100 hover:bg-white/10'
              "
            >
              {{ category }}
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- ========== SIDEBAR DRAWER ========== -->
    <div v-if="isSidebarOpen" class="fixed inset-0 z-50 overflow-hidden">
      <!-- Backdrop Overlay (Fade-In) -->
      <div
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300 ease-out animate-fadeIn"
        @click="closeSidebar"
      ></div>

      <!-- Sidebar Panel (Slide-In) -->
      <div
        class="absolute left-0 top-0 h-full w-72 bg-white shadow-xl flex flex-col justify-between transition-transform duration-300 ease-out transform translate-x-0 animate-slideIn"
        @click.stop
      >
        <!-- Upper Navigation Section -->
        <div class="flex-1 overflow-y-auto no-scrollbar">
          <!-- Sidebar Header -->
          <div
            class="p-6 border-b border-slate-100 flex items-center gap-4 bg-white"
          >
            <img
              :src="userAvatar"
              class="h-11 w-11 rounded-full border border-slate-100 object-cover ring-2 ring-orange-500/10"
              alt="User Avatar"
            />
            <div class="min-w-0 flex-1">
              <h3
                class="font-semibold text-sm text-slate-800 tracking-tight truncate"
              >
                {{ user.name }}
              </h3>
              <p class="text-xs text-slate-400 truncate mt-0.5">
                {{ user.email || 'student@skie.sch.id' }}
              </p>
            </div>
          </div>

          <!-- Sidebar Navigation Menu Links -->
          <nav class="p-4 space-y-1">
            <!-- Beranda -->
            <Link
              href="/"
              class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-600 hover:text-orange-500 hover:bg-slate-50 transition-all duration-200 group relative"
            >
              <div
                class="absolute left-0 top-3 bottom-3 w-1 bg-orange-500 rounded-r-md opacity-0 group-hover:opacity-100 transition-opacity"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-400 group-hover:text-orange-500 transition-colors"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                />
              </svg>
              <span class="text-sm font-medium tracking-wide">Beranda</span>
            </Link>

            <!-- Rak Buku Saya -->
            <Link
              href="/my-loans"
              class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-600 hover:text-orange-500 hover:bg-slate-50 transition-all duration-200 group relative"
            >
              <div
                class="absolute left-0 top-3 bottom-3 w-1 bg-orange-500 rounded-r-md opacity-0 group-hover:opacity-100 transition-opacity"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-400 group-hover:text-orange-500 transition-colors"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                />
              </svg>
              <span class="text-sm font-medium tracking-wide">Rak Buku Saya</span>
            </Link>

            <!-- Riwayat Peminjaman -->
            <Link
              href="/loans"
              class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-600 hover:text-orange-500 hover:bg-slate-50 transition-all duration-200 group relative"
            >
              <div
                class="absolute left-0 top-3 bottom-3 w-1 bg-orange-500 rounded-r-md opacity-0 group-hover:opacity-100 transition-opacity"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-400 group-hover:text-orange-500 transition-colors"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span class="text-sm font-medium tracking-wide"
                >Riwayat Peminjaman</span
              >
            </Link>

            <!-- Layanan -->
            <Link
              href="/services"
              class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-600 hover:text-orange-500 hover:bg-slate-50 transition-all duration-200 group relative"
            >
              <div
                class="absolute left-0 top-3 bottom-3 w-1 bg-orange-500 rounded-r-md opacity-0 group-hover:opacity-100 transition-opacity"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-400 group-hover:text-orange-500 transition-colors"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                />
              </svg>
              <span class="text-sm font-medium tracking-wide">Layanan</span>
            </Link>

            <!-- Bantuan -->
            <Link
              href="/help"
              class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-slate-600 hover:text-orange-500 hover:bg-slate-50 transition-all duration-200 group relative"
            >
              <div
                class="absolute left-0 top-3 bottom-3 w-1 bg-orange-500 rounded-r-md opacity-0 group-hover:opacity-100 transition-opacity"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-slate-400 group-hover:text-orange-500 transition-colors"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span class="text-sm font-medium tracking-wide">Bantuan</span>
            </Link>
          </nav>
        </div>

        <!-- Sidebar Footer Action (Clean Logout Button) -->
        <div class="p-4 border-t border-slate-100 bg-white shrink-0">
          <button
            @click="logout"
            class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-all duration-200 group"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-slate-400 group-hover:text-rose-500 transition-colors"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
              />
            </svg>
            <span class="text-sm font-medium tracking-wide">Keluar Akun</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:py-12">
      <section id="catalog-section" class="space-y-16 scroll-mt-28">
        <!-- Empty States -->
        <div v-if="availableBooks.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">📚</div>
          <h3 class="text-lg font-bold text-slate-900">Belum ada buku tersedia</h3>
          <p class="text-slate-500 text-sm mt-1">
            Silahkan tambahkan buku di halaman admin.
          </p>
        </div>

        <div v-else-if="filteredGroups.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">🏜️</div>
          <h3 class="text-lg font-bold text-slate-900">
            Wah, bukunya nggak ketemu...
          </h3>
          <p class="text-slate-500 text-sm mt-1">
            Coba cari dengan kata kunci lain.
          </p>
          <button
            @click="resetFilters"
            class="mt-6 text-orange-600 font-bold text-sm underline hover:text-orange-800 transition-colors"
          >
            Reset Filter
          </button>
        </div>

        <!-- Daftar Grup Kategori -->
        <div
          v-for="group in filteredGroups"
          :key="group.name"
          class="space-y-6"
        >
          <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900">{{ group.name }}</h2>
            <span class="text-sm text-slate-500"
              >{{ group.totalBooks }} buku</span
            >
          </div>

          <!-- Grid Buku -->
          <div
            class="grid gap-4 justify-center"
            style="
              grid-template-columns: repeat(auto-fill, minmax(140px, 160px));
            "
          >
            <BookCard
              v-for="book in group.books"
              :key="book.id"
              :book="book"
              :img-errors="imgErrors"
              @image-error="onImageError"
            />
          </div>

          <!-- TOMBOL MORE -->
          <div
            v-if="
              group.totalBooks > itemsPerPage &&
              group.books.length < group.totalBooks
            "
            class="text-center"
          >
            <button
              @click="loadMore(group.name)"
              class="mt-4 px-8 py-3 bg-white border border-orange-300 text-orange-600 font-bold rounded-full hover:bg-orange-50 shadow-md transition-all active:scale-95"
            >
              Tampilkan Lebih Banyak ({{ group.books.length }} /
              {{ group.totalBooks }})
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- ========== BEST SELLER SECTION ========== -->
    <BestSellerSection :books="bestSellerBooks" />

    <!-- ========== REVIEWS SECTION ========== -->
    <ReviewsSection
      :best-reviews="bestReviews"
      :recent-reviews="recentReviews"
      :trending-reviews="trendingReviews"
    />

    <!-- ========== RIWAYAT SECTION ========== -->
    <section class="p-6 md:p-8 bg-white border-t border-slate-100">
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-orange-50 text-orange-500 rounded-xl">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h2
            class="text-lg md:text-xl font-bold text-slate-900 tracking-tight"
          >
            Riwayat Pinjam Buku
          </h2>
        </div>
        <Link
          href="/my-loans"
          class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl font-semibold text-xs md:text-sm transition-all active:scale-95"
        >
          Peminjaman Saya
        </Link>
      </div>

      <!-- Empty History State -->
      <div v-if="history.length === 0" class="text-center py-12">
        <div class="text-4xl mb-3">📋</div>
        <h3 class="text-sm font-bold text-slate-900">
          Belum ada riwayat peminjaman
        </h3>
        <p class="text-slate-400 text-xs mt-1">
          Mulai pinjam buku dan riwayat akan muncul di sini.
        </p>
      </div>

      <!-- History Content Cards -->
      <div
        v-else
        class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory"
      >
        <Link
          v-for="book in history"
          :key="book.id"
          :href="book.borrowId ? `/borrow/${book.borrowId}` : '/my-loans'"
          class="min-w-[290px] md:min-w-[380px] border border-slate-100 rounded-2xl p-4 flex gap-4 snap-start hover:border-orange-200 hover:shadow-lg transition-all bg-white group"
        >
          <img
            :src="book.cover"
            :alt="book.title"
            class="w-20 h-28 object-cover rounded-xl shadow-sm ring-1 ring-slate-100 flex-shrink-0"
          />
          <div class="flex-1 flex flex-col justify-between min-w-0">
            <div>
              <div class="flex justify-between items-start gap-2 mb-2">
                <h3
                  class="text-sm font-bold text-slate-900 leading-tight truncate group-hover:text-orange-500 transition-colors"
                >
                  {{ book.title }}
                </h3>
                <span
                  :class="[
                    'font-semibold text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full flex-shrink-0',
                    book.statusColor
                  ]"
                >
                  {{ book.status }}
                </span>
              </div>
              <div class="text-xs space-y-0.5 text-slate-500 font-medium">
                <p class="truncate">
                  <span class="text-slate-400 inline-block w-14">Penulis</span>
                  {{ book.author }}
                </p>
                <p class="truncate">
                  <span class="text-slate-400 inline-block w-14"
                    >Penerbit</span
                  >
                  {{ book.publisher }}
                </p>
                <p
                  v-if="book.dueDate"
                  class="text-red-500 font-semibold"
                >
                  <span
                    class="text-slate-400 font-medium inline-block w-14"
                    >Tenggat</span
                  >
                  {{ book.dueDate }}
                </p>
              </div>
            </div>
            <div class="mt-2">
              <span
                v-if="book.isReviewed"
                class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 rounded-lg text-[11px] font-semibold border border-green-200"
              >
                Reviewed
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 px-2.5 py-1 rounded-lg text-[11px] font-semibold border border-slate-200 group-hover:bg-orange-50 group-hover:text-orange-600 group-hover:border-orange-200 transition-all"
              >
                Lihat detail
              </span>
            </div>
          </div>
        </Link>
      </div>
    </section>

    <!-- Toast Notification -->
    <div
      v-if="toast.show"
      class="fixed bottom-4 right-4 z-50 animate-slide-up"
    >
      <div
        :class="[
          'px-6 py-3 rounded-xl shadow-lg text-white',
          toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'
        ]"
      >
        {{ toast.message }}
      </div>
    </div>

    <FooterSection />
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, reactive, onMounted, onUnmounted } from 'vue'
import {
  QrCodeIcon,
  BookOpenIcon,
  ClockIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline'
import FooterSection from '@/Components/FooterSection.vue'
import BookCard from '@/Components/BookCard.vue'
import BestSellerSection from '@/Components/BestSellerSection.vue'
import ReviewsSection from '@/Components/ReviewsSection.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const profilePhotoUrl = computed(() => user.value?.profile_photo_url || null)
const userAvatar = computed(() => {
  return (
    profilePhotoUrl.value ||
    `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value?.name || 'User')}&background=f97316&color=fff&size=128`
  )
})

// CONSTANTS
const MAJOR_CATEGORIES = ['Elektro', 'RPL', 'Las', 'Mesin', 'Kimia']
const ALL_EXPECTED_CATEGORIES = [
  'Fiksi',
  'Non Fiksi',
  'Referensi',
  'Cerita Anak',
  ...MAJOR_CATEGORIES
]
const ITEMS_PER_PAGE = 9

// PROPS
const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  availableBooks: {
    type: Array,
    default: () => []
  },
  recentBorrows: {
    type: Array,
    default: () => []
  },
  bestSellerBooks: {
    type: Array,
    default: () => []
  },
  bestReviews: {
    type: Array,
    default: () => []
  },
  recentReviews: {
    type: Array,
    default: () => []
  },
  trendingReviews: {
    type: Array,
    default: () => []
  }
})

// QUICK ACTIONS
const quickActions = [
  {
    label: 'Scan QR',
    description: 'Pinjam via barcode',
    icon: QrCodeIcon,
    routeName: 'scan-borrow.index'
  },
  {
    label: 'Katalog',
    description: 'Cari koleksi buku',
    icon: BookOpenIcon,
    target: 'catalog'
  },
  {
    label: 'Riwayat',
    description: 'Aktivitas baca kamu',
    icon: ClockIcon,
    href: '/my-loans'
  },
  {
    label: 'Diskusi',
    description: 'Forum ulasan siswa',
    icon: ChatBubbleLeftRightIcon,
    href: '/reviews-showcase'
  }
]

// STATE
const heroSection = ref(null)
const navbarSticky = ref(false)
const bookSearch = ref('')
const activeCategory = ref('')
const itemsPerPage = ref(ITEMS_PER_PAGE)
const imgErrors = reactive({})
const visibleCountPerGroup = reactive({})
const isSidebarOpen = ref(false)
const showMobileSearch = ref(false)
const toast = ref({ show: false, message: '', type: 'success' })
let intersectionObserver = null

// COMPUTED
const allCategories = computed(() => {
  const bookCategories = props.availableBooks
    .map((book) => book.category)
    .filter(Boolean)
    .map((cat) => cat.trim())
  const preferred = preferredMajorCategory.value
  return [...new Set([...bookCategories, ...ALL_EXPECTED_CATEGORIES])].sort(
    (a, b) => {
      if (a === preferred) return -1
      if (b === preferred) return 1
      return a.localeCompare(b)
    }
  )
})

const preferredMajorCategory = computed(() => {
  const jurusan = user.value?.jurusan || props.user?.jurusan || ''
  return (
    MAJOR_CATEGORIES.find((category) =>
      jurusan.toLowerCase().includes(category.toLowerCase())
    ) || ''
  )
})

const filteredGroups = computed(() => {
  let filteredBySearch = [...props.availableBooks]
  if (bookSearch.value) {
    const searchTerm = bookSearch.value.toLowerCase()
    filteredBySearch = filteredBySearch.filter(
      (book) =>
        book.title?.toLowerCase().includes(searchTerm) ||
        book.author?.toLowerCase().includes(searchTerm) ||
        book.category?.toLowerCase().includes(searchTerm)
    )
  }

  let filteredBooks = filteredBySearch
  if (activeCategory.value) {
    filteredBooks = filteredBySearch.filter(
      (book) => book.category === activeCategory.value
    )
  }

  if (filteredBooks.length === 0) return []

  if (bookSearch.value || activeCategory.value) {
    const groupName =
      activeCategory.value ||
      (bookSearch.value ? 'Hasil Pencarian' : 'Semua Buku')

    if (!visibleCountPerGroup[groupName]) {
      visibleCountPerGroup[groupName] = itemsPerPage.value
    }
    const limit = visibleCountPerGroup[groupName]

    return [
      {
        name: groupName,
        books: filteredBooks.slice(0, limit),
        totalBooks: filteredBooks.length
      }
    ]
  }

  const groups = {}
  const orderedCategories = [...ALL_EXPECTED_CATEGORIES].sort((a, b) => {
    const preferred = preferredMajorCategory.value
    if (a === preferred) return -1
    if (b === preferred) return 1
    return ALL_EXPECTED_CATEGORIES.indexOf(a) - ALL_EXPECTED_CATEGORIES.indexOf(b)
  })

  orderedCategories.forEach((cat) => {
    groups[cat] = []
  })

  filteredBooks.forEach((book) => {
    const category = book.category?.trim() || 'Lain-lain'
    if (!groups[category]) groups[category] = []
    groups[category].push(book)
  })

  return Object.entries(groups)
    .filter(([_, books]) => books.length > 0)
    .map(([name, books]) => {
      if (!visibleCountPerGroup[name]) {
        visibleCountPerGroup[name] = itemsPerPage.value
      }
      const limit = visibleCountPerGroup[name]
      return {
        name,
        books: books.slice(0, limit),
        totalBooks: books.length
      }
    })
})

const bestSellerBooks = computed(() => {
  return props.bestSellerBooks || []
})

const history = computed(() => {
  if (!props.recentBorrows?.length) return []

  return props.recentBorrows.map((borrow) => ({
    id: borrow.id,
    borrowId: borrow.id,
    title: borrow.book?.title || 'Judul tidak tersedia',
    author: borrow.book?.author || '-',
    publisher: borrow.book?.publisher || '-',
    status: borrow.status_text || borrow.status,
    statusColor:
      borrow.status === 'borrowed' || borrow.status === 'approved'
        ? 'text-orange-900 bg-orange-100'
        : 'text-orange-700 bg-orange-50',
    cover:
      borrow.book?.cover_url ||
      'https://placehold.co/260x360/E67E22/white?text=Book',
    dueDate: borrow.due_date
      ? new Date(borrow.due_date).toLocaleDateString('id-ID', {
          day: 'numeric',
          month: 'long',
          year: 'numeric'
        })
      : null,
    isReviewed: borrow.is_reviewed || false
  }))
})

const currentTime = ref('')

const libraryStatus = computed(() => {
  const now = new Date()
  const hours = now.getHours()
  const minutes = now.getMinutes()
  const currentTimeNum = hours + minutes / 60

  if (currentTimeNum >= 10 && currentTimeNum < 12) {
    return {
      text: 'Perpustakaan Buka',
      color: 'bg-emerald-400',
      note: 'Silahkan berkunjung'
    }
  } else if (currentTimeNum >= 12 && currentTimeNum < 14) {
    return {
      text: 'Sedang Istirahat',
      color: 'bg-amber-400',
      note: 'Buka kembali jam 14:00'
    }
  } else if (currentTimeNum >= 14 && currentTimeNum < 16) {
    return {
      text: 'Perpustakaan Buka',
      color: 'bg-emerald-400',
      note: 'Tutup jam 16:00'
    }
  } else {
    return {
      text: 'Perpustakaan Tutup',
      color: 'bg-rose-500',
      note: 'Buka Jam 10:00 - 16:00'
    }
  }
})

// METHODS
function onImageError(bookId) {
  imgErrors[bookId] = true
}

function showToast(message, type = 'success') {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

function loadMore(groupName) {
  const currentCount =
    visibleCountPerGroup[groupName] || itemsPerPage.value
  visibleCountPerGroup[groupName] = currentCount + itemsPerPage.value
}

function setActiveCategory(category) {
  activeCategory.value = category
  showMobileSearch.value = false
  Object.keys(visibleCountPerGroup).forEach((key) => {
    delete visibleCountPerGroup[key]
  })
}

function resetFilters() {
  bookSearch.value = ''
  activeCategory.value = ''
  showMobileSearch.value = false
  Object.keys(visibleCountPerGroup).forEach((key) => {
    delete visibleCountPerGroup[key]
  })
}

function triggerSearch() {
  showMobileSearch.value = false
  Object.keys(visibleCountPerGroup).forEach((key) => {
    delete visibleCountPerGroup[key]
  })
}

function handleQuickAction(action) {
  if (action.routeName) {
    router.visit(route(action.routeName))
    return
  }

  if (action.href) {
    router.visit(action.href)
    return
  }

  if (action.target === 'catalog') {
    document
      .getElementById('catalog-section')
      ?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

function openScanBorrow() {
  router.visit(route('scan-borrow.index'))
}

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
  if (isSidebarOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

function closeSidebar() {
  isSidebarOpen.value = false
  document.body.style.overflow = ''
}

function toggleMobileSearch() {
  showMobileSearch.value = !showMobileSearch.value
}

function logout() {
  router.post('/logout')
}

// Timer
const updateTime = () => {
  const now = new Date()
  currentTime.value =
    now.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: false
    }) + ' WIB'
}

let timer = null

// LIFECYCLE
onMounted(() => {
  updateTime()
  timer = setInterval(updateTime, 1000)

  intersectionObserver = new IntersectionObserver(
    ([entry]) => {
      navbarSticky.value = !entry.isIntersecting
    },
    { threshold: 0.05 }
  )
  if (heroSection.value) intersectionObserver.observe(heroSection.value)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
  if (intersectionObserver) intersectionObserver.disconnect()
  document.body.style.overflow = ''
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

@keyframes slideUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}
</style>