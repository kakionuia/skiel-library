<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-800">
    <!-- ========== HERO SECTION ========== -->
    <section
      ref="heroSection"
      class="relative min-h-[600px] flex items-center overflow-hidden bg-gradient-to-br from-orange-700 to-orange-500 text-white"
    >
      <div class="absolute inset-0 z-0">
        <div class="absolute -left-20 -top-20 h-[500px] w-[500px] rounded-full bg-white/10 blur-[120px]" />
        <div class="absolute right-0 bottom-0 h-[400px] w-[400px] rounded-full bg-orange-400/20 blur-[100px]" />
        <div 
          class="absolute inset-0 opacity-[0.05]" 
          style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;" 
        />
      </div>

      <div class="relative z-10 mx-auto max-w-7xl px-6 py-20 w-full">
        <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
          <div class="space-y-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-white text-xs font-bold tracking-wider uppercase backdrop-blur-md">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75" />
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white" />
              </span>
              SKIE-Library System
            </div>

            <h1 class="text-5xl font-extrabold tracking-tight lg:text-6xl leading-[1.1]">
              Selamat Pagi, <br />
              <span class="text-amber-200">{{ user.name }}</span>
            </h1>

            <p class="max-w-md text-lg text-orange-50 leading-relaxed">
              Tingkatkan literasi digital dan temukan referensi terbaik untuk mendukung perjalanan akademikmu.
            </p>

            <div class="relative max-w-md group">
              <input
                v-model="bookSearch"
                type="text"
                placeholder="Cari judul buku, penulis, atau kategori..."
                class="w-full bg-white/10 border border-white/20 rounded-2xl py-4 pl-12 pr-4 text-sm text-white placeholder:text-orange-100 focus:bg-white/20 focus:border-white/40 focus:ring-0 transition-all outline-none backdrop-blur-md shadow-inner"
              />
              <svg 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-orange-200 group-focus-within:text-white transition-colors" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <button
              v-for="action in quickActions"
              :key="action.label"
              class="group relative flex flex-col items-start gap-4 rounded-3xl bg-white p-6 text-left shadow-xl shadow-orange-900/10 transition-all duration-500 hover:-translate-y-2 overflow-hidden"
            >
              <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                <component :is="action.icon" class="h-6 w-6" />
              </div>
              <div>
                <h3 class="text-sm font-bold text-slate-900">{{ action.label }}</h3>
                <p class="text-[10px] text-slate-500 group-hover:text-slate-400 transition-colors leading-tight">
                  {{ action.description }}
                </p>
              </div>
            </button>
          </div>
        </div>
      </div>

      <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
        <svg class="relative block h-12 w-full" viewBox="0 0 1200 120" preserveAspectRatio="none">
          <path d="M1200 120L0 120L0 0C161.333 46.6667 361.333 70 600 70C838.667 70 1038.67 46.6667 1200 0V120Z" fill="#f8fafc" />
        </svg>
      </div>
    </section>

    <!-- ========== STICKY NAVBAR ========== -->
    <nav
      ref="navbarRef"
      class="transition-all duration-300 w-full z-50 bg-slate-50"
      :class="{ 'sticky top-0 shadow-md': navbarSticky }"
    >
      <div class="mx-auto max-w-7xl px-4 md:px-8 py-4 flex justify-between items-center gap-8 md:gap-5">
        <!-- Hamburger Button Mobile -->
        <button @click="toggleSidebar" class="md:hidden text-orange-500 p-1 transition-transform active:scale-90">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <!-- Logo Desktop -->
        <div class="hidden md:block text-orange-500 font-bold text-xl">SKIE-Library</div>

        <!-- Scan & Search - Hidden on mobile -->
        <div class="hidden md:flex gap-2 w-full">
          <button class="flex-shrink-0 bg-white border border-orange-500 text-orange-500 p-2.5 rounded-2xl shadow-lg shadow-black/5 hover:bg-orange-50 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M4 5q-.825 0-1.412.587Q2 6.175 2 7v10q0 .825.588 1.413Q3.175 19 4 19h16q.825 0 1.413-.587Q22 17.825 22 17V7q0-.825-.587-1.413Q20.825 5 20 5h-3.15L15.45 3H8.55L7.15 5ZM12 17q-2.075 0-3.537-1.463Q7 14.075 7 12q0-2.075 1.463-3.537Q9.925 7 12 7q2.075 0 3.538 1.463Q17 9.925 17 12q0 2.075-1.462 3.537Q14.075 17 12 17Zm0-2q1.25 0 2.125-.875T15 12q0-1.25-.875-2.125T12 9q-1.25 0-2.125.875T9 12q0 1.25.875 2.125T12 15Z" />
            </svg>
          </button>

          <div class="flex-1 flex gap-2">
            <div class="relative flex-1">
              <input
                v-model="bookSearch"
                type="text"
                placeholder="Cari buku, penulis, dan tahun terbit"
                class="w-full border border-orange-500 rounded-2xl py-3 px-6 shadow-lg shadow-black/5 focus:ring-0 outline-none text-slate-400 placeholder:text-slate-300 transition"
              />
            </div>
            <button @click="triggerSearch" class="bg-orange-500 text-white p-3 rounded-2xl shadow-lg shadow-black/5 hover:bg-orange-700 transition-all active:scale-95">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Search Button -->
        <button @click="toggleMobileSearch" class="md:hidden text-orange-500 p-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </button>

        <Link href="/profile" class="flex-shrink-0">
          <img :src="userAvatar" class="h-10 w-10 md:h-12 md:w-12 rounded-full border-2 border-slate-100 object-cover hover:scale-105 transition shadow-sm" alt="Profile" />
        </Link>
      </div>

      <!-- Mobile Search Bar (Dropdown) -->
      <div v-if="showMobileSearch" class="md:hidden px-4 pb-4">
        <div class="flex gap-2">
          <div class="relative flex-1">
            <input
              v-model="bookSearch"
              type="text"
              placeholder="Cari buku, penulis, atau kategori..."
              class="w-full border border-orange-500 rounded-2xl py-3 px-4 focus:ring-0 outline-none text-slate-800 placeholder:text-slate-400"
              autofocus
            />
          </div>
          <button @click="triggerSearch" class="bg-orange-500 text-white px-4 rounded-2xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Category Bar - Responsive -->
      <div class="bg-orange-500 shadow-md">
        <div class="mx-auto max-w-7xl px-4 py-3">
          <div class="flex items-center gap-6 overflow-x-auto whitespace-nowrap text-white font-bold text-sm no-scrollbar">
            <button @click="setActiveCategory('')" class="transition-opacity px-2 py-1 rounded-lg hover:bg-white/10 flex-shrink-0" :class="activeCategory === '' ? 'opacity-100 bg-white/20' : 'opacity-80 hover:opacity-100'">
              Semua Buku
            </button>
            <button
              v-for="category in allCategories"
              :key="category"
              @click="setActiveCategory(category)"
              class="transition-opacity px-2 py-1 rounded-lg hover:bg-white/10 flex-shrink-0"
              :class="activeCategory === category ? 'opacity-100 bg-white/20' : 'opacity-80 hover:opacity-100'"
            >
              {{ category }}
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- ========== SIDEBAR (Mobile) ========== -->
    <div 
      v-if="isSidebarOpen" 
      class="fixed inset-0 z-50 md:hidden"
      @click="closeSidebar"
    >
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/50 transition-opacity"></div>
      
      <!-- Sidebar Panel -->
      <div 
        class="absolute left-0 top-0 h-full w-80 bg-white shadow-2xl transition-transform transform translate-x-0"
        @click.stop
      >
        <!-- Sidebar Header -->
        <div class="bg-gradient-to-br from-orange-600 to-orange-500 p-6 text-white">
          <div class="flex items-center gap-4">
            <img :src="userAvatar" class="h-16 w-16 rounded-full border-2 border-white object-cover" />
            <div>
              <h3 class="font-bold text-lg">{{ user.name }}</h3>
              <p class="text-xs text-orange-100">{{ user.email || 'student@skie.sch.id' }}</p>
            </div>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <div class="p-4 space-y-2">
          <Link href="/dashboard" class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="font-medium">Beranda</span>
          </Link>

          <Link href="/my-loans" class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="font-medium">Peminjaman Saya</span>
          </Link>

          <Link href="/loans" class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">Riwayat Peminjaman</span>
          </Link>

          <Link href="/services" class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="font-medium">Layanan</span>
          </Link>

          <Link href="/help" class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">Bantuan</span>
          </Link>
        </div>

