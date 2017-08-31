<div class="catalogBlockItem uk-width-1-2 uk-width-small-1-3 uk-width-medium-1-4 uk-width-xlarge-1-4 uk-margin-large-bottom uk-position-relative" id="product_{{ $data->id }}">
    @level(2)
        <a class="admin_edit" href="/admin/catalog/{{ $data->id }}/edit">Edit element</a>
    @endlevel
    <div class="catalogImage link_block_this111" data-href="{!! URL::current() !!}/{{ $data->url }}">
        <img src="{{ $data->first_image }}" class="catalogImage max-width @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart')) action_add_to_cart @endif pointer" data-id="{{ $data->id }}">
        @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
            <img src="/_assets/_front/_images/icons/icon_cart.png" alt="Добавить в корзину" class="add_to_cart pointer"
                 data-id="{{ $data->id }}" width="40" height="25">
        @endif
        <div class="cost text-center">
            @if($data->cost === 0)
                <span class="empty-cost">под заказ</span>
            @else
                @if($data->cost_old > 0)
                    <span class="old-cost">{{ $data->cost_old }}</span>
                    <span class="default-cost">{{ $data->cost }} <span class="what">{{ $data->what }}</span></span>
                @else
                    <span class="default-cost">{{ $data->cost }} <span class="what">{{ $data->what }}</span></span>
                @endif
            @endif
        </div>
    </div>
    <div class="catalogShort">
        <h5 class="uk-text-center">{{ $data->title }}</h5>
        <div class="catalog-descriptions-rows">
            @foreach($app->rows as $row_key => $row)
                @if($row->template && $row->template === 'in_card' && isset($data->{$row_key}) && !empty($data->{$row_key}))
                    <p><strong>{{ $row->title }}:</strong> {{ $data->{$row_key} }}</p>
                @endif
            @endforeach
        </div>
        <p>{!! $data->short !!}</p>
    </div>
</div>