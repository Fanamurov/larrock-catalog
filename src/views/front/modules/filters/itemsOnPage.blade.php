<div class="module-filter-itemsOnPage module-filter nowrap">
    <span class="label">Позиций на стр:</span>
    <span class="change_limit uk-link @if(Cookie::get('perPage', 24) == 12) uk-active @endif" data-value="12">12</span>
    <span class="change_limit uk-link @if(Cookie::get('perPage', 24) == 24) uk-active @endif" data-value="24">24</span>
    <span class="change_limit uk-link @if(Cookie::get('perPage', 24) == 96) uk-active @endif" data-value="96">96</span>
</div>