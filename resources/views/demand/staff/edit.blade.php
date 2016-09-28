@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js') }}"></script>
@stop

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">编辑{{ $basket->name }}需求</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('goods_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">采购物料</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2" name="goods_id">
                                        <option value="">请选择</option>
                                        @foreach ($goods_records as $goods)
                                            <option @if(old('goods_id', $demand->goods_id) == $goods->id) selected @endif data-unit="{{ $goods->unit }}" value="{{ $goods->id }}">[{{ $goods->code }}] {{ $goods->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('goods_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('goods_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('quantity'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">采购数量</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="quantity" value="{{ old('quantity', strval((float)$demand->quantity)) }}" placeholder="采购量">
                                        <span class="input-group-addon unit">吨</span>
                                    </div>
                                </div>
                                @if ($errors->has('quantity'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('quantity') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('stock'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">当前库存</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="stock" value="{{ old('stock', strval((float)$demand->stock)) }}" placeholder="当前库存">
                                        <span class="input-group-addon unit">吨</span>
                                    </div>
                                </div>
                                @if ($errors->has('stock'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('stock') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('pending'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">未执行量</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="pending" value="{{ old('pending', strval((float)$demand->pending)) }}" placeholder="未执行量">
                                        <span class="input-group-addon unit">吨</span>
                                    </div>
                                </div>
                                @if ($errors->has('pending'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('pending') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('monthly_demand'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">本月需求量</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="monthly_demand" value="{{ old('monthly_demand', strval((float)$demand->monthly_demand)) }}" placeholder="本月需求量">
                                        <span class="input-group-addon unit">吨</span>
                                    </div>
                                </div>
                                @if ($errors->has('monthly_demand'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('monthly_demand') }}</p>
                                @endif
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">可用天数</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input readonly type="text" class="form-control day" name="day" value="{{ old('day', intval(($demand->stock+$demand->pending+$demand->quantity)/($demand->monthly_demand/30))) }}" placeholder="可用天数">
                                        <span class="input-group-addon">天</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">编辑{{ $basket->name }}需求</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="form-group
                                @if ($errors->has('price_floor'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">报价下限</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="price_floor" value="{{ old('price_floor', strval((float)$demand->price_floor)) }}" placeholder="报价下限">
                                        <span class="input-group-addon punit">元/吨</span>
                                    </div>
                                </div>
                                @if ($errors->has('price_floor'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('price_floor') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('price_caps'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">报价上限</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="price_caps" value="{{ old('price_caps', strval((float)$demand->price_caps)) }}" placeholder="报价上限">
                                        <span class="input-group-addon punit">元/吨</span>
                                    </div>
                                </div>
                                @if ($errors->has('price_caps'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('price_caps') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('price_validity'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">报价有效期</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-addon">招投标结束后</div>
                                        <input type="text" class="form-control input-sm" name="price_validity" value="{{ old('price_validity', $demand->price_validity) }}" placeholder="报价有效期">
                                        <div class="input-group-addon">小时</div>
                                    </div>
                                </div>
                                @if ($errors->has('price_validity'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('price_validity') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('assign_rule'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">分配方案</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="assign_rule">
                                        <option value="">请选择分配方案</option>
                                        @foreach ($assign_rules as $rule)
                                            <?php
                                                $assign_rule_data = json_decode($rule->rules, true);
                                                $assign_rule_data = implode('%；', $assign_rule_data).'%';
                                                $current_rule = json_decode($demand->assign_rule,true)['id'];
                                            ?>
                                            <option @if(old('assign_rule', $current_rule) == $rule->id) selected @endif value="{{ $rule->id }}">{{ $rule->name }} {{ $assign_rule_data }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('assign_rule'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('assign_rule') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('invoice'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">发票</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="invoice">
                                        <option @if(old('invoice', $demand->invoice)=='增值税普通发票') selected @endif value="增值税普通发票">增值税普通发票</option>
                                        <option @if(old('invoice', $demand->invoice)=='增值税专用发票') selected @endif value="增值税专用发票">增值税专用发票</option>
                                        <option @if(old('invoice', $demand->invoice)=='增值税普通或专用发票') selected @endif value="增值税普通或专用发票">增值税普通或专用发票</option>
                                    </select>
                                </div>
                                @if ($errors->has('invoice'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('invoice') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('payment'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">付款方式</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="payment" value="{{ old('payment', $demand->payment) }}" placeholder="付款方式">
                                </div>
                                @if ($errors->has('payment'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('payment') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('delivery_date_start'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">到货日（起）</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control date" name="delivery_date_start" value="{{ old('delivery_date_start', $demand->delivery_date_start) }}" placeholder="到货日（起）">
                                </div>
                                @if ($errors->has('delivery_date_start'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('delivery_date_start') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('delivery_date_stop'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">到货日（止）</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control date" name="delivery_date_stop" value="{{ old('delivery_date_stop', $demand->delivery_date_stop) }}" placeholder="到货日（止）">
                                </div>
                                @if ($errors->has('delivery_date_stop'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('delivery_date_stop') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在保存..." type="submit"><span class="fa fa-save"></span> 保存到清单</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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

        $(document).ready(function() {
            function setUnit() {
                var unit = $(this).find("option:selected").attr('data-unit');
                $(this).parents('table').find('.unit').text(unit);
                $(this).parents('table').find('.punit').text('元/'+unit);
            }

            function evaluateDays() {
                var parent = $(this).parents('form');
                var quantity = $.trim(parent.find('input[name="quantity"]').val());
                var stock = $.trim(parent.find('input[name="stock"]').val());
                var pending = $.trim(parent.find('input[name="pending"]').val());
                var monthly_demand = $.trim(parent.find('input[name="monthly_demand"]').val());

                if (quantity && stock && pending && monthly_demand) {
                    var total = Number(quantity) + Number(stock) + Number(pending);
                    var day = total / (monthly_demand / 30);
                    day = parseInt(day);
                    parent.find('.day').val(day);
                }
            };

            $(".select2").change(setUnit);
            $('input[name^="quantity"],input[name="stock"],input[name="pending"],input[name="monthly_demand"]').change(evaluateDays);
        });
    </script>
@stop