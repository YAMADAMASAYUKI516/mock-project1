@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index">
    <div class="index__tabs">
        <a href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => request('keyword')]) }}"
            class="index__tab {{ $activeTab === 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>

        <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
            class="index__tab {{ $activeTab === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="index__items">
        @foreach ($items as $item)
            <a href="{{ url('/item/' . $item->id) }}" class="item-card-link">
                <div class="item-card">
                    <div class="item-card__image-wrap {{ $item->order ? 'sold-out' : '' }}">
                        <img src="{{ asset('storage/items-image/' . basename($item->image_path)) }}"
                            alt="{{ $item->name }}"
                            class="item-card__image">
                        @if ($item->order)
                            <div class="item-card__sold-overlay">sold</div>
                        @endif
                    </div>
                    <div class="item-card__name">{{ $item->name }}</div>
                </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
