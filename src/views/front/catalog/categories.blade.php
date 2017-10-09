@extends('larrock::front.main')
@section('title')
	@if($seo_midd['url'])
        {{ $seo_midd['url'] }}
    @else
        {{ $seo_midd['catalog_category_prefix'] }}{{ $data->first()->get_parent_seo_title }}{{ $seo_midd['catalog_category_postfix'] }}{{ $seo_midd['postfix_global'] }}
    @endif
@endsection
@section('body_class', 'template-catalog-category')

@section('content')
    @if($data->first()->parent && $data->first()->level !== 2)
        {!! Breadcrumbs::render('catalog.category', $data) !!}
    @endif

    <div class="uk-grid uk-grid-large uk-grid-match uk-margin-top">
        @each('larrock::front.catalog.blockCategory', $data, 'data')
    </div>
@endsection