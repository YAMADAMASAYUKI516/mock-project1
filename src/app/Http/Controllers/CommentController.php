<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $item->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->comment,
        ]);

        return redirect()->route('items.show', $item);
    }
}
