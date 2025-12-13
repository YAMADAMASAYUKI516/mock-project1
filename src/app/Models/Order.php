<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'buyer_id', 'shipping_postal_code', 'shipping_address1', 'shipping_address2', 'payment_method'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function tradeMessages()
    {
        return $this->hasMany(TradeMessage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
