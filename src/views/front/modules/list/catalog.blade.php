@if(isset($module_listCatalog['current']->title))
    <ul class="block-module_listCatalog list-unstyled">
        <li class="parent_level @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $module_listCatalog['current']->full_url) active @endif">
            <a href="#" class="dropdown-toggle"
               data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                {{ $module_listCatalog['current']->title }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                @foreach($module_listCatalog['current_level'] as $value)
                    <li @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $value->full_url
                    OR $value->full_url === $module_listCatalog['current']->full_url) class="active" @endif>
                        <a href="{{ $value->full_url }}">{{ $value->title }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        @foreach($module_listCatalog['next_level'] as $item)
            <li class="next_level @if(URL::current() === 'http://'.$_SERVER['SERVER_NAME'] . $item->full_url) active @endif">
                <a href="{{ $item->full_url }}">{{ $item->title }}</a>
            </li>
        @endforeach
    </ul>
@endif