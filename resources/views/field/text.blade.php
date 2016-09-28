@extends($layout)

@section('form')
<div style="width: 600px;">
    <div class="form-group
        @if ($errors->has('tpl'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">类型</label>
        <div class="col-sm-6">
            <select id="template" class="form-control input-sm" name="tpl">
                @foreach ($templates as $k => $template)
                    <option
                        @if ($tpl == $k)
                            selected="selected"
                        @endif
                        value="{{ $k }}">{{ $template['name'] }}</option>
                @endforeach
            </select>
        </div>
        @if ($errors->has('tpl'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('tpl') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('widget'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">类型</label>
        <div class="col-sm-6">
            <label class="radio-inline">
                <input
                    @if (old('widget', 'text') == 'text')
                        checked="checked"
                    @endif
                    type="radio" name="widget" value="text"> 文本框
            </label>
            <label class="radio-inline">
                <input
                    @if (old('widget') == 'password')
                        checked="checked"
                    @endif
                    type="radio" name="widget" value="password"> 密码框
            </label>
        </div>
        @if ($errors->has('widget'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('widget') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('display'))
            has-error
        @endif
        form-group-sm">
        <label class="col-sm-2 control-label">显示名称</label>
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" name="display" value="{{ old('display') }}" placeholder="显示名称">
        </div>
        @if ($errors->has('display'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('display') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('name'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">属性标识</label>
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}" placeholder="显示名称">
        </div>
        @if ($errors->has('name'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('size_min') or $errors->has('size_max'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">长度</label>
        <div class="col-sm-6">
            <div class="input-group input-group-sm">
                <input name="size_min" value="{{ old('size_min') }}" type="text" class="form-control">
                <span class="input-group-addon"><span class="ion-code-working"></span></span>
                <input name="size_max" value="{{ old('size_max') }}" type="text" class="form-control">
            </div>
        </div>
        @if ($errors->has('size_min') or $errors->has('size_max'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('size_min').'；'. $errors->first('size_max')}}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('default'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">默认值</label>
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" name="default" value="{{ old('default') }}" placeholder="默认值">
        </div>
        @if ($errors->has('default'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('default') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('required'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">是否必填</label>
        <div class="col-sm-6">
            <label class="radio-inline" style="margin-left:10px">
                <input name="required"
                    @if (old('required') != 'false')
                       checked="checked"
                    @endif
                    type="radio" value="true">
                是
            </label>
            <label class="radio-inline" style="margin-left:10px">
                <input name="required"
                @if (old('required') == 'false')
                    checked="checked"
                @endif
                   type="radio" value="false">
                否
            </label>
        </div>
        @if ($errors->has('required'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('required') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('rule'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">验证规则</label>
        <div class="col-sm-6">
            <label class="radio-inline" style="margin-left:10px">
                <input
                    @if (old('rule', '*') == '*')
                        checked="checked"
                    @endif
                    type="radio" name="rule" value="*">
                任意格式
            </label>
            <label class="radio-inline" style="margin-left:10px">
                <input
                    @if (old('rule') == 'phone')
                        checked="checked"
                    @endif
                    type="radio" name="rule" value="phone">
                电话号码
            </label>
            <label class="radio-inline">
                <input
                    @if (old('rule') == 'email')
                        checked="checked"
                    @endif
                    type="radio" name="rule" value="email">
                邮件地址
            </label>
            <label class="radio-inline">
                <input
                    @if (old('rule') == 'date')
                        checked="checked"
                    @endif
                    type="radio" name="rule" value="date">
                日期格式
            </label>
            <label class="radio-inline">
                <input
                    @if (old('rule') == 'numeric')
                        checked="checked"
                    @endif
                    type="radio" name="rule" value="numeric">
                数字
            </label>
            <label class="radio-inline">
                <input
                    @if (old('rule') == 'integer')
                        checked="checked"
                    @endif
                    type="radio" name="rule" value="integer">
                整数
            </label>
        </div>
        @if ($errors->has('rule'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('rule') }}</p>
        @endif
    </div>
    <div class="form-group
        @if ($errors->has('prompt'))
            has-error
        @endif
            form-group-sm">
        <label class="col-sm-2 control-label">字段提示</label>
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" name="prompt" value="{{ old('prompt') }}" placeholder="字段提示">
        </div>
        @if ($errors->has('prompt'))
            <p class="col-sm-4 control-label form-msg">{{ $errors->first('prompt') }}</p>
        @endif
    </div>
</div>
@stop

@section('extjs')
    <script type="text/javascript">
        $('#template').change(function() {
            var _$ = top.$;
            _$('.loading-box').hide();
            var tpl = $(this).val();
            var form = $('<form><input type="hidden" name="tpl" value="'+tpl+'" /></form>');
            form.attr('method', 'get');
            form.attr('action', window.location.href);
            form.appendTo($('body'));
            form.submit();
        });
    </script>
@stop