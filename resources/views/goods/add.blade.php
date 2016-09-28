@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('main')
<form id="add-goods-form" class="form-horizontal" method="post" action="{{ URL::full() }}">
    {!! csrf_field() !!}
    <div class="row">
        <div class="col-sm-6 col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="height: 46px;">
                    <h3 class="panel-title">
                        <strong class="pull-left" style="margin-top:6px;">添加商品</strong>
                        <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                    </h3>
                </div>
                <div class="panel-body">
                    <div>
                        @if ($errors->has('form'))
                        <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                        @endif
                        <div class="form-group
                                @if ($errors->has('category_id'))
                                    has-error
                                @endif
                            form-group-sm">
                            <label class="col-sm-2 control-label">所属分类</label>
                            <div class="col-sm-6">
                                <select data-url="{{ route('goods.add') }}" class="form-control input-sm select2" name="category_id">
                                    <option value="">请选择所属分类</option>
                                    @foreach ($categories as $category)
                                        <option @if(old('category_id', Input::get('category_id')) == $category->id) selected @endif value="{{ $category->id }}">[{{ $category->code }}] {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('category_id'))
                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('category_id') }}</p>
                            @endif
                        </div>
                        @if (!empty($current_category))
                        <div class="form-group
                            @if ($errors->has('name'))
                                has-error
                            @endif
                                form-group-sm">
                            <label class="col-sm-2 control-label">商品名称</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}" placeholder="商品名称">
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
                            <label class="col-sm-2 control-label">合同代号</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control input-sm" name="code" value="{{ old('code') }}" placeholder="合同代号">
                            </div>
                            @if ($errors->has('code'))
                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('code') }}</p>
                            @endif
                        </div>
                        <div class="form-group
                                @if ($errors->has('unit'))
                                    has-error
                                @endif
                                    form-group-sm">
                            <label class="col-sm-2 control-label">计量单位</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control input-sm" name="unit" value="{{ old('unit', '吨') }}" placeholder="计量单位">
                            </div>
                            @if ($errors->has('unit'))
                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('unit') }}</p>
                            @endif
                        </div>
                        <div class="form-group
                            @if ($errors->has('price_validity'))
                                has-error
                            @endif
                                form-group-sm">
                            <label class="col-sm-2 control-label">报价有效期</label>
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-addon">招投标结束后</div>
                                    <input type="text" class="form-control input-sm" name="price_validity" value="{{ old('price_validity', '24') }}" placeholder="报价有效期">
                                    <div class="input-group-addon">小时</div>
                                </div>
                            </div>
                            @if ($errors->has('price_validity'))
                                <p class="col-sm-4 control-label form-msg">{{ $errors->first('price_validity') }}</p>
                            @endif
                        </div>
                        <div class="form-group
                            @if ($errors->has('quality_standard'))
                                has-error
                            @endif
                                form-group-sm">
                            <label class="col-sm-2 control-label">质量标准</label>
                            <div class="col-sm-6">
                                <textarea name="quality_standard" class="form-control" rows="3" placeholder="质量描述">{{ old('quality_standard') }}</textarea>
                            </div>
                            @if ($errors->has('quality_standard'))
                                <p class="col-sm-4 control-label form-msg">{{ $errors->first('quality_standard') }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($current_category))
        <div class="col-md-6 col-xs-6 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="height: 46px;">
                    <h3 class="panel-title">
                        <strong class="pull-left" style="margin-top:6px;">其他信息</strong>
                        <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                    </h3>
                </div>
                <div class="panel-body">
                    <div>
                        @foreach ($addition as $field)
                            <div class="form-group
                                    @if ($errors->has($field->name))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">{{ $field->display }}</label>
                                <div class="col-sm-6">
                                    @if ($field->tpl == 'select')
                                        @include('field.widget.'.$field->tpl.'.'.$field->widget, ['list'=> array_map('trim', explode("\n", $field->list)), 'value' => ''])
                                    @elseif($field->tpl == 'text')
                                        @include('field.widget.'.$field->tpl.'.'.$field->widget, ['value' => ''])
                                    @elseif ($field->tpl == 'file')
                                        @include('field.widget.file.file', ['value' => ''])
                                    @else
                                        @include('field.widget.textarea.textarea', ['value' => ''])
                                    @endif
                                </div>
                                @if ($errors->has($field->name))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first($field->name) }}</p>
                                @endif
                            </div>
                        @endforeach
                        <div class="form-group">
                            <label class="col-sm-2 control-label"> </label>
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-plus-circle"></span> 添加商品</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
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
        $('select[name="category_id"]').change(function() {
            var url = $(this).attr('data-url');
            window.location.href = url + '?category_id=' + $(this).val();
        });
    });
</script>
<script type="text/javascript">
    $(".select2").select2({
        language: "zh-CN"
    });
    $('input.date').datetimepicker({
        lang: 'ch',
        format:'Y-m-d'
    });
</script>

<script type="text/javascript">
    function uploadify(uploader) {
        var id = uploader.attr('id');
        var filetype = uploader.attr('data-filetype');
        var ext = uploader.attr('data-file-ext');
        var count = uploader.attr('data-count');
        var buttonText = '<span class="fa fa-folder-open-o"></span> 选择文件';
        var fileSizeLimit = uploader.attr('data-size');
        $('#'+id).uploadify({
                'swf'      : '{{ asset('asset/uploadify/uploadify.swf') }}',
            'uploader' : '{{ route('attachment.upload') }}',
            'fileObjName': 'upfile',
            'fileSizeLimit' : fileSizeLimit + 'KB',
            'fileTypeDesc' : uploader.attr('data-display'),
            'fileTypeExts' : ext,
            'buttonText'   : buttonText,
            'onSelect': function(file) {
            $('#'+id).uploadify('disable', true);
            $('#'+id).find('.uploadify-button').html('<span class="fa fa-spinner fa-spin"></span> 上传中 <strong>0%</strong>');
            $('#'+id).uploadify('settings','formData', {
                'ssid'      : '{{ Crypt::encrypt(auth()->user()->id) }}',
                'timestamp' : '<?php echo $timestamp = time();?>',
                'token'     : '<?php echo md5('tpc_salt' . $timestamp);?>',
                '_token'    : '{{ csrf_token() }}',
                'filetype'  : uploader.attr('data-filetype'),
                'filesize'  : fileSizeLimit * 1024
            });
        },
        'onSelectError' : function(file, errorCode, errorMsg) {
            if (errorCode==-110) {
                this.queueData.errorMsg = '文件大小超出'+parseFloat(fileSizeLimit/1024).toFixed(2)+'MB';
                //alert('文件大小超出'+parseFloat(fileSizeLimit/1024).toFixed(2)+'MB');
            } else {
                this.queueData.errorMsg = '发生错误['+errorCode+']: '+errorMsg;
                //alert('发生错误['+errorCode+']: '+errorMsg);
            }
            $('#'+id).uploadify('disable', false);
            $('#'+id).find('.uploadify-button').html(buttonText);
        },
        'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
            $('#'+id).find('.uploadify-button strong').html(parseInt(bytesUploaded*100/bytesTotal) + '%');
        },
        'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('上传失败['+errorCode+']: '+ errorMsg +'('+ errorString+')');
            $('#'+id).uploadify('disable', false);
            $('#'+id).find('.uploadify-button').html(buttonText);
        },
        'onUploadSuccess' : function(file, data, response) {
            data = eval('('+data+')');
            if (data.state == 'error') {
                alert(data.msg);
            } else {
                if (filetype == 'image') {
                    var icon = '<span class="fa fa-picture-o"></span> ';
                } else {
                    var icon = '<span class="fa fa-file-text-o"></span> ';
                }
                var wrapper = $('#'+id).parent();
                var file = $('<p class="bg-default file" style="padding: 0 10px; line-height: 30px; border:1px dashed #DCDCDC">'+icon+data.filename+'<button style="margin-top: 3px" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>');
                var input = $('<input type="hidden" value="'+data.attachment_id+'/'+data.filename+'" name="'+uploader.attr('data-name')+'[]" />');
                input.appendTo(file);
                file.find('button.close').click(function() {
                    if (wrapper.find('p.file').length - 1 <= count) {
                        $('#'+id).show();
                    }
                    $(this).parent().remove();
                });

                file.appendTo(wrapper);
                if (wrapper.find('p.file').length >= count) {
                    $('#'+id).hide();
                }
            }
            $('#'+id).uploadify('disable', false);
            $('#'+id).find('.uploadify-button').html(buttonText);
        }
    });
    }

    function displayUploadBtn(uploader) {
        var id = uploader.attr('id');
        var wrapper = $('#'+id).parent();
        var count = uploader.attr('data-count');
        if (wrapper.find('p.file').length >= count) {
            $('#'+id).hide();
        }
    }

    $(document).ready(function() {

        $('input.datepick').datetimepicker({
            lang: 'ch',
            format:'Y-m-d'
        });

        var fileUploader = $('.file-upload');
        for (var i = 0; i < fileUploader.length; i = i + 1) {
            var uploader = $(fileUploader.get(i));
            uploadify(uploader);
            displayUploadBtn(uploader);
        }

        $('.file').find('button.close').click(function() {
            var count = $(this).attr('data-count');
            var id = $(this).attr('data-id');
            var wrapper = $('#'+id).parent();
            if (wrapper.find('p.file').length - 1 <= count) {
                $('#'+id).show();
            }
            $(this).parent().remove();
        });

        $('input[type="radio"].other').click(function() {
            var name = $(this).attr('name');
            if ( !! name) {
                $(this).attr('name', '');
                var input = $('<input type="text" class="form-control input-sm other-value" name="' + name + '" />');
                $(this).parent().parent().parent().append(input);
            }
        });
        $('input[type="radio"].except-other').click(function() {
            var name = $(this).attr('name');
            $(this).parents('.form-group').find('input.other-value').remove();
            $(this).parents('.form-group').find('input[type="radio"].other').attr('name', name).prop('checked', false);
            $(this).prop('checked', true);
        });

        $('input[type="checkbox"].other').click(function() {
            var name = $(this).attr('name');
            if ( !! name) {
                $(this).attr('name', '');
                var input = $('<input type="text" class="form-control input-sm other-value" name="' + name + '" />');
                $(this).parent().parent().parent().append(input);
            } else {
                name = $(this).attr('data-name');
                $(this).attr('name', name);
                $(this).parents('.form-group').find('input.other-value').remove();
            }
        });

        $('select.other').change(function() {
            var value = $(this).val();
            var name = $(this).attr('data-name');
            if (value == '[other]') {
                $(this).attr('name', '');
                var input = $('<input style="margin-top:5px;" type="text" class="form-control input-sm other-value" name="' + name + '" />');
                $(this).parent().append(input);
            } else {
                $(this).attr('name', name);
                $(this).parents('.form-group').find('input.other-value').remove();
            }
        });
    });
</script>
@stop