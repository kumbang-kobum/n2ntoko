<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Stok — {{ $product->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #fff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            padding: 12mm 13mm 10mm;
        }

        /* ── Header ── */
        .header { text-align: center; margin-bottom: 8px; }
        .header .toko-nama { font-size: 16px; font-weight: bold; }
        .header .toko-sub  { font-size: 11px; margin-top: 2px; }

        .divider-thick { border: none; border-top: 2px solid #000; margin: 7px 0 4px; }
        .divider-thin  { border: none; border-top: 1px solid #000; margin: 3px 0 8px; }

        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        /* ── Info produk ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 20px;
            font-size: 11px;
            margin-bottom: 10px;
            padding: 7px 10px;
            border: 1px solid #000;
        }
        .info-row { display: flex; gap: 4px; }
        .info-label { width: 90px; flex-shrink: 0; }
        .info-value { font-weight: bold; }

        /* ── Tabel ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        thead th {
            background: #fff;
            color: #000;
            padding: 5px 5px;
            text-align: center;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid #000;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        thead th.left { text-align: left; }

        tbody tr { border-bottom: 1px solid #000; }
        tbody tr.saldo-row { font-style: italic; }
        tfoot tr { border-top: 2px solid #000; }

        td {
            padding: 4px 5px;
            vertical-align: middle;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }
        td:first-child { border-left: 1px solid #000; }
        td:last-child  { border-right: 1px solid #000; }

        .td-no     { text-align: center; width: 24px; font-size: 10px; }
        .td-tgl    { width: 56px; white-space: nowrap; }
        .td-ket    { }
        .td-ket-sub { font-size: 10px; }
        .td-tipe   { text-align: center; width: 52px; }
        .td-angka  { text-align: right; width: 52px; font-weight: bold; }
        .td-saldo  { text-align: right; width: 58px; font-weight: bold; }
        .td-cek    { text-align: center; width: 30px; }

        .cek-box {
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            display: inline-block;
        }

        .badge {
            display: inline-block;
            font-size: 10px;
            font-weight: bold;
        }

        tfoot td {
            padding: 5px 5px;
            font-weight: bold;
        }
        .tfoot-label { text-align: right; }
        .tfoot-val   { text-align: right; }

        /* ── Ringkasan ── */
        .summary {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            font-size: 11px;
        }
        .summary-box {
            flex: 1;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }
        .summary-box .s-label { margin-bottom: 2px; }
        .summary-box .s-val   { font-size: 13px; font-weight: bold; }
        .summary-box .s-unit  { font-size: 10px; }

        /* ── Footer TTD ── */
        .footer-ttd {
            display: flex;
            justify-content: flex-end;
            gap: 30px;
            margin-top: 16px;
        }
        .ttd-box { text-align: center; width: 110px; font-size: 11px; }
        .ttd-line { border-bottom: 1px solid #000; height: 36px; margin: 16px 4px 4px; }

        .footer-note {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #000;
            font-size: 10px;
            text-align: center;
        }

        /* ── Print controls ── */
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
            border: 1px solid #000;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-print { background: #000; color: #fff; }
        .btn-back  { background: #fff; color: #000; text-decoration: none;
                     display: inline-flex; align-items: center; }

        @media print {
            body { background: #fff; }
            .print-controls { display: none; }
            .page { margin: 0; padding: 8mm 10mm 6mm; }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <a href="{{ route('kartu-stok.show', array_filter(['product' => $product->id, 'dari' => $dari, 'sampai' => $sampai])) }}"
       class="btn btn-back">← Kembali</a>
    <button class="btn btn-print" onclick="window.print()">🖨 Cetak</button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="toko-nama">{{ $settings['toko_nama'] ?? 'N2N Toko' }}</div>
        @if(!empty($settings['toko_alamat']))
        <div class="toko-sub">
            {{ $settings['toko_alamat'] }}{{ !empty($settings['toko_kota']) ? ', '.$settings['toko_kota'] : '' }}
            @if(!empty($settings['toko_telepon'])) &nbsp;|&nbsp; Telp. {{ $settings['toko_telepon'] }} @endif
        </div>
        @endif
    </div>
    <hr class="divider-thick">
    <div class="doc-title">Kartu Stok Barang</div>
    <hr class="divider-thin">

    {{-- Info Produk --}}
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Nama Produk</span>
            <span class="info-value">{{ $product->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-value">
                {{ $dari   ? \Carbon\Carbon::parse($dari)->translatedFormat('d M Y')   : 'Semua' }}
                —
                {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') : 'Sekarang' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">SKU / Kode</span>
            <span class="info-value" style="font-family:monospace">{{ $product->sku }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Stok Saat Ini</span>
            <span class="info-value">{{ number_format($product->stock_qty, 0, ',', '.') }} {{ $product->baseUnit?->unit_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Satuan Dasar</span>
            <span class="info-value">{{ $product->baseUnit?->unit_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kategori</span>
            <span class="info-value">{{ $product->category?->name ?? '—' }}</span>
        </div>
    </div>

    {{-- Tabel --}}
    @php
        $totalMasuk  = 0;
        $totalKeluar = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width:24px">No</th>
                <th class="left" style="width:56px">Tanggal</th>
                <th class="left">Keterangan</th>
                <th style="width:52px">Tipe</th>
                <th style="width:52px; color:#a7f3d0">Masuk</th>
                <th style="width:52px; color:#fca5a5">Keluar</th>
                <th style="width:58px">Saldo</th>
                <th style="width:30px">Cek ✓</th>
            </tr>
        </thead>
        <tbody>

            {{-- Baris saldo awal --}}
            @if($ledgers->isNotEmpty())
            @php $saldoAwal = $ledgers->first()->stock_before; @endphp
            <tr class="saldo-row">
                <td class="td-no">—</td>
                <td class="td-tgl" style="font-size:9px; color:#666">
                    {{ $dari
                        ? \Carbon\Carbon::parse($dari)->format('d/m/Y')
                        : \Carbon\Carbon::parse($ledgers->first()->created_at)->format('d/m/Y') }}
                </td>
                <td class="td-ket" style="color:#777">Saldo awal periode</td>
                <td class="td-tipe"></td>
                <td class="td-angka"></td>
                <td class="td-angka"></td>
                <td class="td-saldo" style="color:#555">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                <td class="td-cek"></td>
            </tr>
            @endif

            @foreach($ledgers as $i => $row)
            @php
                $qty    = (float) $row->qty_base;
                $masuk  = null;
                $keluar = null;

                if ($row->movement_type === 'in') {
                    $masuk = abs($qty);
                    $totalMasuk += $masuk;
                } elseif ($row->movement_type === 'out') {
                    $keluar = abs($qty);
                    $totalKeluar += $keluar;
                } else {
                    if ($qty >= 0) { $masuk  = $qty;       $totalMasuk  += $qty; }
                    else           { $keluar = abs($qty);   $totalKeluar += abs($qty); }
                }

                $refLabel = match($row->reference_type) {
                    'purchase'    => 'Pembelian',
                    'sale'        => 'Penjualan',
                    'stok_opname' => 'Stok Opname',
                    default       => ucfirst($row->reference_type),
                };

                $badgeClass = match(true) {
                    $row->movement_type === 'in'                         => 'badge-in',
                    $row->movement_type === 'out'                        => 'badge-out',
                    $row->movement_type === 'adjustment' && $qty >= 0    => 'badge-adj-pos',
                    default                                              => 'badge-adj-neg',
                };
                $badgeLabel = match(true) {
                    $row->movement_type === 'in'                         => 'Masuk',
                    $row->movement_type === 'out'                        => 'Keluar',
                    $row->movement_type === 'adjustment' && $qty >= 0    => 'Koreksi +',
                    default                                              => 'Koreksi −',
                };
            @endphp
            <tr>
                <td class="td-no">{{ $i + 1 }}</td>
                <td class="td-tgl">
                    {{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}<br>
                    <span style="font-size:8.5px; color:#999">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }}</span>
                </td>
                <td class="td-ket">
                    <span style="font-weight:600">{{ $refLabel }}</span>
                    @if($row->reference_id)
                    <span class="td-ket-sub">&nbsp;#{{ $row->reference_id }}</span>
                    @endif
                    @if($row->notes)
                    <br><span class="td-ket-sub">{{ $row->notes }}</span>
                    @endif
                </td>
                <td class="td-tipe">
                    <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                </td>
                <td class="td-angka">
                    @if($masuk !== null)
                    <span class="text-green">+{{ number_format($masuk, 0, ',', '.') }}</span>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="td-angka">
                    @if($keluar !== null)
                    <span class="text-red">{{ number_format($keluar, 0, ',', '.') }}</span>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="td-saldo">{{ number_format($row->stock_after, 0, ',', '.') }}</td>
                <td class="td-cek"><span class="cek-box"></span></td>
            </tr>
            @endforeach
        </tbody>

        @if($ledgers->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="4" class="tfoot-label">Total Periode</td>
                <td class="tfoot-val text-green">+{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                <td class="tfoot-val text-red">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                <td class="tfoot-val">{{ number_format($ledgers->last()->stock_after, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Ringkasan --}}
    @if($ledgers->isNotEmpty())
    <div class="summary">
        <div class="summary-box green">
            <div class="s-label">Total Masuk</div>
            <div class="s-val text-green">{{ number_format($totalMasuk, 0, ',', '.') }}</div>
            <div class="s-unit">{{ $product->baseUnit?->unit_name }}</div>
        </div>
        <div class="summary-box red">
            <div class="s-label">Total Keluar</div>
            <div class="s-val text-red">{{ number_format($totalKeluar, 0, ',', '.') }}</div>
            <div class="s-unit">{{ $product->baseUnit?->unit_name }}</div>
        </div>
        <div class="summary-box blue">
            <div class="s-label">Saldo Akhir</div>
            <div class="s-val" style="color:#1e40af">{{ number_format($ledgers->last()->stock_after, 0, ',', '.') }}</div>
            <div class="s-unit">{{ $product->baseUnit?->unit_name }}</div>
        </div>
        <div class="summary-box" style="flex:2; text-align:left">
            <div class="s-label" style="margin-bottom:4px">Catatan Pemeriksaan</div>
            <div style="border-bottom: 1px solid #ccc; height: 14px; margin-bottom:5px;"></div>
            <div style="border-bottom: 1px solid #ccc; height: 14px;"></div>
        </div>
    </div>
    @endif

    {{-- TTD --}}
    <div class="footer-ttd">
        <div class="ttd-box">
            <div class="ttd-line"></div>
            <div class="ttd-label">Diperiksa oleh</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-line"></div>
            <div class="ttd-label">Diketahui oleh</div>
        </div>
    </div>

    <div class="footer-note">
        Dicetak: {{ now()->format('d/m/Y H:i') }}
        &nbsp;|&nbsp; {{ $settings['toko_nama'] ?? 'N2N Toko' }}
        &nbsp;|&nbsp; {{ $product->name }} ({{ $product->sku }})
        &nbsp;|&nbsp; {{ $ledgers->count() }} transaksi
    </div>

</div>
</body>
</html>
