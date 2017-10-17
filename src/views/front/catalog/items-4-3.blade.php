@extends('larrock::front.main')
@section('title')
    @if($seo_midd['url'])
        {{ $seo_midd['url'] }}
    @else
        {{ $seo_midd['catalog_category_prefix'] }}
        {{ $data->get_seo->seo_title or $data->title }}@foreach(Request::all() as $filter_title)
            @if(is_array($filter_title))
                @foreach($filter_title as $active_filters_title)
                    {{ $active_filters_title }}
                @endforeach
            @endif
        @endforeach{{$seo_midd['catalog_category_postfix']}}{{ $seo_midd['postfix_global'] }}
    @endif
@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.category', $data) !!}

    <div class="catalog-filters uk-flex">
        @if(config('larrock.catalog.modules.sortCost', TRUE) === TRUE)
            @include('larrock::front.modules.filters.sortCost')
        @endif
        @if(config('larrock.catalog.modules.vid', TRUE) === TRUE)
            @include('larrock::front.modules.filters.vid')
        @endif
        @if(config('larrock.catalog.modules.itemsOnPage', TRUE) === TRUE)
            @include('larrock::front.modules.filters.itemsOnPage')
        @endif
    </div>
    <div class="catalog-filters uk-flex">
        @if(config('larrock.catalog.modules.lilu', TRUE) === TRUE)
            @include('larrock::front.modules.filters.lilu')
        @endif
    </div>

    @if($data->description_category_on_link)
        <ul class="uk-tab uk-margin-large-top" data-uk-switcher="{connect:'#catalogCategoryContent'}">
            <li class="uk-active"><a href="">Прайс</a></li>
            <li><a href="">Описание</a></li>
        </ul>
    @endif

    <ul id="catalogCategoryContent" class="uk-switcher">
        <li @if( !$data->description_category_on_link) class="uk-active" @endif>
            <div class="catalogPageCategoryItems uk-grid uk-margin-large-top uk-margin-large-bottom">
                @each('larrock::front.catalog.blockItem', $data->get_tovarsActive, 'data')
            </div>

            {{ $data->get_tovarsActive->links('larrock::front.modules.pagination.uikit') }}
        </li>
        <li>
            <div class="catalogDescriptionTab uk-margin-large-top">
                @if( !empty($data->description))
                    <div class="catalog-CategoryDescription">
                        {!! $data->description !!}
                    </div>
                @endif

                @if(config('larrock.catalog.DescriptionCatalogCategoryLink') && $data->description_category_on_link)
                    @if($data->description_category_on_link->short)
                        <div class="description-link description-link-short">
                            {!! $data->description_category_on_link->short !!}
                        </div>
                    @endif
                    @if($data->description_category_on_link->description)
                        <div class="description-link description-link-description">
                            {!! $data->description_category_on_link->description !!}
                        </div>
                    @endif
                @endif
            </div>
        </li>
    </ul>

@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection