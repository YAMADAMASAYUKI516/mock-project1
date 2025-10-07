<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');
        $user = Auth::user();

        if ($tab === 'mylist') {
            if (!$user) {
                $items = collect();
            } else {
                $likedItems = $user->likes()->with('item')->get()->pluck('item')->filter();

                if ($keyword) {
                    $items = $likedItems->filter(function ($item) use ($keyword) {
                        return stripos($item->name, $keyword) !== false;
                    });
                } else {
                    $items = $likedItems;
                }

                $items = collect($items);
            }
        } else {
            $query = Item::with('order')->orderBy('created_at', 'asc');

            if ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            if ($user) {
                $query->where('seller_id', '!=', $user->id);
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
        $item = Item::with(['likes', 'comments.user', 'condition', 'categories'])->findOrFail($id);
        return view('item', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('exhibition', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        if ($request->hasFile('image_path')) {
            $filename = $request->file('image_path')->store('items-image', 'public');
        }

        $item = new Item();
        $item->name = $request->input('name');
        $item->brand = $request->input('brand');
        $item->description = $request->input('description');
        $item->price = $request->input('price');
        $item->condition_id = $request->input('condition_id');
        $item->seller_id = auth()->id();
        $item->image_path = $filename ?? null;
        $item->save();

        $categoryIds = $request->input('category_ids', []);
        $item->categories()->sync($categoryIds);

        return redirect()->route('items.index');
    }
}
