@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.store', ['item' => $item->id]) }}" method="POST">
    @csrf

    <input type="hidden" name="payment_method" value="{{ old('payment_method') }}" id="payment-method-hidden">

    {{-- 住所保持 --}}
    <input type="hidden" name="shipping_postal_code" value="{{ session("shipping_postal_code_{$item->id}") }}">
    <input type="hidden" name="shipping_address1" value="{{ session("shipping_address1_{$item->id}") }}">
    <input type="hidden" name="shipping_address2" value="{{ session("shipping_address2_{$item->id}") }}">

    <div class="purchase">
        <div class="purchase__inner">
            {{-- 左カラム --}}
            <div class="purchase__left">
                <div class="purchase__product">
                    <img src="{{ asset('storage/items-image/' . basename($item->image_path)) }}" alt="{{ $item->name }}" class="purchase__image">
                    <div class="purchase__details">
                        <h2 class="purchase__name">{{ $item->name }}</h2>
                        <p class="purchase__price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <hr>

                {{-- 支払い方法 --}}
                <div class="purchase__section">
                    <h3 class="purchase__section-title">支払い方法</h3>
                    <select name="payment_method" class="purchase__select" required>
                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }} hidden>選択してください</option>
                        <option value="コンビニ払い" {{ old('payment_method') === 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="カード支払い" {{ old('payment_method') === 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
                    </select>
                    @error('payment_method')
                        <div class="purchase__select-error">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                {{-- 配送先 --}}
                <div class="purchase__section">
                    <div class="purchase__section-header">
                        <h3 class="purchase__section-title">配送先</h3>
                        <a href="{{ route('address.edit', ['item_id' => $item->id]) }}" class="purchase__change-link">変更する</a>
                    </div>
                    <p class="purchase__address">
                        @php
                            $id = $item->id;
                            $sessionPostalCode = session("shipping_postal_code_{$id}");
                            $sessionAddress1 = session("shipping_address1_{$id}");
                            $sessionAddress2 = session("shipping_address2_{$id}");
                        @endphp

                        @if ($sessionPostalCode && $sessionAddress1)
                            〒 {{ $sessionPostalCode }}<br>
                            {{ $sessionAddress1 }} {{ $sessionAddress2 }}
                        @elseif (Auth::user()->profile)
                            〒 {{ Auth::user()->profile->postal_code }}<br>
                            {{ Auth::user()->profile->address_line1 }} {{ Auth::user()->profile->address_line2 }}
                        @else
                            配送先情報が登録されていません。
                        @endif
                    </p>
                    @error('shipping_address')
                        <div class="purchase__select-error">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
            </div>

            {{-- 右カラム --}}
            <div class="purchase__right">
                <div class="purchase__summary">
                    <div class="purchase__summary-row">
                        <span class="purchase__summary-label">商品代金</span>
                        <span class="purchase__summary-body">¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="purchase__summary-row">
                        <span class="purchase__summary-label">支払い方法</span>
                        <div class="purchase__summary-body">
                            <span class="js-payment-method">{{ old('payment_method') ?? '未選択' }}</span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="purchase__button">購入する</button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('.purchase__select');
    const display = document.querySelector('.js-payment-method');
    const hidden = document.getElementById('payment-method-hidden');

    if (select && display && hidden) {
        select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                display.textContent = selectedOption.textContent;
                hidden.value = selectedOption.value;
            }
        });
    }
});
</script>
@endsection
