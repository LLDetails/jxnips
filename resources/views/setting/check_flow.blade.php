@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js') }}"></script>
@endsection

@section('main')
    <div style="display: none" id="tool-box">
        <div class="first">
            <select class="form-control input-sm ws">
                <option value="">请选择</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <div class="input-group input-group-sm">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                </span>
                <input type="text" readonly class="form-control wst form_datetime" placeholder="自动通过时间(分钟)">
                <span class="input-group-btn">
                    <button class="btn btn-info remove-step" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                </span>
            </div>
        </div>
        <div class="normal">
            <p class="text-center"><span class="ion-arrow-down-c"></span></p>
            <p class="text-center"><button type="button" class="btn btn-sm btn-primary insert-step"><span class="glyphicon glyphicon-plus"></span></button></p>
            <p class="text-center"><span class="ion-arrow-down-c"></span></p>
            <select class="form-control input-sm ws">
                <option value="">请选择</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <div class="input-group input-group-sm">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                </span>
                <input type="text" readonly class="form-control wst form_datetime" placeholder="自动通过时间(分钟)">
                <span class="input-group-btn">
                    <button class="btn btn-info remove-step" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                </span>
            </div>
        </div>
    </div>

    <form class="form-inline" id="goods-workflow-form" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="row">
            <div class="col-md-6 col-xs-6 col-sm-12">
                <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading">采购需求审核流程</div>
                    <div class="panel-body">
                        <div class="step-wrapper planning-workflow text-center">
                            <?php $s = 0 ?>
                            @foreach($flow as $k=>$item)
                                @if($s != 0)
                                    <div class="first">
                                        <p class="text-center"><span class="ion-arrow-down-c"></span></p>
                                        <p class="text-center"><button type="button" class="btn btn-sm btn-primary insert-step"><span class="glyphicon glyphicon-plus"></span></button></p>
                                        <p class="text-center"><span class="ion-arrow-down-c"></span></p>
                                        @else
                                            <div class="normal form-group text-center">
                                                <?php $s += 1 ?>
                                                @endif
                                                <select class="form-control input-sm ws">
                                                    <option value="">请选择</option>
                                                    @foreach($roles as $role)
                                                        <option @if($item['role_id'] == $role->id) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group input-group-sm">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-user"></span>
                                        </span>
                                        <input style="color:#f00000" readonly type="text" class="form-control wst text-danger form_datetime" placeholder="自动通过时间(分钟)" value="{{ !empty($item['time'])?$item['time']:'' }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info remove-step" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                                        </span>
                                                </div>
                                            </div>
                                            @endforeach
                                    </div>
                                    <div class="text-center" style="margin-top: 15px">
                                        <button type="button" class="btn btn-primary btn-sm append-step"><span class="glyphicon glyphicon-plus"></span> 追加步骤</button>
                                    </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xs-6 col-sm-12">
                    <div class="panel panel-default">
                        <!-- Default panel contents -->
                        <div class="panel-heading">汇总审核人员名单</div>
                        <div class="panel-body">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">
                                    次日
                                </span>
                                <input type="text" name="member_auto_pass_time" value="{{ old('member_auto_pass_time', $member_auto_pass_time) }}" class="form-control form_datetime" placeholder="自动通过时间(分钟)">
                            </div>
                            <br /><br />
                            <div class="form-group">
                                <select name="member[]" multiple class="form-control input-sm select2">
                                    @foreach ($roles as $role)
                                        <optgroup label="{{ $role->name }}">
                                            @foreach ($role->users as $user)
                                                <option @if(in_array($user->id, old('member', $members))) selected @endif value="{{ $user->id }}">{{ $user->username }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <br /><br />
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-sm" id="save-workflow"><span class="fa fa-save"></span> 保存设置</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {

            $(".form_datetime").datetimepicker({
                format: 'hh:ii:ss',
                startView: 1,
                language: 'zh-CN'
            });

            $(".select2").select2({
                language: "zh-CN"
            });
            $('#goods').change(function() {
                $('#goods-form').submit();
            });

            $('.append-step').click(function() {
                var wrapper = $(this).parents('.panel-body')
                var count = wrapper.find('select.form-control').length;
                var content = null;
                if (count == 0) {
                    content = $('#tool-box > .first').clone(true, true);
                } else {
                    content = $('#tool-box > .normal').clone(true, true);
                }
                wrapper.find('.step-wrapper').append(content);
            });
            $('.insert-step').click(function() {
                var content = $('#tool-box > .normal').clone(true, true);
                var position = $(this).parent().parent();
                content.insertBefore(position);
                return false;
            });

            $('.remove-step').click(function() {
                var step = $(this).parent().parent().parent();
                step.remove();
            });
        });

        $('#save-workflow').click(function() {
            var planning_selects = $('.planning-workflow select.ws');
            for (var i = 0; i < planning_selects.length; i++) {
                var select = $(planning_selects[i]);
                select.attr('name', 'pws[]');
                var time = select.parent().find('input.wst');
                time.attr('name', 'pwst[]');
            }
            $('#goods-workflow-form').submit();
        });
    </script>
@stop