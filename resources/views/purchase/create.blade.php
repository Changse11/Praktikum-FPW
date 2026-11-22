<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Catat Pembelian Stok</h2></x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                @if ($errors->any())
                    <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('purchases.store') }}" method="POST">
                    @csrf

                    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="supplier_id" class="mb-1 block text-sm font-medium">Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="w-full rounded border border-gray-300 px-3 py-2">
                                <option value="">-- Pilih supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="purchase_date" class="mb-1 block text-sm font-medium">Tanggal Pembelian</label>
                            <input type="date" id="purchase_date" name="purchase_date"
                                value="{{ old('purchase_date', now()->format('Y-m-d')) }}"
                                max="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded border border-gray-300 px-3 py-2">
                        </div>
                    </div>

                    <h3 class="mb-2 font-semibold">Barang yang Dibeli</h3>
                    <table class="mb-2 w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-gray-500">
                                <th class="py-2">Produk</th>
                                <th class="w-24 py-2">Qty</th>
                                <th class="w-40 py-2">Harga Beli (Rp)</th>
                                <th class="w-20 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="baris-barang"></tbody>
                    </table>

                    <button type="button" id="tambah-baris" class="mb-4 text-sm text-indigo-600">+ Tambah Baris</button>

                    <p class="mb-4 text-sm text-gray-600">
                        Perkiraan total: <span id="total-perkiraan" class="font-semibold">Rp0</span>
                    </p>

                    <div class="mb-4">
                        <label for="note" class="mb-1 block text-sm font-medium">Catatan (opsional)</label>
                        <textarea id="note" name="note" rows="2"
                            class="w-full rounded border border-gray-300 px-3 py-2">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white">Simpan Pembelian</button>
                        <a href="{{ route('purchases.index') }}" class="text-gray-600">Batal</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
        <script type="module">
            const produk = @json($products);
            const barisLama = Object.values(@json(old('items', [])));

            const tbody = document.getElementById('baris-barang');
            const labelTotal = document.getElementById('total-perkiraan');
            const kelas = 'w-full rounded border border-gray-300 px-2 py-1';
            let nomor = 0;

            function buatKolom(...isi) {
                const td = document.createElement('td');
                td.className = 'py-2 pr-2';
                td.append(...isi);
                return td;
            }

            function tambahBaris(data = {}) {
                const i = nomor++;

                const pilih = document.createElement('select');
                pilih.name = `items[${i}][product_id]`;
                pilih.className = kelas;
                pilih.add(new Option('-- Pilih produk --', ''));
                produk.forEach((p) => {
                    pilih.add(new Option(`${p.code} - ${p.name}`, p.id, false, String(data.product_id) === String(p.id)));
                });

                const qty = document.createElement('input');
                qty.type = 'number';
                qty.min = '1';
                qty.name = `items[${i}][qty]`;
                qty.value = data.qty ?? 1;
                qty.className = kelas;

                const harga = document.createElement('input');
                harga.type = 'number';
                harga.min = '1';
                harga.name = `items[${i}][cost_price]`;
                harga.value = data.cost_price ?? '';
                harga.className = kelas;

                const hapus = document.createElement('button');
                hapus.type = 'button';
                hapus.textContent = 'Hapus';
                hapus.className = 'hapus-baris text-red-600';

                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-100';
                tr.append(buatKolom(pilih), buatKolom(qty), buatKolom(harga), buatKolom(hapus));
                tbody.appendChild(tr);
            }

            function hitungTotal() {
                let total = 0;
                tbody.querySelectorAll('tr').forEach((tr) => {
                    const qty = Number(tr.querySelector('input[name$="[qty]"]').value) || 0;
                    const harga = Number(tr.querySelector('input[name$="[cost_price]"]').value) || 0;
                    total += qty * harga;
                });
                labelTotal.textContent = 'Rp' + total.toLocaleString('id-ID');
            }

            document.getElementById('tambah-baris').addEventListener('click', () => {
                tambahBaris();
                hitungTotal();
            });

            tbody.addEventListener('input', hitungTotal);

            tbody.addEventListener('click', (e) => {
                if (e.target.classList.contains('hapus-baris') && tbody.children.length > 1) {
                    e.target.closest('tr').remove();
                    hitungTotal();
                }
            });

            // Isi awal: pulihkan input lama kalau validasi gagal, kalau tidak mulai dengan satu baris kosong.
            if (barisLama.length > 0) {
                barisLama.forEach((b) => tambahBaris(b));
            } else {
                tambahBaris();
            }
            hitungTotal();
        </script>
    @endpush
</x-app-layout>