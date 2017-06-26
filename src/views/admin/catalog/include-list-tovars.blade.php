{{-- Список товаров --}}
@foreach($data as $data_value)
    <tr>
        <td width="70">
            <a href="/admin/{{ $app->name }}/{{ $data_value->id }}/edit">
                @if($image = $data_value->getMedia('images')->sortByDesc('order_column')->first())
                    <img style="width: 55px" src="{{ $image->getUrl('110x110') }}">
                @else
                    <i class="icon-padding icon-color uk-icon-picture-o" title="Фото не прикреплено"></i>
                @endif
            </a>
        </td>
        <td>
            <a class="uk-h4" href="/admin/{{ $app->name }}/{{ $data_value->id }}/edit">{{ $data_value->title }}</a>
            <br/>
            <a class="link-to-front" target="_blank" href="{{ $data_value->full_url }}" title="ссылка на товар на сайте">
                {{ str_limit($data_value->full_url, 35, '...') }}
            </a>
            @if($data_value->label_popular === 1)
                <span class="uk-badge uk-badge-info">ХИТ!</span><br/>
            @endif
            @if($data_value->label_sale === 1)
                <span class="uk-badge uk-badge-danger">SALE -{{ $data_value->label_sale }}%</span><br/>
            @endif
            @if($data_value->label_new === 1)
                <span class="uk-badge uk-badge-warning">NEW</span><br/>
            @endif
            @if($data_value->arrival_date > \Carbon\Carbon::now())
                @if($data_value->nalichie < 1)
                    <span class="uk-badge uk-badge-info">В пути {{ $data_value->arrival_date->format('d.m.Y') }}г.</span><br/>
                @endif
            @else
                @if($data_value->nalichie < 1)
                    <span class="uk-badge label-danger">под заказ</span><br/>
                @endif
            @endif
        </td>
        <td class="uk-hidden-small">
            <input type="text" name="nalichie" value="{{ $data_value->nalichie }}" class="ajax_edit_row"
                   data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="catalog">
        </td>
        <td class="uk-hidden-small">
            <input type="text" name="cost" value="{{ $data_value->cost }}" class="ajax_edit_row"
                   data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="catalog">
        </td>
        <td class="uk-hidden-small">
            <input type="text" name="cost_promo" value="{{ $data_value->cost_promo }}" class="ajax_edit_row"
                   data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="catalog">
        </td>
        <td class="row-position uk-hidden-small">
            <input type="text" name="position" value="{{ $data_value->position }}" class="ajax_edit_row"
                   data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="catalog"
                   data-toggle="tooltip" data-placement="bottom" title="Вес. Чем больше, тем выше в списках">
        </td>
        <td class="row-active">
            <div class="uk-button-group btn-group_switch_ajax" role="group">
                <button type="button" class="uk-button uk-button-primary uk-button-small @if($data_value->{$row->name} === 0) uk-button-outline @endif"
                        data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="catalog"
                        data-row="active" data-value="1"
                        data-toggle="tooltip" data-placement="bottom" title="Включить">on</button>
                <button type="button" class="uk-button uk-button-danger uk-button-small @if($data_value->{$row->name} === 1) uk-button-outline @endif"
                        data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="catalog"
                        data-row="active" data-value="0"
                        data-toggle="tooltip" data-placement="bottom" title="Выключить">off</button>
            </div>
        </td>
        <td class="row-edit uk-hidden-small">
            <a href="/admin/{{ $app->name }}/{{ $data_value->id }}/edit" class="btn btn-info btn-xs">Свойства</a>
            <form action="/admin/{{ $app->name }}/{{ $data_value->id }}/copy" method="post">
                {{ csrf_field() }}
                <button type="submit" class="uk-button uk-button-small please_conform" title="Копировать товар"><i class="uk-icon-copy"></i> copy</button>
            </form>
        </td>
        <td class="row-delete uk-hidden-small">
            <form action="/admin/{{ $app->name }}/{{ $data_value->id }}" method="post">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="uk-button uk-button-small uk-button-danger please_conform">Удалить</button>
            </form>
        </td>
    </tr>
@endforeach