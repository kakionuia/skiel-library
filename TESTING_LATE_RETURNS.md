# 📋 Testing Guide: Sistem Keterlambatan & Pembayaran Denda

**Created:** May 15, 2026  
**Database State:** Fresh seeded dengan 3-5 data contoh keterlambatan

---

## 🎯 Overview

Sistem ini menyediakan 3 tampilan berbeda untuk mengelola peminjaman yang terlambat:
1. **User View (MyLoans)** - Lihat & bayar denda sendiri
2. **Petugas View** - Monitor semua keterlambatan
3. **Admin View** - Kelola keterlambatan & pembatasan

---

## 1️⃣ USER VIEW - Rak Buku / MyLoans

### 📍 Akses: `http://127.0.0.1:8000/my-loans`

**Login sebagai:** Anggota (member)
- Email: `member@example.com` (atau user dengan role `anggota`)
- Password: `password`

### Apa yang dilihat:

1. **Tab "Aktif Berjalan"** - Peminjaman yang masih berlangsung
   - Tampil daftar buku yang sedang dipinjam
   - Jika ada yang TERLAMBAT: status akan highlight merah dengan hari keterlambatan

2. **Tab "Tagihan Denda"** (BARU) - **HANYA menampilkan jika ada denda**
   - Hanya peminjaman dengan `late_fee > 0` yang tampil
   - Kolom: Buku, Tgl Pinjam, Jatuh Tempo, Hari Terlambat, **DENDA**
   - Tombol: **"BAYAR DENDA"** (warna oranye/merah)

### 💳 Proses Pembayaran (Dari MyLoans):

**User klik tombol "BAYAR DENDA"** →

```
Modal "Proses Invoice?"
┌─────────────────────────────────┐
│ Melanjutkan pembayaran denda     │
│ nominal: Rp 75.000              │
│                                 │
│ [Proceed Gateway]  [Hold]       │
└─────────────────────────────────┘
```

Jika klik "Proceed Gateway" → Redirect ke halaman bayar denda

---

## 2️⃣ PAYMENT PAGE - Pilih Metode Pembayaran

### 📍 Akses: `http://127.0.0.1:8000/borrow/{id}/fine`

**Halaman untuk USER memilih metode pembayaran:**

```
┌─────────────────────────────────────────────┐
│ PILIH METODE PEMBAYARAN                     │
│                                             │
│ Denda: Rp 75.000                           │
│ Hari Terlambat: 15 hari                    │
│ Tarif/Hari: Rp 5.000 (dari System Param)   │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │📱 QRIS Payment                          │ │
│ │ Scan dengan e-wallet (GCash, OVO, dll)  │ │
│ │ ──────────────────────────────────────  │ │
│ │ Status: Pending bayar                   │ │
│ │              [LANJUTKAN QRIS]           │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │💵 UANG TUNAI                            │ │
│ │ Bayar langsung ke petugas perpustakaan │ │
│ │ ──────────────────────────────────────  │ │
│ │ Status: Menunggu konfirmasi petugas     │ │
│ │              [PILIH TUNAI]              │ │
│ └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

### 🔄 Dua Flow Pembayaran:

#### **A. QRIS Payment Flow:**
1. User klik **"LANJUTKAN QRIS"**
2. Redirect ke `/borrow/{id}/fine/qris`
3. Tampil **QR Code** untuk scan
4. User scan dengan e-wallet
5. User klik **"VERIFIKASI PEMBAYARAN"**
6. Status → `payment_status: confirmed`
7. ✅ Denda berstatus "LUNAS"

#### **B. TUNAI Payment Flow:**
1. User klik **"PILIH TUNAI"**
2. Message: "Silakan bayar ke kasir perpustakaan"
3. `payment_status: pending` (menunggu konfirmasi petugas)
4. **Petugas** yang nantinya confirm di halaman Admin
5. ✅ Setelah petugas confirm → Denda "LUNAS"

---

## 3️⃣ PETUGAS VIEW - Daftar Keterlambatan

### 📍 Akses: `http://127.0.0.1:8000/petugas/late-returns`

**Login sebagai:** Petugas
- Email: `petugas@example.com` (atau user dengan role `petugas`)
- Password: `password`

### Dashboard Stats:

```
┌──────────────────┬──────────────────┬──────────────────┐
│ TOTAL KASUS      │ AKUMULASI DENDA  │ RATA-RATA TERLAT │
│     3            │ Rp 150.000       │    5 hari        │
└──────────────────┴──────────────────┴──────────────────┘
```

### Tabel Keterlambatan:

| Anggota | Buku | Pinjam | Batas Waktu | Hari Terlambat | Estimasi Denda | Aksi |
|---------|------|--------|-------------|---|---|---|
| Ahmad | Data Structures | 2026-04-30 | 2026-05-10 | **5** | Rp 25.000 | Proses |
| Siti | Algoritma | 2026-05-01 | 2026-05-08 | **7** | Rp 35.000 | Proses |
| Budi | Database SQL | 2026-04-28 | 2026-05-05 | **10** | Rp 50.000 | Proses |

