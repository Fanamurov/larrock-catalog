@extends('larrock::front.main')
@section('title')
    @if($seo_midd['url'])
        {{ $seo_midd['url'] }}
    @else
        {{$seo_midd['catalog_category_prefix']}}{{$data->get_seo->seo_title or
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
        @if(config('larrock.catalog.modules.lilu', TRUE) === TRUE)
            @include('larrock::front.modules.filters.lilu')
        @endif
    </div>

    <div class="catalogPageCategoryItems row">
        <table class="uk-table uk-margin-large-top uk-margin-large-bottom">
            <thead>
            <tr>
                <th></th>
                <th>Наименование</th>
                <th>Цена</th>
                @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                    <th style="width: 40px"></th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($data->get_tovarsActive as $item)
                <tr>
                    <td class="col-img"><img src="{{ $item->first_image }}" alt="{{ $item->title }}" class="all-width"></td>
                    <td>
                        @if(config('larrock.catalog.ShowItemPage') === true)
                            <a href="{{ $item->full_url }}">{{ $item->title }}</a>
                        @else
                            {{ $item->title }}
                        @endif
                    </td>
                    <td>
                        @if($data->cost > 0)
                            {{ $item->cost }} <small>{{ $item->what }}</small>
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

    {{ $data->get_tovarsActive->links('larrock::front.modules.pagination.uikit') }}
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection