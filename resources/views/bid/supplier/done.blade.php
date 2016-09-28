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
            <th>编号</th>
            <th>物料</th>
            <th>报价开始时间</th>
            <th>报价结束时间</th>
        </tr>
        @if (count($bids) > 0)
            @foreach ($bids as $k => $bid)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        {!! IPSHelper::showButton(['permission'=>'bid.supplier.view', 'type'=>'frame', 'title'=>'查看标书', 'src'=>route('bid.supplier.view', ['bid' => $bid->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                    </td>
                    <td>{{ $bid->code }}</td>
                    <?php $goods = json_decode($bid->goods_static) ?>
                    <td>{{ $goods->name }}</td>
                    <td>{{ $bid->offer_start }}</td>
                    <td>{{ $bid->offer_stop }}</td>
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
