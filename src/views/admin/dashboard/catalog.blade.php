<div class="uk-margin-bottom uk-width-1-1 uk-width-1-2@m">
    <h4 class="panel-p-title"><a href="/admin/{{ $component->name }}">Товары каталога</a></h4>
    <div class="uk-card uk-card-default uk-card-small">
        <div class="uk-card-body">
            @if(count($data) > 0)
                <table class="uk-table uk-table-small uk-table-hover uk-table-divider">
                    @foreach($data as $value)
                        <tr>
                            <td width="55">
                                <a href="/admin/{{ $component->name }}/{{ $value->id }}/edit">
                                    <img src="{{ $value->first_image_110 }}" alt="Photo">
                                </a>
                            </td>
                            <td>
                                <h4 class="uk-margin-remove-bottom"><a href="/admin/{{ $component->name }}/{{ $value->id }}/edit">{{ $value->title }}</a></h4>
                                @if($value->first_cost_value > 0)
                                    {{ $value->first_cost_value }} {{ $value->what }}
                                @else
                                    цена договорная
                                @endif
                            </td>
                            <td>
                                @if($value->active !== 1)
                                    <span class="uk-label uk-label-danger">Не опубликован</span>
                                @endif
                                <div class="uk-text-small">{{ \Carbon\Carbon::parse($value->updated_at)->format('d M Y h:i') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p>Товаров еще нет</p>
            @endif
            <a href="/admin/{{ $component->name }}/create" class="uk-button uk-button-default uk-width-1-1">Создать товар</a>
        </div>
    </div>
</div>