<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code'          => 'required|string|max:255',
            'name'          => 'required|string|max:255',
            'name_kh'       => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:255',
            'cost_price'    => 'required|numeric',
            'selling_price' => 'required|numeric',
            'uom_id'        => 'required|exists:uoms,id',
            'category_code' => 'required|exists:categories,code',
            'thumbnail'     => 'nullable|string|exists:attachments,file_path',
            'is_active'     => 'nullable|boolean|default:true',
        ];
    }
}
