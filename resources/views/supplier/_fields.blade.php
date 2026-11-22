{{-- Kolom form yang dipakai bersama oleh halaman tambah dan ubah supplier --}}
<div class="mb-4">
    <label for="name" class="block text-sm font-medium mb-1">Nama Supplier</label>
    <input type="text" id="name" name="name" value="{{ old('name', $supplier?->name) }}"
        class="w-full rounded border border-gray-300 px-3 py-2" placeholder="Contoh: CV Sumber Pangan">
    @error('name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="phone" class="block text-sm font-medium mb-1">Telepon</label>
    <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier?->phone) }}"
        class="w-full rounded border border-gray-300 px-3 py-2" placeholder="Contoh: 081234567890">
    @error('phone')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="address" class="block text-sm font-medium mb-1">Alamat</label>
    <textarea id="address" name="address" rows="3"
        class="w-full rounded border border-gray-300 px-3 py-2">{{ old('address', $supplier?->address) }}</textarea>
    @error('address')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>