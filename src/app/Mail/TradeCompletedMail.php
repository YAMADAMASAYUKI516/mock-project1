<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing(['item', 'item.seller', 'buyer']);
    }

    public function build()
    {
        return $this->subject('【取引完了】購入者が取引を完了しました')
            ->markdown('emails.trade.completed');
    }
}
