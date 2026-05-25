<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKIE-Library Official Executive Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
        }
        
        .document-page {
            background: white;
            max-width: 842px; /* Proporsi standar lebar A4 */
            margin: 0 auto;
            padding: 60px 50px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        }
        
        /* Kop Surat Resmi / Header Dokumen */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .institution-info h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0f172a;
            text-transform: uppercase;
        }
        
        .institution-info p {
            font-size: 13px;
            color: #475569;
            margin-top: 2px;
            line-height: 1.4;
        }
        
        .report-meta {
            text-align: right;
        }
        
        .report-meta .doc-type {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2563eb;
            background: #eff6ff;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 8px;
        }
        
        .report-meta p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }
        
        /* Judul Laporan */
        .report-title-container {
            margin-bottom: 32px;
        }
        
        .report-title-container h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        
        /* Grid Ringkasan Eksekutif */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            background: #f8fafc;
        }
        
        .summary-card .label {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .summary-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        
        .summary-card.alert {
            background: #fffaf5;
            border-color: #fed7aa;
        }
        
        .summary-card.alert .value {
            color: #c2410c;
        }
        
        /* Penataan Tabel Data */
        .table-container {
            margin-bottom: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        
        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .badge-status {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .badge-status.danger {
            background: #fef2f2;
            color: #b91c1c;
        }
        
        .badge-status.success {
            background: #f0fdf4;
            color: #166534;
        }
        
        /* Bagian Tanda Tangan / Validasi */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-box .date {
            font-size: 13px;
            color: #475569;
            margin-bottom: 60px;
        }
        
        .signature-box .name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 2px;
        }
        
        .signature-box .role {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        
        /* Tombol Cetak / Kontrol */
        .action-bar {
            max-width: 842px;
            margin: 20px auto 0 auto;
            display: flex;
            justify-content: flex-end;
        }
        
        .btn {
            background: #0f172a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: background 0.2s ease;
        }
        
        .btn:hover {
            background: #1e293b;
        }
        
        /* Optimasi Cetak */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .document-page {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .action-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="document-page">
        <div class="document-header">
            <div class="institution-info">
                <h1>SKIE-Library</h1>
                <p>Sistem Informasi Manajemen Perpustakaan Sekolah</p>
                <p>Wilayah Kecamatan Gunungputri, Kab. Bogor, Jawa Barat</p>
            </div>
            <div class="report-meta">
                <span class="doc-type">Laporan Eksekutif</span>
                <p><strong>ID Dokumen:</strong> RP-{{ time() }}</p>
                <p><strong>Periode:</strong> {{ now()->format('F Y') }}</p>
            </div>
        </div>

        <div class="report-title-container">
            <h2>LAPORAN STATISTIK DAN AKTIVITAS PERPUSTAKAAN</h2>
        </div>

        <div class="section-title">1. Ringkasan Sumber Daya & Keanggotaan</div>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Koleksi Buku</div>
                <div class="value">{{ $stats['totalBooks'] }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Anggota Terdaftar</div>
                <div class="value">{{ $stats['totalMembers'] }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Staf Manajemen</div>
                <div class="value">{{ $stats['totalPetugas'] }}</div>
            </div>
            <div class="summary-card alert">
                <div class="label">Verifikasi Tertunda</div>
                <div class="value">{{ $stats['pendingMembers'] }}</div>
            </div>
        </div>

        <div class="section-title">2. Kinerja Sirkulasi & Logistik Buku</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Indikator Aktivitas</th>
                        <th class="text-right">Volume / Jumlah</th>
                        <th>Keterangan Atribusi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Transaksi Peminjaman</td>
                        <td class="text-right font-semibold">{{ $stats['totalBorrows'] }}</td>
                        <td>Akumulasi keseluruhan siklus peminjaman berjalan</td>
                    </tr>
                    <tr>
                        <td>Pelanggaran Batas Waktu (Terlambat)</td>
                        <td class="text-right font-semibold" style="color: #b91c1c;">{{ $stats['lateReturns'] }}</td>
                        <td>Memerlukan tindakan penagihan atau restriksi akun</td>
                    </tr>
                    <tr>
                        <td>Rasio Keterlambatan Pengembalian</td>
                        <td class="text-right font-semibold">
                            {{ $stats['totalBorrows'] > 0 ? number_format(($stats['lateReturns'] / $stats['totalBorrows']) * 100, 1) : 0 }}%
                        </td>
                        <td>Tingkat kepatuhan pengembalian koleksi sirkulasi</td>
                    </tr>
                    <tr>
                        <td>Total Pendapatan Kas Denda</td>
                        <td class="text-right font-semibold" style="color: #166534;">Rp {{ number_format($stats['totalFine'], 0, ',', '.') }}</td>
                        <td>Dana akumulatif sanksi keterlambatan yang berhasil dibukukan</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section-title">3. Status Peninjauan Operasional</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Komponen Peninjauan</th>
                        <th>Metrik Penilaian</th>
                        <th>Status Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pendaftaran Anggota Baru</td>
                        <td>{{ $stats['pendingMembers'] }} Akun menunggu persetujuan</td>
                        <td>
                            <span class="badge-status {{ $stats['pendingMembers'] > 0 ? 'danger' : 'success' }}">
                                {{ $stats['pendingMembers'] > 0 ? 'Butuh Tindakan' : 'Selesai' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Pengembalian Buku Terlambat</td>
                        <td>{{ $stats['lateReturns'] }} Buku belum kembali ke rak</td>
                        <td>
                            <span class="badge-status {{ $stats['lateReturns'] > 0 ? 'danger' : 'success' }}">
                                {{ $stats['lateReturns'] > 0 ? 'Restriksi Aktif' : 'Bersih' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="date">Bogor, {{ $generatedAt->format('d F Y') }}</div>
                <div class="name">Staf Administrasi</div>
                <div class="role">Sistem SKIE-Library</div>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <button class="btn" onclick="window.print()">Cetak Laporan Resmi (A4)</button>
    </div>

</body>
</html>