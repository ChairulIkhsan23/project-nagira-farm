<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <style>
    /* Reset dasar */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Margin halaman untuk print */
    @page {
        margin: 12mm 0 20mm 0;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
        line-height: 1.3;
        color: #111827;
    }

    /* Wrapper halaman */
    .page-wrapper {
        width: 100%;
        text-align: center;
    }

    /* Nomor Halaman*/
    .page-number::after {
    content: "Halaman " counter(page);
    }
    
    /* Kontainer utama */
    .container {
        width: 190mm;
        margin: 0 auto;
        padding: 10mm 12mm 28mm;
        text-align: left;
    }

    /* Header laporan */
    .header {
        border-bottom: 2px solid #111827;
        padding-bottom: 10px;
        margin-bottom: 14px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .header img {
        width: 60px;
        height: 60px;
    }

    .header-title h2 {
        font-size: 14pt;
        color: #111827;
        font-weight: 700;
    }

    .report-info {
        font-size: 8.5pt;
        color: #6b7280;
        gap: 15px;
    }

    /* Informasi dokumen */
    .doc-info {
        margin: 12px 0 18px;
        font-size: 9pt;
    }

    .doc-header h2 {
        font-size: 13pt;
        font-weight: 700;
        text-align: center;
        margin-bottom: 10px;
        color: #111827;
        letter-spacing: 0.4px;
    }

    .doc-row {
        display: flex;
        line-height: 1.35;
        margin-bottom: 3px;
    }

    .doc-label {
        width: 165px;
        font-weight: 600;
        color: #111827;
        white-space: nowrap;
    }

    .doc-value {
        flex: 1;
        font-weight: 500;
        color: #111827;
    }

    /* Badge untuk status */
    .badge {
        display: inline-block;
        padding: 1px 5px;
        font-size: 7pt;
        font-weight: 600;
        border-radius: 3px;
        min-width: 54px;
        text-align: center;
    }

    /* Warna badge kategori */
    .badge-breeding { background:#f3e8ff; color:#6d28d9; border:1px solid #ddd6fe; }
    .badge-fattening { background:#fef9c3; color:#a16207; border:1px solid #fde047; }
    .badge-reguler { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }

    /* Warna badge kesehatan */
    .badge-sehat { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .badge-perawatan { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
    .badge-sakit { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

    /* Warna badge status aktif */
    .badge-aktif { background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe; }
    .badge-nonaktif { background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1; }

    /* Tabel statistik */
    .stats-table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0 16px;
        font-size: 9pt;
    }

    .stats-table th,
    .stats-table td {
        border: 1px solid #d1d5db;
        padding: 6px;
        text-align: center;
    }

    .stats-table th {
        background: #f0fdf4;
        color: #111827;
        font-weight: 600;
    }

    /* Tabel data utama */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 9pt;
    }

    table.data-table th,
    table.data-table td {
        border: 1px solid #d1d5db;
        padding: 4px 6px;
        vertical-align: middle;
        word-break: break-word;
    }

    table.data-table th {
        background: #f3f4f6;
        font-size: 8pt;
        text-align: center;
        font-weight: 600;
    }

    /* Kolom nomor  */
    .no-column {
        width: 30px; 
        text-align: center;
    }

    /* Kolom kode */
    .kode-column {
        width: 100px; 
        text-align: center;
        font-weight: 600;
    }

    /* Kolom nama */
    .nama-column {
        width: 110px;
        text-align: left;
    }

    /* Kolom jenis */
    .jenis-column {
        width: 80px;
        text-align: center;
    }

    /* Kolom kategori */
    .kategori-column {
        width: 80px;
        text-align: center;
    }

    /* Kolom kesehatan */
    .kesehatan-column {
        width: 75px;
        text-align: center;
    }

    /* Kolom status */
    .status-column {
        width: 70px;
        text-align: center;
    }

    /* Kolom tanggal lahir */
    .lahir-column {
        width: 85px;
        text-align: center;
    }

    /* Kolom catatan */
    .catatan-column {
        width: auto;
        text-align: left;
        min-width: 120px;
    }

    /* Baris tabel data */
    table.data-table tbody tr {
        height: 24px;
    }

    /* Warna baris bergantian */
    table.data-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    /* Footer */
    .footer {
        position: fixed;
        bottom: 8mm;
        left: 0;
        right: 0;
        font-size: 8pt;
        color: #4b5563;
    }

    .footer-inner {
        width: 190mm;
        margin: 0 auto;
        border-top: 1px solid #d1d5db;
        padding-top: 6px;
    }

    .footer-table {
        width: 100%;
    }

    .footer-right {
        text-align: right;
    }
    </style>
</head>

<body>
<div class="page-wrapper">
<div class="container">

    <!-- Header laporan dengan logo dan judul -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="58">
                    <img src="{{ $logoBase64 }}">
                </td>
                <td class="header-title">
                    <h2>Laporan Ternak Nagira Farm</h2>
                    <div class="report-info">
                        Periode {{ $period }}<br>
                        Rentang Pembaruan Data : {{ $lastUpdate }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informasi dokumen laporan -->
    <div class="doc-info">
        <div class="doc-header">
            <h2>INFORMASI DOKUMEN</h2>
        </div>
        <div class="doc-row">
            <span class="doc-label">Tanggal & Waktu Cetak : </span>
            <span class="doc-value">{{ now()->format('d F Y, H:i') }}</span>
        </div>
        <div class="doc-row">
            <span class="doc-label">Total Data Ternak : </span>
            <span class="doc-value">{{ $total }} Ekor</span>
        </div>
        <div class="doc-row">
            <span class="doc-label">ID Dokumen : </span>
            <span class="doc-value">{{ $documentId }}</span>
        </div>
    </div>

    <!-- Statistik data ternak -->
    @if(isset($stats) && count($stats))
    <table class="stats-table">
        <thead>
            <tr>
                @foreach($stats as $stat)
                    <th>{{ $stat['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($stats as $stat)
                    <td>{{ $stat['value'] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Tabel data utama -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="no-column">No</th>
                <th class="kode-column">Kode</th>
                <th class="nama-column">Nama</th>
                <th class="jenis-column">Jenis</th>
                <th class="kategori-column">Kategori</th>
                <th class="kesehatan-column">Kesehatan</th>
                <th class="status-column">Status</th>
                <th class="lahir-column">Tanggal Lahir</th>
                <th class="catatan-column">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ternaks as $i => $ternak)
            <tr>
                <!-- Kolom No - lebih sempit -->
                <td class="no-column">{{ $i + 1 }}</td>
                
                <!-- Kolom Kode - lebih lebar -->
                <td class="kode-column">{{ $ternak->kode_ternak }}</td>
                
                <!-- Kolom Nama -->
                <td class="nama-column">{{ $ternak->nama_ternak }}</td>
                
                <!-- Kolom Jenis -->
                <td class="jenis-column">{{ $ternak->jenis_ternak }}</td>

                <!-- Kolom Kategori dengan badge -->
                <td class="kategori-column">
                    <span class="badge badge-{{ $ternak->kategori ?? 'reguler' }}">
                        {{ ucfirst($ternak->kategori ?? 'reguler') }}
                    </span>
                </td>

                <!-- Kolom Kesehatan dengan badge -->
                <td class="kesehatan-column">
                    <span class="badge badge-{{ $ternak->status_kesehatan }}">
                        {{ ucfirst($ternak->status_kesehatan) }}
                    </span>
                </td>

                <!-- Kolom Status dengan badge -->
                <td class="status-column">
                    <span class="badge badge-{{ $ternak->status_aktif }}">
                        {{ ucfirst($ternak->status_aktif) }}
                    </span>
                </td>

                <!-- Kolom Tanggal Lahir -->
                <td class="lahir-column">
                    {{ $ternak->tanggal_lahir ? date('d/m/Y', strtotime($ternak->tanggal_lahir)) : '-' }}
                </td>
                
                <!-- Kolom Catatan -->
                <td class="catatan-column">{{ $ternak->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
</div>

<!-- Footer dokumen -->
<div class="footer">
    <div class="footer-inner">
        <table class="footer-table">
            <tr>
                <td>
                    <strong>Sistem Informasi Peternakan</strong> {{ $generatedBy }}<br>
                    Dokumen ini dihasilkan secara otomatis
                </td>
                <td class="footer-right">
                    <span class="page-number"></span><br>
                    Nagira Farm © {{ date('Y') }}
                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>