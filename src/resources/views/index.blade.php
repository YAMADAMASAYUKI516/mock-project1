@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index">
    <div class="index__tabs">
        {{-- タブ切り替え --}}
        <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
            class="index__tab {{ $activeTab === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        @auth
            <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
                class="index__tab {{ $activeTab === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        @else
            <span class="index__tab disabled">マイリスト</span>
        @endauth
    </div>

    <div class="index__items">
        @foreach ($items as $item)
            <a href="{{ url('/item/' . $item->id) }}" class="item-card-link">
                <div class="item-card">
                    <div class="item-card__image">
                        <img src="{{ asset('storage/items-image/' . basename($item->image_path)) }}" alt="{{ $item->name }}">
                    </div>
                    <div class="item-card__name">{{ $item->name }}</div>
                </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
