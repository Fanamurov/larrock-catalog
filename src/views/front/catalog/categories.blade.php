@extends('larrock::front.main')
@section('title')
    {{ $seo_midd['catalog_category_prefix'] }}
    {{ $data->first()->get_parent_seo_title }} 
    {{ $seo_midd['catalog_category_postfix'] }}{{ $seo_midd['postfix_global'] }}
@endsection
@section('body_class', 'template-catalog-category')

@section('content')
    @if($data->first()->parent)
        {!! Breadcrumbs::render('catalog.category', $data) !!}
    @endif

    <div class="uk-grid uk-grid-medium uk-grid-match uk-margin-large-top">
        @each('larrock::front.catalog.blockCategory', $data, 'data')
    </div>
@endsection