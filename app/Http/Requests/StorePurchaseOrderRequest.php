<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'po_date'               => 'nullable|date',
            'note'                  => 'nullable|string',
            'shop_id'               => 'required|exists:shops,id',
            'status'                => 'nullable|in:pending,approved,rejected,closed',
            'items'                 => 'required|array|min:1',
            'items.*.product_code'  => 'required|exists:products,code',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.price'         => 'required|numeric|min:0',
            'items.*.discount'      => 'nullable|numeric|min:0',
        ];
    }
}
