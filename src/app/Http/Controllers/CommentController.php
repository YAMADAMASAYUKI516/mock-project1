<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Models\Item;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $user = Auth::user();

        $item->comments()->create([
            'user_id' => $user->id,
            'body' => $request->input('body'),
        ]);

        return redirect()->route('items.show', ['id' => $item->id]);
    }
}
