<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Jangan dobel kalau seeder dijalankan dua kali.
        if (DB::table('transactions')->where('invoice_number', 'like', 'INV-%-D%')->exists()) {
            $this->command->warn('Data transaksi uji sudah ada, seeder dilewati.');

            return;
        }

        $kasir = User::where('email', 'kasir@barokahmart.test')->firstOrFail();

        // Urutan produk dibuat tetap supaya hasil dashboard bisa diprediksi.
        $kode = ['BD-001', 'BD-002', 'BD-003', 'MG-001', 'TC-001'];
        $daftar = Product::whereIn('code', $kode)->get()
            ->sortBy(fn ($p) => array_search($p->code, $kode))
            ->values();

        // Cadangan kalau kode produk di databasemu beda.
        if ($daftar->count() < 3) {
            $daftar = Product::orderBy('id')->take(5)->get()->values();
        }

        // [berapa hari lalu, jam, [[nomor produk, qty], ...]]
        $penjualan = [
            [6, '09:15', [[0, 2], [3, 1]]],
            [6, '15:40', [[1, 1]]],
            [5, '10:05', [[0, 3], [2, 2]]],
            [4, '11:30', [[3, 2], [4, 1]]],
            [3, '08:50', [[1, 2], [2, 1]]],
            [3, '17:20', [[0, 1], [3, 1], [4, 2]]],
            [2, '13:10', [[3, 3]]],
            [1, '09:45', [[0, 2], [1, 1], [2, 1]]],
            [1, '16:05', [[3, 1]]],
            [0, '08:30', [[0, 4], [3, 1]]],
            [0, '10:20', [[1, 2], [4, 1]]],
            [0, '12:00', [[2, 3], [3, 2]]],
        ];

        DB::transaction(function () use ($penjualan, $daftar, $kasir) {
            foreach ($penjualan as $no => [$hariLalu, $jam, $items]) {
                $waktu = now()->subDays($hariLalu)->setTimeFromTimeString($jam)->format('Y-m-d H:i:s');

                $total = 0;
                $baris = [];

                foreach ($items as [$nomorProduk, $qty]) {
                    $produk = $daftar[$nomorProduk % $daftar->count()];
                    $subtotal = (int) $produk->price * $qty;
                    $total += $subtotal;

                    $baris[] = [
                        'product_id' => $produk->id,
                        'qty' => $qty,
                        'price' => (int) $produk->price,
                        'subtotal' => $subtotal,
                        'created_at' => $waktu,
                        'updated_at' => $waktu,
                    ];
                }

                $bayar = (int) (ceil($total / 10000) * 10000);

                $transactionId = DB::table('transactions')->insertGetId([
                    'invoice_number' => 'INV-'.substr($waktu, 0, 4).substr($waktu, 5, 2).substr($waktu, 8, 2)
                        .'-D'.str_pad($no + 1, 3, '0', STR_PAD_LEFT),
                    'user_id' => $kasir->id,
                    'total' => $total,
                    'pay' => $bayar,
                    'change' => $bayar - $total,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                foreach ($baris as $b) {
                    DB::table('transaction_details')->insert($b + ['transaction_id' => $transactionId]);
                }
            }
        });

        $this->command->info('12 transaksi uji berhasil dibuat.');
    }
}