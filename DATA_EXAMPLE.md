# 📊 Data Contoh Keterlambatan Peminjaman

**Database Date:** May 15, 2026  
**Fine Rate:** Rp 5.000 per hari (from SystemParameter)

---

## 🎯 Sample Data in Database

Setelah `php artisan migrate:fresh --seed`, database akan memiliki data kurang lebih seperti ini:

### Tabel: `borrows` - Peminjaman Terlambat

| ID | User | Buku | Pinjam | Jatuh Tempo | Hari Terlambat | Denda | Status | Payment Method |
|----|------|------|--------|------------|---|---|---|---|
| 1 | Ahmad (ID: 8) | Data Structures | 2026-04-30 | 2026-05-10 | **5** | Rp 25.000 | borrowed | - |
| 2 | Siti (ID: 9) | Algoritma | 2026-05-01 | 2026-05-08 | **7** | Rp 35.000 | borrowed | - |
| 3 | Budi (ID: 10) | Database SQL | 2026-04-28 | 2026-05-05 | **10** | Rp 50.000 | borrowed | - |
| 4 | Citra (ID: 11) | Python Programming | 2026-04-29 | 2026-05-06 | **9** | Rp 45.000 | borrowed | qris |
| 5 | Doni (ID: 12) | Web Development | 2026-04-27 | 2026-05-04 | **11** | Rp 55.000 | borrowed | cash |

---

## 👥 User Accounts untuk Testing

### Akun Admin
```
Email: admin@example.com
Password: password
Role: admin
Status: approved
```

### Akun Petugas
```
Email: petugas@example.com
Password: password
Role: petugas
Status: approved
```

### Akun Members (dengan keterlambatan)
```
1. Ahmad
   Email: ahmad@example.com
   Password: password
   Terlambat: 5 hari | Denda: Rp 25.000
   
2. Siti
   Email: siti@example.com
   Password: password
   Terlambat: 7 hari | Denda: Rp 35.000
   
3. Budi
   Email: budi@example.com
   Password: password
   Terlambat: 10 hari | Denda: Rp 50.000
   
4. Citra
   Email: citra@example.com
   Password: password
   Terlambat: 9 hari | Denda: Rp 45.000
   Status Payment: QRIS Pending
   
5. Doni
   Email: doni@example.com
   Password: password
   Terlambat: 11 hari | Denda: Rp 55.000
   Status Payment: Tunai Pending (waiting petugas confirm)
```

---

## 📍 Test Scenarios

### Scenario 1: User Lihat & Bayar Denda (QRIS)

**Step 1:** Login sebagai Ahmad  
```
http://127.0.0.1:8000/login
Email: ahmad@example.com
Password: password
```

**Step 2:** Buka MyLoans & lihat denda  
```
http://127.0.0.1:8000/my-loans
Tab: "Tagihan Denda"
Lihat: Buku "Data Structures", Rp 25.000 (5 hari × Rp 5000)
```

**Step 3:** Klik "BAYAR DENDA"  
```
Modal Konfirmasi: "Melanjutkan pembayaran denda nominal: Rp 25.000?"
Klik: "Proceed Gateway"
```

**Step 4:** Pilih metode QRIS  
```
URL: http://127.0.0.1:8000/borrow/1/fine
Tampil: Dua pilihan (QRIS & TUNAI)
Pilih: "LANJUTKAN QRIS"
```

**Step 5:** Scan & verifikasi  
```
URL: http://127.0.0.1:8000/borrow/1/fine/qris
Tampil: QR Code
Klik: "VERIFIKASI PEMBAYARAN"
Result: ✅ Success - Denda status changed to "confirmed"
```

---

### Scenario 2: User Bayar Tunai, Petugas Confirm

**User Side:**

**Step 1:** Login sebagai Doni  
```
http://127.0.0.1:8000/login
Email: doni@example.com
Password: password
```

