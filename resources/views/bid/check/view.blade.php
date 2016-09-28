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
    @if (count($bids) > 0)
        <form id="form" class="form-horizontal" method="post" data-action="{{ URL::full() }}" action="{{ URL::full() }}">
            {!! csrf_field() !!}
            @if ($errors->has('form'))
                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
            @endif

            <table id="form-table" class="list table-fixed-header table table-bordered table-hover">
                <thead class="header">
                    <tr class="info">
                        <th colspan="7">
                            {{ $basket->name }} - 采购需求
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
                                <span style="line-height:40px">
                                    <button class="btn btn-info btn-sm show-history" type="button" data-toggle="modal" data-target="#history-modal">详情</button>
                                </span>
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

            <div class="text-center">
                <textarea class="form-control" id="remark-val" name="remark" placeholder="否决说明">{{ old('remark') }}</textarea>
                <button id="submit-refuse" style="margin-top: 10px;" class="btn btn-warning" type="submit"><span class="ion-close-circled"></span> 否决</button>
                <button id="accept" style="margin-left: 15px; margin-top: 10px;" class="btn btn-success" type="button" data-href="{{ URL::full() }}?action=pass"><span class="fa fa-check"></span> 同意</button>
            </div>
        </form>
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

            $('#submit-refuse').click(function() {
                var remark = $('#remark-val').val();
                remark = $.trim(remark);
                if (remark == '') {
                    alert('请填写否决理由');
                    return false;
                }
                if(!confirm('确定要执行否决操作吗？')) {
                    return false;
                }
                var action = $('#form').attr('data-action');
                $('#form').attr('action', action);
                $('#form').submit();
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
            $('.hidden_box').hide();
            $('.bid_type').change(function() {
                if ($(this).val() == 'invite') {
                    $(this).parent().next().find('.supplier').show();
                } else {
                    $(this).parent().next().find('.supplier').hide();
                }
            });
            $('#accept').click(function() {
                var action = $(this).attr('data-href');
                $('#form').attr('action', action);
                $('#form').submit();
            });
        });
    </script>
@endsection