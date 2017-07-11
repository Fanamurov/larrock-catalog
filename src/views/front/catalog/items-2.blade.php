@extends('larrock::front.main')

@section('title'){{$seo_midd['catalog_category_prefix']}}{{$data->get_seo->seo_title or
$data->title }}{{$seo_midd['catalog_category_postfix']}}{{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.items', $data) !!}

    <div class="catalogPageCategoryItems row">
        @each('larrock::front.catalog.blockItem', $data->get_tovarsActive, 'data')
    </div>

    {{ $data->get_tovarsActive->links('larrock::front.modules.pagination.uikit') }}
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection