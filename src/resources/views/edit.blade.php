@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="edit-profile">
    <h2 class="edit-profile__title">プロフィール設定</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="edit-profile__form">
        @csrf

        {{-- アバター --}}
        <div class="edit-profile__avatar-section">
            <div class="edit-profile__avatar-circle">
                <img
                    id="preview"
                    src="{{ $profile && $profile->avatar_path ? asset('storage/' . $profile->avatar_path) : asset('images/avatar_placeholder.png') }}"
                    class="edit-profile__avatar-image"
                >
            </div>
            <label class="edit-profile__image-button">
                画像を選択する
                <input type="file" id="image" name="avatar" accept="image/*" hidden>
            </label>
        </div>
        @error('avatar')
            <div class="edit-profile__error">{{ $message }}</div>
        @enderror

        {{-- ユーザー名 --}}
        <div class="edit-profile__field">
            <label for="name" class="edit-profile__label">ユーザー名</label>
            <input type="text" id="name" name="name" class="edit-profile__input" value="{{ old('name', $user->name) }}">
        </div>
        @error('name')
            <div class="edit-profile__error">{{ $message }}</div>
        @enderror

        {{-- 郵便番号 --}}
        <div class="edit-profile__field">
            <label for="postal_code" class="edit-profile__label">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="edit-profile__input" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
        </div>
        @error('postal_code')
            <div class="edit-profile__error">{{ $message }}</div>
        @enderror

        {{-- 住所 --}}
        <div class="edit-profile__field">
            <label for="address" class="edit-profile__label">住所</label>
            <input type="text" id="address" name="address_line1" class="edit-profile__input" value="{{ old('address_line1', $profile->address_line1 ?? '') }}">
        </div>
        @error('address_line1')
            <div class="edit-profile__error">{{ $message }}</div>
        @enderror

        {{-- 建物名 --}}
        <div class="edit-profile__field">
            <label for="building_name" class="edit-profile__label">建物名</label>
            <input type="text" id="building_name" name="address_line2" class="edit-profile__input" value="{{ old('address_line', $profile->address_line2 ?? '') }}">
        </div>

        {{-- 送信ボタン --}}
        <button type="submit" class="edit-profile__submit-button">更新する</button>
    </form>
</div>
@endsection

@section('js')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