<Link href="/my-loans" class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-all group">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
  </svg>
  <span class="font-medium">Rak Buku Saya</span>
</Link>

        <!-- Sidebar Footer -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-200">
          <button @click="logout" class="flex items-center gap-4 px-4 py-3 w-full rounded-xl text-red-600 hover:bg-red-50 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-medium">Keluar</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="mx-auto max-w-7xl px-4 py-8 md:py-12">
      <section class="space-y-16">
        <!-- Empty States -->
        <div v-if="availableBooks.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">📚</div>
          <h3 class="text-lg font-bold text-slate-900">Belum ada buku tersedia</h3>
          <p class="text-slate-500 text-sm mt-1">Silahkan tambahkan buku di halaman admin.</p>
        </div>

        <div v-else-if="filteredGroups.length === 0" class="py-32 text-center">
          <div class="text-6xl mb-6">🏜️</div>
          <h3 class="text-lg font-bold text-slate-900">Wah, bukunya nggak ketemu...</h3>
          <p class="text-slate-500 text-sm mt-1">Coba cari dengan kata kunci lain.</p>
          <button @click="resetFilters" class="mt-6 text-orange-600 font-bold text-sm underline hover:text-orange-800 transition-colors">
            Reset Filter
          </button>
        </div>

        <!-- Daftar Grup Kategori -->
        <div v-for="group in filteredGroups" :key="group.name" class="space-y-6">
          <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-slate-900">{{ group.name }}</h2>
            <span class="text-sm text-slate-500">{{ group.totalBooks }} buku</span>
          </div>

          <!-- Grid Buku -->
          <div class="grid gap-4 justify-center" style="grid-template-columns: repeat(auto-fill, minmax(140px, 160px))">
            <BookCard
              v-for="book in group.books"
              :key="book.id"
              :book="book"
              :img-errors="imgErrors"
              @image-error="onImageError"
            />
          </div>

          <!-- TOMBOL MORE -->
          <div v-if="group.totalBooks > itemsPerPage && group.books.length < group.totalBooks" class="text-center">
            <button
              @click="loadMore(group.name)"
              class="mt-4 px-8 py-3 bg-white border border-orange-300 text-orange-600 font-bold rounded-full hover:bg-orange-50 shadow-md transition-all active:scale-95"
            >
              Tampilkan Lebih Banyak ({{ group.books.length }} / {{ group.totalBooks }})
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- ========== BEST SELLER SECTION ========== -->
    <BestSellerSection :books="bestSellerBooks" />

    <!-- ========== REVIEWS SECTION ========== -->
    <ReviewsSection :best-reviews="bestReviews" :recent-reviews="recentReviews" />

    <!-- ========== RIWAYAT SECTION ========== -->
    <!-- ========== RIWAYAT SECTION ========== -->