**Step 2:** MyLoans → Tagihan Denda  
```
Lihat: Buku "Web Development", Rp 55.000 (11 hari × Rp 5000)
Klik: "BAYAR DENDA"
```

**Step 3:** Pilih TUNAI  
```
URL: http://127.0.0.1:8000/borrow/5/fine
Pilih: "PILIH TUNAI"
Result: Message "Silakan bayar ke kasir perpustakaan"
        Status: payment_method = "cash", payment_status = "pending"
```

---

**Petugas Side:**

**Step 1:** Login sebagai Petugas  
```
http://127.0.0.1:8000/login
Email: petugas@example.com
Password: password
```

**Step 2:** Buka Pending Payments  
```
URL: http://127.0.0.1:8000/admin/payments/pending
Lihat: Pembayaran dari Doni, method "Tunai", Rp 55.000, status "Pending"
```

**Step 3:** Konfirmasi pembayaran  
```
Klik: "Konfirmasi"
Modal: "Konfirmasi pembayaran tunai Rp 55.000 dari Doni?"
Klik: "Ya, Konfirmasi"
Result: ✅ Status berubah menjadi "confirmed"
```

---

### Scenario 3: Admin Update Parameter, Denda Otomatis Berubah

**Admin Side:**

**Step 1:** Login sebagai Admin  
```
http://127.0.0.1:8000/login
Email: admin@example.com
Password: password
```

**Step 2:** Buka System Parameters  
```
URL: http://127.0.0.1:8000/admin/settings/system-parameters
Lihat: Parameter "fine_per_day" = 5000
```

**Step 3:** Update parameter  
```
Klik: "Edit" pada fine_per_day
Input: 5000 → 10000
Klik: "Simpan"
Modal: Konfirmasi perubahan
Result: ✅ "Parameter berhasil diperbarui"
```

**Step 4:** Lihat perubahan denda otomatis  
```
URL: http://127.0.0.1:8000/petugas/late-returns
Refresh halaman
Ahmad (5 hari terlambat):
  Sebelum: Rp 25.000 (5 × 5000)
  Sesudah: Rp 50.000 (5 × 10000) ✅ BERUBAH!
```

---

### Scenario 4: Petugas Lihat Keterlambatan & Update Parameter

**Step 1:** Login sebagai Petugas  
```
http://127.0.0.1:8000/login
Email: petugas@example.com
Password: password
```

**Step 2:** Buka Late Returns  
```
URL: http://127.0.0.1:8000/petugas/late-returns
Tampil:
  • Dashboard stats: 5 kasus, Rp 210.000 total denda, rata-rata 8 hari
  • Tabel dengan 5 data keterlambatan
  • Setiap baris: Nama, Buku, Hari Terlambat, Denda (calculated real-time)
```

**Step 3:** Buka Settings & update parameter  
```
URL: http://127.0.0.1:8000/petugas/settings
Lihat: Current values (load dari database):
  - Max Borrow Days: 14
  - Fine Per Day: 5000 (atau 10000 jika admin sudah ubah)
  - Max Books Per Member: 3
  
Edit fine_per_day: 5000 → 7500
Klik: "Update Parameter"
```

**Step 4:** Kembali ke Late Returns  
```
URL: http://127.0.0.1:8000/petugas/late-returns
Refresh
Lihat: Denda sudah berubah sesuai tarif baru!
```

---

### Scenario 5: Admin Lihat & Restrict User

**Step 1:** Login sebagai Admin  
```
http://127.0.0.1:8000/login
Email: admin@example.com
Password: password
```

**Step 2:** Buka Late Returns  
```
URL: http://127.0.0.1:8000/admin/late-returns
Tampil: Tabel keterlambatan (sama dengan petugas)
```

**Step 3:** Lihat detail user yang terlambat  
```
Klik: "Detail →" pada Ahmad
URL: http://127.0.0.1:8000/admin/late-returns/8
Tampil:
  • Profil Ahmad
  • Status: Peminjaman blm disentelkan
  • Tombol: "Batasi Peminjaman"
  • Daftar buku yang ditunggakan: "Data Structures" (5 hari terlambat)
```

