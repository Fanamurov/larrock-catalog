<div class="catalogBlockCategory uk-width-1-2 uk-width-small-1-3 uk-width-medium-1-4 uk-width-xlarge-1-4 category-{{ $data->id }}">
    <div class="link_block_this" data-href="{{ $data->full_url }}">
        @level(2)
            <a class="admin_edit" href="/admin/category/{{ $data->id }}/edit">Edit element</a>
        @endlevel
        <img src="{{ $data->first_image }}" class="categoryImage">
        <h3><a href="{{ $data->full_url }}">{{ $data->title }}</a></h3>
    </div>
</div>