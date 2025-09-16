<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
            </a>

            {{-- ログイン・会員登録ページではロゴだけ表示 --}}
            @if (Request::is('login') || Request::is('register'))
                {{-- ロゴのみ --}}
            @else
                <form class="header__search-form" action="{{ route('items.index') }}" method="GET">
                    <input
                        class="header__search-input"
                        type="text"
                        name="keyword"
                        placeholder="なにをお探しですか？"
                        value="{{ request('keyword') }}"
                    >
                    <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
                </form>

                <nav class="header__nav">
                    @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="header__button-logout" type="submit">ログアウト</button>
                    </form>
                    @else
                        <a class="header__link" href="{{ route('login') }}">ログイン</a>
                    @endauth
                    <a class="header__link" href="{{ route('mypage') }}">マイページ</a>
                    <a class="header__button-listing" href="{{-- route('items.create') --}}">出品</a>
                </nav>
            @endif
        </div>
    </header>

    <main>
        <div class="common__heading">
            @yield('title')
        </div>
        @yield('content')
    </main>
    @yield('js')
</body>

</html>