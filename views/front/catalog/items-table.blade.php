@extends('larrock::front.main')
@section('title')
    @if($seo_midd['url'])
        {{ $seo_midd['url'] }}
    @else
        {{$seo_midd['catalog_category_prefix']}}{{$data->getSeo->seo_title or
        $data->title }}{{$seo_midd['catalog_category_postfix']}}{{ $seo_midd['postfix_global'] }}
    @endif
@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.category', $data) !!}

    <div class="catalog-filters uk-margin-top">
        @if(config('larrock.catalog.modules.sortCost', TRUE) === TRUE)
            @include('larrock::front.modules.filters.sortCost')
        @endif
        @if(config('larrock.catalog.modules.vid', TRUE) === TRUE)
            @include('larrock::front.modules.filters.vid')
        @endif
        @if(config('larrock.catalog.modules.itemsOnPage', TRUE) === TRUE)
            @include('larrock::front.modules.filters.itemsOnPage')
        @endif
    </div>
    <div class="catalog-filters uk-flex">
        @if(config('larrock.catalog.modules.lilu', TRUE) === TRUE)
            @include('larrock::front.modules.filters.lilu')
        @endif
    </div>

    @if($data->description_category_on_link || !empty($data->description))
        <ul class="uk-tab uk-margin-large-top" data-uk-switcher="{connect:'#catalogCategoryContent'}">
            <li class="uk-active"><a href="">Прайс</a></li>
            <li><a href="">Описание</a></li>
        </ul>
    @endif

    <ul id="catalogCategoryContent" class="uk-switcher">
        <li @if( !$data->description_category_on_link) class="uk-active" @endif>
            <div class="catalogPageCategoryItems row">
                <table class="uk-table uk-margin-top uk-margin-large-bottom">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Наименование</th>
                        @foreach($package->rows as $row_key => $row)
                            @if($row->template && ($row->template === 'category' || $row->template === 'all') && !empty($data->getGoodsActive->first()->{$row_key}))
                                <th>{{ $row->title }}</th>
                            @endif
                        @endforeach
                        <th>Цена</th>
                        @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                            <th style="width: 40px"></th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data->getGoodsActive as $item)
                        <tr>
                            <td class="col-img"><img src="{{ $item->first_image }}" alt="{{ $item->title }}" class="all-width"></td>
                            <td>
                                @if(config('larrock.catalog.ShowItemPage') === true)
                                    <a href="{{ $item->full_url }}">{{ $item->title }}</a>
                                @else
                                    {{ $item->title }}
                                @endif
                            </td>
                            @foreach($package->rows as $row_key => $row)
                                @if($row->template && ($row->template === 'category' || $row->template === 'all') && !empty($item->{$row_key}))
                                    <td>{{ $item->{$row_key} }}</td>
                                @endif
                            @endforeach
                            <td>
                                @if($item->cost > 0)
                                    {{ $item->cost }} <small class="uk-text-nowrap">{{ $item->what }}</small>
                                @else
                                    <small>цена договорная</small>
                                @endif
                            </td>
                            @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                                <td><img src="/_assets/_front/_images/icons/icon_cart.png" alt="Добавить в корзину" class="add_to_cart_fast pointer"
                                         width="40" height="25" data-id="{{ $item->id }}"></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $data->getGoodsActive->links('larrock::front.modules.pagination.uikit') }}
        </li>
        <li>
            <div class="catalogDescriptionTab uk-margin-large-top">
                @if( !empty($data->description))
                    <div class="catalog-CategoryDescription">
                        {!! $data->description !!}
                    </div>
                @endif

                @if(config('larrock.catalog.DescriptionCatalogCategoryLink') && $data->description_category_on_link)
                    @if($data->description_category_on_link->short)
                        <div class="description-link description-link-short">
                            {!! $data->description_category_on_link->short !!}
                        </div>
                    @endif
                    @if($data->description_category_on_link->description)
                        <div class="description-link description-link-description">
                            {!! $data->description_category_on_link->description !!}
                        </div>
                    @endif
                @endif
            </div>
        </li>
    </ul>
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection
