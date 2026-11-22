<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiUjiSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('transactions')->where('invoice_number', 'INV-20261112-001')->exists()) {
            $this->command->warn('Data uji sudah ada, seeder dilewati.');
            return;
        }

        $adminId = DB::table('users')->where('email', 'admin@barokahmart.test')->value('id');
        $kasirId = DB::table('users')->where('email', 'kasir@barokahmart.test')->value('id');

        $produk = DB::table('products')
            ->whereIn('code', ['MG-001', 'BD-001', 'BD-002', 'BD-003'])
            ->get()
            ->keyBy('code');

        if ($produk->count() !== 4) {
            $this->command->error('Produk MG-001, BD-001, BD-002, BD-003 harus ada di database.');
            return;
        }

        // [invoice, waktu, user_id, [kode produk => qty]]
        $data = [
            ['INV-20261112-001', '2026-11-12 09:15:00', $kasirId, ['MG-001' => 3, 'BD-001' => 2]],
            ['INV-20261113-001', '2026-11-13 10:30:00', $kasirId, ['MG-001' => 5]],
            ['INV-20261114-001', '2026-11-14 08:45:00', $adminId, ['MG-001' => 2, 'BD-002' => 1]],
            ['INV-20261115-001', '2026-11-15 16:20:00', $kasirId, ['MG-001' => 8]],
            ['INV-20261116-001', '2026-11-16 11:05:00', $kasirId, ['MG-001' => 1, 'BD-003' => 2]],
            ['INV-20261117-001', '2026-11-17 09:40:00', $kasirId, ['MG-001' => 4]],
            ['INV-20261117-002', '2026-11-17 15:10:00', $kasirId, ['MG-001' => 4]],
            ['INV-20261118-001', '2026-11-18 13:25:00', $kasirId, ['MG-001' => 6, 'BD-001' => 5]],
            ['INV-20261119-001', '2026-11-19 17:00:00', $adminId, ['MG-001' => 1]],
        ];

        foreach ($data as [$invoice, $waktu, $userId, $items]) {
            $total = 0;
            $baris = [];

            foreach ($items as $kode => $qty) {
                $harga = $produk[$kode]->price;
                $subtotal = $harga * $qty;
                $total += $subtotal;

                $baris[] = [
                    'product_id' => $produk[$kode]->id,
                    'qty'        => $qty,
                    'price'      => $harga,
                    'subtotal'   => $subtotal,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ];
            }

            $bayar = (int) (ceil($total / 5000) * 5000);

            $trxId = DB::table('transactions')->insertGetId([
                'invoice_number' => $invoice,
                'user_id'        => $userId,
                'total'          => $total,
                'pay'            => $bayar,
                'change'         => $bayar - $total,
                'created_at'     => $waktu,
                'updated_at'     => $waktu,
            ]);

            foreach ($baris as &$b) {
                $b['transaction_id'] = $trxId;
            }
            unset($b);

            DB::table('transaction_details')->insert($baris);
        }

        $this->command->info('Data transaksi uji berhasil dibuat.');
    }
}