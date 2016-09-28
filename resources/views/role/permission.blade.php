@extends('layout.frame')

@section('main')
    <form class="form-horizontal" method="post" action="{{ URL::full() }}">
        {!! csrf_field() !!}
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading" style="height: 46px;">
                <h3 class="panel-title">
                    <strong class="pull-left" style="margin-top: 6px">{{ $role->name }} - 权限绑定</strong>
                    <a href="javascript:void(0)" class="btn btn-default btn-xs pull-right frame-back-link"><span class="fa fa-reply-all"></span> 返回列表</a>
                    <button style="margin-right: 15px" class="btn btn-primary btn-xs normal-load-btn pull-right" data-loading-text="<span class='fa-spin ion-load-c'></span> 正在保存..." type="submit"><span class="fa fa-save"></span> 保存权限设置</button>
                </h3>
            </div>

            <!-- Table -->
            <table class="list table table-bordered table-hover">
                @foreach ($feature_data as $group => $data)
                    <tr>
                        <td width="120">
                            <label class="checkbox-inline">
                                <input class="check-all" type="checkbox" name="rule" value="integer">
                                {{ $group }}
                            </label>
                        </td>
                        <td>
                            @foreach ($data as $k=>$feature)
                                <label class="checkbox-inline" @if($k==0) style="margin-left:10px" @endif>
                                    <input @if(in_array($feature->id, $permissions)) checked @endif type="checkbox" name="feature_id[]" value="{{ $feature->id }}">
                                    {{ $feature->name }}
                                </label>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </form>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.check-all').click(function() {
                if ($(this).prop('checked')) {
                    $(this).parents('tr').find('input[type="checkbox"]').prop('checked', true);
                } else {
                    $(this).parents('tr').find('input[type="checkbox"]').prop('checked', false);
                }
            });
        });
    </script>
@endsection