<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosRequest;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'price', 'stock', 'unit']);

        return view('pos.index', compact('products'));
    }

    public function store(StorePosRequest $request)
    {
        try {
            $transaction = DB::transaction(function () use ($request) {
                $total = 0;
                $details = [];

                foreach ($request->items as $item) {
                    $qty = (int) $item['qty'];

                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $qty) {
                        throw new \DomainException(
                            "Stok {$product->name} tidak mencukupi (tersisa {$product->stock}, diminta {$qty})."
                        );
                    }

                    $subtotal = $product->price * $qty;
                    $total += $subtotal;

                    $details[] = [
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ];

                    $product->decrement('stock', $qty);
                }

                $pay = (int) $request->pay;

                if ($pay < $total) {
                    throw new \DomainException('Nominal bayar kurang dari total belanja.');
                }

                $prefix = 'INV-' . now()->format('Ymd') . '-';
                $last = Transaction::where('invoice_number', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderByDesc('invoice_number')
                    ->value('invoice_number');
                $next = $last ? ((int) substr($last, -4)) + 1 : 1;

                $trx = Transaction::create([
                    'invoice_number' => $prefix . str_pad($next, 4, '0', STR_PAD_LEFT),
                    'user_id' => auth()->id(),
                    'total' => $total,
                    'pay' => $pay,
                    'change' => $pay - $total,
                ]);

                $trx->details()->createMany($details);

                return $trx;
            });
        } catch (\DomainException $e) {
            return redirect()->route('pos.index')->with('error', $e->getMessage());
        }

        return redirect()->route('pos.struk', $transaction)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function struk(Transaction $transaction)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $transaction->user_id !== $user->id) {
            abort(403);
        }

        $transaction->load('details.product', 'user');

        return view('pos.struk', compact('transaction'));
    }

    public function history()
    {
        $transactions = Transaction::withCount('details')
            ->where('user_id', auth()->id())
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);

        return view('pos.history', compact('transactions'));
    }
}