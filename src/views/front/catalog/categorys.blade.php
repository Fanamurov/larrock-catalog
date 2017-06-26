@extends('larrock::front.main')
@section('title'){{ $seo['title'] }}@endsection

@section('content')
    <div class="catalogPageCategory row">
        @each('larrock::front.catalog.blockCategory', $data, 'data')
    </div>
@endsection