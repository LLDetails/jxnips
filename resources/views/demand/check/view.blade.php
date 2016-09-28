@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js') }}"></script>
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
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        @if ($errors->has('form'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
        @endif

        <table id="form-table" class="list table-fixed-header table table-striped table-bordered table-hover">
            <thead class="header">
                <tr class="info">
                    <th colspan="15">
                        {{ $basket->name }} - 采购需求
                        <div class="btn-group btn-group-sm" role="group" style="margin-left: 20px;">
                            @if (Input::get('orderBy') != 'company')
                                <a href="javascript:void(0)" class="btn btn-default btn-sm active">按物料分组</a>
                                <a href="{{ route('demand.check.action', ['basket' => $basket->id, 'orderBy'=>'company']) }}" type="button" class="btn btn-default btn-sm">按公司分组</a>
                            @else
                                <a href="{{ route('demand.check.action', ['basket' => $basket->id]) }}" class="btn btn-default btn-sm">按物料分组</a>
                                <a href="javascript:void(0)" type="button" class="btn btn-default btn-sm active">按公司分组</a>
                            @endif
                        </div>
                    </th>
                </tr>
                <tr>
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
                    <th class="text-center" rowspan="2" style="line-height: 60px;">操作</th>
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
            @foreach ($demands as $k=>$demand)
                <?php $goods = json_decode($demand->goods_static) ?>
                <?php
                    $tmp_data = null;
                    if ($demand->tmp_data_user_id == $current_user->id) {
                        $tmp_data = json_decode($demand->tmp_data);
                    }
                ?>
                <?php
                    $assign_rule = json_decode($demand->assign_rule);
                    $assign_rule_data = $assign_rule->rules;
                    $assign_rule_data = json_decode($assign_rule_data, true);
                    $assign_rule_data = implode('%；', $assign_rule_data).'%';
                ?>
                <tr class="data-row" id="data-row-{{ $k }}">
                    @if (Input::get('orderBy') != 'company')
                        @if ($current_goods_id != $goods->id)
                            <?php $total = 0; $count = 0; ?>
                            <?php $mark += 1 ?>
                            <td data-mark="{{ $mark }}" class="goods"><span style="line-height:40px">{{ $goods->name }}</span></td>
                            <td data-mark="{{ $mark }}" data-latest="" class="text-center total"><span style="line-height:40px"></span></td>
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
                        <span style="line-height:40px"><i class="alpha-number quantity-txt">{{ strval((float)(isset($tmp_data->quantity) ? $tmp_data->quantity :$demand->quantity)) }}</i> {{ $goods->unit }}</span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px"><i class="alpha-number day">{{ intval(($demand->stock+$demand->pending+(isset($tmp_data->quantity)?$tmp_data->quantity:$demand->quantity))/($demand->monthly_demand/30)) }}</i> 天</span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px"><i class="alpha-number price_floor-txt">{{ strval((float)(isset($tmp_data->price_floor)?$tmp_data->price_floor:$demand->price_floor)) }}</i> <i class="alpha-number"><span class="ion-ios-more"></span></i> <i class="alpha-number price_caps-txt">{{ strval((float)(isset($tmp_data->price_caps)?$tmp_data->price_caps:$demand->price_caps)) }}</i> 元/{{ $goods->unit }}</span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px" class="assign_rule-txt">
                            @if (!isset($tmp_data->assign_rule_txt))
                                {{ $assign_rule->name }}
                                【{{$assign_rule_data}}】
                            @else
                                {{ $tmp_data->assign_rule_txt }}
                            @endif
                        </span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px" class="price_validity-txt">招投标结束后{{ isset($tmp_data->price_validity) ? $tmp_data->price_validity : $demand->price_validity }}小时</span>
                    </td>
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
                                        <span class="pull-right"><button data-url="{{ route('demand.check.edit', ['demand' => $demand->id]) }}" type="button" class="save btn btn-xs btn-warning"><span class="fa fa-check"></span> 保存修改</button></span>
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
                        @if (Input::get('orderBy') != 'company')
                            @if (!isset($demands[$k + 1]) or (isset($demands[$k + 1]) and json_decode($demands[$k + 1]->goods_static)->id != $current_goods_id))
                                <input class="count-{{$mark}}" type="hidden" value="{{ $count }}" />
                                <input class="quantity_total-{{$mark}}" type="hidden" value="<i class='alpha-number'>{{ $total }}</i> {{ $goods->unit }}" />
                            @endif
                        @else
                            @if (!isset($demands[$k + 1]) or (isset($demands[$k + 1]) and $demands[$k + 1]->company_id != $current_company_id))
                                <input class="count-{{$mark}}" type="hidden" value="{{ $count }}" />
                                <input class="quantity_total-{{$mark}}" type="hidden" value="<i class='alpha-number'>{{ $total }}</i> {{ $goods->unit }}" />
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="text-center">
            @if (!empty($next_role))
                <select class="select2">
                    <option value="{{ $next_role->id }}">下一级审核：{{ $next_role->name }}</option>
                </select>
            @endif
            <button style="margin-right: 20px;" class="btn btn-info" type="submit"><span class="fa fa-check"></span> 审核需求清单</button>
        </div>
    </form>

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

        $('input.date').datepicker({
            language: "zh-CN",
            format: "yyyy-mm-dd",
            autoclose: true
        });
    </script>
    @if (Input::get('orderBy') != 'company')
    <script type="text/javascript">
        $(document).ready(function() {
            var goods_items = $('.goods');
            var total_items = $('.total');
            for (var i = 0; i < goods_items.length; i += 1) {
                var goods = $(goods_items[i]);
                var total = $(total_items[i]);
                var mark = goods.attr('data-mark');
                var rowspan = $('.count-' + mark).val();
                goods.attr('rowspan', rowspan);
                total.attr('rowspan', rowspan);
                var total_num = $('.quantity_total-' + mark).val();
                total.find('span').html(total_num);
            }
            $('.header-copy').remove();
            $('.table-fixed-header').fixedHeader();
        });
    </script>
    @else
    <script type="text/javascript">
        $(document).ready(function() {
            var company_items = $('.company');
            var total_items = $('.total');
            for (var i = 0; i < company_items.length; i += 1) {
                var company = $(company_items[i]);
                var total = $(total_items[i]);
                var mark = company.attr('data-mark');
                var rowspan = $('.count-' + mark).val();
                company.attr('rowspan', rowspan);
                total.attr('rowspan', rowspan);
                var total_num = $('.quantity_total-' + mark).val();
                total.find('span').html(total_num);
            }
            $('.header-copy').remove();
            $('.table-fixed-header').fixedHeader();
        });
    </script>
    @endif
    <script type="text/javascript">
        $(document).ready(function() {

            /*$('.show-history').click(function() {
                var table = $(this).parent().parent().find('.table').show().clone(true, true);
                $('#history-modal').find('.modal-body').html('');
                $('#history-modal').find('.modal-body').append(table);
            });*/

            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });

            $('input[name="quantity[]"]').change(function() {
                var parent = $(this).parents('tr');
                var mark = $(this).attr('data-mark');
                var ovalue = $.trim($(this).attr('data-value'));
                var value = $.trim($(this).val());
                ovalue = Number(ovalue);
                value = Number(value);
                var total = Number($('.quantity_total-' + mark).val().split(' ')[0]);
                var currentTotal = total - ovalue + value;
                if (!isNaN(currentTotal)) {
                    $('.total[data-mark="' + mark + '"] > span').html(currentTotal + ' 吨');

                    var stock = $.trim(parent.find('.stock').attr('data-value'));
                    var pending = $.trim(parent.find('.pending').attr('data-value'));
                    var monthly_demand = $.trim(parent.find('.monthly_demand').attr('data-value'));

                    var day = (value + Number(stock) + Number(pending)) / (monthly_demand / 30);
                    day = parseInt(day);
                    parent.find('.day').html(day+' 天');
                }
            });

            $('.show-history').click(function() {
                var detail = $(this).parent().parent().find('.detail').clone(true);
                detail.show();
                $('#history-modal').find('.modal-body').html('');
                $('#history-modal').find('.modal-body').append(detail);
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
                        $('#history-modal').modal('hide');
                        updateRow.find('.remark').val(res['data']['remark']);
                        updateRow.find('.price_floor').val(res['data']['price_floor']);
                        updateRow.find('.price_floor-txt').html(res['data']['price_floor']);
                        updateRow.find('.price_caps').val(res['data']['price_caps']);
                        updateRow.find('.price_caps-txt').html(res['data']['price_caps']);
                        updateRow.find('.quantity').val(res['data']['quantity']);
                        updateRow.find('.quantity-txt').html(res['data']['quantity']);
                        updateRow.find('.price_validity').val(res['data']['price_validity']);
                        updateRow.find('.price_validity-txt').html('招投标结束后'+res['data']['price_validity']+'小时');
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
                        }
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