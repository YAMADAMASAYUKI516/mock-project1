@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile">
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

            @if (!is_null($averageRating) && $reviewCount > 0)
                <div class="profile__rating">
                    <div class="profile__rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="profile__rating-star {{ $i <= $averageRating ? 'profile__rating-star--filled' : '' }}"></span>
                        @endfor
                    </div>
                </div>
            @endif
        </div>

        <a href="{{ route('profile.edit') }}" class="profile__edit-button">プロフィールを編集</a>
    </div>

    <div class="profile__tabs">
        <a href="?tab=selling" class="profile__tab {{ $activeTab === 'selling' ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="?tab=purchased" class="profile__tab {{ $activeTab === 'purchased' ? 'active' : '' }}">
            購入した商品
        </a>

        <a href="?tab=trading" class="profile__tab {{ $activeTab === 'trading' ? 'active' : '' }}">
            取引中の商品
            @if (!empty($tradingUnreadTotal) && $tradingUnreadTotal > 0)
                <span class="profile__tab-badge">{{ $tradingUnreadTotal }}</span>
            @endif
        </a>
    </div>

    @if ($activeTab === 'trading')
        <div class="profile__items profile__items--trading">
            @forelse ($orders as $order)
                <a href="{{ route('trade.show', ['order' => $order->id]) }}" class="profile__item-card profile__item-card--trading">
                    <div class="profile__item-image">
                        <img src="{{ asset('storage/items-image/' . basename($order->item->image_path)) }}" alt="{{ $order->item->name }}">
                    </div>

                    <div class="profile__item-name">{{ $order->item->name }}</div>

                    @if (!empty($order->unread_count) && $order->unread_count > 0)
                        <div class="profile__notification-badge">
                            {{ $order->unread_count }}
                        </div>
                    @endif
                </a>
            @empty
                <p class="profile__empty"></p>
            @endforelse
        </div>
    @else
        <div class="profile__items">
            @forelse ($items as $item)
                <a href="{{ route('items.show', ['id' => $item->id]) }}" class="profile__item-card">
                    <div class="profile__item-image {{ $activeTab !== 'purchased' && $item->order ? 'sold-out' : '' }}">
                        <img src="{{ asset('storage/items-image/' . basename($item->image_path)) }}" alt="{{ $item->name }}">

                        @if ($activeTab !== 'purchased' && $item->order)
                            <div class="profile__sold-overlay">sold</div>
                        @endif
                    </div>

                    <div class="profile__item-name">{{ $item->name }}</div>
                </a>
            @empty
                <p class="profile__empty"></p>
            @endforelse
        </div>
    @endif
</div>
@endsection
