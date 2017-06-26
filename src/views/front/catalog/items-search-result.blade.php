@extends('larrock::front.main')
@section('title') Поиск по каталогу - "{{ $words }}" @endsection

@section('content')
    {!! Breadcrumbs::render('catalog.search') !!}

    <div class="catalog-filters">
        @include('larrock::front.modules.filters.vid')
        @include('larrock::front.modules.filters.itemsOnPage')
    </div>

    <div class="catalogPageCategoryItems row">
        @each('larrock::front.catalog.blockItem', $data, 'data')
    </div>

    <div class="Pagination catalogPagination">{!! $data->render() !!}</div>
@endsection

@section('title_search') Поиск: {{ $words }} @endsection