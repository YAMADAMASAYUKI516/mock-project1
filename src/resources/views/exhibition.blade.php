@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection

@section('content')
<form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="exhibition-form">
    @csrf

    <div class="exhibition-form__title">商品の出品</div>

    <div class="exhibition-form__section">
        <label class="exhibition-form__label">商品画像</label>

        <label for="imageInput" class="exhibition-form__image-box">
            <input type="file" id="imageInput" name="image_path" class="exhibition-form__file-input" accept="image/png, image/jpeg">
            <span id="imageText" class="exhibition-form__image-button">画像を選択する</span>

            <img id="imagePreview" class="exhibition-form__image-preview" alt="Preview" style="display: none;">
        </label>

        @error('image_path')
            <div class="exhibition-form__error">{{ $message }}</div>
        @enderror
    </div>

    <div class="exhibition-form__section">
        <label class="exhibition-form__sub-title">商品の詳細</label>
        <hr>

        <div class="exhibition-form__sub-label">カテゴリー</div>
        <div class="exhibition-form__categories">
            @foreach ($categories as $category)
                <label class="exhibition-form__category">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
                    >
                    <span>{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
        @error('category_ids')
            <div class="exhibition-form__error">{{ $message }}</div>
        @enderror

        <div class="exhibition-form__sub-label">商品の状態</div>
        <select name="condition_id" class="exhibition-form__select" required>
            <option value="" selected hidden>選択してください</option>
            @foreach ($conditions as $condition)
                <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                    {{ $condition->condition }}
                </option>
            @endforeach
        </select>
        @error('condition_id')
            <div class="exhibition-form__error">{{ $message }}</div>
        @enderror
    </div>

    <hr>

    <div class="exhibition-form__section">
        <label class="exhibition-form__sub-label">商品名</label>
        <input
            type="text"
            name="name"
            class="exhibition-form__input"
            value="{{ old('name') }}"
        >
        @error('name')
            <div class="exhibition-form__error">{{ $message }}</div>
        @enderror

        <label class="exhibition-form__sub-label">ブランド名</label>
        <input
            type="text"
            name="brand"
            class="exhibition-form__input"
            value="{{ old('brand') }}"
        >

        <label class="exhibition-form__sub-label">商品の説明</label>
        <textarea name="description" class="exhibition-form__textarea">{{ old('description') }}</textarea>
        @error('description')
            <div class="exhibition-form__error">{{ $message }}</div>
        @enderror

        <div class="exhibition-form__price-group">
            <label class="exhibition-form__sub-label">販売価格</label>
            <div class="exhibition-form__price-input-wrap">
                <span class="exhibition-form__price-symbol">¥</span>
                <input
                    type="number"
                    name="price"
                    class="exhibition-form__price-input"
                    value="{{ old('price') }}"
                >
            </div>
            @error('price')
                <div class="exhibition-form__error">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="exhibition-form__button-wrap">
        <button type="submit" class="exhibition-form__submit">出品する</button>
    </div>
</form>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const text = document.getElementById('imageText');
        const box = document.querySelector('.exhibition-form__image-box');

        input.addEventListener('change', function () {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    text.style.display = 'none';

                    box.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                preview.src = '';
                text.style.display = 'block';

                box.classList.remove('has-image');
            }
        });
    });
</script>
@endsection


