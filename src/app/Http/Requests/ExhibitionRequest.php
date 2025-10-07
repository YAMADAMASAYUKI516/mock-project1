<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'image_path' => ['required', 'image', 'mimes:jpeg,png'],
            'category_ids' => ['required'],
            'condition_id' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'image_path.required' => '商品画像をアップロードしてください',
            'image_path.image' => 'アップロードされたファイルは画像ではありません',
            'image_path.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
            'category_ids.required' => 'カテゴリーを1つ以上選択してください',
            'condition_id.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格は数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}
