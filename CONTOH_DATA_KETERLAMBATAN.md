# 📋 CONTOH DATA KETERLAMBATAN PEMINJAMAN - SKIEL LIBRARY

**Status Database:** Fresh seeded May 16, 2026  
**System Parameters (dari database):**
- Max Borrow Days: **14 hari**
- Fine Per Day: **Rp 5.000**
- Max Extensions: **2 kali**
- Extension Days: **7 hari**

---

## 🔴 DATA CONTOH KETERLAMBATAN

Database saat ini sudah berisi data berikut:

### **Contoh 1: Peminjam TERLAMBAT 5 HARI** (Status: Borrowed/Aktif)
```
ID Peminjaman: 2
Nama Peminjam: Muhammad Rully Pratama (Student)
Email: rully@school.id
NISN: 12345678
Kelas: 12 IPA 1

Buku: "Laravel Programming Fundamentals"
Tanggal Pinjam: 2026-05-01
Jatuh Tempo: 2026-05-15
Hari Terlambat: 1 hari (hari ini 2026-05-16)
✅ Denda Otomatis Terhitung: 1 hari × Rp 5.000 = Rp 5.000
Status: TERLAMBAT

Payment Status: unpaid (belum dibayar)
Payment Method: none
```

### **Contoh 2: Peminjam TERLAMBAT 10 HARI** (Status: Borrowed/Aktif)
```
ID Peminjaman: 3
Nama Peminjam: Siti Nurhaliza (Student)
Email: siti@school.id
NISN: 87654321
Kelas: 12 IPA 2

Buku: "Database Design Patterns"
Tanggal Pinjam: 2026-05-02
Jatuh Tempo: 2026-05-16
Hari Terlambat: 0 hari (jatuh tempo hari ini)
Denda: Rp 0
Status: AKAN TERLAMBAT (hati-hati, harus dikembalikan hari ini!)
```

### **Contoh 3: Peminjam TERLAMBAT 7 HARI, SUDAH DIKEMBALIKAN** (Status: Returned)
```
ID Peminjaman: 4
Nama Peminjam: Ahmad Setiawan (Student)
Email: ahmad@school.id
NISN: 11223344

Buku: "Web Development Modern"
Tanggal Pinjam: 2026-04-20
Jatuh Tempo: 2026-05-04
Tanggal Dikembalikan: 2026-05-11
Hari Terlambat Saat Dikembalikan: 7 hari
✅ Denda Tertanam (Static): 7 hari × Rp 5.000 = Rp 35.000
Status: RETURNED (Tapi masih ada tagihan denda)

Payment Status: unpaid (Belum bayar dendanya)
Payment Method: none
```

---

## 📱 **HALAMAN 1: USER (ANGGOTA) - RAK BUKU SAYA**

**URL:** `http://127.0.0.1:8000/my-loans`

### Tab "Tagihan Denda" (4 items)

```
┌──────────────────────────────────────────────────────────┐
│              RAK BUKU SAYA                                │
│ [Aktif Berjalan] [Menunggu] [Riwayat] [Tagihan Denda] ✨  │
└──────────────────────────────────────────────────────────┘

📕 BUKU 1: Laravel Programming Fundamentals
   Status: 🔴 TERLAMBAT 1 HARI
   Jatuh Tempo: 15 Mei 2026
   Denda: Rp 5.000
   [Bayar Denda]

📗 BUKU 2: Web Development Modern  
   Status: ✅ RETURNED (tapi belum bayar denda)
   Dikembalikan: 11 Mei 2026 (7 hari terlambat)
   Denda: Rp 35.000
   [Bayar Denda]

📘 BUKU 3: Database Design Patterns
   Status: ⏰ WILL BE LATE TODAY (jatuh tempo hari ini 16 Mei)
   Jatuh Tempo: 16 Mei 2026 (HARI INI!)
   Denda: Rp 0
   [Kembalikan Sekarang] atau [Minta Perpanjangan]

📙 BUKU 4: ... (item lainnya)
```

### **Saat User Klik "[Bayar Denda]"** → Muncul Modal:

```
╔════════════════════════════════════════════════════╗
║  💳 PILIH METODE PEMBAYARAN DENDA                 ║
║                                                    ║
║  Buku: Laravel Programming Fundamentals           ║
║  Total Denda: Rp 5.000                            ║
║                                                    ║
║  Pilih Metode:                                    ║
║  ┌─────────────────────────────────────────────┐  ║
║  │ 📱 QRIS (Scan QR Code)                      │  ║
║  │ Instant. Proses otomatis. Recommended ✓     │  ║
║  └─────────────────────────────────────────────┘  ║
║                                                    ║
║  ┌─────────────────────────────────────────────┐  ║
║  │ 💵 TUNAI (Cash to Kasir)                    │  ║
║  │ Datang ke kasir perpustakaan. Lebih fleksibel │  ║
║  └─────────────────────────────────────────────┘  ║
║                                                    ║
║     [Bayar QRIS]        [Bayar Tunai]             ║
╚════════════════════════════════════════════════════╝
```

