@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('search')
    <form class="form-inline" method="get" action="{{ route('demand.check.index') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">开始日期</div>
                <input type="text" style="width: 140px" class="form-control input-sm date" name="date_start" value="{{ Input::get('date_start') }}" placeholder="开始日期">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-addon">截止日期</div>
                <input type="text" style="width: 140px" class="form-control input-sm date" name="date_stop" value="{{ Input::get('date_stop') }}" placeholder="截止日期">
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查找</button>
        <a href="{{ route('demand.check.index') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
    </form>
@stop

@section('main')
    <table style="margin-top: 10px;" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>日期</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($baskets) > 0)
            @foreach ($baskets as $k => $basket)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        <?php
                            $check_log = $basket->logs();
                            $check_log = $check_log->where('role_id', $current_user->role->id);
                            if (!empty($current_user->company_id)) {
                                $check_log = $check_log->whereHas('user', function($query) use ($current_user) {
                                    return $query->where('role_id', $current_user->role_id)->where('company_id', $current_user->company_id);
                                });
                            }
                            if (!empty($current_user->area_id)) {
                                $check_log = $check_log->whereHas('user', function($query) use ($current_user) {
                                    return $query->where('role_id', $current_user->role_id)->where('area_id', $current_user->area_id);
                                });
                            }
                            $check_log = $check_log->where('action', 'pass')->first();
                        ?>
                        @if (empty($check_log) and $basket->name == $today and $current_time >= $check_start_time and $current_time < $check_stop_time)
                            {!! IPSHelper::showButton(['permission'=>'demand.check.action', 'type'=>'frame', 'title'=>'审核采购需求', 'src'=>route('demand.check.action', ['basket' => $basket->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'审核']) !!}
                        @endif
                        {!! IPSHelper::showButton(['permission'=>'demand.staff.view', 'type'=>'frame', 'title'=>'查看采购需求', 'src'=>route('demand.staff.view', ['basket' => $basket->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                    </td>
                    <td>{{ $basket->name }}</td>
                    <?php
                        if ($basket->state == 'bided') {
                            if ($basket->bided_at > date('Y-m-d H:i:s')) {
                                $state = '汇总审核';
                            } else {
                                $state = $display_states[$basket->state];
                            }
                        } else {
                            $state = $display_states[$basket->state];
                        }
                    ?>
                    <td>{{ $state }}</td>
                    <td>{{ $basket->created_at }}</td>
                    <td>{{ $basket->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">没有找到记录</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection

@section('js')
    <script type="text/javascript">
        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m-d'
        });
    </script>
@stop