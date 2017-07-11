@extends('larrock::front.main')
@section('title') CATALOG ROOT @endsection

@section('content')
    <div class="uk-grid uk-grid-medium uk-grid-match">
        @each('larrock::front.catalog.blockCategory', $data, 'data')
    </div>
@endsection