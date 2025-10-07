@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile">
    {{-- ユーザー情報 --}}
    <div class="profile__header">
        <div class="profile__avatar">
            @if (Auth::user()->profile && Auth::user()->profile->avatar_path)
                <img src="{{ asset('storage/' . Auth::user()->profile->avatar_path) }}" class="profile__avatar-image">
            @else
                <div class="profile__avatar-image"></div>
            @endif
        </div>
        <div class="profile__user-info">
            <h2 class="profile__username">{{ Auth::user()->name }}</h2>
        </div>
        <a href="{{ route('profile.edit') }}" class="profile__edit-button">プロフィールを編集</a>
    </div>

    {{-- タブ切り替え --}}
    <div class="profile__tabs">
        <a href="?tab=selling" class="profile__tab {{ $activeTab === 'selling' ? 'active' : '' }}">出品した商品</a>
        <a href="?tab=purchased" class="profile__tab {{ $activeTab === 'purchased' ? 'active' : '' }}">購入した商品</a>
    </div>

    {{-- 商品一覧 --}}
        <div class="profile__items">
            @foreach ($items as $item)
                <a href="{{ route('items.show', ['id' => $item->id]) }}" class="profile__item-card">
                    <div class="profile__item-image
                        {{ $activeTab !== 'purchased' && $item->order ? 'sold-out' : '' }}">
                        <img src="{{ asset('storage/items-image/' . basename($item->image_path)) }}" alt="{{ $item->name }}">

                        @if ($activeTab !== 'purchased' && $item->order)
                            <div class="profile__sold-overlay">sold</div>
                        @endif
                    </div>
                    <div class="profile__item-name">{{ $item->name }}</div>
                </a>
            @endforeach
        </div>
</div>
@endsection
