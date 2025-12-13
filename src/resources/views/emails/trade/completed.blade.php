@component('mail::message')
# 取引が完了しました

購入者が取引を完了しました。

**商品名：** {{ $order->item->name }}
**購入者：** {{ $order->buyer->name }}

@component('mail::button', ['url' => url('/')])
サイトを開く
@endcomponent

よろしくお願いいたします。
{{ config('app.name') }}
@endcomponent