### 📊 Fitur:

✅ Lihat daftar semua peminjaman terlambat  
✅ Otomatis hitung **hari terlambat** dari `due_date`  
✅ Otomatis hitung **denda** = `hari_terlambat × fine_per_day` (dari SystemParameter)  
✅ Denda **UPDATE REAL-TIME** jika parameter berubah  
✅ Tombol "Proses" untuk menangani (future enhancement)

---

## 4️⃣ ADMIN VIEW - Manajemen Keterlambatan

### 📍 Akses: `http://127.0.0.1:8000/admin/late-returns`

**Login sebagai:** Admin
- Email: `admin@example.com` (atau user dengan role `admin`)
- Password: `password`

### Tampilan & Fitur:

1. **Tabel Serupa dengan Petugas:**
   - Daftar semua keterlambatan
   - Nama anggota, buku, jatuh tempo, hari terlambat, denda

2. **Kolom Tambahan (Admin Only):**
   - Link "Detail →" untuk setiap borrow

3. **Halaman Detail:**
   - Akses: `/admin/late-returns/{user_id}`
   - Lihat profil lengkap user
   - **Tombol: "Batasi Peminjaman"** / "Cabut Pembatasan"
   - List semua buku yang ditunggakan user tersebut
   - Opsi untuk restrict borrowing jika tetap tidak bayar

---

## 5️⃣ ADMIN VIEW - Pembayaran Tunai

### 📍 Akses: `http://127.0.0.1:8000/admin/payments/pending`

**Khusus untuk pembayaran method TUNAI yang menunggu konfirmasi**

### Dashboard:

```
Daftar Pembayaran Tunai Menunggu Konfirmasi
┌───┬─────────┬────────┬──────────┬───────────┬────────┐
│ # │ Member  │ Metode │ Nominal  │ Status    │ Aksi   │
├───┼─────────┼────────┼──────────┼───────────┼────────┤
│ 1 │ Ahmad   │ Tunai  │ 25.000   │ Pending   │Konfirm │
│ 2 │ Siti    │ QRIS   │ 35.000   │ Confirmed │ - │
└───┴─────────┴────────┴──────────┴───────────┴────────┘
```

### Proses Konfirmasi Tunai:

1. **Petugas** kasir menerima uang dari user untuk pembayaran denda
2. **Petugas** ke halaman `/admin/payments/pending`
3. Lihat payment dengan `method: tunai` dan `status: pending`
4. Klik tombol **"Konfirmasi"**
5. Confirm dialog muncul
6. Klik "Ya, Konfirmasi"
7. ✅ `payment_status` berubah menjadi **"confirmed"**
8. Activity log tercatat: "Admin XYZ mengkonfirmasi pembayaran tunai Rp XXX dari User YYZ"

---

## 6️⃣ TESTING CHECKLIST

### ✅ Setup/Prerequisites:
- [ ] Database fresh seeded dengan data late returns
- [ ] SystemParameters sudah ada:
  - `fine_per_day: 5000` (bisa diubah di admin settings)
  - `max_borrow_days: 14`
  - `max_extensions: 2`

### ✅ User Side Testing:

1. **MyLoans Tab "Tagihan Denda":**
   - [ ] Kunjungi `/my-loans` sebagai user
   - [ ] Klik tab "Tagihan Denda"
   - [ ] Lihat daftar peminjaman dengan denda
   - [ ] Denda = (hari terlambat × Rp 5.000)

2. **Payment Page:**
   - [ ] Klik tombol "BAYAR DENDA"
   - [ ] Modal "Proses Invoice?" muncul
   - [ ] Klik "Proceed Gateway"
   - [ ] Masuk halaman `/borrow/{id}/fine`
   - [ ] Lihat 2 opsi: QRIS & TUNAI

3. **QRIS Payment:**
   - [ ] Klik "LANJUTKAN QRIS"
   - [ ] Masuk halaman `/borrow/{id}/fine/qris`
   - [ ] QR code muncul
   - [ ] Klik "VERIFIKASI PEMBAYARAN"
   - [ ] Success message muncul

4. **TUNAI Payment:**
   - [ ] Dari halaman `/borrow/{id}/fine`, klik "PILIH TUNAI"
   - [ ] Message: "Silakan bayar ke kasir..."
   - [ ] Status berubah ke "Pending"

### ✅ Petugas Side Testing:

1. **Late Returns List:**
   - [ ] Kunjungi `/petugas/late-returns` sebagai petugas
   - [ ] Lihat tabel keterlambatan
   - [ ] Denda otomatis dihitung (real-time)
   - [ ] Stats menunjukkan total kasus, akumulasi denda, rata-rata hari

