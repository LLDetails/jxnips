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
    @if (count($demands) > 0)
        <table id="form-table" class="list table table-bordered table-hover">
            <thead class="header">
                <tr class="info">
                    <?php
                    $goods = json_decode($bid->goods_static);
                    ?>
                    <th colspan="7">
                        {{ $goods->name }}采购标书，报价在{{ $bid->offer_stop }}结束
                    </th>
                </tr>
                <tr class="warning">
                    <th colspan="7">
                        质量指标：{{ $goods->quality_standard }}
                    </th>
                </tr>
                <tr>
                    <th class="text-center">公司名 / 地址</th>
                    <th class="text-center">采购数量</th>
                    <th class="text-center">发票 / 付款方式</th>
                    <th class="text-center" colspan="2">分配方案</th>
                    <th class="text-center">报价有效期</th>
                    <th class="text-center">交货日期</th>
                    {{--<th class="text-center">成交价格</th>--}}
                    {{--<th class="text-center">我的报价</th>--}}
                    {{--<th class="text-center">报价时间</th>--}}
                    {{--<th class="text-center">查看</th>--}}
                </tr>
            </thead>
            @foreach ($demands as $k=>$demand)
                <?php
                $assign_rule = json_decode($demand->assign_rule);
                $assign_rule_data = $assign_rule->rules;
                $assign_rule_data = json_decode($assign_rule_data, true);
                $assign_rule_data = implode('%；', $assign_rule_data).'%';
                ?>
                <tr class="data-row">
                    <td>
                        <b>{{ $demand->company->name }}</b><br />
                        <span class="ion-android-pin"></span> {{ $demand->company->delivery_address }}
                        <input type="hidden" name="demand_id[]" value="{{ $demand->id }}" />
                    </td>
                    <td class="text-center"><span style="line-height:40px"><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</span></td>
                    <td class="text-center">{{ $demand->invoice }}<br />{{ $demand->payment }}</td>
                    <td class="text-center" colspan="2">
                        <span style="line-height:40px">{{ $assign_rule->name }}【{{ $assign_rule_data }}】</span>
                    </td>
                    <td class="text-center"><span style="line-height:40px">招投标结束后{{ $demand->price_validity }}小时</span></td>
                    <td class="text-center"><span style="line-height:40px">{{ $demand->delivery_date_start }} ~ {{ $demand->delivery_date_stop }}</span></td>
                    <?php
                        $current_offers = $demand->offer()->with('supplier.supplier')->orderBy(DB::raw('"price"+"delivery_costs"'))->orderBy('updated_at', 'asc')->whereNull('reason')->get();
                        $my_offer = $demand->offer()->where('price', '>', 0)->where('user_id', auth()->user()->id)->first();
                    ?>

                    {{--<td class="text-center">--}}
                        {{--<span style="line-height:40px">--}}
                            {{--<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#offer-modal">报价详情</button>--}}
                        {{--</span>--}}
                    {{--</td>--}}
                </tr>
                @if ($bid->offer_stop < $datetime)
                @if(!empty($my_offer))
                <tr class="danger">
                    <th>单位名称</th>
                    <th class="text-center">我的报价</th>
                    <th class="text-center">最低成交量</th>
                    <th class="text-center">最高成交量</th>
                    <th class="text-center">成交量</th>
                    <th class="text-center">报价时间</th>
                    <th class="text-center">说明</th>
                </tr>
                <tr>
                    <td>{{ auth()->user()->supplier->name }}</td>
                    <td class="text-center"><i class="alpha-number">{{ strval($my_offer->price + $my_offer->delivery_costs) }}</i> 元/{{ $goods->unit }}</td>
                    <td class="text-center"><i class="alpha-number">{{ strval((float)$my_offer->quantity_floor) }}</i> {{ $goods->unit }}</td>
                    <td class="text-center"><i class="alpha-number">{{ strval((float)$my_offer->quantity_caps) }}</i> {{ $goods->unit }}</td>
                    <td class="text-center"><i class="alpha-number">{{ strval((float)$my_offer->quantity) }}</i> {{ $goods->unit }}</td>
                    <td class="text-center">{{ $my_offer->updated_at }}</td>
                    <td>
                        @if ($demand->is_cancel)
                            <span class="text-danger">报价供应商未满，按规定作流标处理</span>
                        @endif
                    </td>
                </tr>
                @endif
                <tr class="success">
                    <th>中标供应商</th>
                    <th class="text-center">成交价格</th>
                    <th class="text-center">最低成交量</th>
                    <th class="text-center">最高成交量</th>
                    <th class="text-center">成交量</th>
                    <th class="text-center">报价时间</th>
                    <th class="text-center">说明</th>
                </tr>
                @if (count($current_offers) == 0)
                    <tr>
                        <td colspan="7">无人报价</td>
                    </tr>
                @else
                    @foreach($current_offers as $k => $current_offer)
                    @if($current_offer->quantity > 0/* or $current_offer->user_id == $current_user->id*/)
                        <tr>
                            <td>{{ $current_offer->user_id == $current_user->id ? $current_offer->supplier->supplier->name : '**********' }}</td>
                            @if (!is_null($current_offer->reason))
                                <td colspan="2"><span class="text-danger"><b>不参与报价理由：</b>{{ $current_offer->reason }}</span></td>
                                <td class="text-center">{{ $current_offer->updated_at }}</td>
                                <td colspan="3">
                                    @if ($demand->is_cancel)
                                        <span class="text-danger">该供应商选择了不参与报价</span>
                                    @endif
                                </td>
                            @else
                                <td class="text-center"><i class="alpha-number">{{ strval($current_offer->price + $current_offer->delivery_costs) }}</i> 元/{{ $goods->unit }}</td>
                                <td class="text-center"><i class="alpha-number">{{ $current_offer->user_id == $current_user->id ? strval((float)$current_offer->quantity_floor) : '**' }}</i> {{ $goods->unit }}</td>
                                <td class="text-center"><i class="alpha-number">{{ $current_offer->user_id == $current_user->id ? strval((float)$current_offer->quantity_caps) : '**' }}</i> {{ $goods->unit }}</td>
                                <td class="text-center"><i class="alpha-number">{{ strval((float)$current_offer->quantity) }}</i> {{ $goods->unit }}</td>
                                <td class="text-center">{{ $current_offer->updated_at }}</td>
                                <td>
                                    @if ($demand->is_cancel)
                                        <span class="text-danger">报价供应商未满，按规定作流标处理</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endif
                    @endforeach
                @endif
                <tr style="background: #eeeeee">
                    <td colspan="7"></td>
                </tr>
                @endif
            @endforeach
        </table>
    @endif
@stop

@section('js')
    <script type="text/javascript">
        $(".select2").select2({
            language: "zh-CN"
        });

        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m-d H:i:00'
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });
            $('.hidden_box').hide();
            $('.bid_type').change(function() {
                if ($(this).val() == 'invite') {
                    $(this).parent().next().find('.supplier').show();
                } else {
                    $(this).parent().next().find('.supplier').hide();
                }
            });
        });
    </script>
@endsection