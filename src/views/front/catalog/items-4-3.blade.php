@extends('larrock::front.main')
@section('title')
    {{$seo_midd['catalog_category_prefix']}}{{$data->get_seo->seo_title or $data->title }}
    @foreach(Request::all() as $filter_title)
        @foreach($filter_title as $active_filters_title)
            {{ $active_filters_title }}
        @endforeach
    @endforeach
    {{$seo_midd['catalog_category_postfix']}}{{ $seo_midd['postfix_global'] }}
@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.category', $data) !!}

    <div class="catalog-filters uk-flex">
        @include('larrock::front.modules.filters.sortCost')
        @include('larrock::front.modules.filters.itemsOnPage')
        @include('larrock::front.modules.filters.vid')
        @include('larrock::front.modules.filters.lilu')
    </div>

    <div class="catalogPageCategoryItems uk-grid">
        @each('larrock::front.catalog.blockItem', $data->get_tovarsActive, 'data')
    </div>

    <div class="Pagination catalogPagination">{!! $data->get_tovarsActive->render() !!}</div>

    @if( !empty($data->description))
        <div class="catalog-CategoryDescription">
            {!! $data->description !!}
        </div>
    @endif
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection