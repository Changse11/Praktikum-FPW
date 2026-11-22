<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $omzetHariIni = Transaction::whereDate('created_at', today())->sum('total');
        $omzetKemarin = Transaction::whereDate('created_at', today()->subDay())->sum('total');
        $transaksiHariIni = Transaction::whereDate('created_at', today())->count();

        // (koreksi) batas stok menipis diambil dari config/pos.php (Bab 8),
        // bukan angka 10 yang ditulis langsung seperti di buku.
        $batasMenipis = config('pos.low_stock_threshold', 10);
        $produkMenipis = Product::where('stock', '<', $batasMenipis)->count();

        // Tren penjualan 7 hari terakhir, dikelompokkan per tanggal.
        // Hasilnya dijadikan peta: ['2026-11-20' => '54500', ...]
        $tren = Transaction::selectRaw('DATE(created_at) as tanggal, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->pluck('total', 'tanggal');

        // Selalu 7 titik: tanggal yang tidak punya transaksi diisi 0.
        $hari = collect(range(6, 0))->map(fn ($i) => now()->subDays($i));

        $labelTren = $hari->map(fn ($d) => $d->format('d M'))->values()->all();
        $dataTren = $hari->map(fn ($d) => (int) ($tren[$d->format('Y-m-d')] ?? 0))->values()->all();

        // Lima produk terlaris berdasarkan akumulasi qty pada transaction_details.
        $produkTerlaris = Product::select('products.name')
            ->selectRaw('SUM(transaction_details.qty) as total_qty')
            ->join('transaction_details', 'transaction_details.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'omzetHariIni',
            'omzetKemarin',
            'transaksiHariIni',
            'produkMenipis',
            'labelTren',
            'dataTren',
            'produkTerlaris'
        ));
    }
}