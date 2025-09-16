<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');
        $user = Auth::user();

        if ($tab === 'mylist' && $user) {
            $likedItems = $user->likes()->with('item')->get()->pluck('item')->filter();

            if ($keyword) {
                $items = $likedItems->filter(function ($item) use ($keyword) {
                    return stripos($item->name, $keyword) !== false;
                });
            } else {
                $items = $likedItems;
            }

            $items = collect($items);
        } else {
            $query = Item::orderBy('created_at', 'asc');

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $items = $query->get();
        }

        return view('index', [
            'items' => $items,
            'activeTab' => $tab,
        ]);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('item', compact('item'));
    }
}
