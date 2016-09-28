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
                        <?php
                        $offer = $bid->offers()->where('user_id', $current_user->id)->first();
                        ?>
                        @if (!empty($offer))
                            {!! IPSHelper::showButton(['permission'=>'bid.supplier.offer', 'type'=>'frame', 'title'=>'标书报价', 'src'=>route('bid.supplier.offer', ['bid' => $bid->id]), 'css'=>'btn btn-warning btn-xs', 'text'=>'改价']) !!}
                        @else
                            {!! IPSHelper::showButton(['permission'=>'bid.supplier.offer', 'type'=>'frame', 'title'=>'标书报价', 'src'=>route('bid.supplier.offer', ['bid' => $bid->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'报价']) !!}
                        @endif
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
