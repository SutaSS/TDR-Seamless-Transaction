<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /** Hanya user yang sudah login yang bisa buat order. */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Alamat pengiriman (satu string panjang sesuai migrasi)
            'shipping_address'  => ['required', 'string', 'max:1000'],
            'shipping_courier'  => ['required', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'payment_method'    => ['required', 'string', 'max:50'],

            // Item pesanan
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id'=> ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
            'shipping_courier.required' => 'Kurir pengiriman wajib dipilih.',
            'payment_method.required'   => 'Metode pembayaran wajib dipilih.',
            'items.required'            => 'Pesanan harus mengandung minimal 1 item.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.min'      => 'Jumlah item minimal 1.',
        ];
    }
}
