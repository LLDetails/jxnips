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
    <div style="display: none;" id="tpl">
        <table class="list table table-striped table-bordered table-hover" style="margin-bottom:15px;">
            <tr>
                <th class="text-center" style="line-height: 60px;">商品名</th>
                <th class="text-center" style="line-height: 60px;">采购量</th>
                <th class="text-center" rowspan="2" colspan="2">报价区间</th>
                {{--<th class="text-center">分配方案</th>--}}
                <th class="text-center" style="line-height: 60px;">报价有效期</th>
                <th class="text-center" colspan="2" style="line-height: 60px;">到货时间区间</th>
                <th class="text-center" style="line-height: 60px;">操作</th>
            </tr>
            <tr>
                <th class="text-center">下限</th>
                <th class="text-center">上限</th>
            </tr>
            <tr>
                <td width="160">
                    <select class="form-control select2x" name="goods_id[]">
                        <option value="">请选择</option>
                        @foreach ($goods_records as $goods)
                            <option data-unit="{{ $goods->unit }}" value="{{ $goods->id }}">[{{ $goods->code }}] {{ $goods->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center" width="130">
                    <div class="input-group" style="width:120px;">
                        <input type="text" class="form-control text-center" name="quantity[]" value="" placeholder="采购量">
                        <span class="input-group-addon unit">吨</span>
                    </div>
                </td>
                <td class="text-center" width="130">
                    <div class="input-group" style="width:140px;">
                        <input type="text" class="form-control text-center" name="price_floor[]" value="" placeholder="报价下限">
                        <span class="input-group-addon punit">元/吨</span>
                    </div>
                </td>
                <td class="text-center" width="130">
                    <div class="input-group" style="width:140px;">
                        <input type="text" class="form-control text-center" name="price_caps[]" value="" placeholder="报价上限">
                        <span class="input-group-addon punit">元/吨</span>
                    </div>
                </td>
                {{--<td>--}}
                    {{--<select class="form-control" name="assign_rule[]">--}}
                        {{--<option value="">请选择</option>--}}
                        {{--@foreach ($assign_rules as $rule)--}}
                            {{--<option value="{{ $rule->id }}">{{ $rule->name }}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</td>--}}
                <td class="text-center" width="90">
                    <input type="text" style="width:80px;" class="form-control text-center" name="price_validity[]" value="" placeholder="报价有效期">
                </td>
                <td class="text-center" width="130">
                    <input type="text" style="width:150px;" class="form-control text-center datex" name="delivery_date_start[]" value="" placeholder="到货时间起">
                </td>
                <td class="text-center" width="130">
                    <input type="text" style="width:150px;" class="form-control text-center datex" name="delivery_date_stop[]" value="" placeholder="到货时间止">
                </td>
                <td style="line-height:80px" rowspan="2" class="text-center"><a href="javascript:void(0)" class="btn btn-default delete"><span class="fa fa-times"></span></a></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-addon">当前库存</span>
                        <input type="text" class="form-control text-center" name="stock[]" value="" placeholder="当前库存">
                        <span class="input-group-addon unit">吨</span>
                    </div>
                </td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-addon">在途&未执行量</span>
                        <input type="text" class="form-control text-center" name="pending[]" value="" placeholder="在途&未执行">
                        <span class="input-group-addon unit">吨</span>
                    </div>
                </td>
                <td colspan="2">
                    <div class="input-group">
                        <span class="input-group-addon">本月需求量</span>
                        <input type="text" class="form-control text-center" name="monthly_demand[]" value="" placeholder="本月需求量">
                        <span class="input-group-addon unit">吨</span>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">可用</span>
                        <input readonly type="text" name="day[]" class="day form-control text-center" value="" placeholder="可用天数">
                        <span class="input-group-addon">天</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <select class="form-control" name="assign_rule[]">
                        <option value="">请选择分配方案</option>
                        @foreach ($assign_rules as $rule)
                            <?php
                            $assign_rule_data = json_decode($rule->rules, true);
                            $assign_rule_data = implode('%；', $assign_rule_data).'%';
                            ?>
                            <option value="{{ $rule->id }}">{{ $rule->name }} {{ $assign_rule_data }}</option>
                        @endforeach
                    </select>
                </td>
                <td colspan="2" class="text-right">
                    <select class="form-control" name="invoice[]">
                        <option value="普通发票或增值税发票">普通发票或增值税发票</option>
                        <option value="增值税发票">增值税发票</option>
                        <option value="普通发票">普通发票</option>
                    </select>
                </td>
                <td colspan="2" class="text-right">
                    <div class="input-group">
                        <span class="input-group-addon">付款方式</span>
                        <input type="text" name="payment[]" class="form-control text-center" value="货到15天内付款" placeholder="付款方式">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        @if ($errors->has('form'))
            <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
        @endif
        @if (!is_array(old('goods_id')))
        <table id="form-table" class="list table table-striped table-bordered table-hover">
            <tr>
                <th class="text-center">商品名</th>
                <th class="text-center">采购量</th>
                <th class="text-center" colspan="2">报价上下限</th>
                {{--<th class="text-center">分配方案</th>--}}
                <th class="text-center">报价有效期</th>
                <th class="text-center" colspan="2">到货时间区间</th>
                <th class="text-center">操作</th>
            </tr>
                <tr>
                    <td width="160">
                        <select class="form-control select2" name="goods_id[]">
                            <option value="">请选择</option>
                            @foreach ($goods_records as $goods)
                                <option data-unit="{{ $goods->unit }}" value="{{ $goods->id }}">[{{ $goods->code }}] {{ $goods->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center" width="130">
                        <div class="input-group" style="width:120px;">
                            <input type="text" class="form-control text-center" name="quantity[]" value="{{ old('quantity') }}" placeholder="采购量">
                            <span class="input-group-addon unit">吨</span>
                        </div>
                    </td>
                    <td class="text-center" width="130">
                        <div class="input-group" style="width:140px;">
                            <input type="text" class="form-control text-center" name="price_floor[]" value="{{ old('price_floor') }}" placeholder="报价下限">
                            <span class="input-group-addon punit">元/吨</span>
                        </div>
                    </td>
                    <td class="text-center" width="130">
                        <div class="input-group" style="width:140px;">
                            <input type="text" class="form-control text-center" name="price_caps[]" value="{{ old('price_caps') }}" placeholder="报价上限">
                            <span class="input-group-addon punit">元/吨</span>
                        </div>
                    </td>
                    {{--<td>--}}
                        {{--<select class="form-control" name="assign_rule[]">--}}
                            {{--<option value="">请选择</option>--}}
                            {{--@foreach ($assign_rules as $rule)--}}
                                {{--<option value="{{ $rule->id }}">{{ $rule->name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</td>--}}
                    <td class="text-center" width="90">
                        <input type="text" style="width:80px;" class="form-control text-center" name="price_validity[]" value="{{ old('price_validity') }}" placeholder="报价有效期">
                    </td>
                    <td class="text-center" width="130">
                        <input type="text" style="width:145px;" class="form-control text-center date" name="delivery_date_start[]" value="{{ old('delivery_date_start') }}" placeholder="到货时间起">
                    </td>
                    <td class="text-center" width="130">
                        <input type="text" style="width:145px;" class="form-control text-center date" name="delivery_date_stop[]" value="{{ old('delivery_date_stop') }}" placeholder="到货时间止">
                    </td>
                    <td style="line-height:80px" rowspan="2" class="text-center"><a href="javascript:void(0)" class="btn btn-default delete"><span class="fa fa-times"></span></a></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="input-group">
                            <span class="input-group-addon">当前库存</span>
                            <input type="text" class="form-control text-center" name="stock[]" value="{{ old('stock') }}" placeholder="当前库存">
                            <span class="input-group-addon unit">吨</span>
                        </div>
                    </td>
                    <td colspan="2">
                        <div class="input-group">
                            <span class="input-group-addon">在途&未执行量</span>
                            <input type="text" class="form-control text-center" name="pending[]" value="{{ old('pending') }}" placeholder="在途&未执行">
                            <span class="input-group-addon unit">吨</span>
                        </div>
                    </td>
                    <td colspan="2">
                        <div class="input-group">
                            <span class="input-group-addon">本月需求量</span>
                            <input type="text" class="form-control text-center" name="monthly_demand[]" value="{{ old('monthly_demand') }}" placeholder="本月需求量">
                            <span class="input-group-addon unit">吨</span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">可用</span>
                            <input readonly type="text" name="day[]" class="day form-control text-center" value="" placeholder="可用天数">
                            <span class="input-group-addon">天</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <select class="form-control" name="assign_rule[]">
                            <option value="">请选择分配方案</option>
                            @foreach ($assign_rules as $rule)
                                <?php
                                $assign_rule_data = json_decode($rule->rules, true);
                                $assign_rule_data = implode('%；', $assign_rule_data).'%';
                                ?>
                                <option value="{{ $rule->id }}">{{ $rule->name }} {{ $assign_rule_data }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td colspan="2" class="text-right">
                        <select class="form-control" name="invoice[]">
                            <option value="普通发票或增值税发票">普通发票或增值税发票</option>
                            <option value="增值税发票">增值税发票</option>
                            <option value="普通发票">普通发票</option>
                        </select>
                    </td>
                    <td colspan="2" class="text-right">
                        <div class="input-group">
                            <span class="input-group-addon">付款方式</span>
                            <input type="text" name="payment[]" class="form-control text-center" value="货到15天内付款" placeholder="付款方式">
                        </div>
                    </td>
                </tr>
            </table>
            @else
            <?php
                $fields = [
                    'goods_id', 'quantity', 'price_floor',
                    'price_caps', 'price_validity',
                    'delivery_date_start', 'delivery_date_stop',
                    'stock', 'pending', 'monthly_demand',
                    'invoice', 'payment'
                ];
                $messages = $errors->getMessages();
            ?>
                @foreach (old('goods_id') as $k => $v)
                    <table id="form-table" class="list table table-striped table-bordered table-hover">
                        <tr>
                            <th class="text-center">商品名</th>
                            <th class="text-center">采购量</th>
                            <th class="text-center" colspan="2">报价上下限</th>
                            {{--<th class="text-center">分配方案</th>--}}
                            <th class="text-center">报价有效期</th>
                            <th class="text-center" colspan="2">到货时间区间</th>
                            <th class="text-center">操作</th>
                        </tr>
                    <?php $err_msg = '' ?>
                    @foreach ($fields as $field)
                        @if (isset($messages[$field.'.'.$k]))
                            <?php $err_msg .= '<span class="fa fa-info-circle"></span>' . $messages[$field.'.'.$k][0]. '；' ?>
                        @endif
                    @endforeach
                    @if (!empty($err_msg))
                        <tr class="form-error-msg danger">
                           <td colspan="8" style="color:#f50000">
                                {!! $err_msg !!}
                           </td>
                        </tr>
                    @endif
                    <tr @if(!empty($err_msg)) class="danger" @endif>
                        <td width="160">
                            <select class="form-control select2" name="goods_id[]">
                                <option value="">请选择</option>
                                @foreach ($goods_records as $goods)
                                    <option @if(old('goods_id.'.$k) == $goods->id) selected @endif data-unit="{{ $goods->unit }}" value="{{ $goods->id }}">[{{ $goods->code }}] {{ $goods->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" width="130">
                            <div class="input-group" style="width:120px;">
                                <input type="text" class="form-control text-center" name="quantity[]" value="{{ old('quantity.'.$k) }}" placeholder="采购量">
                                <span class="input-group-addon unit">吨</span>
                            </div>
                        </td>
                        <td class="text-center" width="130">
                            <div class="input-group" style="width:140px;">
                                <input type="text" class="form-control text-center" name="price_floor[]" value="{{ old('price_floor.'.$k) }}" placeholder="报价下限">
                                <span class="input-group-addon punit">元/吨</span>
                            </div>
                        </td>
                        <td class="text-center" width="130">
                            <div class="input-group" style="width:140px;">
                                <input type="text" class="form-control text-center" name="price_caps[]" value="{{ old('price_caps.'.$k) }}" placeholder="报价上限">
                                <span class="input-group-addon punit">元/吨</span>
                            </div>
                        </td>
                        {{--<td>--}}
                            {{--<select class="form-control" name="assign_rule[]">--}}
                                {{--<option value="">请选择</option>--}}
                                {{--@foreach ($assign_rules as $rule)--}}
                                    {{--<option @if(old('assign_rule.'.$k) == $rule->id) selected @endif value="{{ $rule->id }}">{{ $rule->name }}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        {{--</td>--}}
                        <td class="text-center" width="90">
                            <input type="text" style="width:80px;" class="form-control text-center" name="price_validity[]" value="{{ old('price_validity.'.$k) }}" placeholder="报价有效期">
                        </td>
                        <td class="text-center" width="130">
                            <input type="text" style="width:145px;" class="form-control text-center date" name="delivery_date_start[]" value="{{ old('delivery_date_start.'.$k) }}" placeholder="到货时间起">
                        </td>
                        <td class="text-center" width="130">
                            <input type="text" style="width:145px;" class="form-control text-center date" name="delivery_date_stop[]" value="{{ old('delivery_date_stop.'.$k) }}" placeholder="到货时间止">
                        </td>
                        <td style="line-height:80px" rowspan="2" class="text-center"><a href="javascript:void(0)" class="btn btn-default delete"><span class="fa fa-times"></span></a></td>
                    </tr>
                    <tr @if(!empty($err_msg)) class="danger" @endif>
                        <td colspan="2">
                            <div class="input-group">
                                <span class="input-group-addon">当前库存</span>
                                <input type="text" class="form-control text-center" name="stock[]" value="{{ old('stock.'.$k) }}" placeholder="当前库存">
                                <span class="input-group-addon unit">吨</span>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="input-group">
                                <span class="input-group-addon">在途&未执行量</span>
                                <input type="text" class="form-control text-center" name="pending[]" value="{{ old('pending.'.$k) }}" placeholder="在途&未执行">
                                <span class="input-group-addon unit">吨</span>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="input-group">
                                <span class="input-group-addon">本月需求量</span>
                                <input type="text" class="form-control text-center" name="monthly_demand[]" value="{{ old('monthly_demand.'.$k) }}" placeholder="本月需求量">
                                <span class="input-group-addon unit">吨</span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon">可用</span>
                                <input readonly type="text" name="day[]" class="day form-control text-center" value="{{ old('day.'.$k) }}" placeholder="可用天数">
                                <span class="input-group-addon">天</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="
                            @if ($errors->has('assign_rule.'.$k))
                                danger
                            @endif
                        ">
                            <select class="form-control" name="assign_rule[]">
                                <option value="">请选择分配方案</option>
                                @foreach ($assign_rules as $rule)
                                    <?php
                                        $assign_rule_data = json_decode($rule->rules, true);
                                        $assign_rule_data = implode('%；', $assign_rule_data).'%';
                                    ?>
                                    <option @if(old('assign_rule.'.$k) == $rule->id) selected @endif value="{{ $rule->id }}">{{ $rule->name }} {{ $assign_rule_data }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('assign_rule.'.$k))
                                <p class="form-msg"><span class="fa fa-info-circle"></span> {{ $errors->first('assign_rule.'.$k) }}</p>
                            @endif
                        </td>
                        <td colspan="2" class="text-right">
                            <select class="form-control" name="invoice[]">
                                <option @if (old('invoice.'.$k) == '普通发票或增值税发票') selected @endif value="普通发票或增值税发票">普通发票或增值税发票</option>
                                <option @if (old('invoice.'.$k) == '增值税发票') selected @endif value="增值税发票">增值税发票</option>
                                <option @if (old('invoice.'.$k) == '普通发票') selected @endif value="普通发票">普通发票</option>
                            </select>
                        </td>
                        <td colspan="2" class="text-right">
                            <div class="input-group">
                                <span class="input-group-addon">付款方式</span>
                                <input type="text" name="payment[]" class="form-control text-center" value="{{ old('payment.'.$k, '货到15天内付款') }}" placeholder="付款方式">
                            </div>
                        </td>
                    </tr>
                </table>
                @endforeach
            @endif
        <div class="text-center" id="submit">
            @if (!empty($next_role))
                <select class="select2">
                    <option value="{{ $next_role->id }}">下一级审核：{{ $next_role->name }}</option>
                </select>
            @endif
            <button style="margin-right: 20px;" class="btn btn-info" type="submit"><span class="fa fa-save"></span> 保存需求清单</button>
            <button id="append" class="btn btn-success" type="button"><span class="fa fa-plus"></span> 增加一个需求</button>
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

            function evaluateDays() {
                var parent = $(this).parents('table');
                var quantity = $.trim(parent.find('input[name="quantity[]"]').val());
                var stock = $.trim(parent.find('input[name="stock[]"]').val());
                var pending = $.trim(parent.find('input[name="pending[]"]').val());
                var monthly_demand = $.trim(parent.find('input[name="monthly_demand[]"]').val());

                if (quantity && stock && pending && monthly_demand) {
                    var total = Number(quantity) + Number(stock) + Number(pending);
                    var day = total / (monthly_demand / 30);
                    day = parseInt(day);
                    parent.find('.day').val(day);
                }
            };

            function deleteRow() {
                $(this).parents('table').remove();
            }

            function setUnit() {
                var unit = $(this).find("option:selected").attr('data-unit');
                $(this).parents('table').find('.unit').text(unit);
                $(this).parents('table').find('.punit').text('元/'+unit);
            }

            $('.delete').click(deleteRow);
            $(".select2").change(setUnit);
            $('input[name^="quantity[]"],input[name="stock[]"],input[name="pending[]"],input[name="monthly_demand[]"]').change(evaluateDays);

            $('#append').click(function() {
                var tb = $('#tpl table').clone(true, true);
                tb.insertBefore($('#submit'));
                tb.find('.select2x').select2({
                    language: "zh-CN"
                });
                tb.find('.select2x').change(setUnit);
                tb.find('.datex').datetimepicker({
                    lang: 'ch',
                    format:'Y-m-d'
                });
                tb.find('.delete').click(deleteRow);
                tb.find('input[name^="quantity[]"],input[name="stock[]"],input[name="pending[]"],input[name="monthly_demand[]"]').change(evaluateDays);
            });
        });
    </script>
@endsection