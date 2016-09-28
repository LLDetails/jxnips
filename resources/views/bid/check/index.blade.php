@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('main')
    <table style="margin-top: 10px;" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>日期</th>
            <th>状态</th>
            <th>审核到期</th>
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
                            $check_log->where('collected_at', $basket->collected_at);
                            $check_log = $check_log->where('user_id', $current_user->id);
                            $check_log = $check_log->first();
                        ?>
                        @if (empty($check_log) and $basket->bided_at > $datetime)
                            {!! IPSHelper::showButton(['permission'=>'bid.check.view', 'type'=>'frame', 'title'=>'标书审核', 'src'=>route('bid.check.view', ['basket' => $basket->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'审核']) !!}
                        @else
                            {!! IPSHelper::showButton(['permission'=>'bid.check.view', 'type'=>'frame', 'title'=>'标书审核', 'src'=>route('bid.check.view', ['basket' => $basket->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
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
                    <td>{{ $basket->bided_at }}</td>
                    <td>{{ $basket->created_at }}</td>
                    <td>{{ $basket->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="7">没有找到记录</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection
