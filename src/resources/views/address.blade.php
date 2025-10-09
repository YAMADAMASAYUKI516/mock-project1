@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address">
    <h1 class="address__title">住所の変更</h1>

    <form action="{{ route('address.update') }}" method="POST">
        @csrf

    <input type="hidden" name="return_url" value="{{ old('return_url', url()->previous()) }}">
    <input type="hidden" name="item_id" value="{{ $item_id }}">

        @php
            $profile = Auth::user()->profile;
        @endphp

        <label class="address__label" for="shipping_postal_code">郵便番号</label>
        <input
            class="address__input"
            type="text"
            name="shipping_postal_code"
            value="{{ old('shipping_postal_code', $shipping_postal_code ?? '') }}"
        >
        @error('shipping_postal_code')
            <div class="address__error">{{ $message }}</div>
        @enderror

        <label class="address__label" for="shipping_address1">住所</label>
        <input
            class="address__input"
            type="text"
            name="shipping_address1"
            value="{{ old('shipping_address1', $shipping_address1 ?? '') }}"
        >
        @error('shipping_address1')
            <div class="address__error">{{ $message }}</div>
        @enderror

        <label class="address__label" for="shipping_address2">建物名</label>
        <input
            class="address__input"
            type="text"
            name="shipping_address2"
            value="{{ old('shipping_address2', $shipping_address2 ?? '') }}"
        >
        @error('shipping_address2')
            <div class="address__error">{{ $message }}</div>
        @enderror

        <button class="address__button" type="submit">更新する</button>

    </form>
</div>
@endsection
