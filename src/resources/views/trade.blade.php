@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/trade.css') }}">
@endsection

@section('content')
<div class="trade">

    <aside class="trade__sidebar">
        <h2 class="trade__sidebar-title">その他の取引</h2>

        @forelse ($tradingOrders as $tradingOrder)
            @php
                $isActive = $tradingOrder->id === $order->id;
            @endphp

            <a
                href="{{ route('trade.show', ['order' => $tradingOrder->id]) }}"
                class="trade__sidebar-item"
            >
                <div class="trade__sidebar-item-name">
                    {{ $tradingOrder->item->name }}
                </div>
            </a>
        @empty
            <p class="trade__sidebar-empty">取引中の取引はありません。</p>
        @endforelse
    </aside>

    <main class="trade__main">

        <div class="trade__header">
            <div class="trade__header-left">
                <div class="trade__header-avatar">
                    @if ($partnerUser && $partnerUser->profile && $partnerUser->profile->avatar_path)
                        <img
                            src="{{ asset('storage/' . $partnerUser->profile->avatar_path) }}"
                            alt="{{ $partnerUser->name }}"
                        >
                    @endif
                </div>

                <h2 class="trade__header-title">
                    「{{ $partnerUser ? $partnerUser->name : 'ユーザー' }}」さんとの取引画面
                </h2>
            </div>

            @if ($shouldShowBuyerCompleteButton)
                <button type="button" id="openCompleteModalButton" class="trade__complete-button">
                    取引を完了する
                </button>
            @endif
        </div>

        <section class="trade__item">
            <div class="trade__item-image">
                <img
                    src="{{ asset('storage/items-image/' . basename($order->item->image_path)) }}"
                    alt="{{ $order->item->name }}"
                >
            </div>

            <div class="trade__item-info">
                <h2 class="trade__item-name">{{ $order->item->name }}</h2>
                <p class="trade__item-price">¥{{ number_format($order->item->price) }}</p>
            </div>
        </section>

        <section class="trade__messages">
            @forelse ($messages as $message)
                @php
                    $isOwn   = $message->sender_id === $user->id;
                    $editing = $isOwn && isset($editingMessageId) && (int) $editingMessageId === $message->id;
                @endphp

                <div class="trade__message {{ $isOwn ? 'trade__message--own' : 'trade__message--other' }}">
                    <div class="trade__message-inner">
                        @php
                            $senderUser = $message->sender;
                        @endphp

                        <div class="trade__message-avatar">
                            @if ($senderUser && $senderUser->profile && $senderUser->profile->avatar_path)
                                <img
                                    src="{{ asset('storage/' . $senderUser->profile->avatar_path) }}"
                                    alt="{{ $senderUser->name }}"
                                >
                            @endif
                        </div>

                        <div class="trade__message-content">
                            <div class="trade__message-header {{ $isOwn ? 'trade__message-header--own' : 'trade__message-header--other' }}">
                                <span class="trade__message-sender">{{ $message->sender->name }}</span>
                            </div>

                            <div class="trade__message-body">
                                @if ($editing)
                                    <form
                                        action="{{ route('trade.message.update', ['message' => $message->id]) }}"
                                        method="POST"
                                        enctype="multipart/form-data"
                                        class="trade__edit-form"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <div class="trade__form-group">
                                            <textarea
                                                name="body"
                                                class="trade__textarea trade__textarea--edit"
                                                rows="3"
                                            >{{ old('body', $message->body) }}</textarea>
                                        </div>

                                        <div class="trade__form-group">
                                            <label class="trade__form-label">画像（任意で差し替え）</label>
                                            <input
                                                type="file"
                                                name="image"
                                                class="trade__file-input"
                                                accept=".jpeg,.jpg,.png"
                                            >
                                        </div>

                                        <div class="trade__form-actions trade__form-actions--edit">
                                            <button type="submit" class="trade__submit-button">更新する</button>
                                            <a href="{{ route('trade.show', ['order' => $order->id]) }}" class="trade__cancel-link">
                                                キャンセル
                                            </a>
                                        </div>
                                    </form>
                                @else
                                    <p class="trade__message-text">{{ $message->body }}</p>

                                    @if ($message->image_path)
                                        <div class="trade__message-image">
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="取引画像">
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if ($isOwn && ! $editing)
                                <div class="trade__message-actions">
                                    <a
                                        href="{{ route('trade.show', ['order' => $order->id, 'edit_message' => $message->id]) }}"
                                        class="trade__message-edit"
                                    >
                                        編集
                                    </a>

                                    <form
                                        action="{{ route('trade.message.destroy', ['message' => $message->id]) }}"
                                        method="POST"
                                        class="trade__message-delete-form"
                                        onsubmit="return confirm('このメッセージを削除してよろしいですか？');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="trade__message-delete">削除</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="trade__messages-empty">まだメッセージはありません。</p>
            @endforelse
        </section>

        <section class="trade__form-section">

            @if ($errors->any())
                <div class="trade__errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="trade__error-item">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('trade.message.store', ['order' => $order->id]) }}"
                method="POST"
                enctype="multipart/form-data"
                class="trade__form"
            >
                @csrf

                <div class="trade__form-row">
                    <label for="body" class="trade__form-label trade__form-label--sr-only">本文</label>

                    <textarea
                        id="body"
                        name="body"
                        class="trade__textarea trade__textarea--input"
                        rows="2"
                        placeholder="取引メッセージを記入してください"
                    >{{ old('body') }}</textarea>

                    <div class="trade__form-right">
                        <label for="image" class="trade__image-button">画像を追加</label>
                        <input
                            id="image"
                            type="file"
                            name="image"
                            class="trade__file-input trade__file-input--hidden"
                            accept=".jpeg,.jpg,.png"
                        >

                        <button type="submit" class="trade__submit-button trade__submit-button--icon">
                            <img src="{{ asset('images/input_button.jpg') }}" alt="送信する">
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <div id="tradeCompleteModal" class="trade__modal">
            <div class="trade__modal-overlay"></div>

            <div class="trade__modal-content">
                <h2 class="trade__modal-title">取引が完了しました。</h2>
                <p class="trade__modal-text">今回の取引相手はどうでしたか？</p>

                <form action="{{ route('trade.review', ['order' => $order->id]) }}" method="POST" class="trade__modal-form">
                    @csrf

                    <div class="trade__modal-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                class="trade__star {{ (int) old('rating') >= $i ? 'trade__star--filled' : '' }}"
                                data-value="{{ $i }}"
                            ></button>
                        @endfor

                        <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                    </div>

                    <div class="trade__modal-actions">
                        <button type="submit" class="trade__modal-submit">送信する</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('body');
    if (textarea) {
        const storageKey = 'trade_body_order_{{ $order->id }}_user_{{ $user->id }}';
        const hasOldBody = @json(old('body') !== null);

        if (hasOldBody) {
            localStorage.setItem(storageKey, textarea.value);
        } else {
            const saved = localStorage.getItem(storageKey);
            if (saved !== null && textarea.value === '') {
                textarea.value = saved;
            }
        }

        textarea.addEventListener('input', function () {
            localStorage.setItem(storageKey, textarea.value);
        });

        const form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                localStorage.removeItem(storageKey);
            });
        }
    }

    const modal = document.getElementById('tradeCompleteModal');
    const openBtn = document.getElementById('openCompleteModalButton');
    const overlay = modal ? modal.querySelector('.trade__modal-overlay') : null;

    function openModal() {
        if (modal) modal.classList.add('is-active');
    }

    function closeModal() {
        if (modal) modal.classList.remove('is-active');
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (overlay) overlay.addEventListener('click', closeModal);

    const shouldOpenOnLoad = @json($shouldShowSellerModalOnLoad);
    if (shouldOpenOnLoad) openModal();

    const starButtons = document.querySelectorAll('.trade__star');
    const ratingInput = document.getElementById('rating-input');

    function refreshStars(value) {
        starButtons.forEach(function (btn) {
            const v = Number(btn.dataset.value);
            btn.classList.toggle('trade__star--filled', v <= value);
        });
    }

    if (starButtons.length && ratingInput) {
        refreshStars(Number(ratingInput.value || 0));

        starButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const val = Number(btn.dataset.value);
                ratingInput.value = val;
                refreshStars(val);
            });
        });
    }
});
</script>
@endsection
