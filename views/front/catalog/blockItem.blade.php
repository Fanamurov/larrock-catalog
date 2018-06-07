<div class="catalogBlockItem uk-width-1-2 uk-width-small-1-3 uk-width-medium-1-4 uk-width-xlarge-1-4 uk-margin-large-bottom uk-position-relative"
     id="product_{{ $data->id }}" itemscope itemtype="http://schema.org/Product" data-id="{{ $data->id }}">
    @level(2)
        <a class="admin_edit" href="/admin/catalog/{{ $data->id }}/edit">Edit element</a>
    @endlevel
    <div class="catalogImage @if(config('larrock.catalog.ShowItemPage', TRUE) === TRUE) link_block_this @endif" data-href="{{ $data->full_url }}">
        <img src="{{ $data->first_image }}" class="catalogImage max-width pointer" data-id="{{ $data->id }}" itemprop="image">
        @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
            <img src="/_assets/_front/_images/icons/icon_cart.png" alt="Добавить в корзину" title="Добавить в корзину" class="add_to_cart_fast pointer icon_cart"
                 data-id="{{ $data->id }}" data-costValueId="{{ $data->first_cost_value_id }}" width="40" height="25">
        @endif
        <div class="cost text-center" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            @if($data->cost_old > 0)
                <span class="old-cost">{{ $data->cost_old }}</span>
            @endif
            @if($data->first_cost_value > 0)
                <span class="default-cost"><span class="costValue">{{ $data->first_cost_value }}</span> <span class="what">{{ $data->what }}</span></span>
                <meta itemprop="price" content="{{ $data->first_cost_value }}">
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
            @if(config('larrock.catalog.ShowItemPage', TRUE) === TRUE)
                <a href="{{ $data->full_url }}">
                    {{ $data->title }}
                    @if($data->first_cost_value_title)
                        <span class="costValueTitle">{{ $data->first_cost_value_title }}</span>
                    @endif
                </a>
            @else
                {{ $data->title }} @if($data->first_cost_value_title) <span class="costValueTitle">{{ $data->first_cost_value_title }}</span> @endif
            @endif
        </h5>
        <div class="catalog-descriptions-rows" itemprop="description">
            @foreach($package->rows as $row_key => $row)
                @if($row->template && $row->template === 'in_card' && isset($data->{$row_key}) && !empty($data->{$row_key}))
                    <p class="catalog-d-{{ $row_key }}">{{ $data->{$row_key} }}</p>
                @endif
                @if(isset($row->costValue) && $row->costValue && count($data->cost_values) > 0)
                    <p>
                        @foreach($data->cost_values as $param)
                            <span class="changeParamValue @if($loop->first) uk-active @endif" data-tovar-id="{{ $data->id }}"
                                  data-param="{{ $param->id }}" data-cost="{{ $param->cost }}" data-title="{{ $param->title }}">{{ $param->title }}</span>
                        @endforeach
                    </p>
                @endif
            @endforeach
        </div>
    </div>
</div>