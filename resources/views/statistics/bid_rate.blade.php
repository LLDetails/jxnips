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
    <form class="form-inline" method="get" action="{{ route('statistics.bid_rate') }}">
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
<!-- 增加全部需求单位 20160509 lvze -->
        <select name="company_id" class="form-control input-sm select2">
            <option value="">全部需求单位</option>
            @foreach ($companies as $company)
                <option @if(Input::get('company_id') == $company->id) selected @endif value="{{ $company->id }}">{{ $company->name}}</option>
            @endforeach
        </select>

        <select name="supplier_id" class="form-control input-sm select2">
            <option value="">全部供应商</option>
            @foreach ($suppliers as $supplier)
                <option @if(Input::get('supplier_id') == $supplier->user_id) selected @endif value="{{ $supplier->user_id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
        <button id="base-eval" type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 筛选</button>
        <a href="{{ route('statistics.bid_rate') }}" class="btn btn-info btn-sm">显示全部</a>

        <a target="_blank" href="{{ route('statistics.bid_rate', array_merge(['export' => 'yes'], Input::all())) }}" class="pull-right btn btn-warning btn-sm">导出</a>
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
    <table class="list table table-striped table-bordered table-hover" style="margin-top:10px;">
        <tr>
            <th>序号</th>
            <th>发布标书日期</th>
            <th>报价日期</th>
            <th>物料名称</th>
            <th>需求单位</th>
            <th>参与供应商</th>
            <th>价格（元/吨）</th>
            <th>成交数量（吨）</th>
            <th>成交金额（元）</th>
        </tr>
        @if (count($offers) > 0)
            @foreach ($offers as $k => $offer)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td>{{ explode(' ', $offer->bid->offer_start)[0] }}</td>
                    <td>{{ explode(' ', $offer->updated_at)[0] }}</td>
                    <?php $goods_name = json_decode($offer->bid->goods_static,true)['name'] ?>
                    <td>{{ $goods_name }}</td>
                    <!-- 增加需求单位信息 20160509 lvze -->
                    <td>{{ $offer->demand->company->name }} </td>
                    <td>{{ $offer->supplier->supplier->name }} </td>
                    @if (!empty($offer->reason))
                        <td class="number">0</td>
                        <td class="number">0</td>
                        <td class="number">0</td>
                    @else
                        @if ($offer->quantity > 0)
                            <td class="number">{{ number_format(strval((float)($offer->price + $offer->delivery_costs))) }}</td>
                            <td class="number">{{ strval((float)$offer->quantity) }}</td>
                            <td class="number">{{ number_format(strval((float)(($offer->price + $offer->delivery_costs)*$offer->quantity))) }}</td>
                        @else
                            <td class="number">{{ number_format(strval((float)($offer->price + $offer->delivery_costs))) }}</td>
                            <td class="number">0</td>
                            <td class="number">0</td>
                        @endif
                    @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">没有记录</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection

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