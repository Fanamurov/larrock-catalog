@extends('larrock::front.main')
@section('title') {{ $data->title }} @endsection

@section('content')
    {!! Breadcrumbs::render('catalog.category', $data) !!}
    <div class="catalogPageCategory row">
        @each('larrock::front.catalog.blockCategory', $data->get_child, 'data')
    </div>
@endsection

@section('front.modules.list.catalog')
    @include('larrock::front.modules.list.catalog')
@endsection