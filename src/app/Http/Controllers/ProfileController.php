<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;

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

        $profile->postal_code = $request->input('postal_code');
        $profile->address_line1 = $request->input('address_line1');
        $profile->address_line2 = $request->input('address_line2');

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            $profile->avatar_path = $path;
        }

        $profile->save();

        return redirect()->route('mypage');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'selling');

        if ($tab === 'purchased') {
            $items = $user->orders()->with('item')->get()->pluck('item');
        } else {
            $items = $user->items;
        }

        return view('profile', [
            'items' => $items,
            'activeTab' => $tab,
        ]);
    }
}
