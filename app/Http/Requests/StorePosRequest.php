<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // (tambahan) bawaan false -> 403
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => [
                'required',
                'distinct',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.qty' => 'required|integer|min:1',
            'pay' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'items.*.product_id.exists' => 'Ada produk di keranjang yang sudah tidak tersedia.',
            'items.*.product_id.distinct' => 'Produk yang sama muncul dua kali di keranjang.',
            'items.*.qty.min' => 'Jumlah beli minimal 1.',
            'pay.required' => 'Nominal bayar wajib diisi.',
            'pay.integer' => 'Nominal bayar harus berupa angka bulat.',
        ];
    }
}