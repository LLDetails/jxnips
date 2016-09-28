@extends('layout.frame')

@section('search')
    <form class="form-inline" method="get" action="{{ route('user.staff.manage') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">招标品种占比基数</div>
                <input id="base-number" type="text" style="width: 140px" class="form-control input-sm">
            </div>
        </div>
        <button id="base-eval" type="button" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 计算</button>
    </form>
@endsection

@section('main')
    <table class="list table table-striped table-bordered table-hover" style="margin-top:10px;">
        <tr>
            <th>统计日期</th>
            <th>试运行开始日期</th>
            <th>运行天数（天）</th>
            <th>发布标书数量（次）</th>
            <th>流标次数（次）</th>
            <th>招标品种（个）</th>
            <th>招标品种占比</th>
            <th>成交数量（吨）</th>
            <th>成交金额（万元）</th>
            <th>中标供应商（个）</th>
            <th>参与报价供应商（个）</th>
        </tr>
        @if (count($bid_counts) > 0)
            @foreach ($bid_counts as $k => $item)
                <tr>
                    <td>{{ $item->generated_at }}</td>
                    <td>2015-10-15</td>
                    <td>{{ $item->days }}</td>
                    <td>{{ $item->bid_counts }}</td>
                    <td>{{ $item->failed_bid_counts }}</td>
                    <td>{{ $item->goods_counts }}</td>
                    <td style="color:red" data-count="{{ $item->goods_counts }}" class="rate">—</td>
                    <td>{{ strval((float)$item->quantity) }}</td>
                    <td>{{ strval((float)($item->amount/10000)) }}</td>
                    <td>{{ $item->supplier_counts }}</td>
                    <td>{{ $item->offer_counts }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="11">没有记录</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#base-eval').click(function() {
                var n = $('#base-number').val();
                n = Number(n);
                if (isNaN(n)) {
                    alert('请填写数字');
                    return false;
                } else {
                    n = Math.abs(n);
                }

                var rates = $('.rate');
                for (var i = 0; i < rates.length; i += 1) {
                    var rateElement = $(rates.get(i));
                    var count = rateElement.attr('data-count');
                    count = Number(count);

                    var rate = (count / n) * 100;
                    rate = rate.toFixed(0);
                    rateElement.html(rate+'%');
                }
            });
        });
    </script>
@stop