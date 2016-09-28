@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/uploadify/uploadify.css') }}">
@stop

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-sm-6 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">合同附件</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">合同</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static"><b>{{ $contract->title }}</b></p>
                                    <p class="form-control-static"><b>{{ $contract->code }}</b></p>
                                </div>
                            </div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('attachment'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">附件</label>
                                <div class="col-sm-6">
                                    <button @if(!empty($value) and count($value) >= 5)) style="display:none" @endif class="file-upload" id="attachment" data-prompt="请选择附件" data-filetype="mixed" data-file-ext="{{ implode('; ', config('addition.templates.file.type.mixed.ext')) }}" data-size="5120" data-count="5" data-display="附件" data-name="attachment" type="button">选择文件</button>
                                    <?php
                                        $value = old('attachment', $attachments);
                                    ?>
                                    @if ( ! empty($value))
                                        @foreach ($value as $f)
                                            <?php list($id, $filename) = explode('/', $f) ?>
                                            <p class="bg-default file" style="padding: 0 10px; line-height: 30px; border:1px dashed #DCDCDC">
                                                <span class="fa fa-picture-o"></span> {{ $filename }}
                                                <button data-id="attachment" data-count="5" style="margin-top: 3px" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <input type="hidden" value="{{ $id.'/'.$filename }}" name="attachment[]" />
                                                <a target="_blank" href="{{ route('attachment.download', ['attachment' => $id]) }}">[下载附件]</a>
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                                @if ($errors->has('amount'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('attachment') }}</p>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> </label>
                                <div class="col-sm-6">
                                    @if ($contract->state == 'pending')
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在保存..." type="submit"><span class="fa fa-save"></span> 保存附件</button>
                                    {!! IPSHelper::showButton(['permission'=>'contract.company.confirm', 'type'=>'link', 'css'=>'btn btn-xs btn-warning', 'confirm'=>'要确认该合同吗？确认后无法再修改。', 'href'=>route('contract.company.confirm', ['contract' => $contract->id]), 'text'=>'确认合同']) !!}
                                    @endif
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
    <script type="text/javascript" src="{{ asset('asset/vendor/uploadify/jquery.uploadify.min.js?t='.time()) }}"></script>
    <script type="text/javascript">

        var fakeDownloadLink = '{{ route('attachment.download', ['attachment' => '#']) }}';

        function uploadify(uploader) {
            var id = uploader.attr('id');
            var filetype = uploader.attr('data-filetype');
            var ext = uploader.attr('data-file-ext');
            var count = uploader.attr('data-count');
            var buttonText = '<span class="fa fa-folder-open-o"></span> 选择文件';
            var fileSizeLimit = uploader.attr('data-size');
            $('#'+id).uploadify({
                'swf'      : '{{ asset('asset/vendor/uploadify/uploadify.swf') }}',
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
                        var download = $('<a target="_blank" href="'+fakeDownloadLink.replace('#',data.attachment_id)+'">[下载附件]</a>')
                        input.appendTo(file);
                        download.appendTo(file);
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
        });
    </script>
@stop