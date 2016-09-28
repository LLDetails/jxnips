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
        {!! csrf_field() !!}
        @if ($errors->has('form'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
        @endif

        <table id="form-table" class="list table-fixed-header table table-bordered table-hover">
            <thead class="header">
                <tr class="info">
                    <?php
                        $goods = json_decode($bid->goods_static);
                    ?>
                    <th colspan="9">
                        {{ $goods->name }}采购标书报价将在{{ $bid->offer_stop }}结束，结束倒计时：<span class="colockbox" id="colockbox-global"><span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span></span>
                    </th>
                </tr>
                <tr>
                    <th colspan="9">
                        质量指标：{{ $goods->quality_standard }}
                    </th>
                </tr>
                <tr>
                    <th class="text-center">公司名 / 地址</th>
                    <th class="text-center">采购数量</th>
                    <th class="text-center">发票 / 付款方式</th>
                    <th class="text-center">分配方案</th>
                    <th class="text-center">报价有效期</th>
                    <th class="text-center">交货日期</th>
                    <th class="text-center">我的报价</th>
                    <th class="text-center">报价时间</th>
                    <th class="text-center">操作</th>
                </tr>
            </thead>
            @foreach ($demands as $k=>$demand)
                <?php
                    $assign_rule = json_decode($demand->assign_rule);
                    $assign_rule_data = $assign_rule->rules;
                    $assign_rule_data = json_decode($assign_rule_data, true);
                    $assign = array_filter($assign_rule_data);
                    $min_assign = min($assign);
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
                    <td class="text-center">
                        <span style="line-height:40px">{{ $assign_rule->name }}【{{ $assign_rule_data }}】</span>
                    </td>
                    <td class="text-center"><span style="line-height:40px">招投标结束后{{ $demand->price_validity }}小时</span></td>
                    <td class="text-center"><span style="line-height:40px">{{ $demand->delivery_date_start }} ~ {{ $demand->delivery_date_stop }}</span></td>
                    <?php
                        $offer = $demand->offer()->where('user_id', auth()->user()->id)->first();
                    ?>
                    <td class="text-center">
                        <span style="line-height:40px">
                            @if(!empty($offer) and is_null($offer->reason))
                                <i class="alpha-number">{{ strval((float)$offer->price) }}</i>
                            @endif
                        </span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px">
                            @if(!empty($offer) and is_null($offer->reason))
                                {{ $offer->updated_at }}
                            @endif
                        </span>
                    </td>
                    <td class="text-center">
                        <span style="line-height:40px">
                            <button data-offer-stop="{{ $bid->offer_stop }}" @if(!empty($offer) and !is_null($offer->reason)) disabled @endif type="button" class="btn btn-sm @if(!empty($offer)) btn-warning @else btn-info @endif show-offer" data-toggle="modal" data-target="#offer-modal">@if(!empty($offer)) 改价 @else 报价 @endif</button>
                            <button @if(!empty($offer) and is_null($offer->reason)) disabled @endif type="button" class="btn btn-sm btn-danger show-refuse" data-toggle="modal" data-target="#refuse-modal">不参与</button>
                            <div class="offer-box" style="display: none;">
                                <div style="display: none;" class="alert alert-dismissible alert-danger">
                                </div>
                                <table class="table table-bordered">
                                    <tr class="info">
                                        <th colspan="4">物料名称：{{ $goods->name }}</th>
                                    </tr>
                                    <tr>
                                        <th style="background: #EEEEEE">采购方</th>
                                        <td style="font-size: 14px;">{{ $demand->company->name }}</td>
                                        <th style="background: #EEEEEE">公司地址</th>
                                        <td style="font-size: 14px;">{{ $demand->company->delivery_address }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #EEEEEE">采购数量</th>
                                        <td style="font-size: 14px;"><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</td>
                                        <th style="background: #EEEEEE">分配方案</th>
                                        <td style="font-size: 14px;">{{ $assign_rule->name }}【{{ $assign_rule_data }}】</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #EEEEEE">发票类型</th>
                                        <td style="font-size: 14px;">{{ $demand->invoice }}</td>
                                        <th style="background: #EEEEEE">付款方式</th>
                                        <td style="font-size: 14px;">{{ $demand->payment }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #EEEEEE">报价有效期</th>
                                        <td style="font-size: 14px;">招投标结束后{{ $demand->price_validity }}小时</td>
                                        <th style="background: #EEEEEE">交货时间</th>
                                        <td style="font-size: 14px;">{{ $demand->delivery_date_start }} ~ {{ $demand->delivery_date_stop }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #EEEEEE">最低成交量</th>
                                        <td style="font-size: 14px;"><i class="alpha-number">{{ strval(($demand->quantity*$min_assign)/100) }}</i> {{ $goods->unit }}</td>
                                        <th style="background: #EEEEEE">最高成交量</th>
                                        <td style="font-size: 14px;"><input type="text" class="quantity_caps" value="{{ (!empty($offer) and is_null($offer->reason))?strval((float)$offer->quantity_caps):'' }}"> {{ $goods->unit }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background: #EEEEEE">供方报价</th>
                                        <td style="font-size: 14px;"><input type="text" class="price" value="{{ (!empty($offer) and is_null($offer->reason))?strval((float)$offer->price):'' }}"> 元/{{ $goods->unit }}</td>
                                        <th style="background: #EEEEEE">到货方式</th>
                                        <td style="font-size: 14px;">
                                            <select class="delivery_mode">
                                                <option value="0.00,到库">到库 - 0元/{{ $goods->unit }}</option>
                                                @foreach ($demand->company->delivery_modes as $mode)
                                                    <option @if(old('delivery_mode.'.$k, !empty($offer->delivery_mode)?$offer->delivery_costs.','.$offer->delivery_mode:'') == $mode->costs.','.$mode->mode) selected @endif value="{{ $mode->costs.','.$mode->mode }}">{{ $mode->mode }} - {{ $mode->costs }}元/{{ $goods->unit }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <div class="text-center"><button data-url="{{ URL::full() }}" data-demand-id="{{ $demand->id }}" type="button" class="btn btn-warning btn-sm offer-btn"><span class="fa fa-check"></span> 提交报价</button></div>
                            </div>
                            <div class="refuse-box text-center" style="display: none;">
                                @if(!empty($offer) and !is_null($offer->reason))
                                    {{ $offer->reason }}
                                @else
                                    <div style="display: none;" class="alert alert-dismissible alert-danger">
                                    </div>
                                    <select class="form-control reason">
                                        <option value="无货可供">无货可供</option>
                                        <option value="报价范围受限">报价范围受限</option>
                                        <option value="交货期受限">交货期受限</option>
                                        <option value="款期过长">款期过长</option>
                                        <option value="质量标准无法满足">质量标准无法满足</option>
                                        <option value="不接受此计重方式">不接受此计重方式</option>
                                        <option value="交货地点及方式受限">交货地点及方式受限</option>
                                        <option value="包装达不到要求">包装达不到要求</option>
                                        <option value="不接受此分配方案">不接受此分配方案</option>
                                        <option value="其他">其他</option>
                                    </select>
                                    <br />
                                    <input style="display: none;" type="text" class="form-control other" placeholder="请填写其他理由">
                                    <br />
                                    <button data-url="{{ URL::full() }}" data-demand-id="{{ $demand->id }}" type="button" class="btn btn-sm btn-danger refuse-btn">提交不参与报价理由</button>
                                @endif
                            </div>
                        </span>
                    </td>
                </tr>
            @endforeach
        </table>

        <div id="offer-modal" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title text-center">重要提示：提交报价前请确认能满足各项信息要求！报价倒计时：<span class="colockbox" id="colockbox1"><span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span></span></h4>
                    </div>
                    <div class="modal-body">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div id="refuse-modal" class="modal fade">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">不报价理由</h4>
                    </div>
                    <div class="modal-body">

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
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

        function countDown(end,start,id){
            //var day_elem = $(id).find('.day');
            var hour_elem = $(id).find('.hour');
            var minute_elem = $(id).find('.minute');
            var second_elem = $(id).find('.second');
            var end_time = new Date(end).getTime(),//月份是实际月份-1
            sys_second = (end_time-new Date(start).getTime())/1000;
            var timer = setInterval(function(){
                if (sys_second > 1) {
                    sys_second -= 1;
                    //var day = Math.floor((sys_second / 3600) / 24);
                    var hour = Math.floor((sys_second / 3600) % 24);
                    var minute = Math.floor((sys_second / 60) % 60);
                    var second = Math.floor(sys_second % 60);
                    //day_elem && $(day_elem).text(day);//计算天
                    $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
                    $(minute_elem).text(minute<10?"0"+minute:minute);//计算分钟
                    $(second_elem).text(second<10?"0"+second:second);//计算秒杀
                } else {
                    clearInterval(timer);
                }
            }, 1000);
        }

        $(document).ready(function() {

            $.get('{{ route('system.datetime') }}', function(datetime) {
                countDown('{{ str_replace('-', '/', $bid->offer_stop) }}', datetime.replace(/-/g, '/'), '#colockbox-global');
            });

            $('select.reason').change(function() {
                if ($(this).val() == '其他') {
                    $(this).parent().find('.other').show();
                } else {
                    $(this).parent().find('.other').hide();
                }
            });

            $('.show-refuse').click(function() {
                var refuse = $(this).parent().parent().find('.refuse-box').clone(true);
                refuse.show();
                $('#refuse-modal').find('.modal-body').html('');
                $('#refuse-modal').find('.modal-body').append(refuse);
            });

            $('.show-offer').click(function() {
                var offerStop = $(this).attr('data-offer-stop');
                $.get('{{ route('system.datetime') }}', function(datetime) {
                    countDown(offerStop, datetime, '#colockbox1');
                });
                var offer = $(this).parent().parent().find('.offer-box').clone(true);
                offer.show();
                $('#offer-modal').find('.modal-body').html('');
                $('#offer-modal').find('.modal-body').append(offer);
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

            $('.refuse-btn').click(function() {
                var url = $(this).attr('data-url');
                var demandId = $(this).attr('data-demand-id');
                var parent = $(this).parent();
                var reason = parent.find('.reason').val();
                var other = parent.find('.other').val();
                var data = {
                    type: 'refuse',
                    demand_id: demandId,
                    reason: reason,
                    other: other,
                    '_token': '{{ csrf_token() }}'
                };
                var msgBox = parent.find('.alert');
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
                    }
                });
            });

            $('.offer-btn').click(function() {
                var url = $(this).attr('data-url');
                var demandId = $(this).attr('data-demand-id');
                var parent = $(this).parents('.offer-box');
                var data = {
                    quantity_caps: parent.find('.quantity_caps').val(),
                    price: parent.find('.price').val(),
                    delivery_mode: parent.find('.delivery_mode').val(),
                    type: 'offer',
                    demand_id: demandId,
                    '_token': '{{ csrf_token() }}'
                };
                var msgBox = parent.find('.alert');
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
                    }
                });
            });
        });
    </script>
@endsection