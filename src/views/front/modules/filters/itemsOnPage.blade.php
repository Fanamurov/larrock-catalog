<div class="module-filter-itemsOnPage module-filter">
    <span class="vid">Позиций на стр.:</span>
    <span class="change_limit label link @if(Cookie::get('perPage', 24) == 12) active @endif" data-value="12">12</span>
    <span class="change_limit label link @if(Cookie::get('perPage', 24) == 24) active @endif" data-value="24">24</span>
    <span class="change_limit label link @if(Cookie::get('perPage', 24) == 96) active @endif" data-value="96">96</span>
</div>