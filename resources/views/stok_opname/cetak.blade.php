<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Stok Opname — {{ $settings['toko_nama'] ?? 'N2N Toko' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
            background: #f5f5f5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            padding: 14mm 14mm 10mm;
        }

        /* ── Header ── */
        .header { text-align: center; margin-bottom: 10px; }
        .header .toko-nama { font-size: 15px; font-weight: bold; letter-spacing: 0.5px; }
        .header .toko-sub  { font-size: 11px; color: #555; margin-top: 2px; }
        .header-divider    { border: none; border-top: 2px solid #111; margin: 8px 0 6px; }

        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #444;
            margin-bottom: 10px;
        }
        .meta-right { text-align: right; }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        thead th {
            background: #1e3a5f;
            color: #fff;
            padding: 5px 6px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }
        thead th.left { text-align: left; }

        tbody tr:nth-child(even) { background: #f8f9fb; }
        tbody tr { border-bottom: 1px solid #e0e0e0; }

        tbody td {
            padding: 5px 6px;
            vertical-align: middle;
        }
        .td-no     { text-align: center; width: 28px; color: #888; }
        .td-produk { font-weight: 600; }
        .td-sku    { font-size: 10px; color: #888; font-family: monospace; }
        .td-satuan { text-align: center; width: 55px; }
        .td-stok   { text-align: right; width: 60px; font-family: monospace; font-weight: 600; }
        .td-blank  { width: 65px; }
        .td-ket    { width: 110px; }

        .blank-box {
            border-bottom: 1px solid #999;
            min-height: 16px;
            display: block;
            width: 100%;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .ttd-box {
            text-align: center;
            font-size: 11px;
            width: 130px;
        }
        .ttd-line {
            border-bottom: 1px solid #555;
            height: 40px;
            margin: 20px 4px 4px;
        }
        .ttd-label { font-size: 10px; color: #555; }

        .info-box {
            font-size: 10px;
            color: #666;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 10px;
            max-width: 260px;
            line-height: 1.6;
        }
        .info-box strong { color: #333; }

        /* ── Print controls (non-print only) ── */
        .print-controls {
            position: fixed;
            top: 14px;
            right: 14px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-print { background: #1e3a5f; color: #fff; }
        .btn-back  { background: #e5e7eb; color: #374151; text-decoration: none; display: inline-flex; align-items: center; }

        @media print {
            body { background: #fff; }
            .print-controls { display: none; }
            .page { margin: 0; padding: 10mm 12mm 8mm; box-shadow: none; }
            table { font-size: 10.5px; }
        }
    </style>
</head>
<body>

{{-- Tombol kontrol (hilang saat cetak) --}}
<div class="print-controls">
    <a href="{{ route('stok-opname.index') }}" class="btn btn-back">← Kembali</a>
    <button class="btn btn-print" onclick="window.print()">🖨 Cetak</button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="toko-nama">{{ $settings['toko_nama'] ?? 'N2N Toko' }}</div>
        @if(!empty($settings['toko_alamat']))
        <div class="toko-sub">
            {{ $settings['toko_alamat'] }}{{ !empty($settings['toko_kota']) ? ', ' . $settings['toko_kota'] : '' }}
            @if(!empty($settings['toko_telepon'])) &nbsp;|&nbsp; Telp. {{ $settings['toko_telepon'] }} @endif
        </div>
        @endif
    </div>
    <hr class="header-divider">

    <div class="doc-title">Form Stok Opname</div>

    <div class="meta">
        <div>
            <span>Tanggal&nbsp;&nbsp;:&nbsp;</span>
            <strong>{{ now()->translatedFormat('d F Y') }}</strong><br>
            <span>Waktu&nbsp;&nbsp;&nbsp;:&nbsp;</span>
            <strong>{{ now()->format('H:i') }} WIB</strong>
        </div>
        <div class="meta-right">
            <span>Petugas&nbsp;:&nbsp;</span><span style="border-bottom:1px solid #999; min-width:120px; display:inline-block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
            <span>Paraf&nbsp;&nbsp;&nbsp;:&nbsp;</span><span style="border-bottom:1px solid #999; min-width:120px; display:inline-block;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
    </div>

    {{-- Tabel --}}
    <table>
        <thead>
            <tr>
                <th style="width:28px">No</th>
                <th class="left">Produk</th>
                <th style="width:55px">Satuan</th>
                <th style="width:60px">Stok<br>Sistem</th>
                <th style="width:65px">Stok<br>Fisik</th>
                <th style="width:65px">Selisih</th>
                <th style="width:110px">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
            <tr>
                <td class="td-no">{{ $i + 1 }}</td>
                <td>
                    <div class="td-produk">{{ $product->name }}</div>
                    <div class="td-sku">{{ $product->sku }}</div>
                </td>
                <td class="td-satuan">{{ $product->baseUnit?->unit_name ?? '—' }}</td>
                <td class="td-stok">{{ number_format($product->stock_qty, 0, ',', '.') }}</td>
                <td class="td-blank"><span class="blank-box"></span></td>
                <td class="td-blank"><span class="blank-box"></span></td>
                <td class="td-ket"><span class="blank-box"></span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="info-box">
            <strong>Petunjuk pengisian:</strong><br>
            • Isi kolom <em>Stok Fisik</em> dengan hasil hitung nyata di gudang<br>
            • Isi <em>Selisih</em> = Stok Fisik − Stok Sistem (+ jika lebih, − jika kurang)<br>
            • Isi <em>Keterangan</em> jika ada selisih (rusak, hilang, dll)
        </div>
        <div style="display:flex; gap:20px;">
            <div class="ttd-box">
                <div class="ttd-line"></div>
                <div class="ttd-label">Diperiksa</div>
            </div>
            <div class="ttd-box">
                <div class="ttd-line"></div>
                <div class="ttd-label">Disetujui</div>
            </div>
        </div>
    </div>

    <div style="text-align:center; font-size:10px; color:#aaa; margin-top:10px; border-top:1px solid #eee; padding-top:6px;">
        Dicetak pada {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; {{ $settings['toko_nama'] ?? 'N2N Toko' }} &nbsp;|&nbsp; Total {{ $products->count() }} produk
    </div>

</div>

</body>
</html>
