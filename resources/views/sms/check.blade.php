@extends('layout.frame')

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">审核短信模板</strong>
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
                                    <input type="text" class="form-control input-sm" name="title" value="{{ old('title', $sms->title) }}" placeholder="模板标题">
                                </div>
                                @if ($errors->has('title'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('title') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('txt'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">内容</label>
                                <div class="col-sm-6">
                                    <textarea name="txt" class="form-control" style="height: 200px;" placeholder="短信内容">{{ old('txt', $sms->txt) }}</textarea>
                                </div>
                                @if ($errors->has('txt'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('txt') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('ali_code'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">授权ID</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="ali_code" value="{{ old('ali_code', $sms->ali_code) }}" placeholder="授权ID">
                                </div>
                                @if ($errors->has('ali_code'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('ali_code') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-plus-circle"></span> 审核授权</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop