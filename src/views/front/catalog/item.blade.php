@extends('larrock::front.main')
@section('title'){{$seo_midd['catalog_category_prefix']}}{{$data->get_seo->seo_title or
$data->title }}{{$seo_midd['catalog_category_postfix']}}{{ $seo_midd['postfix_global'] }}@endsection

@section('content')
    {!! Breadcrumbs::render('catalog.item', $data) !!}

    <div class="catalogPageItem row">
        <h1>{{ $data->title }}</h1>
        <div class="row">
            <div class="col-xs-12">
                <div class="catalogImage">
                    @if(count($data->images) > 0)
                        <img src="{{ $data->images->first()->getUrl() }}" alt="{{ $data->title }}" class="TovarImage">
                    @endif
                    <div class="cost">
                        @if($data->cost == 0)
                            <span class="empty-cost">цена договорная</span>
                        @else
                            <span class="default-cost">&nbsp;&nbsp;&nbsp;&nbsp;{{ $data->cost }} <span class="what">{{ $data->what }}</span></span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                @if(file_exists(base_path(). '/vendor/fanamurov/larrock-cart'))
                    <div class="form-addToCart">
                        <div class="input-group">
                            <span class="input-group-addon addon-x">X</span>
                            <input type="text" class="form-control kolvo" id="kolvo-{{ $data->id }}" name="kolvo" value="{{ $data->min_part*1000 }}">
                            <span class="input-group-addon addon-what">кг</span>
                            <div class="input-group-btn">
                                <span class="btn btn-info pull-right">
                                    <img src="/_assets/_front/_images/icons/icon_cart_white.png"
                                         alt="Добавить в корзину" class="submit_to_cart pointer"
                                         data-id="{{ $data->id }}" width="32" height="32">
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                @endif
            </div>
        </div>
        <div class="row row-description">
            <div class="col-xs-12">
                <div class="catalogFull">
                    <div>{!! $data->description !!}</div>
                    <div class="catalog-descriptions-rows">
                        @foreach($app->rows as $row_key => $row)
                            @if($row->template === 'description' && isset($data->{$row_key}) && !empty($data->{$row_key}))
                                <p><strong>{{ $row->title }}:</strong> {{ $data->{$row_key} }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xs-12 other-photos">
                @if(count($data->images) > 1)
                    @foreach($data->images as $image)
                        @if($image->id !== $data->images->first()->id)
                            <div class="other-photos-bg" style="background-image: url('{!! $image->getUrl() !!}')"></div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection

@push('scripts')
    <script src="/_assets/bower_components/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="/_assets/bower_components/jquery-validation/dist/additional-methods.min.js"></script>
    <script>
        $( ".form-addToCart" ).validate({
            rules: {
                kolvo: {
                    required: true,
                    min: {{ $data->min_part*1000 }}
                }
            },
            messages: {
                kolvo: {
                    min: "Минимальная партия для заказа {{ $data->min_part*1000 }}",
                }
            }
        });
    </script>
@endpush