<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name'           => ['required', 'string'],
            'postal_code'    => ['required', 'string', 'size:8'],
            'address_line1'  => ['required', 'string'],
            'address_line2'  => ['nullable', 'string'],
            'avatar'         => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'           => 'ユーザー名を入力してください。',
            'postal_code.required'    => '郵便番号を入力してください。',
            'postal_code.size'        => '郵便番号はハイフンを入れて8文字で入力してください。',
            'address_line1.required'  => '住所を入力してください。',
            'avatar.image'            => '画像ファイルを選択してください。',
            'avatar.mimes'            => '拡張子は.jpegもしくは.pngでアップロードしてください。',
        ];
    }
}
