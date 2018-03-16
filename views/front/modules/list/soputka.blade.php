<div class="block-module-CatalogSoputka block-module uk-width-1-1">
    <p class="uk-h2 uk-h2-module-right">Сопутствующие товары</p>
    <div class="uk-grid">
        @each('larrock::front.catalog.blockItem', $SoputkaCatalogItems, 'data')
    </div>
</div>