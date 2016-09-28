@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>

    <style type="text/css">
        i.alpha-number {
            font-size: 12px;
            font-family: "Arial", sans-serif;
            font-weight: bold;
            color: #222222;
            font-style: normal;
        }
    </style>
@stop

@section('main')
    <table id="form-table" class="list table table-fixed-header table-bordered table-hover">
        <thead class="header">
        <tr class="info">
            <th colspan="12">
                {{ $offer_basket->name }} - 报价需求
                @if ($offer_basket->name == date('Y-m-d'))
                    {!! IPSHelper::showButton(['permission'=>'offer.information.append', 'type'=>'frame', 'title'=>'追加需求', 'src'=>route('offer.information.append', ['offer_basket' => $offer_basket->id]), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-left:30px;', 'text'=>'追加']) !!}
                @endif
                <a style="margin-left: 10px;" href="{{ URL::full() }}" class="btn btn-default btn-xs">重新加载</a>
            </th>
        </tr>
        <tr>
            <th class="text-center">操作</th>
            <th class="text-center">物料名称</th>
            <th class="text-center">可供数量（吨）</th>
            <th class="text-center">单价（元/吨）</th>
            <th class="text-center">交货地点</th>
            <th class="text-center">款期（天）</th>
            <th class="text-center">报价有效期（小时）</th>
            <th class="text-center">送货日期</th>
            <th class="text-center">线下议价</th>
        </tr>
        </thead>
        @foreach ($information as $key=>$item)
            <?php
                $prices = array_keys($item->prices_with_addresses);
                $addresses = array_values($item->prices_with_addresses);
                $rowspan = count($addresses, 1);
            ?>
            <tr class="data-row">
                <td rowspan="{{ $rowspan }}" class="text-center">
                    @if ($offer_basket->name == date('Y-m-d'))
                        {!! IPSHelper::showButton(['permission'=>'offer.information.view', 'type'=>'frame', 'title'=>'编辑报价', 'src'=>route('offer.information.view', ['offer_information' => $item->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                        {!! IPSHelper::showButton(['permission'=>'offer.information.edit', 'type'=>'frame', 'title'=>'编辑报价', 'src'=>route('offer.information.edit', ['offer_information' => $item->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'offer.information.delete', 'confirm'=>'确定要删除此条记录？', 'type'=>'link', 'href'=>route('offer.information.delete', ['offer_information' => $item->id]), 'css'=>'btn btn-danger btn-xs', 'text'=>'删除']) !!}
                    @else
                        {!! IPSHelper::showButton(['permission'=>'offer.information.view', 'type'=>'frame', 'title'=>'编辑报价', 'src'=>route('offer.information.view', ['offer_information' => $item->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                    @endif
                </td>
                <td rowspan="{{ $rowspan }}">
                    <span >{{ $item->goods->name }}</span>
                </td>
                <td rowspan="{{ $rowspan }}" class="text-center">
                    <span><i class="alpha-number">{{ $item->quantity == -1 ? '不限' : strval((float)$item->quantity) }}</i></span>
                </td>
                <td rowspan="{{ count($addresses[0]) }}" class="text-center">
                    <span><i class="alpha-number">{{ strval((float)$prices[0]) }}</i></span>
                </td>
                <td>{{ $addresses[0][0] }}</td>
                <td rowspan="{{ $rowspan }}" class="text-center">
                    <span >{{ $item->payment }}</span>
                </td>
                <td rowspan="{{ $rowspan }}" class="text-center">
                    <span >{{ $item->price_validity }}</span>
                </td>
                <td rowspan="{{ $rowspan }}" class="text-center">
                    <span >{{ explode(' ', $item->delivery_start)[0] }} <span class="ion-ios-more"></span> {{ explode(' ', $item->delivery_stop)[0] }}</span>
                </td>
                <td rowspan="{{ $rowspan }}" class="text-center">
                    {{ $item->bargaining ? '接受' : '不接受' }}
                </td>
            </tr>
            @foreach ($addresses as $k=>$addrs)
                <tr>
                @if ($k == 0)
                    @foreach ($addrs as $addr_idx => $addr)
                        @if ($addr_idx > 0)
                            <td>{{ $addr }}</td></tr><tr>
                        @endif
                    @endforeach
                @else
                    @foreach ($addrs as $addr_idx => $addr)
                        @if ($addr_idx == 0)
                            <td rowspan="{{ count($addresses[$k]) }}" class="text-center">
                                <span><i class="alpha-number">{{ strval((float)$prices[$k]) }}</i></span>
                            </td>
                        @endif
                        <td>{{ $addr }}</td></tr><tr>
                    @endforeach
                @endif
            @endforeach
        @endforeach
    </table>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });
            $(".select2").select2({
                language: "zh-CN"
            });
        });
    </script>
@stop