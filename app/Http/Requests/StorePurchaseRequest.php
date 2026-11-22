<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'distinct',
                // produk yang sudah di-soft delete tidak boleh dibeli lagi
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.cost_price' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'supplier',
            'purchase_date' => 'tanggal pembelian',
            'note' => 'catatan',
            'items' => 'daftar barang',
            'items.*.product_id' => 'produk',
            'items.*.qty' => 'jumlah',
            'items.*.cost_price' => 'harga beli',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'integer' => ':attribute harus berupa angka bulat.',
            'date' => ':attribute tidak valid.',
            'note.max' => 'Catatan maksimal 500 karakter.',
            'items.required' => 'Minimal satu barang harus diisi.',
            'items.min' => 'Minimal satu barang harus diisi.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh dipilih dua kali.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
            'items.*.cost_price.min' => 'Harga beli minimal Rp1.',
            'purchase_date.before_or_equal' => 'Tanggal pembelian tidak boleh melebihi hari ini.',
        ];
    }
}