<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');
        $user = Auth::user();

        if ($tab === 'mylist' && $user) {
            $likedItems = $user->likes()->with('item')->get()->pluck('item')->filter();

            if ($keyword) {
                $items = $likedItems->filter(function ($item) use ($keyword) {
                    return stripos($item->name, $keyword) !== false;
                });
            } else {
                $items = $likedItems;
            }

            $items = collect($items);
        } else {
            $query = Item::with('order')->orderBy('created_at', 'asc');

            // 検索キーワードがある場合
            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            // ログイン中であれば、自分の出品商品を除外
            if ($user) {
                $query->where('seller_id', '!=', $user->id);
            }

            $items = $query->get();
        }

        return view('index', [
            'items' => $items,
            'activeTab' => $tab,
        ]);
    }

    public function show($id)
    {
        $item = Item::with(['likes', 'comments.user', 'condition', 'categories'])->findOrFail($id);
        return view('item', compact('item'));
    }
}
