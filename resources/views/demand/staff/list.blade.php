@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>

    <style type="text/css">
        i.alpha-number {
            font-size: 12px;
            font-family: "Arial", sans-serif;
            font-weight: bold;
            color: #222222;
            font-style: normal;
        }
    </style>
@stop

@section('main')
    <table id="form-table" class="list table table-fixed-header table-striped table-bordered table-hover">
        <thead class="header">
            <tr class="info">
                <th colspan="12">
                    {{ $basket->name }} - 采购需求
                    {!! IPSHelper::showButton(['permission'=>'demand.staff.append', 'type'=>'frame', 'title'=>'追加需求', 'src'=>route('demand.staff.append', ['basket' => $basket->id]), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-left:30px;', 'text'=>'追加']) !!}
                    <a style="margin-left: 10px;" href="{{ URL::full() }}" class="btn btn-default btn-xs">重新加载</a>
                </th>
            </tr>
            <tr>
                <th class="text-center">操作</th>
                <th class="text-center">物料名称</th>
                <th class="text-center">采购数量</th>
                <th class="text-center">当前库存</th>
                <th class="text-center">在途&未执行</th>
                <th class="text-center">本月需求量</th>
                <th class="text-center">可用天数</th>
                <th class="text-center">报价区间</th>
                <th class="text-center">分配方案</th>
                <th class="text-center">报价有效期</th>
                <th class="text-center">交货日期</th>
            </tr>
        </thead>
        @foreach ($demands as $key=>$demand)
            <?php
                $goods = json_decode($demand->goods_static);
                $assign_rule = json_decode($demand->assign_rule);
                $assign_rule_data = $assign_rule->rules;
                $assign_rule_data = json_decode($assign_rule_data, true);
                $assign_rule_data = implode('%；', $assign_rule_data).'%';
            ?>
            <tr class="data-row">
                <td class="text-center">
                    {!! IPSHelper::showButton(['permission'=>'demand.staff.edit', 'type'=>'frame', 'title'=>'编辑需求', 'src'=>route('demand.staff.edit', ['demand' => $demand->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                    {!! IPSHelper::showButton(['permission'=>'demand.staff.delete', 'confirm'=>'确定要删除此条需求吗？', 'type'=>'link', 'href'=>route('demand.staff.delete', ['demand' => $demand->id]), 'css'=>'btn btn-danger btn-xs', 'text'=>'删除']) !!}
                </td>
                <td>
                    <span >{{ $goods->name }}</span>
                </td>
                <td class="text-center">
                    <span><i class="alpha-number">{{ strval((float)$demand->quantity) }}</i> {{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span><i class="alpha-number">{{ strval((float)$demand->stock) }}</i> {{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span><i class="alpha-number">{{ strval((float)$demand->pending) }}</i> {{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span><i class="alpha-number">{{ strval((float)$demand->monthly_demand) }}</i> {{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span><i class="alpha-number">{{ intval(($demand->stock+$demand->pending+$demand->quantity)/($demand->monthly_demand/30)) }}</i> 天</span>
                </td>
                <td class="text-center">
                    <span><i class="alpha-number">{{ strval((float)$demand->price_floor) }} <span class="ion-ios-more"></span> {{ strval((float)$demand->price_caps) }}</i> 元/{{ $goods->unit }}</span>
                </td>
                <td class="text-center">
                    <span >{{ $assign_rule->name }}
                        【{{$assign_rule_data}}】</span>
                </td>
                <td class="text-center">
                    <span >招投标结束后{{ $demand->price_validity }}小时</span>
                </td>
                <td class="text-center">
                    <span >{{ $demand->delivery_date_start }} <span class="ion-ios-more"></span> {{ $demand->delivery_date_stop }}</span>
                </td>
            </tr>
        @endforeach
    </table>

    <div class="text-left" id="submit">
        @if (!empty($next_role))
            <select class="select2">
                <option value="{{ $next_role->id }}">下一级审核：{{ $next_role->name }}</option>
            </select>
        @endif
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('tr.data-row').click(function() {
                if ($(this).hasClass('warning')) {
                    $(this).removeClass('warning');
                } else {
                    $(this).addClass('warning');
                }
            });
            $(".select2").select2({
                language: "zh-CN"
            });
        });
    </script>
@stop