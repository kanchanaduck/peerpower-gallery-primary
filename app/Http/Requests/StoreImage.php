<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'files' => 'required|max:10240|mimes:png,jpg,jpeg',
        ];
    }
    public function messages()
    {
        return [
            'files.required' => 'File is required.',
            'files.max'  => 'File size exceeded.',
            'files.mimes'  => 'File type not supported.',
        ];
    }
}
