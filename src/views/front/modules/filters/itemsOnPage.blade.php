<div class="module-filter-itemsOnPage module-filter uk-text-nowrap">
    <span class="label">Позиций на стр:</span>
    <span class="change_option_ajax change_limit uk-link @if(Cookie::get('perPage', config('larrock.catalog.DefaultItemsOnPage', 36)) == 24) uk-active @endif"
          data-value="24" data-option="editPerPage">24</span>
    <span class="change_option_ajax change_limit uk-link @if(Cookie::get('perPage', config('larrock.catalog.DefaultItemsOnPage', 36)) == 36) uk-active @endif"
          data-value="{{ config('larrock.catalog.DefaultItemsOnPage', 36) }}"
          data-option="editPerPage">{{ config('larrock.catalog.DefaultItemsOnPage', 36) }}</span>
    <span class="change_option_ajax change_limit uk-link @if(Cookie::get('perPage', config('larrock.catalog.DefaultItemsOnPage', 36)) == 96) uk-active @endif"
          data-value="96" data-option="editPerPage">96</span>
</div>