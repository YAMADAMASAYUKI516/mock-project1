<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use App\Mail\TradeCompletedMail;
use App\Models\Order;
use App\Models\Review;
use App\Models\TradeMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TradeController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $user = Auth::user();

        $order->loadMissing([
            'item',
            'item.seller.profile',
            'buyer.profile',
        ]);

        [$isSeller, $isBuyer, $sellerId] = $this->resolveRoles($user, $order);
        $this->authorizeTrade($isSeller, $isBuyer);

        $partnerUser = $this->getPartnerUser($order, $isBuyer, $sellerId);

        $tradingOrders = $this->getTradingOrders($user);

        $messages = $order->tradeMessages()
            ->with(['sender.profile'])
            ->orderBy('created_at')
            ->get();

        $this->markMessagesAsRead($order, $user->id);

        $buyerId = $order->buyer_id;

        $buyerReview = Review::where('order_id', $order->id)
            ->where('rater_id', $buyerId)
            ->where('rated_user_id', $sellerId)
            ->first();

        $sellerReview = Review::where('order_id', $order->id)
            ->where('rater_id', $sellerId)
            ->where('rated_user_id', $buyerId)
            ->first();

        $shouldShowBuyerCompleteButton = $isBuyer && ! $buyerReview;
        $shouldShowSellerModalOnLoad   = $isSeller && $buyerReview && ! $sellerReview;

        return view('trade', [
            'order'                        => $order,
            'messages'                     => $messages,
            'tradingOrders'                => $tradingOrders,
            'user'                         => $user,
            'editingMessageId'             => $request->query('edit_message'),
            'shouldShowBuyerCompleteButton'=> $shouldShowBuyerCompleteButton,
            'shouldShowSellerModalOnLoad'  => $shouldShowSellerModalOnLoad,
            'partnerUser'                  => $partnerUser,
        ]);
    }

    public function store(TradeMessageRequest $request, Order $order)
    {
        $user = Auth::user();

        $order->loadMissing(['item']);

        [$isSeller, $isBuyer] = $this->resolveRoles($user, $order);
        $this->authorizeTrade($isSeller, $isBuyer);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('trade-images', 'public');
        }

        TradeMessage::create([
            'order_id'   => $order->id,
            'sender_id'  => $user->id,
            'body'       => $request->input('body'),
            'image_path' => $imagePath,
            'is_read'    => false,
        ]);

        return redirect()->route('trade.show', ['order' => $order->id]);
    }

    public function update(TradeMessageRequest $request, TradeMessage $message)
    {
        $user  = Auth::user();
        $order = $message->order;

        $order->loadMissing(['item']);

        [$isSeller, $isBuyer] = $this->resolveRoles($user, $order);
        $this->authorizeTrade($isSeller, $isBuyer);

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        $message->body = $request->input('body');

        if ($request->hasFile('image')) {
            if ($message->image_path) {
                Storage::disk('public')->delete($message->image_path);
            }
            $message->image_path = $request->file('image')->store('trade-images', 'public');
        }

        $message->save();

        return redirect()->route('trade.show', ['order' => $order->id]);
    }

    public function destroy(TradeMessage $message)
    {
        $user  = Auth::user();
        $order = $message->order;

        $order->loadMissing(['item']);

        [$isSeller, $isBuyer] = $this->resolveRoles($user, $order);
        $this->authorizeTrade($isSeller, $isBuyer);

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $message->delete();

        return redirect()->route('trade.show', ['order' => $order->id]);
    }

    public function review(Request $request, Order $order)
    {
        $user = Auth::user();

        $order->loadMissing(['item', 'item.seller', 'buyer']);

        [$isSeller, $isBuyer, $sellerId] = $this->resolveRoles($user, $order);
        $this->authorizeTrade($isSeller, $isBuyer);

        if ($isBuyer && Review::where('order_id', $order->id)->where('rater_id', $user->id)->exists()) {
            return redirect()->route('trade.show', ['order' => $order->id]);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ], [
            'rating.required' => '評価を選択してください',
            'rating.integer'  => '評価は数値で指定してください',
            'rating.min'      => '評価は1以上を選択してください',
            'rating.max'      => '評価は5以下を選択してください',
        ]);

        $rating = (int) $validated['rating'];

        if ($isBuyer) {
            $raterId     = $user->id;
            $ratedUserId = $sellerId;

            if (! $ratedUserId) {
                return back()->withErrors(['rating' => '出品者IDが取得できません。']);
            }

            $order->status       = 'completed';
            $order->completed_at = now();
            $order->save();

            $seller = $order->item?->seller;
            if ($seller && $seller->email) {
                Mail::to($seller->email)->send(new TradeCompletedMail($order));
            }
        } else {
            $raterId     = $user->id;
            $ratedUserId = $order->buyer_id;
        }

        Review::updateOrCreate(
            [
                'order_id'      => $order->id,
                'rater_id'      => $raterId,
                'rated_user_id' => $ratedUserId,
            ],
            [
                'rating' => $rating,
            ]
        );

        return redirect('/');
    }

    private function resolveRoles(User $user, Order $order): array
    {
        $sellerId = $order->item?->seller_id;
        $isSeller = ($sellerId === $user->id);
        $isBuyer  = ($order->buyer_id === $user->id);

        return [$isSeller, $isBuyer, $sellerId];
    }

    private function authorizeTrade(bool $isSeller, bool $isBuyer): void
    {
        if (! $isSeller && ! $isBuyer) {
            abort(403);
        }
    }

    private function getPartnerUser(Order $order, bool $isBuyer, ?int $sellerId): ?User
    {
        if ($isBuyer) {
            return $order->item?->seller ?? ($sellerId ? User::find($sellerId) : null);
        }

        return $order->buyer ?? ($order->buyer_id ? User::find($order->buyer_id) : null);
    }

    private function getTradingOrders($user)
    {
        return Order::query()
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
    }

    private function markMessagesAsRead(Order $order, int $userId): void
    {
        TradeMessage::where('order_id', $order->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