2. **Update Parameter & Lihat Denda Berubah:**
   - [ ] Sebagai petugas, buka `/petugas/settings`
   - [ ] Ubah `fine_per_day`: 5000 → 10000
   - [ ] Klik "Update Parameter"
   - [ ] Kembali ke `/petugas/late-returns`
   - [ ] Refresh halaman
   - [ ] **Denda seharusnya 2x lipat sekarang!** ✅

### ✅ Admin Side Testing:

1. **Late Returns List:**
   - [ ] Kunjungi `/admin/late-returns` sebagai admin
   - [ ] Lihat tabel keterlambatan (sama dengan petugas)

2. **User Detail & Restrict:**
   - [ ] Klik user yang terlambat
   - [ ] Masuk `/admin/late-returns/{user_id}`
   - [ ] Lihat profil & daftar buku yang ditunggakan
   - [ ] Klik "Batasi Peminjaman"
   - [ ] User tidak bisa pinjam buku baru

3. **Pending Payments:**
   - [ ] Kunjungi `/admin/payments/pending`
   - [ ] Lihat payment dengan method "tunai" & status "pending"
   - [ ] Klik "Konfirmasi"
   - [ ] Confirm dialog muncul
   - [ ] Klik "Ya, Konfirmasi"
   - [ ] Status berubah menjadi "confirmed"

4. **System Parameters:**
   - [ ] Kunjungi `/admin/settings/system-parameters`
   - [ ] Ubah `fine_per_day` dan nilai lainnya
   - [ ] Lihat perubahan langsung di halaman keterlambatan

---

## 📊 Data Flow Diagram

```
SKENARIO 1: User Bayar QRIS
═══════════════════════════
User di MyLoans
    ↓ (lihat denda di tab "Tagihan Denda")
    ↓ (klik "BAYAR DENDA")
    ↓
PayFine.vue (pilih metode)
    ↓ (pilih QRIS)
    ↓
QrisPayment.vue (scan QR)
    ↓ (klik "VERIFIKASI")
    ↓
UPDATE: payment_status = "confirmed"
    ✅ Denda LUNAS


SKENARIO 2: User Bayar TUNAI + Petugas Konfirmasi
══════════════════════════════════════════════════
User di MyLoans
    ↓ (lihat denda, klik "BAYAR DENDA")
    ↓
PayFine.vue
    ↓ (pilih TUNAI)
    ↓
UPDATE: payment_status = "pending"
    ↓ (User bayar ke kasir)
    ↓
Petugas buka /admin/payments/pending
    ↓ (lihat pending tunai)
    ↓ (klik "Konfirmasi")
    ↓
UPDATE: payment_status = "confirmed"
    ✅ Denda LUNAS


SKENARIO 3: Admin Update Parameter, Denda Otomatis Berubah
═══════════════════════════════════════════════════════════
Admin buka /admin/settings/system-parameters
    ↓ (ubah fine_per_day: 5000 → 10000)
    ↓ (klik Update)
    ↓
UPDATE: SystemParameter.fine_per_day = 10000
    ↓
Petugas/Admin buka /petugas/late-returns
    ↓ (refresh halaman)
    ↓
PetugasController->borrows() kalkulasi ulang:
    late_fee = hari_terlambat × getByKey('fine_per_day')
    = 5 hari × 10.000 = Rp 50.000 (sebelumnya 25.000)
    ✅ Denda OTOMATIS BERUBAH
```

---

## 🔐 Test Accounts

| Role | Email | Password | Status |
|------|-------|----------|--------|
| Admin | admin@example.com | password | approved |
| Petugas | petugas@example.com | password | approved |
| Member 1 | member@example.com | password | approved |
| Member 2 | member2@example.com | password | approved |

---

## 🚀 URLS Quick Reference

| Purpose | URL | Role |
|---------|-----|------|
| Lihat Denda | `/my-loans` → Tab "Tagihan Denda" | User |
| Bayar Denda | `/borrow/{id}/fine` | User |
| QRIS Payment | `/borrow/{id}/fine/qris` | User |
| Late Returns (Petugas) | `/petugas/late-returns` | Petugas |
| Petugas Settings | `/petugas/settings` | Petugas |
| Late Returns (Admin) | `/admin/late-returns` | Admin |
| User Detail | `/admin/late-returns/{user_id}` | Admin |
| Pending Payments | `/admin/payments/pending` | Admin |
| System Parameters | `/admin/settings/system-parameters` | Admin |

---

## 💡 Notes

- **Real-time Calculation:** Setiap refresh halaman, denda dihitung ulang dari database dengan formula terbaru
- **System Parameters:** Dapat diubah kapan saja tanpa restart aplikasi
- **Activity Logging:** Semua aksi tercatat di activity table untuk audit trail
- **Payment Status:** Ada 4 status: `unpaid`, `pending`, `confirmed`, `paid`
- **Extension Count:** Tracked untuk membatasi user tidak bisa perpanjang selamanya

---

**Generated:** May 15, 2026  
**Status:** ✅ Production Ready

