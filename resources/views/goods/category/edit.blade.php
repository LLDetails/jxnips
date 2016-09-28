@extends('layout.frame')

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">编辑商品分类</strong>
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
                                <label class="col-sm-2 control-label">分类名称</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="name" value="{{ old('name', $category->name) }}" placeholder="分类名称">
                                </div>
                                @if ($errors->has('name'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('code'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">代号</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="code" value="{{ old('code', $category->code) }}" placeholder="代号">
                                </div>
                                @if ($errors->has('code'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('code') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('display_order'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">排序</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="display_order" value="{{ old('display_order', $category->display_order) }}" placeholder="排序">
                                </div>
                                @if ($errors->has('display_order'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('display_order') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在保存..." type="submit"><span class="fa fa-save"></span> 编辑商品分类</button>
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
    <script type="text/javascript" src="{{ asset('asset/vendor/pinyin/pinyin.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[name="name"]').change(function() {
                var val = $.trim($(this).val());
                var py = pinyin.getCamelChars(val);
                $('input[name="code"]').val(py);
            });
        });
    </script>
@endsection