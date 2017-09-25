<div class="catalogBlockItem uk-width-1-2 uk-width-small-1-3 uk-width-medium-1-4 uk-width-xlarge-1-4 uk-margin-large-bottom uk-position-relative"
     id="product_{{ $data->id }}" itemscope itemtype="http://schema.org/Product">
    @level(2)
        <a class="admin_edit" href="/admin/catalog/{{ $data->id }}/edit">Edit element</a>
    @endlevel
    <div class="catalogImage @if(config('larrock.catalog.ShowItemPage') === true) link_block_this @endif" data-href="{{ $data->full_url }}">
        <img src="{{ $data->first_image }}" class="catalogImage max-width pointer" data-id="{{ $data->id }}" itemprop="image">
        @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
            <img src="/_assets/_front/_images/icons/icon_cart.png" alt="Добавить в корзину" title="Добавить в корзину" class="add_to_cart_fast pointer icon_cart"
                 data-id="{{ $data->id }}" width="40" height="25">
        @endif
        <div class="cost text-center" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            @if($data->cost_old > 0)
                <span class="old-cost">{{ $data->cost_old }}</span>
            @endif
            @if($data->cost > 0)
                <span class="default-cost">{{ $data->cost }} <span class="what">{{ $data->what }}</span></span>
                <meta itemprop="price" content="{{ $data->cost }}">
                <meta itemprop="priceCurrency" content="RUB">
                <link itemprop="availability" href="http://schema.org/InStock">
            @else
                <span class="empty-cost"><span>цена</span>договорная</span>
                <meta itemprop="price" content="под заказ">
                <meta itemprop="priceCurrency" content="RUB">
                <link itemprop="availability" href="http://schema.org/PreOrder">
            @endif
        </div>
    </div>
    <div class="catalogShort">
        <h5 itemprop="name">
            @if(config('larrock.catalog.ShowItemPage') === true)
                <a href="{{ $data->full_url }}">{{ $data->title }}</a>
            @else
                {{ $data->title }}
            @endif
        </h5>
        <div class="catalog-descriptions-rows" itemprop="description">
            @foreach($app->rows as $row_key => $row)
                @if($row->template && $row->template === 'in_card' && isset($data->{$row_key}) && !empty($data->{$row_key}))
                    <p class="catalog-d-{{ $row_key }}">{{ $data->{$row_key} }}</p>
                @endif
            @endforeach
        </div>
    </div>
</div>