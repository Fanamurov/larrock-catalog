<div class="catalogBlockItem uk-width-1-2 uk-width-small-1-3 uk-width-medium-1-4 uk-width-xlarge-1-4" id="product_{{ $data->id }}">
    @level(2)
        <a class="admin_edit" href="/admin/catalog/{{ $data->id }}/edit">Edit element</a>
    @endlevel
    <div class="catalogImage link_block_this" data-href="{{ $data->full_url }}">
        <img src="{{ $data->first_image }}" class="catalogImage max-width pointer" data-id="{{ $data->id }}" >
        <div class="cost text-center">
            @if($data->cost == 0)
                <span class="empty-cost">под заказ</span>
            @else
                @if($data->cost_old > 0)
                    <span class="old-cost">{{ $data->cost_old }} <span class="what">{{ $data->what }}</span></span><br/>
                    <span class="default-cost">{{ $data->cost }} <span class="what">{{ $data->what }}</span></span>
                @else
                    <span class="default-cost">{{ $data->cost }} <span class="what">{{ $data->what }}</span></span>
                @endif
            @endif
        </div>
    </div>
    <div class="catalogShort">
        <h5>
            <a href="{{ $data->full_url }}">{{ $data->title }}</a>
        </h5>
        <div class="catalog-descriptions-rows">
            @foreach($app->rows as $row)
                @if($row->template === 'description' && isset($data->{$row->name}) && !empty($data->{$row->name}))
                    <p><strong>{{ $row->title }}:</strong> {{ $data->{$row->name} }}</p>
                @endif
            @endforeach
        </div>
        <p>{!! $data->short !!}</p>
    </div>
</div>