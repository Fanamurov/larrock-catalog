@extends('larrock::front.main')
@section('title'){{$seo_midd['catalog_category_prefix']}} {{$data->get_seo->seo_title or
$data->title }} {{$seo_midd['catalog_category_postfix']}}. {{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.category', $data) !!}

    <div class="catalog-filters">
        @include('larrock::front.modules.filters.sortCost')
        @include('larrock::front.modules.filters.vid')
        @include('larrock::front.modules.filters.itemsOnPage')
    </div>

    <div class="catalogPageCategoryItems row">
        <table class="table">
            <thead>
            <tr>
                <th>Наименование</th>
                <th>Описание</th>
                <th>Цена</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($data->get_tovarsActive as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->short }}</td>
                    <td>{{ $item->cost }} {{ $item->what }}</td>
                    <td><img src="/_assets/_front/_images/icons/icon_cart.png" alt="Добавить в корзину" class="add_to_cart pointer"
                             data-id="{{ $item->id }}" width="40" height="25"></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="Pagination catalogPagination">{!! $data->get_tovarsActive->render() !!}</div>
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection