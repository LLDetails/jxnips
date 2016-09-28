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
            @if ($errors->has('display'))
                has-error
            @endif
                form-group-sm">
            <label class="col-sm-2 control-label">显示名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control input-sm" name="display" value="{{ old('display', isset($addition->display) ? $addition->display : '') }}" placeholder="显示名称">
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
                <input type="text" class="form-control input-sm" name="name" value="{{ old('name', isset($addition->name) ? $addition->name : '') }}" placeholder="显示名称">
            </div>
            @if ($errors->has('name'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
            @endif
        </div>
        <div class="form-group
            @if ($errors->has('size'))
                has-error
            @endif
                form-group-sm">
            <label class="col-sm-2 control-label">单个文件大小</label>
            <div class="col-sm-6">
                <div class="input-group input-group-sm">
                    <input type="text" value="{{ old('size', isset($addition->size) ? $addition->size : '') }}" name="size" class="form-control" placeholder="单个文件大小">
                    <span class="input-group-addon">KB</span>
                </div>
            </div>
            @if ($errors->has('size'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('size') }}</p>
            @endif
        </div>
        <div class="form-group
            @if ($errors->has('filetype'))
                has-error
            @endif
                form-group-sm">
            <label class="col-sm-2 control-label">文件类型</label>
            <div class="col-sm-6">
                <label class="radio">
                    <input
                    @if (old('filetype', isset($addition->filetype) ? $addition->filetype : '') == 'image')
                        checked="checked"
                        @endif
                        type="radio" name="filetype" value="image">
                    图像文件（jpg、png、gif）
                </label>
                <label class="radio">
                    <input
                    @if (old('filetype', isset($addition->filetype) ? $addition->filetype : '') == 'document')
                        checked="checked"
                        @endif
                        type="radio" name="filetype" value="document">
                    文档文件（doc、docx、rtf、pdf、wps）
                </label>
                <label class="radio">
                    <input
                            @if (old('filetype', isset($addition->filetype) ? $addition->filetype : '') == 'mixed')
                            checked="checked"
                            @endif
                            type="radio" name="filetype" value="mixed">
                    混合（jpg、png、gif、doc、docx、rtf、pdf、wps）
                </label>
            </div>
            @if ($errors->has('filetype'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('filetype') }}</p>
            @endif
        </div>
        <div class="form-group
            @if ($errors->has('count'))
                has-error
            @endif
                form-group-sm">
            <label class="col-sm-2 control-label">文件个数</label>
            <div class="col-sm-6">
                <input type="text" class="form-control input-sm" name="count" value="{{ old('count', isset($addition->count) ? $addition->count : '') }}" placeholder="文件个数">
            </div>
            @if ($errors->has('count'))
                <p class="col-sm-4 control-label form-msg">{{ $errors->first('count') }}</p>
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
                    @if (old('required', isset($addition->required) ? $addition->required : '') != 'false')
                           checked="checked"
                           @endif
                           type="radio" value="true">
                    是
                </label>
                <label class="radio-inline" style="margin-left:10px">
                    <input name="required"
                    @if (old('required', isset($addition->required) ? $addition->required : '') == 'false')
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
                <input type="text" class="form-control input-sm" name="prompt" value="{{ old('prompt', isset($addition->prompt) ? $addition->prompt : '') }}" placeholder="字段提示">
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