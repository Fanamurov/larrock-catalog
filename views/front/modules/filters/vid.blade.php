<div class="module-filter-vid module-filter uk-text-nowrap">
    <span class="label">Вид:</span>
    <span class="change_option_ajax change_catalog_template uk-link @if(Cookie::get('vid', 'cards') === 'cards') uk-active @endif" data-value="cards" data-option="vid">плитка</span>
    <span class="change_option_ajax change_catalog_template uk-link @if(Cookie::get('vid', 'cards') === 'table') uk-active @endif" data-value="table" data-option="vid">таблица</span>
</div>