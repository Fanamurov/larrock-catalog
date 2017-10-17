<form action="" method="get" class="catalog-filters module-filter" id="block_sorters">
    @foreach($filter as $filter_key => $filter_value)
        @if(count($filter_value['values']) > 1)
            <div class="uk-button-dropdown uk-text-nowrap" data-uk-dropdown="{mode: 'click'}">
                <button class="uk-button" type="button">
                    {{ $filter_value['name'] }}:
                    @if(Request::has($filter_key))
                        @foreach(Request::get($filter_key) as $active_value)
                            {{ $active_value }}@if( !$loop->last), @endif
                        @endforeach
                    @else
                        Все
                    @endif
                    <i class="uk-icon-caret-down"></i>
                </button>
                <div class="uk-dropdown">
                    <ul class="uk-nav uk-nav-dropdown">
                        @foreach($filter_value['values'] as $value)
                            <li class="@if(collect(Request::get($filter_key))->contains($value->{$filter_key})) uk-active @endif @if( !isset($value->allow)) uk-disabled @endif">
                                @if( !empty($value->{$filter_key}))
                                    <label class="showModalLoading"><input type="checkbox" onchange="$('.module-filter').submit()"
                                                  name="{{$filter_key}}[]" value="{{ $value->{$filter_key} }}"
                                                  @if(collect(Request::get($filter_key))->contains($value->{$filter_key}) || count($filter_value['values']) === 1) checked @endif>
                                        {{ $value->{$filter_key} }}</label>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @endforeach
    @if(count(Request::all()) > 0 && !Request::has('page'))
        <div id="clear_filter"><a href="{{ URL::current() }}" class="uk-button">Сбросить фильтры</a></div>
    @endif
</form>