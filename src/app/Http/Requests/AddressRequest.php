<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'shipping_postal_code'    => ['required', 'string', 'size:8'],
            'shipping_address1'  => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'shipping_postal_code.required'    => '郵便番号を入力してください。',
            'shipping_postal_code.size'        => '郵便番号はハイフンを入れて8文字で入力してください。',
            'shipping_address1.required'       => '住所を入力してください。',
        ];
    }
}
