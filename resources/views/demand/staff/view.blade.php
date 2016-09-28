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
    <table id="form-table" class="list table-fixed-header table table-striped table-bordered table-hover">
        <thead class="header">
            <tr class="info">
                <th colspan="14">
                    {{ $basket->name }} - 采购需求
                    <div class="btn-group btn-group-sm" role="group" style="margin-left: 20px;">
                        @if (Input::get('orderBy') != 'company')
                            <a href="javascript:void(0)" class="btn btn-default btn-sm active">按物料分组</a>
                            <a href="{{ route('demand.staff.view', ['basket' => $basket->id, 'orderBy'=>'company']) }}" type="button" class="btn btn-default btn-sm">按公司分组</a>
                        @else
                            <a href="{{ route('demand.staff.view', ['basket' => $basket->id]) }}" class="btn btn-default btn-sm">按物料分组</a>
                            <a href="javascript:void(0)" type="button" class="btn btn-default btn-sm active">按公司分组</a>
                        @endif
                    </div>
                </th>
            </tr>
            <tr>
                <th class="text-center" rowspan="2" style="line-height: 60px;">#</th>
                @if (Input::get('orderBy') != 'company')
                    <th class="text-center" rowspan="2" style="line-height: 60px;">物料名称</th>
                @else
                    <th class="text-center" rowspan="2" style="line-height: 60px;">公司名称</th>
                @endif
                <th class="text-center" colspan="3">采购数量</th>
                <th class="text-center" rowspan="2" style="line-height: 60px;">可用天数</th>
                <th class="text-center" rowspan="2" style="line-height: 60px;">报价区间</th>
                <th class="text-center" rowspan="2" style="line-height: 60px;">分配方案</th>
                <th class="text-center" rowspan="2" style="line-height: 60px;">报价有效期</th>
                <th class="text-center" rowspan="2" style="line-height: 60px;">进度</th>
                <th class="text-center" rowspan="2" style="line-height: 60px;">查看</th>
            </tr>
            <tr>
                <th class="text-center">合计</th>
                @if (Input::get('orderBy') != 'company')
                    <th class="text-center">公司名称</th>
                @else
                    <th class="text-center">物料名称</th>
                @endif
                <th class="text-center">数量</th>
            </tr>
        </thead>
        <?php
            if (Input::get('orderBy') != 'company') {
                $current_goods_id = null;
            } else {
                $current_company_id = null;
            }
            $total = 0;
            $count = 0;
            $mark = 0;
        ?>
        @foreach ($demands as $key=>$demand)
            <?php $goods = json_decode($demand->goods_static) ?>
            <?php
                $assign_rule = json_decode($demand->assign_rule);
                $assign_rule_data = $assign_rule->rules;
                $assign_rule_data = json_decode($assign_rule_data, true);
                $assign_rule_data = implode('%；', $assign_rule_data).'%';
            ?>
            <tr class="data-row">
                <td><span style="line-height:40px"><i class="alpha-number">{{ $key + 1 }}</i></span></td>
                @if (Input::get('orderBy') != 'company')
                    @if ($current_goods_id != $goods->id)
                        <?php $total = 0; $count = 0; ?>
                        <?php $mark += 1 ?>
                        <td data-mark="{{ $mark }}" class="goods"><span style="line-height:40px">{{ $goods->name }}</span></td>
                        <td data-mark="{{ $mark }}" class="text-center total"><span style="line-height:40px"></span></td>
                    @endif
                @else
                    @if ($current_company_id != $demand->company_id)
                        <?php $total = 0; $count = 0; ?>
                        <?php $mark += 1 ?>
                        <td data-mark="{{ $mark }}" class="company"><span style="line-height:40px">{{ $demand->company->name }}</span></td>
                        <td data-mark="{{ $mark }}" class="text-center total"><span style="line-height:40px"></span></td>
                    @endif
                @endif
                <?php
                    if (Input::get('orderBy') != 'company') {
                        $current_goods_id = $goods->id;
                    } else {
                        $current_company_id = $demand->company_id;
                    }
                    $total += $demand->quantity;
                    $count += 1;
                ?>
                <td>
                    @if (Input::get('orderBy') != 'company')
                        <span style="line-height:40px">{{ $demand->company->name }}</span>
                    @else
                        <span style="line-height:40px">{{ $goods->name }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <span style="line-height:40px"><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span style="line-height:40px"><i class="alpha-number">{{ intval(($demand->stock+$demand->pending+$demand->quantity)/($demand->monthly_demand/30)) }}</i> 天</span>
                </td>
                <td class="text-center">
                    <span style="line-height:40px"><i class="alpha-number">{{ strval((float)$demand->price_floor) }} <span class="ion-ios-more"></span> {{ strval((float)$demand->price_caps) }}</i> 元/{{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span style="line-height:40px">{{ $assign_rule->name }}
                        【{{$assign_rule_data}}】</span>
                </td>
                <td class="text-center">
                    <span style="line-height:40px">招投标结束后{{ $demand->price_validity }}小时</span>
                </td>
                <td class="text-center">
                    <?php
                        $tmp_times = array_merge($check_times, [$datetime]);
                        sort($tmp_times);
                        $index = array_search($datetime, $tmp_times);
                        $progress = $index . '/' . count($check_times);
                    ?>
                    <span style="line-height:40px">
                    {{ $progress }}
                    </span>
                </td>
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
                    @if (Input::get('orderBy') != 'company')
                        @if (!isset($demands[$key + 1]) or (isset($demands[$key + 1]) and json_decode($demands[$key + 1]->goods_static)->id != $current_goods_id))
                            <input class="count-{{$mark}}" type="hidden" value="{{ $count }}" />
                            <input class="quantity_total-{{$mark}}" type="hidden" value="<i class='alpha-number'>{{ $total }}</i> {{ $goods->unit }}" />
                        @endif
                    @else
                        @if (!isset($demands[$key + 1]) or (isset($demands[$key + 1]) and $demands[$key + 1]->company_id != $current_company_id))
                            <input class="count-{{$mark}}" type="hidden" value="{{ $count }}" />
                            <input class="quantity_total-{{$mark}}" type="hidden" value="<i class='alpha-number'>{{ $total }}</i> {{ $goods->unit }}" />
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <div id="history-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">查看详情</h4>
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
    @if (Input::get('orderBy') != 'company')
    <script type="text/javascript">
        $(document).ready(function() {


            var goods_items = $('.goods');
            var total_items = $('.total');
            for (var i = 0; i < goods_items.length; i+=1) {
                var goods = $(goods_items[i]);
                var total = $(total_items[i]);
                var mark = goods.attr('data-mark');
                var rowspan = $('.count-'+mark).val();
                goods.attr('rowspan', rowspan);
                total.attr('rowspan', rowspan);
                var total_num = $('.quantity_total-'+mark).val();
                total.find('span').html(total_num);
            }
            $('.header-copy').remove();
            $('.table-fixed-header').fixedHeader();

            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });

            $('.show-history').click(function() {
                var detail = $(this).parent().parent().find('.detail').clone(true);
                detail.show();
                $('#history-modal').find('.modal-body').html('');
                $('#history-modal').find('.modal-body').append(detail);
            });
            $('.show-offer').click(function() {
                var detail = $(this).parent().parent().find('.offer-detail').clone(true);
                detail.show();
                $('#offer-modal').find('.modal-body').html('');
                $('#offer-modal').find('.modal-body').append(detail);
            });
        });
    </script>
    @else
    <script type="text/javascript">
        $(document).ready(function() {
            var company_items = $('.company');
            var total_items = $('.total');
            for (var i = 0; i < company_items.length; i+=1) {
                var company = $(company_items[i]);
                var total = $(total_items[i]);
                var mark = company.attr('data-mark');
                var rowspan = $('.count-'+mark).val();
                company.attr('rowspan', rowspan);
                total.attr('rowspan', rowspan);
                var total_num = $('.quantity_total-'+mark).val();
                total.find('span').html(total_num);
            }
            $('.header-copy').remove();
            $('.table-fixed-header').fixedHeader();

            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });

            $('.show-history').click(function() {
                var detail = $(this).parent().parent().find('.detail').clone(true);
                detail.show();
                $('#history-modal').find('.modal-body').html('');
                $('#history-modal').find('.modal-body').append(detail);
            });

            $('.show-offer').click(function() {
                var detail = $(this).parent().parent().find('.offer-detail').clone(true);
                detail.show();
                $('#offer-modal').find('.modal-body').html('');
                $('#offer-modal').find('.modal-body').append(detail);
            });
        });
    </script>
    @endif
@stop