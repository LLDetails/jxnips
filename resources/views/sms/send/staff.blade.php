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
                            <strong class="pull-left" style="margin-top:6px;">给采购方发送短信</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('area_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">所属地区</label>
                                <div class="col-sm-6">
                                    <select data-url="{{ route('sms.send.staff', ['sms'=>$sms->id]) }}" class="form-control input-sm" name="area_id">
                                        <option value="">所有地区</option>
                                        @foreach($areas as $area)
                                            <option @if(Input::get('area_id') == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('area_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('area_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('phones'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">采购方名单</label>
                                <div class="col-sm-6">
                                    <select multiple class="form-control input-sm select2" name="phones[]">
                                        <option value="-1">无</option>
                                        <option value="">所有采购方</option>
                                        @foreach($staffs as $user)
                                            <option @if(in_array($user->phone, old('phones', []))) selected @endif value="{{ $user->phone }}">{{ $user->staff->realname }} [{{ $user->role->name }}]</option>
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

            $('select[name="area_id"]').change(function() {
                var redirect_url = $(this).attr('data-url') + '?area_id='+$(this).val();
                window.location.href = redirect_url;
            });
        });
    </script>
@stop