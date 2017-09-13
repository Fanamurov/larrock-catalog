@extends('larrock::front.main')
@section('title') Поиск по каталогу - "{{ $words }}" @endsection

@section('content')
    {!! Breadcrumbs::render('catalog.search') !!}

    <div class="catalog-filters">
        @if(config('larrock.catalog.modules.vid', TRUE) === TRUE)
            @include('larrock::front.modules.filters.vid')
        @endif
        @if(config('larrock.catalog.modules.itemsOnPage', TRUE) === TRUE)
            @include('larrock::front.modules.filters.itemsOnPage')
        @endif
    </div>

    <div class="uk-grid uk-grid-medium uk-grid-match uk-margin-large-top">
        @each('larrock::front.catalog.blockItem', $data, 'data')
    </div>

    {{ $data->links('larrock::front.modules.pagination.uikit') }}
@endsection