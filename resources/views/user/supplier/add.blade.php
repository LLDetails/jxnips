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
                                @if ($errors->has('type'))
                                    has-error
                                @endif
                                    form-group-sm">
                                <label class="col-sm-2 control-label">供应商类型</label>
                                <div class="col-sm-6">
                                    <select id="supplier-type" data-url="{{ route('user.supplier.add') }}" class="form-control input-sm select2" name="type">
                                        <option value="">请选择供应商类型</option>
                                        @foreach ($supplier_types as $k=>$type)
                                            <option @if(old('type', Input::get('type')) == $k) selected @endif value="{{ $k }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('type'))
                                    <p class="col-sm-4 control-label form-msg">{{ $errors->first('type') }}</p>
                                @endif
                            </div>
                            @if ( ! empty($supplier_type))
                                <div class="form-group
                                    @if ($errors->has('name'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">供应商名称</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="name" value="{{ old('name') }}" placeholder="供应商名称">
                                    </div>
                                    @if ($errors->has('name'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                @if ($supplier_type == '企业法人' or $supplier_type == '个体户')
                                    <div class="form-group
                                        @if ($errors->has('business_license'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">营业执照号</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="business_license" value="{{ old('business_license') }}" placeholder="营业执照号">
                                        </div>
                                        @if ($errors->has('business_license'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('business_license') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group
                                        @if ($errors->has('tax_id'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">税务登记号</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="tax_id" value="{{ old('tax_id') }}" placeholder="税务登记号">
                                        </div>
                                        @if ($errors->has('tax_id'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('tax_id') }}</p>
                                        @endif
                                    </div>
                                @endif
                                @if ($supplier_type == '企业法人')
                                    <div class="form-group
                                        @if ($errors->has('organization_code'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">组织机构代码</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="organization_code" value="{{ old('organization_code') }}" placeholder="组织机构代码">
                                        </div>
                                        @if ($errors->has('organization_code'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('organization_code') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group
                                        @if ($errors->has('registered_capital'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">注册资本</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="registered_capital" value="{{ old('registered_capital') }}" placeholder="注册资本">
                                        </div>
                                        @if ($errors->has('registered_capital'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('registered_capital') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group
                                        @if ($errors->has('company_scale'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">企业规模</label>
                                        <div class="col-sm-6">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control input-sm" name="company_scale" value="{{ old('company_scale') }}" placeholder="企业规模">
                                                <span class="input-group-addon">人</span>
                                            </div>
                                        </div>
                                        @if ($errors->has('company_scale'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('company_scale') }}</p>
                                        @endif
                                    </div>
                                @endif
                                @if ($supplier_type == '自然人')
                                    <div class="form-group
                                        @if ($errors->has('id_number'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">身份证号</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-sm" name="id_number" value="{{ old('id_number') }}" placeholder="身份证号">
                                        </div>
                                        @if ($errors->has('id_number'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('id_number') }}</p>
                                        @endif
                                    </div>
                                @endif
                                <div class="form-group
                                    @if ($errors->has('goods'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">供应商品</label>
                                    <div class="col-sm-6">
                                        <select name="goods[]" class="form-control input-sm select2" multiple="multiple">
                                            <option value="">请选择</option>
                                            @foreach ($categories as $category)
                                                <optgroup label="[{{ $category->code }}] {{ $category->name }}">
                                                    @foreach ($category->goods_records as $goods_item)
                                                        <option @if(in_array($goods_item->id, old('goods', []))) selected @endif value="{{ $goods_item->id }}">[{{ $goods_item->code }}] {{ $goods_item->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('goods'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('goods') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('username'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">用户帐号</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="username" value="{{ old('username') }}" placeholder="登录帐号">
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
                                        <input type="text" class="form-control input-sm" name="password" value="{{ old('password') }}" placeholder="登录密码">
                                    </div>
                                    @if ($errors->has('password'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>
                                @if ( ! empty($areas))
                                    <div class="form-group
                                        @if ($errors->has('area_id'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">所属地区</label>
                                        <div class="col-sm-6">
                                            <select class="form-control input-sm select2" name="area_id">
                                                <option value="">请选择地区</option>
                                                @foreach ($areas as $area)
                                                    <option @if(old('area_id') == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('area_id'))
                                            <p class="col-sm-4 control-label form-msg">{{ $errors->first('area_id') }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endif
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
                        @if ( ! empty($supplier_type))
                            <div>
                                <div class="form-group
                                    @if ($errors->has('bank'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">开户银行</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="bank" value="{{ old('bank') }}" placeholder="开户银行">
                                    </div>
                                    @if ($errors->has('bank'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('bank') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('bank_account'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">银行帐号</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="bank_account" value="{{ old('bank_account') }}" placeholder="银行帐号">
                                    </div>
                                    @if ($errors->has('bank_account'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('bank_account') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('contact'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">联系人</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="contact" value="{{ old('contact') }}" placeholder="联系人">
                                    </div>
                                    @if ($errors->has('contact'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('contact') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('tel'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">联系电话</label>
                                    <div class="col-sm-6">
                                        <textarea name="tel" class="form-control" rows="3" placeholder="每行填写一个联系电话，可填写多行">{{ old('tel') }}</textarea>
                                    </div>
                                    @if ($errors->has('tel'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('tel') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('zipcode'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">邮编</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="zipcode" value="{{ old('zipcode') }}" placeholder="邮编">
                                    </div>
                                    @if ($errors->has('zipcode'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('zipcode') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('address'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">地址</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="address" value="{{ old('address') }}" placeholder="地址">
                                    </div>
                                    @if ($errors->has('address'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('address') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('fax'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">传真</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="fax" value="{{ old('fax') }}" placeholder="传真">
                                    </div>
                                    @if ($errors->has('fax'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('fax') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('email'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">电子邮箱</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="email" value="{{ old('email') }}" placeholder="电子邮箱">
                                    </div>
                                    @if ($errors->has('email'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>
                                <div class="form-group
                                    @if ($errors->has('website'))
                                        has-error
                                    @endif
                                        form-group-sm">
                                    <label class="col-sm-2 control-label">网站</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-sm" name="website" value="{{ old('website') }}" placeholder="网站">
                                    </div>
                                    @if ($errors->has('website'))
                                        <p class="col-sm-4 control-label form-msg">{{ $errors->first('website') }}</p>
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
                                        <button class="btn btn-primary btn-xs normal-load-btn" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在提交..." type="submit"><span class="ion-person-add"></span> 新建供应商</button>
                                    </div>
                                </div>
                            </div>
                        @endif
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
        $('#supplier-type').change(function() {
            var url = $(this).attr('data-url');
            window.location.href = url + '?type=' + $(this).val();
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