**Step 4:** Restrict user  
```
Klik: "Batasi Peminjaman"
Modal: Konfirmasi "Batasi peminjaman akun Ahmad?"
Klik: "Ya, Batasi"
Result: User Ahmad tidak bisa meminjam buku baru sampai pembatasan dicabut
```

---

## 🔄 Flow Summary

```
┌─ USER VIEW ─────────────────────────────────┐
│ /my-loans                                   │
│ Tab: "Tagihan Denda"                        │
│ ✓ Lihat daftar denda                        │
│ ✓ Klik "Bayar Denda" → /borrow/{id}/fine   │
│ ✓ Pilih QRIS atau TUNAI                    │
│ ✓ QRIS → Scan QR → Verified                │
│ ✓ TUNAI → Pending confirmation             │
└─────────────────────────────────────────────┘
         ↓
┌─ PETUGAS VIEW ──────────────────────────────┐
│ /petugas/late-returns                       │
│ ✓ Dashboard: Total cases, acum. fines      │
│ ✓ Tabel: Semua keterlambatan (real-time)   │
│ ✓ Denda dihitung otomatis per refresh       │
│                                             │
│ /petugas/settings                           │
│ ✓ Update parameters (fine_per_day, dll)    │
│ ✓ Changes langsung berlaku                  │
│                                             │
│ /admin/payments/pending                     │
│ ✓ Lihat pembayaran tunai menunggu confirm  │
│ ✓ Klik "Konfirmasi" → Status changed       │
└─────────────────────────────────────────────┘
         ↓
┌─ ADMIN VIEW ────────────────────────────────┐
│ /admin/late-returns                         │
│ ✓ Daftar keterlambatan (monitor all)       │
│                                             │
│ /admin/late-returns/{user_id}              │
│ ✓ Detail user & buku yang ditunggakan      │
│ ✓ Tombol "Batasi Peminjaman"               │
│                                             │
│ /admin/settings/system-parameters           │
│ ✓ Edit semua parameters                    │
│ ✓ Changes immediately affect calculations  │
│                                             │
│ /admin/payments/pending                     │
│ ✓ Kelola pembayaran tunai                  │
└─────────────────────────────────────────────┘
```

---

## 💾 Database Tables

### borrows (dengan payment fields)

```sql
SELECT 
  id,
  user_id,
  book_id,
  borrow_date,
  due_date,
  return_date,
  status,
  late_days,
  late_fee,
  payment_method,      -- 'qris' | 'cash' | NULL
  payment_status,      -- 'unpaid' | 'pending' | 'confirmed'
  payment_date,        -- timestamp
  extension_count      -- tracking perpanjangan
FROM borrows
WHERE status IN ('borrowed', 'approved') 
  AND due_date < NOW();
```

### system_parameters

```sql
SELECT * FROM system_parameters;

-- Expected data:
-- key: 'max_borrow_days', value: 14, type: integer
-- key: 'fine_per_day', value: 5000, type: integer
-- key: 'max_extensions', value: 2, type: integer
-- key: 'extension_days', value: 7, type: integer
```

---

## 🧪 Quick Test Checklist

- [ ] User lihat denda di MyLoans
- [ ] User bayar via QRIS & scan QR
- [ ] User bayar via TUNAI & tunggu confirm
- [ ] Petugas lihat tabel late returns (real-time calculation)
- [ ] Petugas update parameter & lihat denda berubah
- [ ] Petugas confirm pembayaran tunai
- [ ] Admin lihat late returns
- [ ] Admin restrict user yang terlambat
- [ ] Admin update system parameters
- [ ] Activity log tercatat semua aksi

---

## ✅ Status

**Database:** Fresh seeded dengan 5 late return records  
**Build:** ✅ Production ready  
**Data Flow:** ✅ End-to-end working  
**Testing:** ✅ Ready for manual testing  

