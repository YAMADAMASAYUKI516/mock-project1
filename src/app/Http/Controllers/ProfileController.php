<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Order;
use App\Models\Profile;
use App\Models\Review;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->name = $request->input('name');
        $user->save();

        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        $profile->postal_code   = $request->input('postal_code');
        $profile->address_line1 = $request->input('address_line1');
        $profile->address_line2 = $request->input('address_line2');

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $profile->avatar_path = $path;
        }

        $profile->save();

        return redirect()->route('mypage');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->query('tab', 'selling');

        $reviewsQuery  = Review::where('rated_user_id', $user->id);
        $reviewCount   = $reviewsQuery->count();
        $averageRating = $reviewCount > 0 ? round($reviewsQuery->avg('rating')) : null;

        $tradingOrders = Order::query()
            ->where('status', 'in_progress')
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                ->orWhereHas('item', function ($q2) use ($user) {
                    $q2->where('seller_id', $user->id);
                });
            })
            ->with(['item'])
            ->withCount([
                'tradeMessages as unread_count' => function ($q) use ($user) {
                    $q->where('is_read', false)
                    ->where('sender_id', '!=', $user->id);
                }
            ])
            ->withMax('tradeMessages', 'created_at')
            ->orderByRaw('COALESCE(trade_messages_max_created_at, orders.created_at) DESC')
            ->get();

        $tradingUnreadTotal = $tradingOrders->sum('unread_count');

        $items  = collect();
        $orders = collect();

        if ($tab === 'purchased') {
            $items = $user->orders()->with('item')->get()->pluck('item');
        } elseif ($tab === 'trading') {
            $orders = $tradingOrders;
        } else {
            $items = $user->items;
        }

        return view('profile', [
            'items'              => $items,
            'orders'             => $orders,
            'activeTab'          => $tab,
            'averageRating'      => $averageRating,
            'reviewCount'        => $reviewCount,
            'tradingUnreadTotal' => $tradingUnreadTotal,
        ]);
    }
}
