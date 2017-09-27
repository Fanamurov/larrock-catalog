<?= '<'.'?'.'xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="{{ Carbon\Carbon::today()->format('Y-m-d h:s') }}">
    <shop>
        <name>{{ env('SITE_NAME') }}</name>
        <company>{{ env('SITE_NAME') }}</company>
        <url>{{ env('APP_URL') }}</url>

        <currencies>
            <currency id="RUR" rate="1" plus="0"/>
        </currencies>

        <categories>
            @foreach($categories as $category)
            <category id="{{ $category->id }}" @if($category->parent) parentId="{{ $category->parent }}" @endif>{{ $category->title }}</category>
            @endforeach
        </categories>

        <offers>
            @foreach($data as $value)
                @if($value->cost > 0)
                    <offer id="{{ $value->id }}" type="vendor.model" @if($value->cost > 0) available="true" @else available="false" @endif>
                        <url>{!! env('APP_URL') !!}{{ $value->full_url }}</url>
                        <price>{{ $value->cost }}</price>
                        <currencyId>RUR</currencyId>
                        <categoryId type="Own">{{ $value->get_category->first()->id }}</categoryId>
                        <picture>{!! env('APP_URL') !!}{{ $value->first_image }}</picture>
                        <typePrefix>{{ $value->get_category->first()->title }}</typePrefix>
                        @if($value->manufacture)
                            <vendor>{{ $value->manufacture }}</vendor>
                        @else
                            <vendor>{{ env('SITE_NAME') }}</vendor>
                        @endif
                        <delivery>false</delivery>
                        <local_delivery_cost>0</local_delivery_cost>
                        <model>{{ $value->title }}</model>
                        @if( !empty($value->description))<description>{{ $value->description }}</description>@endif
                    </offer>
                @endif
            @endforeach
        </offers>
    </shop>
</yml_catalog>
