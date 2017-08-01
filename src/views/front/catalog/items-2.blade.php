@extends('larrock::front.main')

@section('title'){{$seo_midd['catalog_category_prefix']}}{{$data->get_seo->seo_title or
$data->title }}{{$seo_midd['catalog_category_postfix']}}{{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.items', $data) !!}

    <div class="catalog-filters uk-flex">
        @if(config('larrock.catalog.modules.sortCost', TRUE) === TRUE)
            @include('larrock::front.modules.filters.sortCost')
        @endif
        @if(config('larrock.catalog.modules.lilu', TRUE) === TRUE)
            @include('larrock::front.modules.filters.lilu')
        @endif
        @if(config('larrock.catalog.modules.itemsOnPage', TRUE) === TRUE)
            @include('larrock::front.modules.filters.itemsOnPage')
        @endif
        @if(config('larrock.catalog.modules.vid', TRUE) === TRUE)
            @include('larrock::front.modules.filters.vid')
        @endif
    </div>

    <div class="catalogPageCategoryItems row">
        @each('larrock::front.catalog.blockItem', $data->get_tovarsActive, 'data')
    </div>

    {{ $data->get_tovarsActive->links('larrock::front.modules.pagination.uikit') }}
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection