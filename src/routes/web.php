<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisterController;

// トップ画面（誰でも）
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');

// 会員登録
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// ログイン
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [LoginController::class, 'login']);

// メール認証
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/edit');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '確認用メールを再送信しました。');
    })->middleware(['throttle:6,1'])->name('verification.resend');
});

// 認証・メール確認済みユーザーのみアクセス可
Route::middleware(['auth', 'verified'])->group(function () {
    // マイページ
    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

    // プロフィール編集
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // お気に入り
    Route::post('/like/{item}', [LikeController::class, 'store']);
    Route::delete('/unlike/{item}', [LikeController::class, 'destroy']);

    // コメント
    Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

    // 購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 住所変更
    Route::get('/address', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/address', [AddressController::class, 'update'])->name('address.update');

    // 出品
    Route::get('/exhibition', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
});
