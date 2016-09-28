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
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($baskets) > 0)
            @foreach ($baskets as $k => $basket)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        @if ($basket->state != 'refused')
                            @if ($basket->state == 'bided' or $basket->state == 'cancelled')
                                {!! IPSHelper::showButton(['permission'=>'bid.demand.view', 'type'=>'frame', 'title'=>'查看需求汇总', 'src'=>route('bid.demand.view', ['basket' => $basket->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看汇总']) !!}
                            @else
                                {!! IPSHelper::showButton(['permission'=>'bid.demand.collect', 'type'=>'frame', 'title'=>'采购需求汇总', 'src'=>route('bid.demand.collect', ['basket' => $basket->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'需求汇总']) !!}
                            @endif
                        @else
                            @if ($basket->bided_at < date('Y-m-d H:i:s'))
                                {!! IPSHelper::showButton(['permission'=>'bid.demand.edit', 'type'=>'frame', 'title'=>'采购需求汇总', 'src'=>route('bid.demand.edit', ['basket' => $basket->id]), 'css'=>'btn btn-danger btn-xs', 'text'=>'重新汇总']) !!}
                            @else
                                {!! IPSHelper::showButton(['permission'=>'bid.demand.view', 'type'=>'frame', 'title'=>'查看需求汇总', 'src'=>route('bid.demand.view', ['basket' => $basket->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看汇总']) !!}
                            @endif
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
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">没有找到记录</td>
            </tr>
        @endif
    </table>
@stop

