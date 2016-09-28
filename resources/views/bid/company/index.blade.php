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
            <th>对应需求记录</th>
            <th>物料</th>
            <th>供应商</th>
            <th>供应量</th>
            <th>送货方式</th>
            <th>额外运费</th>
            <th>报价</th>
            <th>合计</th>
            <th>报价时间</th>
        </tr>
        @if (count($offers) > 0)
            @foreach ($offers as $k => $offer)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="180">
                        @if (!empty($offer->contract))
                            @if (!$offer->contract->offline)
                            {!! IPSHelper::showButton(['permission'=>'contract.company.view', 'type'=>'frame', 'title'=>'查看合同', 'src'=>route('contract.company.view', ['contract' => $offer->contract->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看合同']) !!}
                            @else
                            {!! IPSHelper::showButton(['permission'=>'contract.attachment', 'type'=>'frame', 'title'=>'合同附件', 'src'=>route('contract.attachment', ['contract' => $offer->contract->id]), 'css'=>'btn '.($offer->contract->state == 'pending' ? 'btn-success' : 'btn-primary').' btn-xs', 'text'=>'合同附件']) !!}
                            @endif
                        @else
                            {!! IPSHelper::showButton(['permission'=>'bid.company.offer', 'type'=>'frame', 'title'=>'生成合同', 'src'=>route('bid.company.offer', ['offer' => $offer->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'生成合同']) !!}
                            {!! IPSHelper::showButton(['permission'=>'bid.company.upload_contract', 'type'=>'frame', 'title'=>'合同附件', 'confirm'=>"确定要生成附件合同？此操作不可逆！", 'src'=>route('bid.company.upload_contract', ['offer' => $offer->id]), 'css'=>'btn btn-warning btn-xs', 'text'=>'上传合同']) !!}
                        @endif
                    </td>
                    <td>{{ $offer->demand->basket->name }}</td>
                    <?php
                        $goods = json_decode($offer->demand->goods_static);
                    ?>
                    <td>{{ $goods->name }}</td>
                    <td>{{ $offer->supplier->supplier->name }}</td>
                    <td>{{ strval((float)$offer->quantity) }} {{ $goods->unit }}</td>
                    <td>{{ $offer->delivery_mode }}</td>
                    <td>{{ strval((float)$offer->delivery_costs) }} 元/{{ $goods->unit }}</td>
                    <td>{{ strval((float)$offer->price) }} 元/{{ $goods->unit }}</td>
                    <td>{{ strval(($offer->price + $offer->delivery_costs)*$offer->quantity) }} 元</td>
                    <td>{{ $offer->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="11">没有找到记录</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection
