@extends('vendor.larrock.emails.template.body')

@section('content')
    <h1 style="font:26px/32px Calibri,Helvetica,Arial,sans-serif;">Отправлена форма заявки</h1>
    <p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>Имя:</strong> {{ $name }}</p>
    <p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>Контакты:</strong> {{ $contact }}</p>
    <p style="font:14px/16px Calibri,Helvetica,Arial,sans-serif;"><strong>Комментарий:</strong> {{ $comment }}</p>
    <table lang="ru" style="width: 100%; padding-top: 15px" cellspacing="0" cellpadding="5">
        <thead>
        <tr>
            <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">Наименование</td>
            <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">Количество</td>
            <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">Стоимость</td>
            <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">Итого</td>
        </tr>
        </thead>
        <tbody>
        @foreach($cart as $item)
            <tr>
                <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">{{ $item->name }}</td>
                <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">{{ $item->qty }}</td>
                <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">{{ $item->price }}</td>
                <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;">{{ $item->subtotal }} руб.</td>
            </tr>
        @endforeach
        <tr>
            <td style="border: #bcbcbc 1px solid;font:14px/16px Calibri,Helvetica,Arial,sans-serif;" colspan="4">Всего к оплате: {!! Cart::total() !!} руб.</td>
        </tr>
        </tbody>
    </table>
@endsection

@section('footer')
    @include('vendor.larrock.emails.template.footer')
@endsection