@extends('layout.frame')

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">流标条件</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('num'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-addon">报价数量小于</div>
                                        <input type="text" class="form-control input-sm" name="num" value="{{ old('num', $offer_min_num) }}" placeholder="">
                                        <div class="input-group-addon">则自动流标</div>
                                    </div>
                                </div>
                                @if ($errors->has('num'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('num') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-plus-circle"></span> 保存条件</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop