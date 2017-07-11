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

    {{ $data->links('larrock::front.modules.pagination.uikit') }}
@endsection

@section('title_search') Поиск: {{ $words }} @endsection