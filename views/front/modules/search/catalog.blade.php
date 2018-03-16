<form class="form-search uk-form" method="post" action="/search/catalog/serp">
    <div class="uk-form-row">
        <input type="text" name="query" placeholder="@yield('title_search', 'Поиск по каталогу')">
        <button class="uk-button" type="submit"><i class="uk-icon-search"></i></button>
        {{ csrf_field() }}
    </div>
</form>