<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Detail Pembelian {{ $purchase->reference_number }}</h2></x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <x-card>
                <dl class="mb-6 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">No. Referensi</dt>
                        <dd class="font-mono font-semibold">{{ $purchase->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tanggal Pembelian</dt>
                        <dd>{{ $purchase->purchase_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Supplier</dt>
                        <dd>{{ $purchase->supplier->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Dicatat Oleh</dt>
                        <dd>{{ $purchase->user->name }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500">Catatan</dt>
                        <dd>{{ $purchase->note ?? '-' }}</dd>
                    </div>
                </dl>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-500">
                            <th class="py-2">Produk</th>
                            <th class="py-2 text-right">Qty</th>
                            <th class="py-2 text-right">Harga Beli</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->details as $detail)
                            <tr class="border-b border-gray-100">
                                <td class="py-2">{{ $detail->product->code }} - {{ $detail->product->name }}</td>
                                <td class="py-2 text-right">{{ $detail->qty }} {{ $detail->product->unit }}</td>
                                <td class="py-2 text-right">Rp{{ number_format($detail->cost_price, 0, ',', '.') }}</td>
                                <td class="py-2 text-right">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="pt-3 text-right font-semibold">Total</td>
                            <td class="pt-3 text-right font-semibold">Rp{{ number_format($purchase->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-6">
                    <a href="{{ route('purchases.index') }}" class="text-indigo-600">&larr; Kembali ke riwayat</a>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>