@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>


    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js') }}"></script>
    <style type="text/css">
        .number {
            font-size:18px;
            font-weight: bold;
            font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }
    </style>
@stop

@section('search')
    <form class="form-inline" method="get" action="{{ route('statistics.supplier') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">时间（起）</div>
                <input name="date_start" value="{{ Input::get('date_start') }}" type="text" style="width: 140px" class="form-control input-sm date">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">时间（止）</div>
                <input name="date_stop" value="{{ Input::get('date_stop') }}" type="text" style="width: 140px" class="form-control input-sm date">
            </div>
        </div>
        <select name="goods_id" class="form-control input-sm select2">
            <option value="">全部物料</option>
            @foreach ($goods as $goods_item)
                <option @if(Input::get('goods_id') == $goods_item->id) selected @endif value="{{ $goods_item->id }}">{{ $goods_item->name }}</option>
            @endforeach
        </select>
        <select name="supplier_id" class="form-control input-sm select2">
            <option value="">全部供应商</option>
            @foreach ($suppliers as $supplier)
                <option @if(Input::get('supplier_id') == $supplier->user_id) selected @endif value="{{ $supplier->user_id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
        <button id="base-eval" type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 筛选</button>
        <a href="{{ route('statistics.supplier') }}" class="btn btn-info btn-sm">显示全部</a>

    </form>
@endsection

@section('main')
    @if (!empty(Input::get('goods_id')) and !empty(Input::get('supplier_id')))
        <table class="list table table-striped table-bordered table-hover" style="margin-top:10px;">
            @if ($invite_count > 0)
                <tr>
                    <th width="200">交易参与次数比率（%）</th>
                    <td class="number">{{ sprintf('%.2f', ($join_count/$invite_count)*100) }}</td>
                    <td class="number">({{ $join_count }} ÷ {{ $invite_count }}) × 100%</td>
                    <td>(参与次数 ÷ 收到报价邀请次数) × 100%</td>
                </tr>

                <tr>
                    <th>报价次数比率（%）</th>
                    <td class="number">{{ sprintf('%.2f', ($offer_count/$invite_count)*100) }}</td>
                    <td class="number">({{ $offer_count }} ÷ {{ $invite_count }}) × 100%</td>
                    <td>(报价次数 ÷ 收到报价邀请次数) × 100%</td>
                </tr>
                <tr>
                    <th>成交次数比率（%）</th>
                    <td class="number">{{ sprintf('%.2f', ($deal_count/$invite_count)*100) }}</td>
                    <td class="number">({{ $deal_count }} ÷ {{ $invite_count }}) × 100%</td>
                    <td>(成交次数 ÷ 收到报价邀请次数) × 100%</td>
                </tr>
            @endif
            @if ($total_deal_quantity > 0)
                <tr>
                    <th>成交数量比率（%）</th>
                    <td class="number">{{ sprintf('%.2f', ($deal_quantity/$total_deal_quantity)*100) }}</td>
                    <td class="number">({{ strval((float)$deal_quantity) }} ÷ {{ strval((float)$total_deal_quantity) }}) × 100%</td>
                    <td>(成交数量 ÷ 总成交数量) × 100%</td>
                </tr>
            @endif
            @if ($deal_quantity > 0)
                <tr>
                    <th>成交均价（元/吨）</th>
                    <td class="number">{{ sprintf('%.2f', ($deal_amount/$deal_quantity)) }}</td>
                    <td class="number">{{ strval((float)$deal_amount) }} ÷ {{ strval((float)$deal_quantity) }}</td>
                    <td>成交金额 ÷ 成交数量</td>
                </tr>
            @endif
        </table>
    @endif
@stop

@section('js')
    <script type="text/javascript">
        $(".select2").select2({
            language: "zh-CN"
        });

        $('input.date').datepicker({
            language: "zh-CN",
            format: "yyyy-mm-dd",
            autoclose: true
        });
    </script>
@stop