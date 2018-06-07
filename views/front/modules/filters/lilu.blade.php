@if(isset($filters))
    <form action="" method="get" class="catalog-filters module-filter uk-width-1-1" id="block_sorters">
        @foreach($filters as $key => $filter)
            @if(count($filter) > 1 || (count($filter) === 1) && Request::has($key))
                <div class="uk-button-dropdown uk-text-nowrap" data-uk-dropdown="{mode: 'click'}">
                    <button class="uk-button" type="button">
                        {{ $package->getRows()[$key]->title }}:
                        @if(Request::has($key) && is_array(Request::get($key)))
                            @foreach(Request::get($key) as $active_value)
                                {{ $active_value }}@if( !$loop->last), @endif
                            @endforeach
                        @else
                            Все
                        @endif
                        <i class="uk-icon-caret-down"></i>
                    </button>
                    <div class="uk-dropdown">
                        <ul class="uk-nav uk-nav-dropdown">
                            @foreach($filter as $value)
                                <li class="@if(is_array(Request::get($key)) && in_array($value, Request::get($key))) uk-active @endif">
                                    <label>
                                        <input @if(is_array(Request::get($key)) && in_array($value, Request::get($key))) checked @endif type="checkbox"
                                               onchange="$('.module-filter').submit()"
                                               name="{{ $key }}[]" value="{{ $value }}"> {{ $value }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endforeach

        @if(count(Request::except(['page'])) > 0)
            <div class="uk-width-1-1 uk-text-right"><a href="{{ URL::current() }}" class="uk-button">Сбросить фильтры</a></div>
        @endif
    </form>
@endif