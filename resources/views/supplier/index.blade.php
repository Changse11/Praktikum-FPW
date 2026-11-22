<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Data Supplier</h2></x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <x-card>
                <div class="mb-4 flex justify-between">
                    <a href="{{ route('suppliers.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm text-white">
                        + Tambah Supplier
                    </a>
                    <a href="{{ route('purchases.index') }}" class="py-2 text-sm text-indigo-600">Riwayat Pembelian &rarr;</a>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-500">
                            <th class="py-2">Nama</th>
                            <th class="py-2">Telepon</th>
                            <th class="py-2">Alamat</th>
                            <th class="py-2 text-right">Jumlah Pembelian</th>
                            <th class="py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr class="border-b border-gray-100">
                                <td class="py-2">{{ $supplier->name }}</td>
                                <td class="py-2">{{ $supplier->phone ?? '-' }}</td>
                                <td class="py-2">{{ $supplier->address ?? '-' }}</td>
                                <td class="py-2 text-right">{{ $supplier->purchases_count }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600">Ubah</a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-red-600">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Belum ada supplier.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $suppliers->links() }}</div>
            </x-card>
        </div>
    </div>
</x-app-layout>