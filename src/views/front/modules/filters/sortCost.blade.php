@foreach($sort as $sort_key => $sort_value)
    @if(count($sort_value['values']) > 0)
        <div class="module-filter-{{ $sort_key }} module-filter">
            <span class="vid">{{ $sort_value['name'] }}:</span>
            @foreach($sort_value['values'] as $v_key => $value)
                <span class="change_sort_{{ $sort_key }} label link
                    @if(Cookie::get('sort_'. $sort_key, 'none') === $sort_value['data'][$v_key]) active @endif"
                      data-value="{{ $sort_value['data'][$v_key] }}" data-type="{{ $sort_key }}">{!! $value !!}</span>
            @endforeach
        </div>
    @endif
@endforeach