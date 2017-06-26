<form class="form-search" method="post" action="/search/catalog/serp">
    <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="@yield('title_search', 'Поиск по каталогу')">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
          {{ csrf_field() }}
      </span>
    </div>
</form>