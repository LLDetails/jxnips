@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
    <style type="text/css">
        .number {
            font-size:18px;
            font-weight: bold;
            font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }
    </style>
@stop

@section('main')
    <form class="form-horizontal">
    <div class="row">
        <div class="col-sm-6 col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="height: 46px;">
                    <h3 class="panel-title">
                        <strong class="pull-left" style="margin-top:6px;">{{ $basket->name }}供应物料查看</strong>
                        <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                    </h3>
                </div>
                <div class="panel-body">
                    <div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">供应物料</label>
                            <div class="col-sm-6">
                                <div class="form-control-static">{{ $offer_information->goods->name }}</div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">质量标准</label>
                            <div class="col-sm-6">
                                <textarea readonly id="quality_standard" class="form-control" style="height: 80px">{{ old('quality_standard', $offer_information->quality_standard) }}</textarea>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">可供数量</label>
                            <div class="col-sm-6">
                                <div class="form-control-static">
                                    @if ($offer_information->quantity >= 0)
                                    {{ strval((float)$offer_information->quantity) }} 吨
                                    @else
                                    不限
                                    @endif
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
                        <strong class="pull-left" style="margin-top:6px;">{{ $basket->name }}供应编辑物料</strong>
                        <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                    </h3>
                </div>
                <div class="panel-body">
                    <div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">收款方式</label>
                            <div class="col-sm-6">
                                <div class="form-control-static">{{ $offer_information->payment }}</div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">报价有效期</label>
                            <div class="col-sm-6">
                                <div class="form-control-static">报价后{{ $offer_information->price_validity }}小时</div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">交货日</label>
                            <div class="col-sm-6">
                                <div class="form-control-static">{{ explode(' ', $offer_information->delivery_start)[0] }} ~ {{ explode(' ', $offer_information->delivery_stop)[0] }}</div>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-2 control-label">线下议价</label>
                            <div class="col-sm-6">
                                <div class="form-control-static">{{ $offer_information->bargaining?'接受':'不接受' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered" id="price-list">
        <tr>
            <th width="120">单价（元/吨）</th>
            <th>交货地点</th>
        </tr>
        @if (!empty(old('prices', $offer_information->prices)))
            @foreach (old('prices', $offer_information->prices) as $k => $v)
                <tr>
                    <td valign="middle" class="text-center"><span class="number">{{ $v }}</span></td>
                    <td>
                        @if (!empty(old('addresses.'.$k, $offer_information->addresses[$k])))
                            @foreach (old('addresses.'.$k, $offer_information->addresses[$k]) as $type => $val_list)
                                @if (!empty($val_list))
                                    @foreach ($val_list as $val)
                                        <p>{{ $val }}</p>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    </table>

    </form>
@stop

@section('js')
    <script type="text/javascript">
        $(".select2").select2({
            language: "zh-CN"
        });

        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m-d'
        });

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
        });
    </script>
@stop