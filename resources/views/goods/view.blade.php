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
                            <strong class="pull-left" style="margin-top:6px;">查看商品</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">所属分类</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{ $goods->category->name }}</p>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">商品名称</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{ $goods->name }}</p>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">合同代号</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{ $goods->code }}</p>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">计量单位</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{ $goods->unit }}</p>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">报价有效期</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">招投标结束后 {{ $goods->price_validity }} 小时</p>
                                </div>
                            </div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">质量标准</label>
                                <div class="col-sm-6">
                                    <textarea readonly class="form-control" rows="3" placeholder="质量标准">{{ old('quality_standard', $goods->quality_standard) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">{{ $field->display }}</label>
                                    <?php
                                    $name = $field->name;
                                    $value = empty($addition_data->$name) ? '' : $addition_data->$name;
                                    ?>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">
                                        @if ($field->tpl == 'select')
                                            {{ is_array($value) ? implode('，', $value) : $value }}
                                        @elseif($field->tpl == 'text')
                                            {{ $value }}
                                        @elseif ($field->tpl == 'file')
                                            @if(!empty($value))
                                                @foreach ($value as $f)
                                                    <?php
                                                    list($fid, $fname) = explode('/', $f);
                                                    ?>
                                                    <p style="line-height: 30px;"><a style="line-height: 30px;" class="fv" data-fid="{{ $fid }}" target="_blank" href="{{ route('attachment.download', ['attachment' => $fid]) }}">{{ $fname }}</a></p>
                                                    @endforeach
                                                    @endif
                                                    @else
                                                    {{ str_replace([' ', "\n", "\r"], ['&nbsp;&nbsp; ', '<br />', ''], $value) }}
                                                    @endif
                                                    </p>
                                    </div>
                                </div>
                            @endforeach

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