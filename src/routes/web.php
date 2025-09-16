<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// トップ画面
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

// マイページ
Route::middleware(['auth'])->get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

// プロフィール編集
Route::middleware(['auth'])->group(function () {
    Route::get('edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// お気に入り
Route::middleware('auth')->group(function () {
    Route::post('/like/{item}', [LikeController::class, 'store']);
    Route::delete('/unlike/{item}', [LikeController::class, 'destroy']);
});

// コメント
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

// 購入
Route::get('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase');

