<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function __invoke(Request $request, Transaction $transaction)
    {
        // Kasir hanya boleh melihat struk transaksinya sendiri, admin boleh melihat semuanya.
        if ($request->user()->role === 'kasir' && $transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        // Memakai query builder (bukan relasi Eloquent) supaya nama produk
        // tetap muncul walaupun produknya sudah di-soft delete setelah terjual.
        $items = DB::table('transaction_details')
            ->join('products', 'products.id', '=', 'transaction_details.product_id')
            ->where('transaction_details.transaction_id', $transaction->id)
            ->orderBy('transaction_details.id')
            ->get([
                'products.name',
                'transaction_details.qty',
                'transaction_details.price',
                'transaction_details.subtotal',
            ]);

        $kasir = User::find($transaction->user_id);

        return view('pos.struk', compact('transaction', 'items', 'kasir'));
    }
}