<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <table border="1">
            <tr>
                <th>序号</th>
                <th>发布标书日期</th>
                <th>报价日期</th>
                <th>物料名称</th>
                <th>需求单位</th>
                <th>供应商</th>
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
                        <td>{{ $offer->supplier->supplier->name }}</td>
                        @if (!empty($offer->reason))
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        @else
                            @if ($offer->quantity > 0)
                                <td>{{ number_format(strval((float)($offer->price + $offer->delivery_costs))) }}</td>
                                <td>{{ strval((float)$offer->quantity) }}</td>
                                <td>{{ number_format(strval((float)(($offer->price + $offer->delivery_costs)*$offer->quantity))) }}</td>
                            @else
                                <td>{{ number_format(strval((float)($offer->price + $offer->delivery_costs))) }}</td>
                                <td>0</td>
                                <td>0</td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="8">没有数据</td>
                </tr>
            @endif
        </table>
    </body>
</html>