<section class="p-4 md:p-8 bg-white">
  <div class="flex justify-between items-center mb-8">
    <div class="flex items-center gap-3">
      <div class="p-2.5 rounded-xl bg-orange-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h2 class="text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Riwayat Pinjam Buku</h2>
    </div>
    <Link href="/my-loans" class="bg-[#e67e00] hover:bg-orange-700 text-white px-4 md:px-8 py-2 md:py-3 rounded-xl font-semibold text-sm md:text-base transition-all active:scale-95 inline-block">
      Peminjaman Saya
    </Link>
  </div>

  <!-- Empty State jika tidak ada data -->
  <div v-if="history.length === 0" class="text-center py-16">
    <div class="text-5xl mb-4">📋</div>
    <h3 class="text-lg font-bold text-gray-900">Belum ada riwayat peminjaman</h3>
    <p class="text-gray-500 text-sm mt-1">Mulai pinjam buku dan riwayat akan muncul di sini.</p>
    <Link href="/dashboard" class="mt-4 inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-all">
      Jelajahi Buku
    </Link>
  </div>

  <!-- Card Riwayat -->
  <div v-else class="flex gap-4 md:gap-6 overflow-x-auto pb-6 scrollbar-hide snap-x snap-mandatory">
    <Link
      v-for="book in history"
      :key="book.id"
      :href="book.borrowId ? `/borrow/${book.borrowId}` : '/my-loans'"
      class="min-w-[320px] md:min-w-[450px] border border-gray-200 rounded-2xl md:rounded-[2rem] p-4 md:p-5 flex gap-4 md:gap-5 snap-start hover:border-orange-300 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 bg-white group"
    >
      <img :src="book.cover" :alt="book.title" class="w-24 h-32 md:w-32 md:h-44 object-cover rounded-xl shadow-sm ring-1 ring-gray-100" />
      <div class="flex-1 flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-start mb-3">
            <h3 class="text-base md:text-xl font-bold text-gray-900 leading-tight">{{ book.title }}</h3>
            <span :class="['font-semibold text-xs uppercase tracking-wider px-2 py-1 rounded-full', book.statusColor]">
              {{ book.status }}
            </span>
          </div>
          <div class="text-xs md:text-sm space-y-1 text-gray-600 font-medium">
            <p class="flex items-center gap-2">
              <span class="text-gray-400 w-12 md:w-16">Penulis</span>
              <span class="text-gray-800">{{ book.author }}</span>
            </p>
            <p class="flex items-center gap-2">
              <span class="text-gray-400 w-12 md:w-16">Penerbit</span>
              <span class="text-gray-800">{{ book.publisher }}</span>
            </p>
            <p v-if="book.dueDate" class="flex items-center gap-2">
              <span class="text-gray-400 w-12 md:w-16">Tenggat</span>
              <span class="font-semibold text-red-600 text-xs md:text-sm">{{ book.dueDate }}</span>
            </p>
          </div>
        </div>
        <div class="mt-3 md:mt-4">
          <span v-if="book.isReviewed" class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 px-3 md:px-4 py-1 rounded-lg text-xs md:text-sm font-semibold border border-orange-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Reviewed
          </span>
          <span v-else class="inline-flex items-center gap-2 bg-white text-orange-700 px-3 md:px-4 py-1 rounded-lg text-xs md:text-sm font-semibold border border-orange-200 group-hover:bg-orange-50">
            Lihat detail
          </span>
        </div>
      </div>
    </Link>
  </div>
