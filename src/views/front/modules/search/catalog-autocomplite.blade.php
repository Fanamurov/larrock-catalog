<form id="search-autocomplite" class="form-search uk-form form-search-autocomplite" method="post" action="/search/catalog/serp">
    <div class="uk-form-row">
        <select name="query" class="uk-width-1-1 uk-form-large" id="search_site">
            <option value="@yield('title_search')">@yield('title_search')</option>
        </select>
        <button class="uk-button uk-button-large uk-button-primary" type="submit"><i class="uk-icon-search"></i></button>
        {{ csrf_field() }}
    </div>
</form>

@if(isset($catalogSearch))
    <link rel="stylesheet" href="/_assets/bower_components/selectize/dist/css/selectize.css">
    <link rel="stylesheet" href="/_assets/bower_components/selectize/dist/css/selectize.default.css">
    <script src="/_assets/bower_components/selectize/dist/js/standalone/selectize.min.js"></script>
    <script type="text/javascript">
        $('#search_site').selectize({
            maxItems: 1,
            valueField: 'title',
            labelField: 'title',
            searchField: 'title',
            persist: true,
            createOnBlur: false,
            create: false,
            plugins: ['remove_button'],
            allowEmptyOption: true,
            sortField: {
                field: 'title',
                direction: 'asc'
            },
            placeholder: 'Поиск по каталогу',
            options: [
                @foreach($catalogSearch as $item)
                {title: '{{ $item['title'] }}', id: {{ $item['id'] }}, category: '{{ $item['category'] }}'},
                @endforeach
            ],
            render: {
                item: function(item, escape) {
                    return '<div>' +
                        (item.title ? '<span class="title">' + escape(item.title.replace('&quot;', '').replace('&quot;', '')) + '</span>' : '') +
                        (item.category ? '<span class="category">/' + escape(item.category.replace('&quot;', '').replace('&quot;', '')) + '</span>' : '') +
                        '</div>';
                },
                option: function(item, escape) {
                    return '<div>' +
                        '<span class="uk-label">' + escape(item.title.replace('&quot;', '').replace('&quot;', '')) + '</span>' +
                        '<span class="caption">в разделе: ' + escape(item.category.replace('&quot;', '').replace('&quot;', '')) + '</span>' +
                        '</div>';
                }
            },
            onChange: function () {
                noty_show('message', 'Перенаправляем к результатам поиска...');
                $('#search-autocomplite').submit();
            }
        });
    </script>
@else
    <p class="alert alert-danger">Middleware CatalogSearch not loaded!</p>
@endif