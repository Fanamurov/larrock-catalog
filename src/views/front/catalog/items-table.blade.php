@extends('larrock::front.main')
@section('title'){{$seo_midd['catalog_category_prefix']}} {{$data->get_seo->seo_title or
$data->title }} {{$seo_midd['catalog_category_postfix']}}. {{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.category', $data) !!}

    <div class="catalog-filters uk-margin-top">
        @include('larrock::front.modules.filters.sortCost')
        @include('larrock::front.modules.filters.vid')
        @include('larrock::front.modules.filters.itemsOnPage')
        @include('larrock::front.modules.filters.lilu')
    </div>

    <div class="catalogPageCategoryItems row">
        <table class="uk-table uk-margin-large-top uk-margin-large-bottom">
            <thead>
            <tr>
                <th></th>
                <th>Наименование</th>
                <th>Хаб</th>
                <th>Бел</th>
                <th>Нов</th>
                <th>СПб</th>
                <th>Цена</th>
                @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                    <th style="width: 40px"></th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($data->get_tovarsActive as $item)
                <tr>
                    <td><img src="{{ $item->first_image }}" alt="{{ $item->title }}"></td>
                    <td>{{ $item->title }}</td>
                    <td>
                        @if($item->khabarovsk)
                            <i class="uk-icon-check"></i>
                        @else
                            <i class="uk-icon-close"></i>
                        @endif
                    </td>
                    <td>
                        @if($item->belgorod)
                            <i class="uk-icon-check"></i>
                        @else
                            <i class="uk-icon-close"></i>
                        @endif
                    </td>
                    <td>
                        @if($item->novosibirsk)
                            <i class="uk-icon-check"></i>
                        @else
                            <i class="uk-icon-close"></i>
                        @endif
                    </td>
                    <td>
                        @if($item->s_peterburg)
                            <i class="uk-icon-check"></i>
                        @else
                            <i class="uk-icon-close"></i>
                        @endif
                    </td>
                    <td>{{ $item->cost }} <small>руб./{{ $item->what }}</small></td>
                    @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                        <td><img src="/_assets/_front/_images/icons/icon_cart.png" alt="Добавить в корзину" class="add_to_cart pointer"
                                 width="40" height="25" data-id="{{ $item->id }}"></td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $data->get_tovarsActive->links('larrock::front.modules.pagination.uikit') }}
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection