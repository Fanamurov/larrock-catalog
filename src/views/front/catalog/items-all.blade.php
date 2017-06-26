@extends('larrock::front.main')
@section('title'){{$seo_midd['catalog_category_prefix']}} {{$data->get_seo->seo_title or
$seo['title'] }} {{$seo_midd['catalog_category_postfix']}}. {{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.all') !!}

    <div class="catalogPageCategoryItems row">
        @foreach($data as $value)
            @each('larrock::front.catalog.blockItem', $value->get_tovarsActive, 'data')
        @endforeach
    </div>

    <div class="Pagination catalogPagination">{!! $paginator->render() !!}</div>
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection