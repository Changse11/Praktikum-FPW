<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Transaksi Saya</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">No. Invoice</th>
                            <th class="py-2 pr-4">Waktu</th>
                            <th class="py-2 pr-4 text-right">Item</th>
                            <th class="py-2 pr-4 text-right">Total</th>
                            <th class="py-2 pr-4 text-right">Bayar</th>
                            <th class="py-2 pr-4 text-right">Kembali</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trx)
                            <tr class="border-b">
                                <td class="py-2 pr-4">{{ $trx->invoice_number }}</td>
                                <td class="py-2 pr-4">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                <td class="py-2 pr-4 text-right">{{ $trx->details_count }}</td>
                                <td class="py-2 pr-4 text-right">Rp{{ number_format($trx->total, 0, ',', '.') }}</td>
                                <td class="py-2 pr-4 text-right">Rp{{ number_format($trx->pay, 0, ',', '.') }}</td>
                                <td class="py-2 pr-4 text-right">Rp{{ number_format($trx->change, 0, ',', '.') }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('pos.struk', $trx) }}" class="text-indigo-600 hover:underline">Lihat Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-500">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $transactions->links() }}</div>
            </x-card>
        </div>
    </div>
</x-app-layout>