@extends('larrock::admin.main')
@section('title') {{ $app->name }} admin @endsection

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="add-panel uk-margin-bottom uk-text-right">
            <a class="uk-button" href="#modal-help" data-uk-modal="{target:'#modal-help'}"><i class="uk-icon-question"></i></a>
            <a class="uk-button uk-button-primary" href="/admin/{{ $app->name }}/create">Добавить товар</a>
            <a href="#add_category" class="uk-button uk-button-primary show-please" data-target="create-category" data-focus="create-category-title">Добавить раздел</a>
        </div>
        <div id="modal-help" class="uk-modal">
            <div class="uk-modal-dialog">
                <a class="uk-modal-close uk-close"></a>
                <p>{{ $app->description }}</p>
            </div>
        </div>
        <div class="uk-clearfix"></div>
        {!! Breadcrumbs::render('admin.'. $app->name .'.index') !!}
        <div class="uk-clearfix"></div>
        <a class="link-blank" href="/{{ $app->name }}/">/{{ $app->name }}/</a>
    </div>
    <div id="modal-help" class="uk-modal">
        <div class="uk-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            <p>{{ $app->description }}</p>
        </div>
    </div>
    <div class="uk-clearfix"></div>

    @if(count($nalichie) === 0)
        <div class="uk-alert uk-alert-success">Все товары в наличии</div>
    @else
        <div class="uk-margin-large-bottom">
            @if(count($nalichie) === 0)
                <div class="uk-alert uk-alert-success">Все товары в наличии</div>
            @else
                <p class="uk-h2">Товары не в наличии</p>
                <table class="uk-table uk-table-striped uk-form">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Название</th>
                        <th class="uk-hidden-small">Метки</th>
                        <th width="90" class="uk-hidden-small">В наличии</th>
                        <th width="90" class="uk-hidden-small">Цена</th>
                        <th width="90" class="uk-hidden-small">Цена промо</th>
                        <th width="90" class="uk-hidden-small">Порядок</th>
                        <th width="93"></th>
                        <th width="90" class="uk-hidden-small"></th>
                        <th width="90" class="uk-hidden-small"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @include('larrock::admin.catalog.include-list-tovars', array('data' => $nalichie))
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    @if(isset($data))
        <div class="uk-margin-large-bottom">
            <table class="uk-table uk-table-striped uk-form uk-margin-large-bottom">
                <thead>
                <tr>
                    <th></th>
                    <th>Заголовок</th>
                    @include('larrock::admin.admin-builder.additional-rows-th')
                </tr>
                </thead>
                <tbody>
                @foreach($data as $data_value)
                    <tr>
                        <td width="70">
                            <a href="/admin/{{ $app->name }}/{{ $data_value->id }}/edit">
                                @if(array_key_exists('images', $app->plugins_backend) && $image = $data_value->getMedia('images')->sortByDesc('order_column')->first())
                                    <img style="width: 55px" src="{{ $image->getUrl('110x110') }}">
                                @else
                                    <i class="icon-padding icon-color glyphicon glyphicon-picture"></i>
                                @endif
                            </a>
                        </td>
                        <td>
                            <a class="h4" href="/admin/{{ $app->name }}/{{ $data_value->id }}/edit">{{ $data_value->title }}</a>
                            <br/>
                            <a class="link-to-front" target="_blank" href="{{ $data_value->full_url }}" title="ссылка на элемент на сайте">
                                {{ str_limit($data_value->full_url, 35, '...') }}
                            </a>
                        </td>
                        @include('larrock::admin.admin-builder.additional-rows-td')
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if(count($data) === 0)
                <div class="uk-alert uk-alert-warning">Данных еще нет</div>
            @endif
            {!! $data->render() !!}
        </div>
    @endif

    @if(isset($categories))
        <p class="uk-h4">Разделы:</p>
        <div class="uk-margin-large-bottom">
            <table class="uk-table uk-table-striped uk-form">
                <thead>
                <tr>
                    <th></th>
                    <th>Название</th>
                    <th class="uk-hidden-small">Вес</th>
                    <th>Активность</th>
                    @include('larrock::admin.admin-builder.additional-rows-th')
                </tr>
                </thead>
                <tbody>
                @include('larrock::admin.category.include-create-easy', array('parent' => 0, 'component' => $app->name))
                @if(count($categories) === 0)
                    <div class="uk-alert uk-alert-danger">Разделов еще нет</div>
                @else
                    @include('larrock::admin.category.include-list-categories', array('data' => $categories))
                @endif
                </tbody>
            </table>
            {!! $categories->render() !!}
        </div>
    @endif
@endsection