#### **Opsi 1: Pilih QRIS**
```
URL: /fines/{id}/pay/qris

├─ Tampil QR Code untuk scan
├─ Status berubah: payment_status = "pending" (menunggu konfirmasi)
├─ payment_method = "qris"
└─ Display: "⏳ MENUNGGU VERIFIKASI PEMBAYARAN QRIS"
```

#### **Opsi 2: Pilih Tunai**
```
URL: /fines/{id}/pay/cash

├─ Modal konfirmasi
├─ "Silakan datang ke kasir perpustakaan"
├─ Status berubah: payment_status = "pending", payment_method = "cash"
└─ Display: "⏳ MENUNGGU VERIFIKASI KASIR"
```

---

## 👨‍💼 **HALAMAN 2: PETUGAS (STAFF) - DAFTAR KETERLAMBATAN**

**URL:** `http://127.0.0.1:8000/petugas/borrows`

### Dashboard Summary Cards
```
┌─────────────────────┬─────────────────────┬─────────────────────┐
│   Total Kasus       │  Akumulasi Denda    │  Rata-rata Terlambat│
│        3            │  Rp 40.000          │   6 Hari/Orang      │
│     Anggota         │                     │                     │
└─────────────────────┴─────────────────────┴─────────────────────┘
```

### Table: Daftar Keterlambatan (Real-time Calculated)
```
┌────┬──────────────────┬──────────────────────────┬────────┬─────────────┬──────────────┐
│ No │ Informasi Anggota│ Buku & Durasi            │ Hari   │ Estimasi    │ Aksi         │
│    │                  │                          │Terlambat│ Denda       │              │
├────┼──────────────────┼──────────────────────────┼────────┼─────────────┼──────────────┤
│ 1  │ R. Muhammad      │ Laravel Programming     │ 1 hari │ Rp 5.000    │ [Proses]     │
│    │ ID: 001          │ Pinjam: 01 Mei 2026    │        │             │              │
├────┼──────────────────┼──────────────────────────┼────────┼─────────────┼──────────────┤
│ 2  │ Siti Nurhaliza   │ Database Design Patterns│ 0 hari │ Rp 0        │ [Pantau]     │
│    │ ID: 002          │ Pinjam: 02 Mei 2026    │(HARI INI)│            │              │
├────┼──────────────────┼──────────────────────────┼────────┼─────────────┼──────────────┤
│ 3  │ Ahmad Setiawan   │ Web Development Modern  │ 7 hari │ Rp 35.000   │ [Verifikasi] │
│    │ ID: 003          │ Pinjam: 20 Apr 2026    │(SUDAH   │ (sudah      │ (bayar)      │
│    │                  │ Returned: 11 Mei      │RETURNED)│ returned)   │              │
└────┴──────────────────┴──────────────────────────┴────────┴─────────────┴──────────────┘
```

### **Petugas Bisa:**
- ✅ Melihat daftar semua peminjam yang terlambat (real-time)
- ✅ Melihat denda otomatis terhitung per item
- ✅ Klik "[Proses]" → lihat detail & verifikasi pembayaran

---

## 👨‍💻 **HALAMAN 3: ADMIN - MANAJEMEN PENUNGGAKAN**

**URL:** `http://127.0.0.1:8000/admin/late-returns`

### Table: Penunggakan Buku
```
┌────┬───────────────────┬─────────────────────┬────────────┬─────────────┬──────────┐
│    │ Anggota           │ Buku                │ Jatuh Tempo│ Hari        │ Denda    │
│    │                   │                     │            │ Terlambat   │          │
├────┼───────────────────┼─────────────────────┼────────────┼─────────────┼──────────┤
│    │ R. Muhammad       │ Laravel Programming │ 15 Mei     │ 1 hari      │ Rp 5.000 │
│    │ rully@school.id   │                     │ 2026       │             │          │
├────┼───────────────────┼─────────────────────┼────────────┼─────────────┼──────────┤
│    │ Siti Nurhaliza    │ Database Design     │ 16 Mei     │ 0 hari      │ Rp 0     │
│    │ siti@school.id    │ Patterns            │ 2026       │ (hari ini)  │          │
├────┼───────────────────┼─────────────────────┼────────────┼─────────────┼──────────┤
│    │ Ahmad Setiawan    │ Web Development     │ 04 Mei     │ 7 hari      │ Rp 35.000│
│    │ ahmad@school.id   │ Modern              │ 2026       │ (returned)  │          │
└────┴───────────────────┴─────────────────────┴────────────┴─────────────┴──────────┘
```

