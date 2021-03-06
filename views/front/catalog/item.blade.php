@extends('larrock::front.main')
@section('title'){{$seo_midd['catalog_item_prefix']}}{{$data->getSeo->seo_title or
$data->title }}{{$seo_midd['catalog_item_postfix']}}{{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.item', $data) !!}

    <div class="catalogPageItem uk-margin-large-top uk-position-relative" itemscope itemtype="http://schema.org/Product">
        @level(2)
        <a class="admin_edit" href="/admin/catalog/{{ $data->id }}/edit">Edit element</a>
        @endlevel
        <div class="uk-grid">
            <div class="uk-width-1-1 uk-width-medium-1-2 uk-width-large-1-3">
                <div class="catalogImage">
                    <a data-fancybox="gallery" data-caption="{{ $data->title }}" href="{{ $data->first_image }}">
                        <img src="{{ $data->first_image }}" class="catalogImage all-width" itemprop="image"></a>
                </div>
            </div>
            <div class="uk-width-1-1 uk-width-medium-1-2 uk-width-large-2-3">
                <h1 itemprop="name">{{ $data->title }}</h1>
                <div class="catalog-description">
                    <div class="default-description" itemprop="description">{!! $data->description !!}</div>
                    <div class="catalog-descriptions-rows">
                        @foreach($package->rows as $row_key => $row)
                            @if($row->template === 'in_card' && isset($data->{$row_key}) && !empty($data->{$row_key}))
                                <p><strong>{{ $row->title }}:</strong> {{ $data->{$row_key} }}</p>
                            @endif
                            @if(isset($row->costValue) && $row->costValue && count($data->cost_values) > 0)
                                <p>Варианты поставки:</p>
                                <ul class="uk-list">
                                    @foreach($data->cost_values as $param)
                                        <li><label class="changeCostValue">
                                                <input value="{{ $param->cost }}" data-costValueId="{{ $param->id }}" type="radio" name="costValue" @if($loop->first) checked @endif>
                                            {{ $param->title }} - {{ $param->cost }} {{ $data->what }}</label></li>
                                    @endforeach
                                </ul>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="cost" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    @if($data->first_cost_value == 0)
                        <span class="empty-cost" itemprop="price">цена договорная</span>
                        <meta itemprop="price" content="под заказ">
                        <meta itemprop="priceCurrency" content="RUB">
                        <link itemprop="availability" href="http://schema.org/PreOrder">
                    @else
                        Цена: <span class="default-cost" itemprop="price"><span class="cost_value">{{ $data->first_cost_value }}</span> <span class="what">{{ $data->what }}</span></span>
                        <meta itemprop="price" content="{{ $data->first_cost_value }}">
                        <meta itemprop="priceCurrency" content="RUB">
                        <link itemprop="availability" href="http://schema.org/InStock">
                    @endif
                </div>
                @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                    <div class="add-to-cart uk-button uk-button-large uk-button-primary add_to_cart_fast"
                         data-id="{{ $data->id }}" data-costValueId="{{ $data->first_cost_value_id }}">
                        Добавить в корзину
                    </div>
                @endif
            </div>
            <div class="uk-width-1-1">
                @if(config('larrock.catalog.DescriptionCatalogItemLink') && $data->description_item_on_link)
                    <div class="uk-margin-large-top">
                        @if($data->description_item_on_link->short)
                            <div class="description-link description-link-short">
                                {!! $data->description_item_on_link->short !!}
                            </div>
                        @endif
                        @if($data->description_item_on_link->description)
                            <div class="description-link description-link-description">
                                {!! $data->description_item_on_link->description !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(isset($SoputkaCatalogItems) && \count($SoputkaCatalogItems) > 0)
        <div class="uk-margin-large-top">
            @include('larrock::front.modules.list.soputka')
        </div>
    @endif
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection