@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('main')
    <form class="form-horizontal">
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
                            <div class="form-group form-group-sm">
                                <label class="col-sm-2 control-label">供应商类型</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">{{ $supplier_types[$supplier_type] }}</p>
                                </div>
                            </div>
                            @if ( ! empty($supplier_type))
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">供应商名称</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->name }}</p>
                                    </div>
                                </div>
                                @if ($supplier_type == '企业法人' or $supplier_type == '个体户')
                                    <div class="form-group form-group-sm">
                                        <label class="col-sm-2 control-label">营业执照号</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $user->supplier->business_license }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label class="col-sm-2 control-label">税务登记号</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $user->supplier->tax_id }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($supplier_type == '企业法人')
                                    <div class="form-group form-group-sm">
                                        <label class="col-sm-2 control-label">组织机构代码</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $user->supplier->organization_code }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label class="col-sm-2 control-label">注册资本</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $user->supplier->registered_capital }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group
                                        @if ($errors->has('company_scale'))
                                            has-error
                                        @endif
                                            form-group-sm">
                                        <label class="col-sm-2 control-label">企业规模</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $user->supplier->company_scale }} 人</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($supplier_type == '自然人')
                                    <div class="form-group form-group-sm">
                                        <label class="col-sm-2 control-label">身份证号</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">{{ $user->supplier->id_number }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">供应商品</label>
                                    <div class="col-sm-6">
                                        <?php
                                            $supply_goods = $user->supplier->goods;
                                            $supply_goods = json_decode($supply_goods);
                                            try {
                                                $supply_goods_name = \App\Goods::whereIn('id', $supply_goods)->where('is_available', true)->whereNull('deleted_at')->lists('name');
                                                if ( ! empty($supply_goods_name)) {
                                                    $supply_goods_name = $supply_goods_name->toArray();
                                                } else {
                                                    $supply_goods_name = [];
                                                }
                                            } catch (\Exception $e) {
                                                $supply_goods_name = [];
                                            }
                                        ?>
                                        <p class="form-control-static">{{ implode('；', $supply_goods_name) }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">用户帐号</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->username }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">所属地区</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->area->name }}</p>
                                    </div>
                                </div>
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
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">开户银行</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->bank }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">银行帐号</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->bank_account }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">联系人</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->contact }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">联系电话</label>
                                    <div class="col-sm-6">
                                        <?php
                                            $tel = $user->supplier->tel;
                                            $tel = json_decode($tel);
                                            $tel = implode("\r\n", $tel);
                                            $tel = trim($tel);
                                            $tel = str_replace("\r\n", ' | ', $tel);
                                        ?>
                                        <p class="form-control-static">{{ $tel }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">邮编</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->zipcode }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">地址</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->address }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">传真</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->fax }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">电子邮箱</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->email }}</p>
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="col-sm-2 control-label">网站</label>
                                    <div class="col-sm-6">
                                        <p class="form-control-static">{{ $user->supplier->website }}</p>
                                    </div>
                                </div>
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('js')
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
            window.location.href = url + '?supplier_type=' + $(this).val();
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
@endsection