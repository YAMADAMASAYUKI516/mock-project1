<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'payment_method' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $profile = $user->profile;
            $itemId = $this->route('item')->id;

            $postal = session("shipping_postal_code_{$itemId}") ?? optional($profile)->postal_code;
            $address1 = session("shipping_address1_{$itemId}") ?? optional($profile)->address_line1;

            if (empty($postal) || empty($address1)) {
                $validator->errors()->add('shipping_address', '配送先を入力してください。');
            }
        });
    }
}
