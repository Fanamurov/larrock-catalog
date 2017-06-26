<form action="" method="get" class="catalog-filters" id="block_sorters">
    @foreach($filter as $filter_key => $filter_value)
        @if(count($filter_value['values']) > 0)
            <div class="dropdown block_lilu_item pull-left">
                <p class="pull-left lilu-label btn-group-label">{{ $filter_value['name'] }}:</p>
                <div class="btn-group">
                    <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="btn-group-label">
                                    @if(Request::has($filter_key))
                                        @foreach(Request::get($filter_key) as $active_value)
                                            {{ $active_value }}@if( !$loop->last), @endif
                                        @endforeach
                                    @else
                                        Все
                                    @endif
                                </span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                        @foreach($filter_value['values'] as $value)
                            <li class="@if(collect(Request::get($filter_key))->contains($value->{$filter_key})) active @endif @if( !isset($value->allow)) disabled @endif">
                                @if( !empty($value->{$filter_key}))
                                    <label class="lilu-checkbox"><input type="checkbox" name="{{$filter_key}}[]" value="{{ $value->{$filter_key} }}"
                                                                        @if(collect(Request::get($filter_key))->contains($value->{$filter_key})) checked @endif
                                                                        @if( !isset($value->allow)) disabled @endif
                                        > <span>{{ $value->{$filter_key} }}</span></label>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @endforeach
    @if(count(Request::all()) > 0)
        <div id="clear_filter"><a href="{{ URL::current() }}" class="btn">Сбросить фильтры</a></div>
    @endif
</form>