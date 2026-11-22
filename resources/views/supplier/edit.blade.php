<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Ubah Supplier</h2></x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('supplier._fields', ['supplier' => $supplier])

                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white">Simpan Perubahan</button>
                        <a href="{{ route('suppliers.index') }}" class="text-gray-600">Batal</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>