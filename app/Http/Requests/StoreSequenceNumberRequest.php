<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSequenceNumberRequest extends FormRequest
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
            'shop_id'       => 'nullable|exists:shops,id',
            'type'          => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('sequence_numbers')->where(function ($query) {
                    return $query->where('shop_id', $this->shop_id);
                }),
            ],
            'prefix'        => 'nullable|string|max:50',
            'suffix'        => 'nullable|string|max:50',
            'current_number'=> 'nullable|integer|min:0',
            'padding'       => 'nullable|integer|min:1|max:20',
            'description'   => 'nullable|string',
        ];
    }
}
