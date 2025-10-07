<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Profile;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

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

        $order = Order::create([
            'buyer_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $request->payment_method,
            'shipping_postal_code' => $shipping_postal_code,
            'shipping_address1' => $shipping_address1,
            'shipping_address2' => $shipping_address2,
            'status' => 'pending',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amount = (int)$item->price;

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('mypage') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.store', ['item' => $item->id]),
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        session()->forget([
            "shipping_postal_code_{$itemId}",
            "shipping_address1_{$itemId}",
            "shipping_address2_{$itemId}",
        ]);

        return redirect($session->url);
    }
}
