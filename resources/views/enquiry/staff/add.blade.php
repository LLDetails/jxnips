@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">发布询价单</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('title'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">标题</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="title" value="{{ old('title') }}" placeholder="标题">
                                </div>
                                @if ($errors->has('title'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('title') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('goods_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">品种</label>
                                <div class="col-sm-6">
                                    <select name="goods_id" class="form-control select2">
                                        <option value="">请选择询价品种</option>
                                        @foreach ($goods_items as $item)
                                            <option @if(old('goods_id') == $item->id) selected @endif value="{{ $item->id }}">[{{ $item->code }}] {{ $item->name }}</option>
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
                                        <input type="text" class="form-control input-sm" name="quantity" value="{{ old('quantity') }}" placeholder="采购数量">
                                        <div class="input-group-addon">吨</div>
                                    </div>
                                </div>
                                @if ($errors->has('quantity'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('quantity') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('sailing_date'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">船期</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="sailing_date" value="{{ old('sailing_date') }}" placeholder="船期">
                                </div>
                                @if ($errors->has('sailing_date'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('sailing_date') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('terms_of_delivery'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">地点&方式</label>
                                <div class="col-sm-6">
                                    <textarea placeholder="交货地点及方式" class="form-control" style="height:50px" name="terms_of_delivery">{{ old('terms_of_delivery') }}</textarea>
                                </div>
                                @if ($errors->has('terms_of_delivery'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('terms_of_delivery') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('quality'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">质量要求</label>
                                <div class="col-sm-6">
                                    <textarea placeholder="质量要求" class="form-control" name="quality" >{{ old('quality') }}</textarea>
                                </div>
                                @if ($errors->has('quality'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('quality') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('start_at'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">询价开始</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm date" name="start_at" value="{{ old('start_at') }}" placeholder="询价开始">
                                </div>
                                @if ($errors->has('start_at'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('start_at') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('stop_at'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">询价截止</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm date" name="stop_at" value="{{ old('stop_at') }}" placeholder="询价截止">
                                </div>
                                @if ($errors->has('stop_at'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('stop_at') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-plus-circle"></span> 发布询价单</button>
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
    $('input.date').datetimepicker({
        lang: 'ch',
        format:'Y-m-d H:i:00'
    });
</script>
@stop