<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $item_id = $request->item_id;

        return view('address', [
            'item_id' => $item_id,
            'shipping_postal_code' => old('shipping_postal_code')
                ?? session("shipping_postal_code_$item_id")
                ?? optional($profile)->postal_code,

            'shipping_address1' => old('shipping_address1')
                ?? session("shipping_address1_$item_id")
                ?? optional($profile)->address_line1,

            'shipping_address2' => old('shipping_address2')
                ?? session("shipping_address2_$item_id")
                ?? optional($profile)->address_line2,
        ]);
    }

    public function update(AddressRequest $request)
    {
        $itemId = $request->input('item_id');

        session([
            "shipping_postal_code_{$itemId}" => $request->shipping_postal_code,
            "shipping_address1_{$itemId}" => $request->shipping_address1,
            "shipping_address2_{$itemId}" => $request->shipping_address2,
        ]);

        $returnUrl = $request->input('return_url', route('mypage'));
        return redirect($returnUrl);
    }
}
