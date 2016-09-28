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
    <div class="alert alert-dismissible alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        @foreach ($logs as $log)
        <p><strong>[{{ $log->user->role->name }} - {{ $log->user->staff->realname }}]退回理由：{{ $log->remark }}</strong></p>
        @endforeach
    </div>
    @if (count($bids) > 0)
        <form class="form-horizontal" method="post" action="{{ URL::full() }}">
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
                <?php
                    $tomorrow = date('Y-m-d', strtotime("+1 day"));
                    $default_offer_start = $tomorrow.' 10:00:00';
                    $default_offer_stop = $tomorrow.' 16:00:00';
                ?>
                @foreach ($bids as $index => $bid)
                    <?php
                        $goods = json_decode($bid->goods_static);
                        $quantity_count = $bid->demands()->sum('quantity');
                    ?>
                    <tr>
                        <td colspan="7"></td>
                    </tr>
                    <tr class="success">
                        <td><span style="line-height:40px">采购物料：{{ $goods->name }}</span><input type="hidden" name="bid_ids[]" value="{{ $bid->id }}" /></td>
                        <td class="text-center
                            @if ($errors->has('offer_start.'.$index))
                                danger
                            @endif
                                ">
                            <input type="text" @if ($quantity_count == 0) style="display:none" @endif class="form-control text-center date" name="offer_start[]" value="{{ old('offer_start.'.$index, $default_offer_start) }}" placeholder="报价时间起">
                            @if ($errors->has('offer_start.'.$index))
                                <p class="form-msg"><span class="fa fa-info-circle"></span> {{ $errors->first('offer_start.'.$index) }}</p>
                            @endif
                        </td>
                        <td class="text-center
                            @if ($errors->has('offer_stop.'.$index))
                                danger
                            @endif
                                ">
                            <input type="text" @if ($quantity_count == 0) style="display:none" @endif class="form-control text-center date" name="offer_stop[]" value="{{ old('offer_stop.'.$index, $default_offer_stop) }}" placeholder="报价时间止">
                            @if ($errors->has('offer_stop.'.$index))
                                <p class="form-msg"><span class="fa fa-info-circle"></span> {{ $errors->first('offer_stop.'.$index) }}</p>
                            @endif
                        </td>
                        <td class="
                            @if ($errors->has('type.'.$index))
                                danger
                            @endif
                                ">
                            <select @if ($quantity_count == 0) style="display:none" @endif class="form-control bid_type" name="type[]">
                                <option @if (old('type.'.$index, $bid->type) == 'global') selected @endif value="global">公开招标</option>
                                <option @if (old('type.'.$index, $bid->type) == 'invite') selected @endif value="invite">邀请供应商</option>
                            </select>
                            @if ($errors->has('type.'.$index))
                                <p class="form-msg"><span class="fa fa-info-circle"></span> {{ $errors->first('type.'.$index) }}</p>
                            @endif
                        </td>
                        <?php
                            $suppliers = \App\User::with('supplier')->where('type', 'supplier')
                                ->whereHas('supplier', function($query) use($goods) {
                                    return $query->whereRaw('\'["' . $goods->id . '"]\'::jsonb <@ "goods"');
                                })->get();
                        ?>
                        <td colspan="3" class="
                            @if ($errors->has('suppliers.'.$index))
                                danger
                            @endif
                        ">
                            <div @if ($quantity_count == 0) style="display:none" @endif class="supplier @if (old('type.'.$index, $bid->type) != 'invite') hidden_box @endif">
                                <select name="suppliers[{{ $index }}][]" class="form-control select2" multiple>
                                    @foreach($suppliers as $supplier)
                                        <option @if(in_array($supplier->id, old('suppliers.'.$index, json_decode($bid->suppliers, true)))) selected @endif value="{{ $supplier->id }}">{{ $supplier->supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('suppliers.'.$index))
                                <p class="form-msg"><span class="fa fa-info-circle"></span> {{ $errors->first('suppliers.'.$index) }}</p>
                            @endif
                        </td>
                    </tr>
                    @foreach ($bid->demands as $k=> $demand)
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
                                <div class="detail" data-row="{{ $k }}" style="display: none">
                                    <div style="display: none;" class="alert alert-dismissible alert-danger">
                                    </div>
                                    <table class="table table-bordered">
                                        <tr class="info">
                                            <th colspan="5">
                                                <span class="pull-left"><span class="ion-ios-home"></span> {{ $demand->company->name }}</span>
                                                <span class="pull-right"><button data-url="{{ route('demand.check.modify', ['demand' => $demand->id]) }}" type="button" class="save btn btn-xs btn-warning"><span class="fa fa-check"></span> 保存修改</button></span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">采购物料</th>
                                            <td class="text-center" colspan="2" style="font-size: 14px;">{{ $goods->name }}</td>
                                            <th class="text-center" style="background:#EEEEEE">采购数量（{{$goods->unit}}）</th>
                                            <td class="text-center" style="font-size: 14px;">
                                                <input type="text" class="form-control input-sm text-center quantity" value="{{ strval((float)(isset($tmp_data->quantity)?$tmp_data->quantity:$demand->quantity)) }}">
                                            </td>
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">当前库存</th>
                                            <td class="text-center" colspan="2" style="font-size: 14px"><i class="alpha-number stock">{{ strval((float)$demand->stock) }}</i> {{ $goods->unit }}</td>
                                            <th class="text-center" style="background:#EEEEEE">未执行&在途</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number pending">{{ strval((float)$demand->pending) }}</i> {{ $goods->unit }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">月消耗量</th>
                                            <td class="text-center" colspan="2" style="font-size: 14px"><i class="alpha-number monthly_demand">{{ strval((float)$demand->monthly_demand) }}</i> {{ $goods->unit }}</td>
                                            <th class="text-center" style="background:#EEEEEE">可用天数</th>
                                            <td class="text-center" style="font-size: 14px"><i class="alpha-number day">{{ intval(($demand->stock+$demand->pending+(isset($tmp_data->quantity)?$tmp_data->quantity:$demand->quantity))/($demand->monthly_demand/30)) }}</i> 天</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">报价区间（元/吨）</th>
                                            <td class="text-center" style="font-size: 14px;">
                                                <input type="text" class="form-control input-sm text-center price_floor" value="{{ strval((float)(isset($tmp_data->price_floor)?$tmp_data->price_floor:$demand->price_floor)) }}">
                                            </td>
                                            <td class="text-center" style="font-size: 14px;">
                                                <input type="text" class="form-control input-sm text-center price_caps" value="{{ strval((float)(isset($tmp_data->price_caps)?$tmp_data->price_caps:$demand->price_caps)) }}">
                                            </td>
                                            <th class="text-center" style="background:#EEEEEE">分配方案</th>
                                            <td class="text-center" style="font-size: 14px">
                                                <select class="form-control input-sm assign_rule">
                                                    <option value="">请选择分配方案</option>
                                                    @foreach ($assign_rules as $rule)
                                                        <?php
                                                        $rule_data = json_decode($rule->rules, true);
                                                        $rule_data = '【'.implode('%，', $rule_data).'%】';
                                                        ?>
                                                        <option @if((isset($tmp_data->assign_rule_id)?$tmp_data->assign_rule_id:$assign_rule->id) == $rule->id) selected @endif value="{{ $rule->id }}">{{ $rule->name }} {{ $rule_data }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">报价有效期</th>
                                            <td class="text-center" colspan="2" style="font-size: 14px">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-addon">招投标结束后</div>
                                                    <input type="text" class="form-control input-sm text-center price_validity" value="{{ isset($tmp_data->price_validity)?$tmp_data->price_validity:$demand->price_validity }}">
                                                    <div class="input-group-addon">小时</div>
                                                </div>
                                            </td>
                                            <th class="text-center" style="background:#EEEEEE">到货时间</th>
                                            <td class="text-center" style="font-size: 14px">{{ $demand->delivery_date_start }} <span class="ion-ios-more"></span> {{ $demand->delivery_date_stop }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">发票类型</th>
                                            <td class="text-center" colspan="2" style="font-size: 14px">{{ $demand->invoice }}</td>
                                            <th class="text-center" style="background:#EEEEEE">支付方式</th>
                                            <td class="text-center" style="font-size: 14px">{{ $demand->payment }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="background:#EEEEEE">更改理由</th>
                                            <td colspan="4">
                                                <textarea class="form-control remark">{{ isset($tmp_data->remark)?$tmp_data->remark:'' }}</textarea>
                                            </td>
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

            <div class="text-center">
                <button style="margin-right: 20px;" class="btn btn-info" type="submit"><span class="ion-filing"></span> 需求汇总</button>
                {!! IPSHelper::showButton(['permission'=>'bid.demand.cancel', 'type'=>'link', 'css'=>'btn btn-danger', 'confirm'=>'确定要放弃吗？', 'href'=>route('bid.demand.cancel', ['basket' => $basket->id]), 'text'=>'放弃']) !!}
            </div>
        </form>
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

            $('.show-history').click(function() {
                var detail = $(this).parent().parent().find('.detail').clone(true);
                detail.show();
                $('#history-modal').find('.modal-body').html('');
                $('#history-modal').find('.modal-body').append(detail);
            });

            $(window).scroll(function() {
                $('.header-copy').remove();
                $('.table-fixed-header').fixedHeader();
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

            $('button.save').click(function() {
                var parent = $(this).parents('div.detail');
                var data = {
                    'quantity': parent.find('.quantity').val(),
                    'price_floor': parent.find('.price_floor').val(),
                    'price_caps': parent.find('.price_caps').val(),
                    'assign_rule': parent.find('.assign_rule').val(),
                    'price_validity': parent.find('.price_validity').val(),
                    'remark': parent.find('.remark').val(),
                    '_token': '{{ csrf_token() }}'
                };
                var url = $(this).attr('data-url');
                var msgBox = parent.find('.alert');
                var updateRow = $('#data-row-' + parent.attr('data-row'));
                $.post(url, data, function(res) {
                    if (typeof res['state'] == 'undefined') {
                        msgBox.show();
                        msgBox.find('p').remove();
                        msgBox.append('<p>服务器繁忙,请稍候再试</p>');
                        return false;
                    }
                    if (res['state'] == 'error') {
                        var msg = '';
                        for(var field in res['message']) {
                            msg += '<p>'+ res['message'][field][0] +'</p>';
                        }
                        msgBox.show();
                        msgBox.find('p').remove();
                        msgBox.append(msg);
                        return false;
                    }
                    if (res['state'] == 'success') {
                        window.location.href = window.location.href;
                        /*$('#history-modal').modal('hide');
                        updateRow.find('.remark').val(res['data']['remark']);
                        updateRow.find('.price_floor').val(res['data']['price_floor']);
                        updateRow.find('.price_floor-txt').html(res['data']['price_floor']);
                        updateRow.find('.price_caps').val(res['data']['price_caps']);
                        updateRow.find('.price_caps-txt').html(res['data']['price_caps']);
                        updateRow.find('.quantity').val(res['data']['quantity']);
                        updateRow.find('.quantity-txt').html(res['data']['quantity']);
                        updateRow.find('.price_validity').val(res['data']['price_validity']);
                        updateRow.find('.price_validity-txt').html(res['data']['price_validity']);
                        updateRow.find('.assign_rule').find('option').removeAttr('selected');
                        updateRow.find('.assign_rule').find('option[value="'+res['data']['assign_rule_id']+'"]').attr('selected', 'selected');
                        updateRow.find('.assign_rule').val(res['data']['assign_rule_id']);
                        updateRow.find('.assign_rule-txt').html(res['data']['assign_rule_txt']);
                        var day = evaluateDays(
                                res['data']['quantity'],
                                updateRow.find('.stock').html(),
                                updateRow.find('.pending').html(),
                                updateRow.find('.monthly_demand').html()
                        );
                        if (day) {
                            updateRow.find('.day').html(day);
                        }*/
                    }
                });
            });

            $('.detail .quantity').change(function() {
                var perent = $(this).parents('div.detail');
                var day = evaluateDays(
                        $(this).val(),
                        perent.find('.stock').html(),
                        perent.find('.pending').html(),
                        perent.find('.monthly_demand').html()
                );
                if (day && !isNaN(day)) {
                    perent.find('.day').html(day);
                }
            });

            function evaluateDays(quantity, stock, pending, monthly_demand) {
                if (quantity && stock && pending && monthly_demand) {
                    var total = Number(quantity) + Number(stock) + Number(pending);
                    var day = total / (monthly_demand / 30);
                    day = parseInt(day);
                    return day;
                }
            };
        });
    </script>
@endsection