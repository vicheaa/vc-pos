<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
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
            'file'      => 'required|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,txt|max:5120', // 10MB max
            'attach_to' => 'required|string|max:255'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    // public function messages(): array
    // {
    //     return [
    //         'file.required'         => 'Please select a file to upload.',
    //         'file.file'             => 'The uploaded file is not valid.',
    //         'file.mimes'            => 'File type not supported. Allowed types: jpeg, png, jpg, gif, pdf, doc, docx, xls, xlsx, txt.',
    //         'file.max'              => 'The file must not exceed 128 kilobytes.',
    //         'attach_to.required'    => 'The attach_to field is required.',
    //         'attach_to.string'      => 'The attach_to field must be a string.',
    //         'attach_to.max'         => 'The attach_to field must not exceed 255 characters.'
    //     ];
    // }
}
