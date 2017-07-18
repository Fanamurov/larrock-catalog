<div class="module-filter-vid module-filter nowrap">
    <span class="label">Вид:</span>
    <span class="change_catalog_template uk-link @if(Cookie::get('vid', 'cards') === 'cards') uk-active @endif" data-value="cards">плитка</span>
    <span class="change_catalog_template uk-link @if(Cookie::get('vid', 'cards') === 'table') uk-active @endif" data-value="table">таблица</span>
</div>