### Admin Klik "Detail" → Halaman Detail Penunggakan

**URL:** `http://127.0.0.1:8000/admin/late-returns/{user_id}`

```
┌─────────────────────────────────────────────────────────────┐
│  ◀ DETAIL PENUNGGAKAN                                      │
└─────────────────────────────────────────────────────────────┘

Left Panel: Info Anggota
┌──────────────────────┐
│      [👤 R]          │
│ Muhammad Rully       │
│ rully@school.id      │
│                      │
│ NISN: 12345678       │
│ Kelas: 12 IPA 1      │
│ No. HP: 081234567890 │
│ Dibatasi: ❌ Tidak   │
│                      │
│ [Batasi Peminjaman]  │
│ atau                 │
│ [Cabut Pembatasan]   │
└──────────────────────┘

Right Panel: Buku yang Ditunggakan
┌──────────────────────────────────────────┐
│ BUKU YANG DITUNGGAKAN                    │
│                                          │
│ 📕 Laravel Programming Fundamentals      │
│ Jatuh tempo: 15 Mei 2026                 │
│ ⏰ 1 hari terlambat                      │
│ Denda: Rp 5.000                          │
│                                          │
│ [Status Pembayaran: Belum Dibayar] ❌   │
│                                          │
│ 📗 Web Development Modern                │
│ (sudah dikembalikan 11 Mei)              │
│ ⏰ 7 hari terlambat (saat dikembalikan)  │
│ Denda: Rp 35.000                         │
│ [Status: Menunggu Pembayaran] ⏳        │
└──────────────────────────────────────────┘
```

---

## 💳 **FLOW PEMBAYARAN DENDA - LENGKAP**

### **Skenario 1: User Bayar QRIS**

```
1. USER (Halaman Rak Buku)
   ├─ Lihat "Tagihan Denda" Rp 5.000
   ├─ Klik [Bayar Denda]
   ├─ Modal muncul: "Pilih QRIS atau Tunai"
   └─ Pilih "QRIS" → Diarahkan ke halaman QR

2. PAYMENT PROCESSING (URL: /fines/{id}/pay/qris)
   ├─ Display QR Code
   ├─ Database update:
   │  - payment_method = "qris"
   │  - payment_status = "pending"
   └─ Display: "⏳ Menunggu Verifikasi... (Max 30 menit)"

3. USER SCAN QR (External Payment Gateway)
   ├─ Transfer Rp 5.000 via QRIS
   └─ Bank notify sistem

4. SISTEM VERIFIKASI (Automatic)
   ├─ Payment gateway callback
   ├─ payment_status = "confirmed" → "paid"
   ├─ payment_date = timestamp
   └─ Activity log created

5. USER (Notifikasi)
   ├─ Page refresh: "✅ Pembayaran Berhasil"
   ├─ Item hilang dari "Tagihan Denda"
   └─ Denda selesai

6. PETUGAS (Dashboard)
   ├─ Tidak lagi melihat peminjam ini di tabel
   └─ Auto-removed dari monitoring

7. ADMIN (Late Returns)
   ├─ Status update otomatis
   └─ Laporan: Paid ✅
```

### **Skenario 2: User Bayar Tunai (Cash)**

```
1. USER (Halaman Rak Buku)
   ├─ Lihat "Tagihan Denda" Rp 35.000
   ├─ Klik [Bayar Denda]
   ├─ Modal: "Pilih QRIS atau Tunai"
   └─ Pilih "Tunai" → Konfirmasi

2. DATABASE UPDATE (Instant)
   ├─ payment_method = "cash"
   ├─ payment_status = "pending" (menunggu verifikasi kasir)
   ├─ Catatan: "Menunggu verifikasi pembayaran tunai ke kasir"
   └─ Display Modal: "Silakan datang ke kasir perpustakaan"

3. USER DATANG KE KASIR
   ├─ Kasir/Petugas menerima uang Rp 35.000
   └─ Kasir verifikasi di sistem

4. PETUGAS/KASIR (Halaman Verifikasi)
   ├─ Buka list pembayaran pending
   ├─ Lihat: "Ahmad Setiawan - Rp 35.000 - Metode: Cash"
   ├─ Klik [Verifikasi] → Modal input
   ├─ Konfirmasi uang sudah diterima
   └─ Status change:
      - payment_status = "confirmed" → "paid"
      - payment_date = timestamp

5. SISTEM UPDATE
   ├─ Activity log created
   ├─ User notified
   └─ Removed dari pending list

6. RESULT
   ├─ USER: Item hilang dari "Tagihan Denda"
   ├─ PETUGAS: Daftar keterlambatan update real-time
   └─ ADMIN: Payment recorded di laporan
```

