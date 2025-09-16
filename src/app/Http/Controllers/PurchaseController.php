<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function purchase($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('purchase', compact('item'));
    }
}
