@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>
@endsection

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">给供应商发送短信</strong>
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
                                <label class="col-sm-2 control-label">物料品种</label>
                                <div class="col-sm-6">
                                    <select data-url="{{ route('sms.send.supplier', ['sms'=>$sms->id]) }}" class="form-control input-sm select2" name="goods_id">
                                        <option value="">所有物料</option>
                                        @foreach($goods as $goods_item)
                                            <option @if(Input::get('goods_id') == $goods_item->id) selected @endif value="{{ $goods_item->id }}">{{ $goods_item->name }} [{{ $goods_item->code }}]</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('goods_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('goods_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('phones'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">供应商名单</label>
                                <div class="col-sm-6">
                                    <select multiple class="form-control input-sm select2" name="phones[]">
                                        <option value="-1">无</option>
                                        <option value="">所有供应商</option>
                                        @foreach($suppliers as $user)
                                            <option @if(in_array($user->phone, old('phones', []))) selected @endif value="{{ $user->phone }}">{{ $user->supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('phones'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('phones') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('ext_phones'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">指定号码</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" style="height: 80px" name="ext_phones" placeholder="指定手机号码">{{ old('ext_phones') }}</textarea>
                                    <p>号码之间用英文逗号隔开,如:1888888888,1888888887,1888888886</p>
                                </div>
                                @if ($errors->has('ext_phones'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('ext_phones') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('txt'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">内容</label>
                                <div class="col-sm-6">
                                    <textarea readonly class="form-control" style="height: 200px;" placeholder="短信内容">{{ old('txt', $sms->txt) }}</textarea>
                                </div>
                                @if ($errors->has('txt'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('txt') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-plane"></span> 发送</button>
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
        $(document).ready(function() {
            $(".select2").select2({
                language: "zh-CN"
            });

            $('select[name="goods_id"]').change(function() {
                var redirect_url = $(this).attr('data-url') + '?goods_id='+$(this).val();
                window.location.href = redirect_url;
            });
        });
    </script>
@stop