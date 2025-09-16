<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Like;

class LikeController extends Controller
{
    public function store(Item $item)
    {
        $user = auth()->user();

        if (!$item->likes()->where('user_id', $user->id)->exists()) {
            Like::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
        }

        return response()->json(['status' => 'liked']);
    }

    public function destroy(Item $item)
    {
        $user = auth()->user();

        $item->likes()->where('user_id', $user->id)->delete();

        return response()->json(['status' => 'unliked']);
    }
}
