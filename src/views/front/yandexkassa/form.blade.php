{{--
  -- For more information about form fields
  -- you can visit Yandex Kassa documentation page
  --
  -- @see https://tech.yandex.com/money/doc/payment-solution/payment-form/payment-form-http-docpage/
  --}}
<form action="{{yandex_kassa_form_action()}}" method="{{yandex_kassa_form_method()}}" class="uk-form form-ya-kassa uk-align-left">
    <input name="scId" type="hidden" value="{{yandex_kassa_sc_id()}}">
    <input name="shopId" type="hidden" value="{{yandex_kassa_shop_id()}}">
    @if($data->cost_discount > 0 && $data->cost_discount < $data->cost)
        <input name="sum" type="hidden" value="{{ $data->cost_discount }}">
    @else
        <input name="sum" type="hidden" value="{{ $data->cost }}">
    @endif
    <input name="customerNumber" value="{{ $data->user_id }}" type="hidden"/>
    <input name="orderNumber" value="{{ $data->order_id }}" type="hidden"/>
    <input name="cps_phone" value="{{ $current_user->tel }}" type="hidden"/>
    <input name="cps_email" value="{{ $current_user->email }}" type="hidden"/>
    <input name="paymentType" value="" type="hidden">

    <div class="form-group">
        <button type="submit" class="uk-button uk-button-primary">{{trans('yandex_kassa::form.button.pay')}}</button>
    </div>
</form>