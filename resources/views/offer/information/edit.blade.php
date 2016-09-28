@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js') }}"></script>
    <style type="text/css">
        .number {
            font-size:18px;
            font-weight: bold;
            font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }
    </style>
@stop

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        @if ($errors->has('msg'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('msg') }}</p>
        @endif
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">{{ $basket->name }}供应编辑物料</strong>
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
                                <label class="col-sm-2 control-label">物料名称</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2 goods_id" name="goods_id">
                                        <option value="">请选择</option>
                                        @foreach ($goods_records as $goods)
                                            <option @if(old('goods_id', $offer_information->goods_id) == $goods->id) selected @endif data-unit="{{ $goods->unit }}" value="{{ $goods->id }}">{{ $goods->name }}</option>
                                        @endforeach
                                    </select>
                                    <div style="display:none">
                                        @foreach ($goods_records as $goods)
                                            <p id="g-d-{{ $goods->id }}">{{ $goods->quality_standard }}</p>
                                        @endforeach
                                    </div>
                                </div>
                                @if ($errors->has('goods_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('goods_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">质量标准</label>
                                <div class="col-sm-6">
                                    <textarea name="quality_standard" readonly id="quality_standard" class="form-control" style="height: 80px">{{ old('quality_standard', $offer_information->quality_standard) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group
                                @if ($errors->has('quantity'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">可供数量</label>
                                <div class="col-sm-6">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" @if(old('quantity_type', ($offer_information->quantity == -1 ? 'infinite' : 'limited')) == 'limited') checked @endif name="quantity_type" value="limited">
                                            可供数量 <input type="text" @if(old('quantity_type', ($offer_information->quantity == -1 ? 'infinite' : 'limited')) == 'limited') value="{{ strval((float)old('quantity', $offer_information->quantity)) }}" @endif name="quantity" style="width: 90px" /> 吨
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" @if(old('quantity_type', ($offer_information->quantity == -1 ? 'infinite' : 'limited')) == 'infinite') checked @endif name="quantity_type" value="infinite">
                                            不限数量
                                        </label>
                                    </div>

                                    {{--<div class="input-group input-group-sm">--}}
                                        {{--<input type="text" class="form-control" name="quantity" value="{{ old('quantity', $offer_information->quantity) }}" placeholder="可供数量">--}}
                                        {{--<span class="input-group-addon unit">吨</span>--}}
                                    {{--</div>--}}
                                </div>
                                @if ($errors->has('quantity'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('quantity') }}</p>
                                @endif
                            </div>
                            {{--<div class="form-group--}}
                                {{--@if ($errors->has('price'))--}}
                                    {{--has-error--}}
                                {{--@endif--}}
                                    {{--form-group-sm">--}}
                                {{--<label class="col-sm-2 control-label">单价</label>--}}
                                {{--<div class="col-sm-6">--}}
                                    {{--<div class="input-group input-group-sm">--}}
                                        {{--<input type="text" class="form-control" name="price" value="{{ strval((float)old('price', $offer_information->price)) }}" placeholder="单价">--}}
                                        {{--<span class="input-group-addon unit">元/吨</span>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--@if ($errors->has('price'))--}}
                                    {{--<p class="col-sm-4 control-label form-msg">{{ $errors->first('price') }}</p>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                            {{--<div class="form-group--}}
                                {{--@if ($errors->has('companies'))--}}
                                    {{--has-error--}}
                                {{--@endif--}}
                                    {{--form-group-sm">--}}
                                {{--<label class="col-sm-2 control-label">收货单位</label>--}}
                                {{--<div class="col-sm-6">--}}
                                    {{--<select class="form-control select2" multiple name="companies[]">--}}
                                        {{--<!--<option value="">请选择收货单位</option>-->--}}
                                        {{--@foreach ($companies as $company)--}}
                                            {{--<option @if(in_array('[到库]-'.$company->name.','.$company->id, old('companies', json_decode($offer_information->delivery_modes, true)))) selected @endif value="[到库]-{{ $company->name }},{{ $company->id }}">[到库]-{{ $company->name }}</option>--}}
                                            {{--@if (!empty($company->delivery_modes))--}}
                                                {{--@foreach ($company->delivery_modes as $mode)--}}
                                                    {{--<option @if(in_array('['.$mode->mode.']-'.$company->name.','.$company->id, old('companies', json_decode($offer_information->delivery_modes, true)))) selected @endif value="[{{ $mode->mode }}]-{{ $company->name }},{{ $company->id }}">[{{ $mode->mode }}]-{{ $company->name }}</option>--}}
                                                {{--@endforeach--}}
                                            {{--@endif--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                                {{--@if ($errors->has('companies'))--}}
                                    {{--<p class="col-sm-4 control-label form-msg">{{ $errors->first('companies') }}</p>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">{{ $basket->name }}供应编辑物料</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="form-group
                                @if ($errors->has('payment') or $errors->has('payment_day'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">收款方式</label>
                                <div class="col-sm-6">
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" @if(old('payment', $offer_information->payment) == '先款后货') checked @endif name="payment" value="先款后货">
                                            先款后货
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" @if(preg_match('#(\d+)#', $offer_information->payment)) checked @endif name="payment" value="货到x天后付款">
                                            @if ($offer_information->payment != '先款后货')
                                            货到<input type="text" @if(preg_match('#(\d+)#', $offer_information->payment, $matched)) value="{{ old('payment_day', $matched[1]) }}" @endif name="payment_day" style="width: 30px" />天后付款
                                            @else
                                            货到<input type="text" @if(old('payment') == '货到x天后付款') value="{{ old('payment_day') }}" @endif name="payment_day" style="width: 30px" />天后付款
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                @if ($errors->has('payment') or $errors->has('payment_day'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('payment') }} {{ $errors->first('payment_day') }}</p>
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
                                        <span class="input-group-addon punit">报价后</span>
                                        <input type="text" class="form-control" name="price_validity" value="{{ old('price_validity', $offer_information->price_validity) }}" placeholder="小时数">
                                        <span class="input-group-addon punit">小时</span>
                                    </div>
                                </div>
                                @if ($errors->has('price_validity'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('price_validity') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('delivery_start'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">交货日(起)</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control date" name="delivery_start" value="{{ old('delivery_start', $offer_information->delivery_start) }}" placeholder="交货日（起）">
                                </div>
                                @if ($errors->has('delivery_start'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('delivery_start') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('delivery_stop'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">交货日(止)</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control date" name="delivery_stop" value="{{ old('delivery_stop', $offer_information->delivery_stop) }}" placeholder="交货日（止）">
                                </div>
                                @if ($errors->has('delivery_stop'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('delivery_stop') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('bargaining'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">线下议价</label>
                                <div class="col-sm-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="bargaining" @if(old('bargaining', ($offer_information->bargaining?'true':'false')) == 'true') checked @endif value="true"> 接受
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="bargaining" @if(old('bargaining', ($offer_information->bargaining?'true':'false')) =='false') checked @endif value="false"> 不接受
                                    </label>
                                </div>
                                @if ($errors->has('bargaining'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('bargaining') }}</p>
                                @endif
                            </div>
                            {{--<div class="form-group">--}}
                                {{--<label class="col-sm-2 control-label"> </label>--}}
                                {{--<div class="col-sm-6">--}}
                                    {{--<button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在保存..." type="submit"><span class="fa fa-save"></span> 保存到清单</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($errors->has('prices'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('prices') }}</p>
        @endif
        @if ($errors->has('addresses'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('addresses') }}</p>
        @endif
        <table class="table table-bordered" id="price-list">
            <tr>
                <th width="55">操作</th>
                <th width="120">单价（元/吨）</th>
                <th>交货地点</th>
            </tr>
            @if (!empty(old('prices', $offer_information->prices)))
                @foreach (old('prices', $offer_information->prices) as $k => $v)
                    <tr>
                        <td><button type="button" class="btn btn-default remove-price"><span class="fa fa-close"></span></button></td>
                        <td valign="middle"><input name="prices[]" placeholder="请填写" type="text" value="{{ $v }}" class="form-control text-center number price"></td>
                        <td>
                            @if (!empty(old('addresses.'.$k, $offer_information->addresses[$k])))
                                @foreach (old('addresses.'.$k, $offer_information->addresses[$k]) as $type => $val_list)
                                    @if (!empty($val_list))
                                        @foreach ($val_list as $val)
                                            @if ($type == 'company')
                                                <div style="margin:0 0 10px 0;">
                                                    <button style="margin-right:5px;" class="btn btn-danger" type="button"><span class="fa fa-close"></span></button>
                                                    <select data-index="{{ $k }}" style="width: 120px;" class="address_type" name="address_type[{{ $k }}]">
                                                        <option value="">请选择地址类型</option>
                                                        <option selected value="company">公司</option>
                                                        <option value="port">港口</option>
                                                        <option value="station">货运站</option>
                                                        <option value="since">自提</option>
                                                        <option value="other">其他</option>
                                                    </select>
                                                    <select class="type_company" name="addresses[{{ $k }}][company][]">
                                                        <option value="">请选择</option>
                                                        <option @if ($val == '所有公司') selected @endif value="所有公司">所有公司</option>
                                                        @foreach ($companies as $company)
                                                            <option @if ($val == $company->name) selected @endif value="{{ $company->name }}">{{ $company->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif ($type == 'port')
                                                <div style="margin:0 0 10px 0;">
                                                    <button style="margin-right:5px;" class="btn btn-danger" type="button"><span class="fa fa-close"></span></button>
                                                    <select data-index="{{ $k }}" style="width: 120px;" class="address_type" name="address_type[{{ $k }}]">
                                                        <option value="">请选择地址类型</option>
                                                        <option value="company">公司</option>
                                                        <option selected value="port">港口</option>
                                                        <option value="station">货运站</option>
                                                        <option value="since">自提</option>
                                                        <option value="other">其他</option>
                                                    </select>
                                                    <select style="width: 240px" class="type_port" name="addresses[{{ $k }}][port][]">
                                                        <option value="">请选择</option>
                                                        @foreach ($ports as $port)
                                                            <option @if ($val == $port) selected @endif value="{{ $port }}">{{ $port }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif ($type == 'station')
                                                <div style="margin:0 0 10px 0;">
                                                    <button style="margin-right:5px;" class="btn btn-danger" type="button"><span class="fa fa-close"></span></button>
                                                    <select data-index="{{ $k }}" style="width: 120px;" class="address_type" name="address_type[{{ $k }}]">
                                                        <option value="">请选择地址类型</option>
                                                        <option value="company">公司</option>
                                                        <option value="port">港口</option>
                                                        <option selected value="station">货运站</option>
                                                        <option value="since">自提</option>
                                                        <option value="other">其他</option>
                                                    </select>
                                                    <select style="width: 240px" class="type_station" name="addresses[{{ $k }}][station][]">
                                                        <option value="">请选择</option>
                                                        @foreach ($stations as $station)
                                                            <option @if ($val == $station) selected @endif value="{{ $station }}">{{ $station }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif ($type == 'since')
                                                <div style="margin:0 0 10px 0;">
                                                    <button style="margin-right:5px;" class="btn btn-danger" type="button"><span class="fa fa-close"></span></button>
                                                    <select data-index="{{ $k }}" style="width: 120px;" class="address_type" name="address_type[{{ $k }}]">
                                                        <option value="">请选择地址类型</option>
                                                        <option value="company">公司</option>
                                                        <option value="port">港口</option>
                                                        <option value="station">货运站</option>
                                                        <option selected value="since">自提</option>
                                                        <option value="other">其他</option>
                                                    </select>
                                                    <select style="width: 240px" class="type_since" name="addresses[{{ $k }}][since][]">
                                                        <option value="供方工厂（自提）">供方工厂（自提）</option>
                                                    </select>
                                                </div>
                                            @elseif ($type == 'other')
                                                <div style="margin:0 0 10px 0;">
                                                    <button style="margin-right:5px;" class="btn btn-danger" type="button"><span class="fa fa-close"></span></button>
                                                    <select data-index="{{ $k }}" style="width: 120px;" class="address_type" name="address_type[{{ $k }}]">
                                                        <option value="">请选择地址类型</option>
                                                        <option value="company">公司</option>
                                                        <option value="port">港口</option>
                                                        <option value="station">货运站</option>
                                                        <option value="since">自提</option>
                                                        <option selected value="other">其他</option>
                                                    </select>
                                                    <input name="addresses[{{ $k }}][{{ $type }}][]" value="{{ $val }}" style="width: 350px; display: inline-block" type="text" class="form-control type_other">
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                            <button type="button" class="btn btn-sm btn-success add-addr">添加地点</button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><button type="button" class="btn btn-default remove-price"><span class="fa fa-close"></span></button></td>
                    <td valign="middle"><input name="prices[]" placeholder="请填写" type="text" class="form-control text-center number price"></td>
                    <td><button type="button" class="btn btn-sm btn-success add-addr">添加地点</button></td>
                </tr>
            @endif
            <tr>
                <td colspan="3" class="text-center">
                    <button id="add-price" type="button" class="btn btn-info"><span class="fa fa-plus"> 追加报价</span></button>
                    <button style="margin-left:20px;" type="submit" class="btn btn-warning"><span class="fa fa-check"> 提交保存</span></button>
                </td>
            </tr>
        </table>
    </form>
    <div id="tpl" style="display: none;">
        <table class="price-item">
            <tr>
                <td><button type="button" class="btn btn-default remove-price"><span class="fa fa-close"></span></button></td>
                <td valign="middle"><input name="prices[]" placeholder="请填写" type="text" class="form-control text-center number price"></td>
                <td><button type="button" class="btn btn-sm btn-success add-addr">添加地点</button></td>
            </tr>
            <tr>
            </tr>
        </table>
        <select style="width: 120px;" class="address_type" name="address_type[]">
            <option value="">请选择地址类型</option>
            <option value="company">公司</option>
            <option value="port">港口</option>
            <option value="station">货运站</option>
            <option value="since">自提</option>
            <option value="other">其他</option>
        </select>
        <select class="type_company" name="addresses[]">
            <option value="">请选择</option>
            <option value="所有公司">所有公司</option>
            @foreach ($companies as $company)
                <option value="{{ $company->name }}">{{ $company->name }}</option>
            @endforeach
        </select>
        <select style="width: 240px" class="type_since" name="addresses[]">
            <option value="供方工厂（自提）">供方工厂（自提）</option>
        </select>
        <select style="width: 240px" class="type_port" name="addresses[]">
            <option value="">请选择</option>
            @foreach ($ports as $port)
                <option value="{{ $port }}">{{ $port }}</option>
            @endforeach
        </select>
        <select style="width: 240px" class="type_station" name="addresses[]">
            <option value="">请选择</option>
            @foreach ($stations as $station)
                <option value="{{ $station }}">{{ $station }}</option>
            @endforeach
        </select>
        <input name="addresses[]" style="width: 350px; display: inline-block" type="text" class="form-control type_other">
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(".select2").select2({
            language: "zh-CN"
        });

        /*$('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m-d'
        });*/
        $('input.date').datepicker({
            language: "zh-CN",
            format: "yyyy-mm-dd",
            autoclose: true
        });

        function removeAddr() {
            $(this).parent().remove();
        }

        $(document).ready(function() {
            $('.goods_id').change(function() {
                var goods_id = $(this).val();
                if (goods_id) {
                    var id = '#g-d-' + $(this).val();
                    $('#quality_standard').val($(id).html());
                } else {
                    $('#quality_standard').val('');
                }
            });

            $('.remove-price').click(function() {
                $(this).parent().parent().remove();
            });

            function addressTypeChangeEvent() {
                var value = $(this).val();
                var currentIndex = $(this).attr('data-index');
                switch (value) {
                    case "company":
                        $(this).parent().find('.type_port').next('.select2').remove();
                        $(this).parent().find('.type_station').next('.select2').remove();
                        $(this).parent().find('.type_since').next('.select2').remove();
                        $(this).parent().find('.type_other').remove();
                        var ele = $('#tpl .type_company').clone(true, true);
                        ele.attr('name', 'addresses['+currentIndex+'][company][]');
                        $(this).parent().append(ele);
                        ele.select2({
                            language: "zh-CN",
                            placeholder: "请选择公司"
                        });
                        break;
                    case "port":
                        $(this).parent().find('.type_other').remove();
                        $(this).parent().find('.type_station').next('.select2').remove();
                        $(this).parent().find('.type_company').next('.select2').remove();
                        $(this).parent().find('.type_since').next('.select2').remove();
                        var ele = $('#tpl .type_port').clone(true, true);
                        ele.attr('name', 'addresses['+currentIndex+'][port][]');
                        $(this).parent().append(ele);
                        ele.select2({
                            language: "zh-CN",
                            placeholder: "请选择港口"
                        });
                        break;
                    case "station":
                        $(this).parent().find('.type_other').remove();
                        $(this).parent().find('.type_port').next('.select2').remove();
                        $(this).parent().find('.type_company').next('.select2').remove();
                        $(this).parent().find('.type_since').next('.select2').remove();
                        var ele = $('#tpl .type_station').clone(true, true);
                        ele.attr('name', 'addresses['+currentIndex+'][station][]');
                        $(this).parent().append(ele);
                        ele.select2({
                            language: "zh-CN",
                            placeholder: "请选择车站"
                        });
                        break;
                    case "since":
                        $(this).parent().find('.type_other').remove();
                        $(this).parent().find('.type_port').next('.select2').remove();
                        $(this).parent().find('.type_company').next('.select2').remove();
                        $(this).parent().find('.type_station').next('.select2').remove();
                        var ele = $('#tpl .type_since').clone(true, true);
                        ele.attr('name', 'addresses['+currentIndex+'][since][]');
                        $(this).parent().append(ele);
                        ele.select2({
                            language: "zh-CN"
                        });
                        break;
                    case "other":
                        $(this).parent().find('.type_port').next('.select2').remove();
                        $(this).parent().find('.type_station').next('.select2').remove();
                        $(this).parent().find('.type_company').next('.select2').remove();
                        $(this).parent().find('.type_since').next('.select2').remove();
                        var ele = $('#tpl .type_other').clone(true, true);
                        ele.attr('name', 'addresses['+currentIndex+'][other][]');
                        $(this).parent().append(ele);
                        break;
                    default:
                        $(this).parent().remove();
                        break;
                }
            }

            $('#tpl .address_type').change(addressTypeChangeEvent);

            $('#add-price').click(function() {
                var tr = $('#tpl .price-item').find('tr').clone(true, true);
                tr.insertBefore($(this).parent().parent());
            });

            $('.add-addr').click(function() {
                var wrapper = $('<div style="margin:0 0 10px 0;"></div>');
                var closeBtn = $('<button style="margin-right:5px;" class="btn btn-danger" type="button"><span class="fa fa-close"></span></button>');
                closeBtn.click(removeAddr);
                var type = $('#tpl .address_type').clone(true, true);
                var currentIndex = $('#price-list').find('.price').index($(this).parent().parent().find('.price'));
                type.attr('name', 'address_type['+currentIndex+']').attr('data-index', currentIndex);
                wrapper.append(closeBtn);
                wrapper.append(type);
                wrapper.insertBefore($(this));
                type.select2({
                    language: "zh-CN",
                    placeholder: "请选择收货地址类型"
                });
            });

            $('#price-list .btn-danger').click(removeAddr);

            $('#price-list .address_type').change(addressTypeChangeEvent).select2({
                language: "zh-CN",
                placeholder: "请选择收货地址类型"
            });

            $('#price-list .type_company').select2({
                language: "zh-CN",
                placeholder: "请选择公司"
            });

            $('#price-list .type_port').select2({
                language: "zh-CN",
                placeholder: "请选择港口"
            });

            $('#price-list .type_station').select2({
                language: "zh-CN",
                placeholder: "请选择车站"
            });

            $('#price-list .type_since').select2({
                language: "zh-CN",
            });
        });
    </script>
@stop