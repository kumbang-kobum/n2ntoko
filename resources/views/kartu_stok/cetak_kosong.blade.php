<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kartu Stok Kosong — {{ $settings['toko_nama'] ?? 'N2N Toko' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #111;
            background: #e0e0e0;
        }

        /* A4 portrait — 2 kolom kiri/kanan */
        .page {
            width: 210mm;
            height: 297mm;
            background: #fff;
            margin: 0 auto;
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }

        /* Masing-masing setengah A4 */
        .half {
            width: 105mm;
            height: 297mm;
            padding: 7mm 6mm 5mm;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Garis potong vertikal */
        .cut-line {
            flex-shrink: 0;
            width: 0;
            height: 297mm;
            border-left: 1px dashed #aaa;
            position: relative;
        }
        .cut-line::before {
            content: '✂';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(90deg);
            background: #fff;
            padding: 3px;
            font-size: 9px;
            color: #bbb;
        }

        /* ── Header ── */
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .toko-nama  { font-size: 10.5px; font-weight: bold; }
        .toko-sub   { font-size: 8px; color: #666; margin-top: 1px; }
        .doc-title-box {
            border: 1.5px solid #1e3a5f;
            padding: 3px 8px;
            border-radius: 3px;
            text-align: center;
        }
        .doc-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1e3a5f;
            white-space: nowrap;
        }

        .divider { border: none; border-top: 1.5px solid #1e3a5f; margin: 3px 0 4px; }

        /* ── Info produk (isian tangan) ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 10px;
            margin-bottom: 5px;
        }
        .info-row   { display: flex; align-items: flex-end; gap: 3px; }
        .info-label { color: #555; white-space: nowrap; width: 60px; flex-shrink: 0; font-size: 8px; }
        .info-line  { flex: 1; border-bottom: 1px solid #999; height: 13px; min-width: 20px; }

        /* ── Tabel ── */
        table { width: 100%; border-collapse: collapse; font-size: 8.5px; flex: 1; }

        thead th {
            background: #1e3a5f;
            color: #fff;
            padding: 3px 2px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            text-align: center;
            border: 1px solid #18304d;
            white-space: nowrap;
        }
        thead th.left { text-align: left; padding-left: 3px; }

        tbody tr            { height: 17px; }
        tbody tr:nth-child(even) { background: #f7f8fc; }

        td { border: 1px solid #ccc; padding: 0 2px; vertical-align: middle; }

        .td-no  { width: 18px; text-align: center; color: #ccc; font-size: 7.5px; }
        .td-tgl { width: 46px; }
        .td-num { width: 36px; text-align: right; }
        .td-cek { width: 20px; text-align: center; }

        .cek-box {
            width: 10px; height: 10px;
            border: 1.5px solid #555;
            display: inline-block;
            border-radius: 2px;
        }

        .row-saldo td {
            background: #edf2f9;
            font-size: 7.5px;
            color: #666;
            font-style: italic;
            height: 15px;
        }
        .row-total td {
            background: #dde5f0;
            border-top: 1.5px solid #1e3a5f;
            font-weight: bold;
            font-size: 8px;
            height: 17px;
        }
        .total-label { text-align: right; padding-right: 4px; color: #444; }

        /* ── Ringkasan + TTD ── */
        .footer-area {
            margin-top: 4px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .summary-row { display: flex; gap: 3px; }
        .sum-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 2px;
            padding: 2px 4px;
            font-size: 7.5px;
        }
        .sum-label { color: #666; margin-bottom: 2px; }
        .sum-line  { border-bottom: 1px solid #aaa; height: 12px; }

        .ttd-area  { display: flex; gap: 6px; }
        .ttd-box   { flex: 1; text-align: center; font-size: 7.5px; }
        .ttd-line  { border-bottom: 1px solid #666; height: 20px; margin: 6px 2px 2px; }
        .ttd-label { color: #555; }

        /* ── Kontrol layar ── */
        .screen-controls {
            position: fixed;
            top: 14px; right: 14px;
            display: flex; gap: 8px;
            z-index: 100;
        }
        .btn-back {
            padding: 8px 16px; background: #e5e7eb; color: #374151;
            border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;
        }
        .btn-print {
            padding: 8px 16px; background: #1e3a5f; color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
        }

        @media print {
            .screen-controls { display: none !important; }
            body { background: #fff; }
            .page { margin: 0; }
        }
    </style>
</head>
<body>

<div class="screen-controls">
    <a href="{{ route('kartu-stok.index') }}" class="btn-back">← Kembali</a>
    <button class="btn-print" onclick="window.print()">🖨 Cetak</button>
</div>

@php
    $tokoNama = $settings['toko_nama'] ?? 'N2N Toko';
    $tokoSub  = trim(($settings['toko_alamat'] ?? '') . (!empty($settings['toko_kota']) ? ', '.$settings['toko_kota'] : ''));
    if (!empty($settings['toko_telepon'])) $tokoSub .= ($tokoSub ? ' | ' : '') . 'Telp. '.$settings['toko_telepon'];
@endphp

<div class="page">

    @for($col = 0; $col < 2; $col++)

    @if($col === 1)
    <div class="cut-line"></div>
    @endif

    <div class="half">

        {{-- Header --}}
        <div class="header">
            <div>
                <div class="toko-nama">{{ $tokoNama }}</div>
                @if($tokoSub)<div class="toko-sub">{{ $tokoSub }}</div>@endif
            </div>
            <div class="doc-title-box">
                <div class="doc-title">Kartu Stok</div>
            </div>
        </div>
        <hr class="divider">

        {{-- Info produk — kosong diisi tangan --}}
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Nama Produk</span>
                <span class="info-line"></span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode</span>
                <span class="info-line"></span>
            </div>
            <div class="info-row">
                <span class="info-label">SKU / Kode</span>
                <span class="info-line"></span>
            </div>
            <div class="info-row">
                <span class="info-label">Satuan</span>
                <span class="info-line"></span>
            </div>
            <div class="info-row">
                <span class="info-label">Kategori</span>
                <span class="info-line"></span>
            </div>
            <div class="info-row">
                <span class="info-label">Lokasi / Rak</span>
                <span class="info-line"></span>
            </div>
        </div>

        {{-- Tabel --}}
        <table>
            <thead>
                <tr>
                    <th style="width:18px">No</th>
                    <th class="left" style="width:46px">Tanggal</th>
                    <th class="left">Keterangan</th>
                    <th style="width:36px">Masuk</th>
                    <th style="width:36px">Keluar</th>
                    <th style="width:36px">Stok</th>
                    <th style="width:20px">✓</th>
                </tr>
            </thead>
            <tbody>
                <tr class="row-saldo">
                    <td class="td-no">—</td>
                    <td class="td-tgl"></td>
                    <td style="padding-left:3px; font-size:7.5px">Saldo Awal</td>
                    <td class="td-num"></td>
                    <td class="td-num"></td>
                    <td class="td-num" style="border-bottom:1px solid #999;"></td>
                    <td class="td-cek"></td>
                </tr>

                @for($r = 1; $r <= 20; $r++)
                <tr>
                    <td class="td-no">{{ $r }}</td>
                    <td class="td-tgl"></td>
                    <td></td>
                    <td class="td-num"></td>
                    <td class="td-num"></td>
                    <td class="td-num"></td>
                    <td class="td-cek"><span class="cek-box"></span></td>
                </tr>
                @endfor

                <tr class="row-total">
                    <td colspan="3" class="total-label">Total</td>
                    <td class="td-num"></td>
                    <td class="td-num"></td>
                    <td class="td-num"></td>
                    <td class="td-cek"></td>
                </tr>
            </tbody>
        </table>

        {{-- Ringkasan --}}
        <div class="footer-area">
            <div class="summary-row">
                <div class="sum-box">
                    <div class="sum-label">Saldo Awal</div>
                    <div class="sum-line"></div>
                </div>
                <div class="sum-box">
                    <div class="sum-label">Total Masuk</div>
                    <div class="sum-line"></div>
                </div>
                <div class="sum-box">
                    <div class="sum-label">Total Keluar</div>
                    <div class="sum-line"></div>
                </div>
                <div class="sum-box">
                    <div class="sum-label">Saldo Akhir</div>
                    <div class="sum-line"></div>
                </div>
            </div>

            {{-- TTD --}}
            <div class="ttd-area">
                <div class="ttd-box">
                    <div class="ttd-line"></div>
                    <div class="ttd-label">Diperiksa</div>
                </div>
                <div class="ttd-box">
                    <div class="ttd-line"></div>
                    <div class="ttd-label">Diketahui</div>
                </div>
            </div>
        </div>

    </div>{{-- .half --}}

    @endfor

</div>{{-- .page --}}

</body>
</html>
