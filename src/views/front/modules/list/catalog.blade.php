@if(isset($module_listCatalog['current']->title))
    <ul class="uk-nav uk-nav-parent-icon uk-nav-side block-listCatalog" data-uk-nav="{multiple:true}">
        @if(count($module_listCatalog['next_level']) > 0)
            <li class="current-level">
                <span>{{ $module_listCatalog['current']->title }}</span>
                <div class="uk-button-dropdown uk-float-right" data-uk-dropdown="{mode:'click'}" aria-haspopup="true" aria-expanded="false">
                    <button class="uk-button"><i class="uk-icon-caret-down"></i></button>
                    <div class="uk-dropdown" aria-hidden="true">
                        <ul class="uk-nav uk-nav-dropdown">
                            @foreach($module_listCatalog['current_level'] as $value)
                                <li @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $value->full_url
                        || $value->full_url === $module_listCatalog['current']->full_url) class="uk-active" @endif>
                                    <a href="{{ $value->full_url }}">{{ $value->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </li>

            @foreach($module_listCatalog['next_level'] as $item)
                <li class="next_level @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $item->full_url) uk-active @endif">
                    <a href="{{ $item->full_url }}">{{ $item->title }}</a>
                </li>
            @endforeach
        @else
            @foreach($module_listCatalog['current_level'] as $item)
                <li class="next_level @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $item->full_url) uk-active @endif">
                    <a href="{{ $item->full_url }}">{{ $item->title }}</a>
                </li>
            @endforeach
        @endif
    </ul>
@endif