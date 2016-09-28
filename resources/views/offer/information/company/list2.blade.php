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
                <div class="btn-group btn-group-sm" role="group" style="margin-left: 20px;">
                    <a href="javascript:void(0)" class="btn btn-default btn-sm active">按物料分组</a>
                    <a href="{{ route('offer.information.company.list', ['offer_basket' => $offer_basket->id, 'order_by'=>'supplier']) }}" type="button" class="btn btn-default btn-sm">按供应商分组</a>
                </div>
            </th>
        </tr>
        <tr>
            <th class="text-center">物料名称</th>
            <th class="text-center">供应商</th>
            <th class="text-center">可供数量（吨）</th>
            <th class="text-center">单价（元/吨）</th>
            <th class="text-center">交货地点</th>
            <th class="text-center">款期（天）</th>
            <th class="text-center">报价有效期（小时）</th>
            <th class="text-center">送货日期</th>
            <th class="text-center">线下议价</th>
        </tr>
        </thead>
        <?php
        $current_goods = null;
        $merge_flag = true;
        $merge_count = 0;
        $merge_index = 0;
        ?>
        @foreach ($information as $key=>$item)
            <?php
                $current_goods = $item->goods_id;
                $prices = array_keys($item->prices_with_addresses);
                $addresses = array_values($item->prices_with_addresses);
                $rowspan = count($addresses, 1) - count($addresses);//echo $rowspan;
                $merge_count += $rowspan;
            ?>
            <tr class="data-row">
                @if ($merge_flag)
                    <td class="merge-{{ $merge_index }}">
                        <span >{{ $item->goods->name }}</span>
                    </td>
                @endif
                <td rowspan="{{ $rowspan }}">
                    <span >{{ $item->supplier->supplier->name }}</span>
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
                <td data-test="{{ $merge_count }}" rowspan="{{ $rowspan }}" class="text-center">
                    {{ $item->bargaining ? '接受' : '不接受' }}
                    @if (!isset($information[$key+1]) or (isset($information[$key+1]) and $information[$key+1]->goods_id != $current_goods))
                        <input type="hidden" class="merge-count" data-class="merge-{{ $merge_index }}" value="{{ $merge_count }}" />
                        <?php
                        $merge_flag = true;
                        $merge_count = 0;
                        $merge_index += 1;
                        ?>
                    @else
                        <?php
                        $merge_flag = false;
                        //$merge_count += 1
                        ?>
                    @endif
                </td>
            </tr>
                @foreach ($addresses as $k=>$addrs)
                        @if ($k == 0)
                            @foreach ($addrs as $addr_idx => $addr)
                                @if ($addr_idx > 0)
                                <tr data="s{{ $k }}"><td>{{ $addr }}</td></tr>
                                @endif
                            @endforeach
                        @else
                            <tr data="{{ $k }}">
                            @foreach ($addrs as $addr_idx => $addr)
                                @if ($addr_idx == 0)
                                    <td rowspan="{{ count($addresses[$k]) }}" class="text-center">
                                        <span><i class="alpha-number">{{ strval((float)$prices[$k]) }}</i></span>
                                    </td>
                                @endif
                                <td>{{ $addr }}</td></tr>
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

            var merges = $('.merge-count');
            merges.each(function(i, e) {
                var className = $(e).attr('data-class');
                $('.'+className).attr('rowspan', $(e).val());
            });
        });
    </script>
@stop