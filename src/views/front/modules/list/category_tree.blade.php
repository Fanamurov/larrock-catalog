<ul class="list-unstyled block-module_listCatalog">
@foreach($data->getChild as $value)
    <li @if($current_category === $value->url) class="active" @endif><a class="h4" href="{{ $value->full_url }}">{{ $value->title }}</a></li>
    @if(count($value->getChild) > 0)
        <ul class="list-unstyled">
    @endif
    @foreach($value->getChild as $child_value)
        <li @if($current_category === $child_value->url) class="active" @endif><a href="{{ $child_value->full_url }}">{{ $child_value->title }}</a></li>
    @endforeach
    @if(count($value->getChild) > 0)
        </ul>
    @endif
@endforeach
</ul>
<div class="clearfix"></div><br/><br/>