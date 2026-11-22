<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Riwayat Pembelian Stok</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <x-card>
                <div class="mb-4 flex justify-between">
                    <a href="{{ route('purchases.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm text-white">
                        + Catat Pembelian
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="py-2 text-sm text-indigo-600">Data Supplier &rarr;</a>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-500">
                            <th class="py-2">No. Referensi</th>
                            <th class="py-2">Tanggal</th>
                            <th class="py-2">Supplier</th>
                            <th class="py-2">Dicatat Oleh</th>
                            <th class="py-2 text-right">Total</th>
                            <th class="py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $purchase)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 font-mono">{{ $purchase->reference_number }}</td>
                                <td class="py-2">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td class="py-2">{{ $purchase->supplier->name }}</td>
                                <td class="py-2">{{ $purchase->user->name }}</td>
                                <td class="py-2 text-right">Rp{{ number_format($purchase->total, 0, ',', '.') }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-600">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">Belum ada pembelian stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $purchases->links() }}</div>
            </x-card>
        </div>
    </div>
</x-app-layout>