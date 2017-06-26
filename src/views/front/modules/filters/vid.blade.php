<div class="module-filter-vid module-filter">
    <span class="vid">Вид:</span>
    <span class="change_catalog_template label link @if(Cookie::get('vid', 'cards') === 'cards') active @endif" data-value="cards">плитка</span>
    <span class="change_catalog_template label link @if(Cookie::get('vid', 'cards') === 'table') active @endif" data-value="table">таблица</span>
</div>