@if(isset($module_listCatalog['current']->title))
    <ul class="uk-nav uk-nav-parent-icon uk-nav-side block-module-listCatalog block-module uk-width-1-1" data-uk-nav="{multiple:true}">
        <li class="not-hover">
            @if(count($module_listCatalog['next_level']) === 0 && count($module_listCatalog['parent_level']) > 0)
                <a href="{{ $module_listCatalog['parent']->full_url }}" class="up_button"><img src="/_assets/_front/_images/up_button.png" alt="Наверх"></a>
            @else
                <a href="/" class="up_button"><img src="/_assets/_front/_images/up_button.png" alt="Наверх"></a>
            @endif
        </li>
        @if(count($module_listCatalog['next_level']) > 0)
            <li class="current-level">
                <span class="uk-active uk-width-1-1 @if(count($module_listCatalog['current_level'] ) > 1) with-dropdown @endif">
                    Все <span class="uk-text-lowercase">{{ $module_listCatalog['current']->title }}</span></span>
                @if(count($module_listCatalog['current_level']) > 1)
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
                @endif
            </li>

            @foreach($module_listCatalog['next_level'] as $item)
                <li class="next_level @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $item->full_url) uk-active @endif">
                    <h5 class="link_block"><a href="{{ $item->full_url }}">{{ $item->title }}</a></h5>
                </li>
            @endforeach
        @else
            @if(count($module_listCatalog['parent_level']) > 0)
                <li class="current-level">
                    <span @if(count($module_listCatalog['parent_level'] ) > 1) class="with-dropdown" @endif>
                        <a href="{{ $module_listCatalog['parent']->full_url }}">Все <span class="uk-text-lowercase">{{ $module_listCatalog['parent']->title }}</span></a></span>
                    @if(count($module_listCatalog['parent_level']) > 1)
                        <div class="uk-button-dropdown uk-float-right" data-uk-dropdown="{mode:'click'}" aria-haspopup="true" aria-expanded="false">
                            <button class="uk-button"><i class="uk-icon-caret-down"></i></button>
                            <div class="uk-dropdown" aria-hidden="true">
                                <ul class="uk-nav uk-nav-dropdown">
                                    @foreach($module_listCatalog['parent_level'] as $value)
                                        <li>
                                            <a href="{{ $value->full_url }}">{{ $value->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </li>
            @endif
            @foreach($module_listCatalog['current_level'] as $item)
                <li class="next_level @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $item->full_url) uk-active @endif
                        @if($module_listCatalog['current']->full_url === $item->full_url) uk-active @endif">
                    <h5 class="link_block"><a href="{{ $item->full_url }}">{{ $item->title }}</a></h5>
                </li>
            @endforeach
        @endif
    </ul>
@endif