<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->invoice_number }}</title>

    <style>
        /* Ukuran kertas thermal 58mm. Tinggi dihitung dari jumlah item supaya
           hasil cetak tidak menyisakan kertas kosong yang panjang. */
        @page {
            size: 58mm {{ 80 + $items->count() * 12 }}mm;
            margin: 0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #e5e7eb;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
        }

        /* Tampilan di layar: kertas 58mm diletakkan di tengah halaman */
        .toolbar { text-align: center; padding: 16px 0 0; }
        .toolbar button,
        .toolbar a {
            display: inline-block;
            margin: 0 4px;
            padding: 8px 16px;
            border: 0;
            border-radius: 4px;
            background: #4f46e5;
            color: #fff;
            font: 14px sans-serif;
            text-decoration: none;
            cursor: pointer;
        }
        .toolbar a { background: #6b7280; }

        /* Lebar kertas 58mm, area cetak efektif +-48mm (padding kiri-kanan 5mm) */
        .struk {
            width: 58mm;
            margin: 16px auto;
            padding: 4mm 5mm;
            background: #fff;
            font-size: 11px;
            line-height: 1.4;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        .tengah { text-align: center; }
        .tebal { font-weight: bold; }
        .judul { font-size: 14px; font-weight: bold; }
        .garis { border-top: 1px dashed #000; margin: 6px 0; }
        .baris { display: flex; justify-content: space-between; gap: 4px; }
        .nama-item { overflow-wrap: anywhere; }

        /* Hanya berlaku saat dicetak */
        @media print {
            html, body { width: 58mm; background: #fff; }
            .no-print { display: none !important; }
            .struk {
                width: 58mm;
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button type="button" onclick="window.print()">Cetak Struk</button>
        <a href="{{ route('dashboard') }}">Kembali</a>
    </div>

    <div class="struk">
        <div class="tengah">
            <div class="judul">{{ config('app.name') }}</div>
            <div>Struk Pembelian</div>
        </div>

        <div class="garis"></div>

        <div>No : {{ $transaction->invoice_number }}</div>
        <div>Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        <div>Kasir: {{ $kasir?->name ?? '-' }}</div>

        <div class="garis"></div>

        @foreach ($items as $item)
            <div class="nama-item">{{ $item->name }}</div>
            <div class="baris">
                <span>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="garis"></div>

        <div class="baris tebal">
            <span>TOTAL</span>
            <span>Rp{{ number_format($transaction->total, 0, ',', '.') }}</span>
        </div>
        <div class="baris">
            <span>Bayar</span>
            <span>Rp{{ number_format($transaction->pay, 0, ',', '.') }}</span>
        </div>
        <div class="baris">
            <span>Kembali</span>
            <span>Rp{{ number_format($transaction->change, 0, ',', '.') }}</span>
        </div>

        <div class="garis"></div>

        <div class="tengah">
            Terima kasih,<br>
            selamat berbelanja kembali
        </div>
    </div>
</body>
</html>