@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('search')
    <form class="form-inline" method="get" action="{{ route('demand.staff.manage') }}">
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
        <a href="{{ route('demand.staff.manage') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
        {!! IPSHelper::showButton(['permission'=>'demand.staff.add', 'type'=>'frame', 'title'=>'发布需求', 'src'=>route('demand.staff.add'), 'css'=>'btn btn-success btn-sm pull-right', 'text'=>'<span class="fa fa-plus-circle"></span> 创建清单']) !!}
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
                @if(($basket->demands()->where('company_id', auth()->user()->company_id)->count() > 0) or ($basket->state == 'pending' and ($basket->name == $tomorrow or ($basket->name == $today and $current_time < $check_time))))
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        @if ($basket->state == 'pending' and ($basket->name == $tomorrow or ($basket->name == $today and $current_time < $check_time)))
                            {!! IPSHelper::showButton(['permission'=>'demand.staff.demand_list', 'type'=>'frame', 'title'=>'编辑需求清单', 'src'=>route('demand.staff.demand_list', ['basket' => $basket->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        @else
                            {!! IPSHelper::showButton(['permission'=>'demand.staff.view', 'type'=>'frame', 'title'=>'查看采购需求', 'src'=>route('demand.staff.view', ['basket' => $basket->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                        @endif
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
                @endif
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