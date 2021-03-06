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
                <select disabled id="template" class="form-control input-sm">
                    @foreach ($templates as $k => $template)
                        <option
                        @if ($tpl == $k)
                            selected="selected"
                            @endif
                            value="{{ $k }}">{{ $template['name'] }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="tpl" value="{{ $tpl }}" />
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
                    @if (old('widget', $addition->widget) == 'radio')
                        checked="checked"
                        @endif
                        type="radio" name="widget" value="radio"> 单选按钮
                </label>
                <label class="radio-inline">
                    <input
                    @if (old('widget', $addition->widget) == 'checkbox')
                        checked="checked"
                        @endif
                        type="radio" name="widget" value="checkbox"> 多选框
                </label>
                <label class="radio-inline">
                    <input
                    @if (old('widget', $addition->widget) == 'select')
                        checked="checked"
                        @endif
                        type="radio" name="widget" value="select"> 下拉列表
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
                <input type="text" class="form-control input-sm" name="display" value="{{ old('display', $addition->display) }}" placeholder="显示名称">
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
                <input type="text" class="form-control input-sm" name="name" value="{{ old('name', $addition->name) }}" placeholder="显示名称">
            </div>
            @if ($errors->has('name'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
            @endif
        </div>
        <div class="form-group
                @if ($errors->has('list'))
                    has-error
                @endif
                form-group-sm">
            <label class="col-sm-2 control-label">选项列表</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="list" style="height:120px;" placeholder="选项列表">{{ old('list', $addition->list) }}</textarea>
            </div>
            @if ($errors->has('list'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('list') }}</p>
            @endif
        </div>
        <div class="form-group
                @if ($errors->has('other'))
                    has-error
                @endif
                form-group-sm">
            <label class="col-sm-2 control-label">允许其他</label>
            <div class="col-sm-6">
                <label class="radio-inline" style="margin-left:10px">
                    <input name="other"
                    @if (old('other', $addition->other) == 'true')
                           checked="checked"
                           @endif
                           type="radio" value="true">
                    是
                </label>
                <label class="radio-inline" style="margin-left:10px">
                    <input name="other"
                    @if (old('other', $addition->other) != 'true')
                           checked="checked"
                           @endif
                           type="radio" value="false">
                    否
                </label>
            </div>
            @if ($errors->has('other'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('other') }}</p>
            @endif
        </div>
        <div class="form-group
        @if ($errors->has('default'))
            has-error
        @endif
                form-group-sm">
            <label class="col-sm-2 control-label">默认值</label>
            <div class="col-sm-6">
                <input type="text" class="form-control input-sm" name="default" value="{{ old('default', $addition->default) }}" placeholder="默认值">
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
                    @if (old('required', $addition->required) != 'false')
                           checked="checked"
                           @endif
                           type="radio" value="true">
                    是
                </label>
                <label class="radio-inline" style="margin-left:10px">
                    <input name="required"
                    @if (old('required', $addition->required) == 'false')
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
        @if ($errors->has('prompt'))
            has-error
        @endif
                form-group-sm">
            <label class="col-sm-2 control-label">字段提示</label>
            <div class="col-sm-6">
                <input type="text" class="form-control input-sm" name="prompt" value="{{ old('prompt', $addition->prompt) }}" placeholder="字段提示">
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