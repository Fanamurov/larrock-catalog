@extends('larrock::front.main')
@section('title') Поиск по каталогу - "{{ $words }}" @endsection

@section('content')
    {!! Breadcrumbs::render('catalog.search') !!}

    <div class="catalog-filters">
        @include('larrock::front.modules.filters.vid')
        @include('larrock::front.modules.filters.itemsOnPage')
    </div>

    <div class="uk-grid uk-grid-medium uk-grid-match uk-margin-large-top">
        @each('larrock::front.catalog.blockItem', $data, 'data')
    </div>

    {{ $data->links('larrock::front.modules.pagination.uikit') }}
@endsection