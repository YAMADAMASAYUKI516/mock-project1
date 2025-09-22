<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;

class PurchaseController extends Controller
{
    public function purchase($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('purchase', compact('item'));
    }

    public function show(Item $item)
    {
        $user = Auth::user();
        return view('purchase', compact('item', 'user'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $itemId = $item->id;

        $shipping_postal_code = session("shipping_postal_code_{$itemId}") ?? optional($profile)->postal_code;
        $shipping_address1 = session("shipping_address1_{$itemId}") ?? optional($profile)->address_line1;
        $shipping_address2 = session("shipping_address2_{$itemId}") ?? optional($profile)->address_line2;

        // バリデーション（念のため）
        if (!$shipping_postal_code || !$shipping_address1) {
            return redirect()->back()
                ->withErrors(['配送先情報が登録されていません。'])
                ->withInput();
        }

        Order::create([
            'buyer_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $request->payment_method,
            'shipping_postal_code' => $shipping_postal_code,
            'shipping_address1' => $shipping_address1,
            'shipping_address2' => $shipping_address2,
        ]);

        session()->forget([
            "shipping_postal_code_{$itemId}",
            "shipping_address1_{$itemId}",
            "shipping_address2_{$itemId}",
        ]);

        return redirect()->route('mypage');
    }
}