</section>
    <!-- Toast Notification -->
    <div v-if="toast.show" class="fixed bottom-4 right-4 z-50 animate-slide-up">
      <div :class="['px-6 py-3 rounded-xl shadow-lg text-white', toast.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
        {{ toast.message }}
      </div>
    </div>

    <FooterSection />
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import {
  QrCodeIcon,
  BookOpenIcon,
  ClockIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';
import FooterSection from '@/Components/FooterSection.vue';
import BookCard from '@/Components/BookCard.vue';
import BestSellerSection from '@/Components/BestSellerSection.vue';
import ReviewsSection from '@/Components/ReviewsSection.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const profilePhotoUrl = computed(() => user.value?.profile_photo_url || null);
const userAvatar = computed(() => {
  return profilePhotoUrl.value || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value?.name || 'User')}&background=f97316&color=fff&size=128`;
});

// CONSTANTS
const ALL_EXPECTED_CATEGORIES = ['Fiksi', 'Non Fiksi', 'Referensi', 'Cerita Anak'];
const ITEMS_PER_PAGE = 9;

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
  }
});

// QUICK ACTIONS
const quickActions = [
  { label: 'Scan QR', description: 'Pinjam via barcode', icon: QrCodeIcon },
  { label: 'Katalog', description: 'Cari koleksi buku', icon: BookOpenIcon },
  { label: 'Riwayat', description: 'Aktivitas baca kamu', icon: ClockIcon },
  { label: 'Diskusi', description: 'Forum ulasan siswa', icon: ChatBubbleLeftRightIcon }
];

// BEST SELLER DATA
const bestSellerBooks = computed(() => {
  if (props.bestSellerBooks && props.bestSellerBooks.length > 0) {
    return props.bestSellerBooks;
  }
  
  return [
    { 
      title: 'One Piece', 
      cover: 'https://images.unsplash.com/photo-1589998059171-988d887df646?q=80&w=2076&auto=format&fit=crop',
      author: 'Eiichiro Oda',
      rating: 4.9,
      totalBorrowed: 1523
    },
    { 
      title: 'Ronggeng Dukuh Paruk', 
      cover: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=1974&auto=format&fit=crop',
      author: 'Ahmad Tohari',
      rating: 4.7,
      totalBorrowed: 1256
    },
    { 
      title: 'Witch Fantasy', 
      cover: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=1996&auto=format&fit=crop',
      author: 'Sarah J. Maas',
      rating: 4.5,
      totalBorrowed: 1102
    },
    { 
      title: 'Hujan', 
      cover: 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?q=80&w=2029&auto=format&fit=crop',
      author: 'Tere Liye',
      rating: 4.8,
      totalBorrowed: 987
    },
    { 
      title: 'Laskar Pelangi', 
      cover: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2028&auto=format&fit=crop',
      author: 'Andrea Hirata',
      rating: 4.9,
      totalBorrowed: 1456
    },
    { 
      title: 'Bumi Manusia', 
      cover: 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=2073&auto=format&fit=crop',
      author: 'Pramoedya Ananta Toer',
      rating: 4.8,
      totalBorrowed: 1345
    },
    { 
      title: 'Harry Potter', 
      cover: 'https://images.unsplash.com/photo-1601645192563-3b20dd4cef46?q=80&w=2070&auto=format&fit=crop',
      author: 'J.K. Rowling',
      rating: 4.9,
      totalBorrowed: 1678
    },
    { 
      title: 'Sang Pemimpi', 
      cover: 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?q=80&w=2070&auto=format&fit=crop',
      author: 'Andrea Hirata',
      rating: 4.7,
      totalBorrowed: 1123
    },
    { 
      title: 'Filosofi Teras', 
      cover: 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?q=80&w=2070&auto=format&fit=crop',
      author: 'Henry Manampiring',
      rating: 4.6,
      totalBorrowed: 1034
    },
    { 
      title: 'Atomic Habits', 
      cover: 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=2072&auto=format&fit=crop',
      author: 'James Clear',
      rating: 4.8,
      totalBorrowed: 1890
    },
  ];
});

// STATE
const heroSection = ref(null);
const navbarSticky = ref(false);
const bookSearch = ref('');
const activeCategory = ref('');
const itemsPerPage = ref(ITEMS_PER_PAGE);
const imgErrors = reactive({});
const visibleCountPerGroup = reactive({});
const isSidebarOpen = ref(false);
const showMobileSearch = ref(false);
const toast = ref({ show: false, message: '', type: 'success' });
let intersectionObserver;

// COMPUTED
const allCategories = computed(() => {
  const bookCategories = props.availableBooks
    .map(book => book.category)
    .filter(Boolean)
    .map(cat => cat.trim());
  return [...new Set([...bookCategories, ...ALL_EXPECTED_CATEGORIES])].sort();
});

const filteredGroups = computed(() => {
  let filteredBySearch = [...props.availableBooks];
  if (bookSearch.value) {
    const searchTerm = bookSearch.value.toLowerCase();
    filteredBySearch = filteredBySearch.filter(book =>
      book.title?.toLowerCase().includes(searchTerm) ||
      book.author?.toLowerCase().includes(searchTerm) ||
      book.category?.toLowerCase().includes(searchTerm)
    );
  }

  let filteredBooks = filteredBySearch;
  if (activeCategory.value) {
    filteredBooks = filteredBySearch.filter(book => book.category === activeCategory.value);
  }

  if (filteredBooks.length === 0) return [];

  if (bookSearch.value || activeCategory.value) {
    const groupName = activeCategory.value || (bookSearch.value ? 'Hasil Pencarian' : 'Semua Buku');
    
    if (!visibleCountPerGroup[groupName]) {
      visibleCountPerGroup[groupName] = itemsPerPage.value;
    }
    const limit = visibleCountPerGroup[groupName];
    
    return [{
      name: groupName,
      books: filteredBooks.slice(0, limit),
      totalBooks: filteredBooks.length
    }];
  }

  const groups = {};
  ALL_EXPECTED_CATEGORIES.forEach(cat => { groups[cat] = []; });

  filteredBooks.forEach(book => {
    const category = book.category?.trim() || 'Lain-lain';
    if (!groups[category]) groups[category] = [];
    groups[category].push(book);
  });

  return Object.entries(groups)
    .filter(([_, books]) => books.length > 0)
    .map(([name, books]) => {
      if (!visibleCountPerGroup[name]) {
        visibleCountPerGroup[name] = itemsPerPage.value;
      }
      const limit = visibleCountPerGroup[name];
      return {
        name,
        books: books.slice(0, limit),
        totalBooks: books.length
      };
    });
});

// METHODS
function onImageError(bookId) {
  imgErrors[bookId] = true;
}

function showToast(message, type = 'success') {
  toast.value = { show: true, message, type };
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
}

function loadMore(groupName) {
  const currentCount = visibleCountPerGroup[groupName] || itemsPerPage.value;
  visibleCountPerGroup[groupName] = currentCount + itemsPerPage.value;
}

function setActiveCategory(category) {
  activeCategory.value = category;
  showMobileSearch.value = false;
  Object.keys(visibleCountPerGroup).forEach(key => {
    delete visibleCountPerGroup[key];
  });
}

function resetFilters() {
  bookSearch.value = '';
  activeCategory.value = '';
  showMobileSearch.value = false;
  Object.keys(visibleCountPerGroup).forEach(key => {
    delete visibleCountPerGroup[key];
  });
}

function triggerSearch() {
  showMobileSearch.value = false;
  Object.keys(visibleCountPerGroup).forEach(key => {
    delete visibleCountPerGroup[key];
  });
}

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value;
  if (isSidebarOpen.value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
}

function closeSidebar() {
  isSidebarOpen.value = false;
  document.body.style.overflow = '';
}

function toggleMobileSearch() {
  showMobileSearch.value = !showMobileSearch.value;
}

function logout() {
  router.post('/logout');
}

// LIFECYCLE
onMounted(() => {
  intersectionObserver = new IntersectionObserver(
    ([entry]) => { navbarSticky.value = !entry.isIntersecting; },
    { threshold: 0.05 }
  );
  if (heroSection.value) intersectionObserver.observe(heroSection.value);
});

onBeforeUnmount(() => {
  if (intersectionObserver) intersectionObserver.disconnect();
  document.body.style.overflow = '';
});

const history = computed(() => {
  // Jika tidak ada data dari database, kembalikan array kosong
  if (!props.recentBorrows?.length) return [];

  // Mapping data asli dari database
  return props.recentBorrows.map((borrow) => ({
    id: borrow.id,
    borrowId: borrow.id,
    title: borrow.book?.title || 'Judul tidak tersedia',
    author: borrow.book?.author || '-',
    publisher: borrow.book?.publisher || '-',
    status: borrow.status_text || borrow.status,
    statusColor: borrow.status === 'borrowed' || borrow.status === 'approved'
      ? 'text-orange-900 bg-orange-100'
      : 'text-orange-700 bg-orange-50',
    cover: borrow.book?.cover_url || 'https://placehold.co/260x360/E67E22/white?text=Book',
    dueDate: borrow.due_date ? new Date(borrow.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : null,
    isReviewed: borrow.is_reviewed || false
  }));
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

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
