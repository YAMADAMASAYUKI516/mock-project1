@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item">
    {{-- 左側: 画像 --}}
    <div class="item__image">
        <img src="{{ asset('storage/items-image/' . basename($item->image_path)) }}" alt="{{ $item->name }}">
    </div>

    {{-- 右側: 商品詳細 --}}
    <div class="item__details">
        <div class="item__title">{{ $item->name }}</div>
        <div class="item__brand">{{ $item->brand }}</div>
        <div class="item__price">
            ￥{{ number_format($item->price) }}
            <span class="item__price-tax">（税込）</span>
        </div>

        {{-- ライク・コメントアイコン --}}
        <div class="item__actions">
            @php
                $isLiked = auth()->check() && $item->likes->contains('user_id', auth()->id());
            @endphp

            <div class="item__meta">
                <div class="item__meta-block">
                    <img
                        src="{{ asset($isLiked ? 'images/star_filled.png' : 'images/star.png') }}"
                        alt="お気に入り"
                        class="item__icon like-button"
                        data-item-id="{{ $item->id }}"
                        data-liked="{{ $isLiked ? 'true' : 'false' }}"
                    >
                    <span class="item__meta-count">{{ $item->likes->count() }}</span>
                </div>

                <div class="item__meta-block">
                    <img src="{{ asset('images/speech_bubble.png') }}" alt="コメント" class="item__icon">
                    <span class="item__meta-count">{{ $item->comments->count() }}</span>
                </div>
            </div>

            @auth
                <a href="{{ route('purchase', ['item_id' => $item->id]) }}" class="item__buy-button">
                    購入手続きへ
                </a>
            @else
                <a href="{{ route('login') }}" class="item__buy-button">
                    購入手続きへ
                </a>
            @endauth
        </div>

        {{-- 商品説明 --}}
        <div class="item__description">
            <div>商品説明</div>
            <p>{{ $item->description }}</p>
        </div>

        {{-- 商品情報 --}}
        <div class="item__info">
            <div>商品の情報</div>
            <p>カテゴリ: {{ optional($item->category)->name ?? '未設定' }}</p>
            <p>商品の状態: {{ optional($item->condition)->condition ?? '未設定' }}</p>
        </div>

        {{-- コメント表示 --}}
        <div class="item__comment">
            コメント ({{ $item->comments->count() }})

            @foreach ($item->comments as $comment)
                <div class="item__comment-box">
                    <div class="item__comment-header">
                        <div class="item__comment-icon"></div>
                        <div class="item__comment-name">{{ $comment->user->name }}</div>
                    </div>
                    <div class="item__comment-text">{{ $comment->body }}</div>
                </div>
            @endforeach
        </div>

        {{-- コメント投稿 --}}
        @auth
            {{-- ログイン済みの場合はフォームを表示 --}}
            <form action="{{ route('comments.store', ['item' => $item->id]) }}" method="POST" class="item__comment-form">
                @csrf
                <label for="comment" class="item__comment-label">商品へのコメント</label>
                <textarea name="comment" id="comment" rows="4" class="item__comment-textarea" required></textarea>
                <button type="submit" class="item__comment-button">コメントを送信する</button>
            </form>
        @else
            {{-- 未ログインの場合はログインページへのリンクボタンを同じデザインで表示 --}}
            <form class="item__comment-form">
                <label class="item__comment-label">商品へのコメント</label>
                <textarea rows="4" class="item__comment-textarea" disabled></textarea>
                <a href="{{ route('login') }}" class="item__comment-button-link">
                    コメントを送信する
                </a>
            </form>
        @endauth
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async () => {
            const itemId = button.dataset.itemId;
            const liked = button.dataset.liked === 'true';

            const url = liked ? `/unlike/${itemId}` : `/like/${itemId}`;
            const method = liked ? 'DELETE' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                // ボタン状態切り替え
                const newLiked = !liked;
                button.dataset.liked = newLiked.toString();
                button.src = newLiked
                    ? "{{ asset('images/star_filled.png') }}"
                    : "{{ asset('images/star.png') }}";

                // カウント更新
                const countSpan = button.nextElementSibling; // すぐ右の <span> を取得
                let count = parseInt(countSpan.textContent);
                countSpan.textContent = newLiked ? count + 1 : count - 1;
            }
        });
    });
});
</script>
@endsection

