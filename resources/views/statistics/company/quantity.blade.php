@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>
@endsection

@section('search')
    <form class="form-inline" method="get" action="{{ URL::full() }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">月份</div>
                <input type="text" style="width: 140px" class="form-control input-sm date" name="date" value="{{ Input::get('date') }}" placeholder="起始月份">
                <div class="input-group-addon">往后6个月</div>
            </div>
            <select name="goods_id" class="form-control input-sm select2">
                <option value="">请选择物料</option>
                @foreach ($supply_goods_data as $goods_item)
                    <option @if(Input::get('goods_id') == $goods_item->id) selected @endif value="{{ $goods_item->id }}">{{ $goods_item->name }} [{{ $goods_item->code }}]</option>
                @endforeach
            </select>
            <div class="input-group input-group-sm">
                <select name="type" class="form-control input-sm">
                    <option value="">请选择查询数据</option>
                    @foreach ($company_data_types as $k=>$type)
                        <option @if(Input::get('type') == $k) selected @endif value="{{ $k }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查询</button>
    </form>
@endsection

@section('main')
    <table style="margin-top: 10px;" class="list table table-striped table-bordered table-hover">
        <tr>
            <td style="color: blue;" colspan="4">{{ $date_start }} - {{ $date_stop }}, 总成交量 {{ strval((float)$total_quantity) }}</td>
        </tr>
        <tr>
            <th>#</th>
            <th>采购方</th>
            <th>成交量</th>
            <th>占比</th>
        </tr>
        @foreach ($data as $k=>$item)
        <tr>
            <td>{{ $k+1 }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ strval((float)$item->sum_quantity) }}</td>
            <td>{{ sprintf('%.2f', ($item->sum_quantity/$total_quantity)*100) }} %</td>
        </tr>
        @endforeach
    </table>
@stop


@section('js')
    <script type="text/javascript">
        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m'
        });
        $(document).ready(function() {
            $(".select2").select2({
                language: "zh-CN"
            });
        });
    </script>
@stop