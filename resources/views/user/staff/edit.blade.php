@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/uploadify/uploadify.css') }}">
@endsection

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-md-6 col-xs-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="height: 46px;">
                        <h3 class="panel-title">
                            <strong class="pull-left" style="margin-top:6px;">基本信息</strong>
                            <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div>
                            @if ($errors->has('form'))
                                <p style="color:#f50000"><span class="fa fa-info-circle"></span> {{ $errors->first('form') }}</p>
                            @endif
                            <div class="form-group
                                @if ($errors->has('username'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">用户帐号</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="username" value="{{ old('username', $user->username) }}" placeholder="登录帐号">
                                </div>
                                @if ($errors->has('username'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('username') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('password'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">登录密码</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="password" value="" placeholder="登录密码；若不更改密码，请留空">
                                </div>
                                @if ($errors->has('password'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('role_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">所属角色</label>
                                <div class="col-sm-6">
                                    <select @if(auth()->user()->role->name != '超级管理员') disabled @endif class="form-control input-sm" name="role_id">
                                        <option value="">请选择所属角色</option>
                                        @foreach ($roles as $role)
                                            <option @if(old('role_id', $user->role_id) == $role->id) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(auth()->user()->role->name != '超级管理员')
                                        <input type="hidden" name="role_id" value="{{ $user->role_id }}" />
                                    @endif
                                </div>
                                @if ($errors->has('role_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('role_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('company_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">分公司</label>
                                <div class="col-sm-6">
                                    <select @if(auth()->user()->role->name != '超级管理员') disabled @endif class="form-control input-sm select2" name="company_id">
                                        <option value="">不属于任何分公司</option>
                                        @foreach ($companies as $company)
                                            <option @if(old('company_id', $user->company_id) == $company->id) selected @endif value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(auth()->user()->role->name != '超级管理员')
                                        <input type="hidden" name="company_id" value="{{ $user->company_id }}" />
                                    @endif
                                </div>
                                @if ($errors->has('company_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('company_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('area_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">所属地区</label>
                                <div class="col-sm-6">
                                    <select @if(auth()->user()->role->name != '超级管理员') disabled @endif class="form-control input-sm select2" name="area_id">
                                        <option value="">不属于任何地区</option>
                                        @foreach ($areas as $area)
                                            <option @if(old('area_id', $user->area_id) == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(auth()->user()->role->name != '超级管理员')
                                        <input type="hidden" name="area_id" value="{{ $user->area_id }}" />
                                    @endif
                                </div>
                                @if ($errors->has('area_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('area_id') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('category_id'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">管理品类</label>
                                <div class="col-sm-6">
                                    <select @if(auth()->user()->role->name != '超级管理员') disabled @endif class="form-control input-sm select2" name="category_id">
                                        <option value="">管理所有品类</option>
                                        @foreach ($categories as $category)
                                            <option @if(old('category_id', $user->category_id) == $category->id) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(auth()->user()->role->name != '超级管理员')
                                        <input type="hidden" name="area_id" value="{{ $user->area_id }}" />
                                    @endif
                                </div>
                                @if ($errors->has('category_id'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('category_id') }}</p>
                                @endif
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
                            <div class="form-group
                                @if ($errors->has('realname'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">真实姓名</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-sm" name="realname" value="{{ old('realname', $user->staff->realname) }}" placeholder="真实姓名">
                                </div>
                                @if ($errors->has('realname'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('realname') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('hiredate'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">入职时间</label>
                                <div class="col-sm-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control input-sm date" name="hiredate" value="{{ old('hiredate', $user->staff->hiredate) }}" placeholder="入职时间">
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                </div>
                                @if ($errors->has('hiredate'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('hiredate') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('is_regular'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">入职方式</label>
                                <div class="col-sm-6">
                                    <select class="form-control input-sm" name="is_regular">
                                        <option value="">请选择入职方式</option>
                                        <option @if(old('is_regular', $user->staff->regular ? 'true' : 'false') == 'true') selected @endif value="true">正式聘用</option>
                                        <option @if(old('is_regular', $user->staff->regular ? 'true' : 'false') == 'false') selected @endif value="false">临时工</option>
                                    </select>
                                </div>
                                @if ($errors->has('is_regular'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('is_regular') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('address'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">联系地址</label>
                                <div class="col-sm-6">
                                    <textarea name="address" class="form-control" rows="3" placeholder="请填写联系地址">{{ old('address', $user->staff->address) }}</textarea>
                                </div>
                                @if ($errors->has('address'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('address') }}</p>
                                @endif
                            </div>
                            <div class="form-group
                                @if ($errors->has('phone'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">联系电话</label>
                                <div class="col-sm-6">
                                    <?php
                                        $phone = $user->staff->phone;
                                        if (!empty($phone)) {
                                            $phone = json_decode($phone);
                                            $phone = implode("\r\n", $phone);
                                            $phone = trim($phone);
                                        } else {
                                            $phone = '';
                                        }
                                    ?>
                                    <textarea name="phone" class="form-control" rows="3" placeholder="每行填写一个联系电话，可填写多行">{{ old('phone', $phone) }}</textarea>
                                </div>
                                @if ($errors->has('phone'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('phone') }}</p>
                                @endif
                            </div>
                            @foreach ($addition as $field)
                                <div class="form-group
                                    @if ($errors->has($field->name))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">{{ $field->display }}</label>
                                    <div class="col-sm-6">
                                        <?php
                                            $name = $field->name;
                                            $value = empty($addition_data->$name) ? '' : $addition_data->$name;
                                        ?>
                                        @if ($field->tpl == 'select')
                                            @include('field.widget.'.$field->tpl.'.'.$field->widget, ['list'=> array_map('trim', explode("\n", $field->list)), 'value' => $value])
                                        @elseif($field->tpl == 'text')
                                            @include('field.widget.'.$field->tpl.'.'.$field->widget, ['value' => $value])
                                        @elseif ($field->tpl == 'file')
                                            @include('field.widget.file.file', ['value' => $value])
                                        @else
                                            @include('field.widget.textarea.textarea', ['value' => $value])
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
                                    <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="fa fa-edit"></span> 编辑用户</button>
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
        $(".select2").select2({
            language: "zh-CN"
        });
        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m-d'
        });
    </script>

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
                        var file = $('<p class="bg-default file" style="padding: 0 10px; line-height: 30px; border:1px dashed #DCDCDC">'+icon+data.filename+' <a target="_blank" href="'+fakeDownloadLink.replace('#',data.attachment_id)+'">下载</a><button style="margin-top: 3px" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>');
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
@endsection