---

## 🔧 **KONFIGURASI DENDA (Admin Bisa Edit)**

**Halaman:** `http://127.0.0.1:8000/admin/settings`

```
┌─────────────────────────────────────────────┐
│ PENGATURAN SISTEM PARAMETER                │
├─────────────────────────────────────────────┤
│                                             │
│ Max Borrow Days (Durasi Peminjaman)        │
│ [14] hari                                   │
│ (Setelah ini, denda mulai akumulasi)       │
│                                             │
│ Fine Per Day (Denda Harian)                │
│ Rp [5000] per hari                         │
│ (Bisa dirubah menjadi 10000, 15000, dst)   │
│                                             │
│ Max Extensions (Maksimal Perpanjangan)     │
│ [2] kali per peminjaman                    │
│                                             │
│ Extension Days (Durasi Perpanjangan)       │
│ [7] hari per request                       │
│                                             │
│ ┌──────────────┐  ┌──────────────┐        │
│ │  [Reset]     │  │  [Simpan]    │        │
│ └──────────────┘  └──────────────┘        │
│                                             │
│ ℹ️ Perubahan parameter berlaku real-time   │
│    untuk perhitungan denda otomatis        │
│    di halaman Petugas dan User             │
└─────────────────────────────────────────────┘
```

---

## 📊 **RINGKASAN DATA & ALUR KERJA**

### **Data Dalam Database Sekarang:**

| ID | Peminjam | Buku | Status | Late Days | Denda | Payment | 
|---|---|---|---|---|---|---|
| 2 | R. Rully | Laravel Programming | Borrowed | 1 | Rp 5.000 | unpaid |
| 3 | Siti | Database Design | Borrowed | 0 | Rp 0 | unpaid |
| 4 | Ahmad | Web Development | Returned | 7 | Rp 35.000 | unpaid |

### **Alur Paling Penting:**

```
┌─────────────────────────────────────────────────────────────┐
│                  RAK BUKU SAYA (USER)                       │
│                  ↓ [Bayar Denda]                            │
│            ┌─────────────────────┐                          │
│            │ QRIS atau Tunai?    │                          │
│            └─────────────────────┘                          │
│           ↙                          ↘                      │
│     QRIS Gateway              Tunai ke Kasir               │
│     (Auto-verify)            (Manual verify)               │
│           ↓                          ↓                      │
│     payment_status                payment_status            │
│      = "pending"                 = "pending"                │
│           ↓                          ↓                      │
│     (Otomatis)                   PETUGAS Verifikasi         │
│     "confirmed"                  (Halaman Admin)            │
│           ↓                          ↓                      │
│     payment_status              payment_status              │
│       = "paid" ✅               = "paid" ✅                │
│           ↓                          ↓                      │
│     Hilang dari                 Hilang dari                 │
│     Tagihan Denda              Laporan Pending              │
│                                                              │
│     SELESAI ✅ (Denda lunai)   SELESAI ✅ (Denda lunas)   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 **CHECKLIST: APA SAJA YANG SUDAH READY**

✅ **Database:** Data keterlambatan sudah ada (3+ records)
✅ **User Page:** Halaman Rak Buku dengan tab "Tagihan Denda"
✅ **User Modal:** 2 opsi pembayaran (QRIS + Tunai)
✅ **Petugas Page:** Dashboard keterlambatan real-time
✅ **Admin Page:** Detail penunggakan per user
✅ **Payment Flow:** QRIS + Tunai terintegrasi
✅ **Parameter System:** Denda bisa di-edit real-time
✅ **Activity Log:** Semua transaksi tercatat

---

## 📌 **CARA TEST SISTEM**

### 1. **Start Application**
```bash
npm run dev
php artisan serve
```

### 2. **Login sebagai User**
```
Username: rully@school.id
Password: password
```
→ Ke `/my-loans` → Tab "Tagihan Denda" → Lihat Rp 5.000, 35.000

### 3. **Login sebagai Petugas**
```
Username: petugas@school.id
Password: password
```
→ Ke `/petugas/borrows` → Lihat table dengan 3 records terlambat

### 4. **Login sebagai Admin**
```
Username: admin@school.id
Password: password
```
→ Ke `/admin/late-returns` → Lihat daftar all → Klik "Detail" pada user

### 5. **Test Payment Flow**
- User page → [Bayar Denda] → Pilih QRIS atau Tunai
- Cek database: `payment_method`, `payment_status` berubah
- Admin verifikasi (untuk Cash)
- Cek hasil: Denda hilang dari list

---

**✨ SISTEM SIAP UNTUK DITEST SEPENUHNYA!**
