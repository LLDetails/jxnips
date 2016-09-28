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
        i.delete {
            text-decoration: line-through;
        }
    </style>
@stop

@section('main')
    @if (count($bids) > 0)
        {!! csrf_field() !!}
        @if ($errors->has('form'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
        @endif

        <table id="form-table" class="list table-fixed-header table table-bordered table-hover">
            <thead class="header">
                <tr class="info">
                    <th colspan="7">
                        {{ $basket->name }} - 采购需求 @if($basket->state == 'cancelled') （已放弃） @else（将在{{ $basket->bided_at }}自动通过审核）@endif
                    </th>
                </tr>
                <tr>
                    <th class="text-center">公司名</th>
                    <th class="text-center">采购数量</th>
                    <th class="text-center">可用天数</th>
                    <th class="text-center">价格上下限</th>
                    <th class="text-center">分配方案</th>
                    <th class="text-center">报价有效期</th>
                    <th class="text-center">详情</th>
                </tr>
            </thead>
            @foreach ($bids as $bid)
                <?php
                    $goods = json_decode($bid->goods_static);
                ?>
                <tr>
                    <td colspan="7"></td>
                </tr>
                <tr class="success">
                    <td><span style="line-height:40px">采购物料：{{ $goods->name }}</span></td>
                    <td class="text-center">
                        <span style="line-height:40px">报价开始：{{ $bid->offer_start }}</span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px">报价结束：{{ $bid->offer_stop }}</span>
                    </td>
                    <td>
                        <span style="line-height:40px">招标类型：{{ $bid->type == 'invite' ? '邀请' : '公开' }}</span>
                    </td>
                    <?php
                    if ($bid->type == 'invite') {
                        $suppliers = \App\Supplier::whereIn('user_id', json_decode($bid->suppliers, true))
                                ->lists('name')->toArray();
                    } else {
                        $suppliers = [];
                    }
                    ?>
                    <td colspan="3">
                        {{ implode('；', $suppliers) }}
                    </td>
                </tr>
                @foreach ($bid->demands as $demand)
                    <?php
                    $assign_rule = json_decode($demand->assign_rule);
                    $assign_rule_data = $assign_rule->rules;
                    $assign_rule_data = json_decode($assign_rule_data, true);
                    $assign_rule_data = implode('%；', $assign_rule_data).'%';
                    ?>
                    <tr class="data-row">
                        <td><span style="line-height:40px">{{ $demand->company->name }}</span></td>
                        <td class="text-center"><span style="line-height:40px"><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</span></td>
                        <td class="text-center"><span style="line-height:40px"><i class="alpha-number">{{ intval(($demand->stock+$demand->pending+$demand->quantity)/($demand->monthly_demand/30)) }}</i> 天</span></td>
                        <td class="text-center"><span style="line-height:40px"><i class="alpha-number">{{ strval((float)$demand->price_floor) }} <span class="ion-ios-more"></span> {{ strval((float)$demand->price_caps) }}</i> 元/{{ $goods->unit }}</span></td>
                        <td class="text-center">
                            <span style="line-height:40px">{{ $assign_rule->name }} 【{{$assign_rule_data}}】</span>
                        </td>
                        <td class="text-center"><span style="line-height:40px">招投标结束后{{ $demand->price_validity }}小时</span></td>
                        <td class="text-center">
                            <?php
                            $datetime = date('Y-m-d H:i:s');
                            $current_offers = $demand
                                    ->offer()
                                    ->with('supplier.supplier')
                                    ->orderBy('reason', 'desc')
                                    ->orderBy(DB::raw('"price"+"delivery_costs"'))
                                    ->orderBy('updated_at', 'asc')
                                    ->whereHas('bid', function($query) use($datetime) {
                                        return $query->where('offer_stop', '<', $datetime);
                                    })
                                    ->get();
                            ?>
                            <span style="line-height:40px">
                                <button class="btn btn-info btn-sm show-history" type="button" data-toggle="modal" data-target="#history-modal">详情</button>
                                @if (count($current_offers) > 0)
                                    <button class="btn btn-success btn-sm show-offer" type="button" data-toggle="modal" data-target="#offer-modal">报价单</button>
                                @else
                                    <button class="btn btn-default btn-sm" disabled type="button">报价单</button>
                                @endif
                            </span>
                            @if (count($current_offers) > 0)
                                <div class="offer-detail" style="display: none">
                                    <table class="table table-bordered">
                                        <tr class="info">
                                            <th colspan="4"><span class="ion-ios-home"></span> {{ $demand->company->name }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">采购物料</th>
                                            <td class="text-center" style="font-size: 14px">{{ $goods->name }}</td>
                                            <th class="text-center" style="background:#EEEEEE">采购数量</th>
                                            <td class="text-center" style="font-size: 14px;"><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</td>
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">当前库存</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->stock) }}</i> {{ $goods->unit }}</td>
                                            <th class="text-center" style="background:#EEEEEE">未执行&在途</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->pending) }}</i> {{ $goods->unit }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">月消耗量</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->monthly_demand) }}</i> {{ $goods->unit }}</td>
                                            <th class="text-center" style="background:#EEEEEE">可用天数</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ intval(($demand->stock+$demand->pending+$demand->quantity)/($demand->monthly_demand/30)) }}</i> 天</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">报价区间</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->price_floor) }} <span class="ion-ios-more"></span> {{ strval((float)$demand->price_caps) }}</i> 元/{{ $goods->unit }}</td>
                                            <th class="text-center" style="background:#EEEEEE">分配方案</th>
                                            <td class="text-center" style="font-size: 14px">{{ $assign_rule->name }}
                                                【{{$assign_rule_data}}】</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">报价有效期</th>
                                            <td class="text-center" style="font-size: 14px">招投标结束后{{ $demand->price_validity }}小时</td>
                                            <th class="text-center" style="background:#EEEEEE">到货时间</th>
                                            <td class="text-center" style="font-size: 14px">{{ $demand->delivery_date_start }} <span class="ion-ios-more"></span> {{ $demand->delivery_date_stop }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">发票类型</th>
                                            <td class="text-center" style="font-size: 14px">{{ $demand->invoice }}</td>
                                            <th class="text-center" style="background:#EEEEEE">支付方式</th>
                                            <td class="text-center" style="font-size: 14px">{{ $demand->payment }}</td>
                                        </tr>
                                    </table>
                                    <table class="table table-bordered">
                                        <tr class="success">
                                            <th>报价供应商</th>
                                            <th class="text-center">报价</th>
                                            <th class="text-center">最低成交量</th>
                                            <th class="text-center">最高成交量</th>
                                            <th class="text-center">分配量</th>
                                            <th class="text-center">报价时间</th>
                                            <th class="text-center">说明</th>
                                        </tr>
                                        @foreach($current_offers as $k => $current_offer)
                                            <tr>
                                                <td>{{ $current_offer->supplier->supplier->name }}</td>
                                                @if (!is_null($current_offer->reason))
                                                    <td colspan="4"><span class="text-danger"><b>不参与报价理由：</b>{{ $current_offer->reason }}</span></td>
                                                    <td class="text-center">{{ $current_offer->updated_at }}</td>
                                                    <td>
                                                        @if ($demand->is_cancel)
                                                            <span class="text-danger">该供应商选择了不参与报价</span>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td class="text-center"><i class="alpha-number">{{ strval($current_offer->price + $current_offer->delivery_costs) }}</i> 元/{{ $goods->unit }}</td>
                                                    <td class="text-center"><i class="alpha-number">{{ strval((float)$current_offer->quantity_floor) }}</i> {{ $goods->unit }}</td>
                                                    <td class="text-center"><i class="alpha-number">{{ strval((float)$current_offer->quantity_caps) }}</i> {{ $goods->unit }}</td>
                                                    <td class="text-center"><i class="alpha-number @if ($current_offer->demand->is_cancel) delete @endif">{{ strval((float)$current_offer->quantity) }}</i> {{ $goods->unit }}</td>
                                                    <td class="text-center">{{ $current_offer->updated_at }}</td>
                                                    <td>
                                                        @if ($demand->is_cancel)
                                                            <span class="text-danger">报价供应商未满，按规定作流标处理</span>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                            <div class="detail" style="display: none">
                                <table class="table table-bordered">
                                    <tr class="info">
                                        <th colspan="4"><span class="ion-ios-home"></span> {{ $demand->company->name }}</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background:#EEEEEE">采购物料</th>
                                        <td class="text-center" style="font-size: 14px">{{ $goods->name }}</td>
                                        <th class="text-center" style="background:#EEEEEE">采购数量</th>
                                        <td class="text-center" style="font-size: 14px;"><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</td>
                                    </tr>
                                    <tr>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background:#EEEEEE">当前库存</th>
                                        <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->stock) }}</i> {{ $goods->unit }}</td>
                                        <th class="text-center" style="background:#EEEEEE">未执行&在途</th>
                                        <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->pending) }}</i> {{ $goods->unit }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background:#EEEEEE">月消耗量</th>
                                        <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->monthly_demand) }}</i> {{ $goods->unit }}</td>
                                        <th class="text-center" style="background:#EEEEEE">可用天数</th>
                                        <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ intval(($demand->stock+$demand->pending+$demand->quantity)/($demand->monthly_demand/30)) }}</i> 天</td>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background:#EEEEEE">报价区间</th>
                                        <td class="text-center" style="font-size: 14px"><i class="alpha-number">{{ strval((float)$demand->price_floor) }} <span class="ion-ios-more"></span> {{ strval((float)$demand->price_caps) }}</i> 元/{{ $goods->unit }}</td>
                                        <th class="text-center" style="background:#EEEEEE">分配方案</th>
                                        <td class="text-center" style="font-size: 14px">{{ $assign_rule->name }}
                                            【{{$assign_rule_data}}】</td>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background:#EEEEEE">报价有效期</th>
                                        <td class="text-center" style="font-size: 14px">招投标结束后{{ $demand->price_validity }}小时</td>
                                        <th class="text-center" style="background:#EEEEEE">到货时间</th>
                                        <td class="text-center" style="font-size: 14px">{{ $demand->delivery_date_start }} <span class="ion-ios-more"></span> {{ $demand->delivery_date_stop }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="background:#EEEEEE">发票类型</th>
                                        <td class="text-center" style="font-size: 14px">{{ $demand->invoice }}</td>
                                        <th class="text-center" style="background:#EEEEEE">支付方式</th>
                                        <td class="text-center" style="font-size: 14px">{{ $demand->payment }}</td>
                                    </tr>
                                </table>
                                <?php
                                $history = json_decode($demand->history, true);
                                ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>操作人</th>
                                        <th>数量</th>
                                        <th>报价区间</th>
                                        <th>报价有效</th>
                                        <th>分配方案</th>
                                        <th>操作时间</th>
                                        <th>备注</th>
                                    </tr>
                                    @foreach ($history as $item)
                                        <tr>
                                            <td>{{ isset($item['realname'])?$item['realname']:'' }}</td>
                                            <td><i class="alpha-number">{{ $item['quantity'] }}</i> {{ $goods->unit }}</td>
                                            <td><i class="alpha-number">{{ $item['price_floor'] }} <span class="ion-ios-more"></span> {{ $item['price_caps'] }}</i> 元/{{ $goods->unit }}</td>
                                            <td>招投标结束后{{ $item['price_validity'] }}小时</td>
                                            <?php
                                            $rule = json_decode($item['assign_rule'], true);
                                            $rule_data = $rule['rules'];
                                            $rule_data = json_decode($rule_data, true);
                                            $rule_data = implode('%；', $rule_data).'%';
                                            ?>
                                            <td>{{ $rule_data }}</td>
                                            <td>{{ $item['date'] }}</td>
                                            <td>{!! isset($item['remark']) ? $item['remark'] : '<span style="color:blue">没有填写</span>' !!}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>
    @endif

    <div id="history-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">历史记录</h4>
                </div>
                <div class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="offer-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">报价单</h4>
                </div>
                <div class="modal-body">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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

            $('.show-offer').click(function() {
                var detail = $(this).parent().parent().find('.offer-detail').clone(true);
                detail.show();
                $('#offer-modal').find('.modal-body').html('');
                $('#offer-modal').find('.modal-body').append(detail);
            });

            $('.show-history').click(function() {
                var detail = $(this).parent().parent().find('.detail').clone(true);
                detail.show();
                $('#history-modal').find('.modal-body').html('');
                $('#history-modal').find('.modal-body').append(detail);
            });

            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });
        });
    </script>
@endsection