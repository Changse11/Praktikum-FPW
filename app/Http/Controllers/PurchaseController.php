<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->latest('purchase_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('purchase.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $products = Product::orderBy('name')->get(['id', 'code', 'name', 'unit']);

        return view('purchase.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $data = $request->validated();

        $purchase = DB::transaction(function () use ($data, $request) {
            $total = 0;
            $baris = [];

            foreach ($data['items'] as $item) {
                $subtotal = $item['qty'] * $item['cost_price'];
                $total += $subtotal;

                $baris[] = [
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $subtotal,
                ];
            }

            // Nomor referensi sementara, lalu diganti memakai id agar pasti unik.
            // (koreksi) pola count()+1 di buku rawan bentrok kalau dua orang menyimpan bersamaan.
            $purchase = Purchase::create([
                'reference_number' => 'TMP-'.Str::uuid(),
                'supplier_id' => $data['supplier_id'],
                'user_id' => $request->user()->id,
                'purchase_date' => $data['purchase_date'],
                'total' => $total,
                'note' => $data['note'] ?? null,
            ]);

            $purchase->update([
                'reference_number' => sprintf(
                    'PO-%s-%04d',
                    $purchase->purchase_date->format('Ymd'),
                    $purchase->id
                ),
            ]);

            $purchase->details()->createMany($baris);

            // Stok bertambah sebesar qty yang dibeli. increment() berjalan
            // sebagai UPDATE ... SET stock = stock + n, jadi aman dari bentrok.
            foreach ($baris as $b) {
                Product::whereKey($b['product_id'])->increment('stock', $b['qty']);
            }

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Pembelian '.$purchase->reference_number.' berhasil dicatat dan stok produk sudah bertambah.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'details.product']);

        return view('purchase.show', compact('purchase'));
    }
}