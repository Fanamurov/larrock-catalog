@extends('larrock::front.main')
@section('title'){{ $data->get_seo_title }}@endsection

@section('content')
    <h1>{{ $data->title }}</h1>
    <div class="uk-grid uk-grid-medium uk-grid-match">
        @each('larrock::front.catalog.blockCategory', $data->get_childActive, 'data')
    </div>
@endsection