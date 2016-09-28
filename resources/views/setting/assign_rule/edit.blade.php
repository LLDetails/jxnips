@extends('layout.frame')

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">编辑分配规则</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('name'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">规则名</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="name" value="{{ old('name', $rule->name) }}" placeholder="规则名">
                                </div>
                                @if ($errors->has('name'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('rules'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">分配规则</label>
                                <div class="col-sm-6">
                                    <textarea name="rules" class="form-control" rows="3" placeholder="分配规则">{{ old('rules', $rules) }}</textarea>
                                </div>
                                @if ($errors->has('rules'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('rules') }}</p>
                                @endif
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <p style="margin-top: -20px;" class="form-control-static text-info">每行填写一个百分数，第一行表示第一名，第二行表示第二名，以此类推</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在保存..." type="submit"><span class="fa fa-save"></span> 保存分配规则</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop