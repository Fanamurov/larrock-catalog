<div class="block-module-RandomCatalogItems block-module uk-width-1-1">
    <p class="uk-h2 uk-h2-module-right">Продажа оптом</p>
    @foreach($RandomCatalogItems as $data)
        <div class="catalogBlockItem uk-position-relative uk-grid uk-grid-medium" id="product_{{ $data->id }}">
            @level(2)
            <a class="admin_edit" href="/admin/catalog/{{ $data->id }}/edit">Edit element</a>
            @endlevel
            <div class="catalogImage link_block_this uk-width-1-2" data-href="{{ $data->full_url }}">
                <img src="{{ $data->first_image }}" class="catalogImage max-width pointer" data-id="{{ $data->id }}">
            </div>
            <div class="catalogShort uk-width-1-2">
                <p class="uk-h3"><a href="{{ $data->full_url }}">{{ $data->title }}</a></p>
                <p class="category">Раздел: <a href="{{ $data->get_category()->first()->full_url }}">{{ $data->get_category()->first()->title }}</a></p>
            </div>
        </div>
    @endforeach
</div>