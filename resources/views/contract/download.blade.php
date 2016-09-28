
<style type="text/css">


    .paper * {
        margin:0; padding:0
    }

    .paper h1 {
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    .paper table {margin:0 auto; width:100%; border-collapse:collapse;border:none;}
    .paper table.bordered td, .paper table.bordered th {border:solid #000 1px;}
    .paper table.padded td, .paper table.padded th {padding: 0px 4px 0px 4px;}

    .paper h2 {
        text-align: justify;
        font-size: 14px;
        font-weight: normal;
    }
</style>

<?php
$company = $contract->offer->demand->company;
$offer = $contract->offer;
?>
<div class="paper">
    <h1>{{ $contract->title }}<br /></h1>
    <table>
        <tr>
            <td colspan="4" align="right">合同编号：{{ $contract->code }}</td>
        </tr>
        <tr>
            <td width="50" align="right">签订时间：</td>
            <td width="150">{{ date('Y年m月d日', strtotime($contract->created_at)) }}</td>
            <td align="right">签订地点：</td>
            <td>{!! !empty($data[0]) ? $data[0] : '深圳市' !!}</td>
        </tr>
        <tr>
            <td width="50" align="right">买方：</td>
            <td>{{ $company->name }}</td>
            <td align="right">卖方：</td>
            <td>{{ $offer->supplier->supplier->name }}</td>
        </tr>
        <tr>
            <td width="50" align="right">地址：</td>
            <td>{{ $company->delivery_address }}</td>
            <td align="right">地址：</td>
            <td>{{ $offer->supplier->supplier->address }}</td>
        </tr>
        <tr>
            <td width="50" align="right">电话：</td>
            <td>{{ $company->contract_tel }}</td>
            <td align="right">电话：</td>
            <td>{{ isset(json_decode($offer->supplier->supplier->tel,true)[0])?json_decode($offer->supplier->supplier->tel,true)[0]:'' }}</td>
        </tr>
        <tr>
            <td width="50" align="right">传真：</td>
            <td>{{ $company->contract_fax }}</td>
            <td align="right">传真：</td>
            <td>{{ $offer->supplier->supplier->fax }}</td>
        </tr>
    </table>
    <br /><br />
    <table width="100%">
        <tr>
            <td width="492">&nbsp;&nbsp;&nbsp;&nbsp;买卖双方经平等、自愿协商，根据《中华人民共和国合同法》的有关规定，同意按下列条款签订本合同。</td>
        </tr>
    </table>
    <br /><br />
    <table width="100%">
        <tr>
            <td width="492" colspan="2">一、产品名称、包装、规格、数量、金额</td>
        </tr>
    </table>
    <table class="bordered padded" style="margin-top:5pt; margin-bottom:5pt;">
        <tr>
            <td>商品名称</td>
            <td>包装/规格</td>
            <td>单位</td>
            <td>单价（元/{{ $goods->unit }}）</td>
            <td>数量</td>
            <td>合同金额（元）</td>
        </tr>
        <tr>
            <td align="center">{{ $goods->name }}</td>
            <td align="center">{!! !empty($data[1]) ? $data[1] : '无' !!}</td>
            <td align="center">{{ $goods->unit }}</td>
            <td align="center">{{ strval($offer->price + $offer->delivery_costs) }}</td>
            <td align="center">{{ strval((float)$offer->quantity) }}</td>
            <td align="center">{{ number_format(($offer->price + $offer->delivery_costs) * $offer->quantity) }}</td>
        </tr>
        <tr>
            <td>合计</td>
            <td colspan="5">{{ number_format(($offer->price + $offer->delivery_costs) * $offer->quantity) }} 元（大写：{{ $upper_total_price }}）</td>
        </tr>
        <tr>
            <td colspan="6">包装物不计重，不计价，不返还，每件包装物扣重{!! !empty($data[2]) ? $data[2] : '' !!}</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">二、质量要求、技术标准、供方对质量负责的条件和期限：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">{!! !empty($data[3]) ? $data[3] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">无论何种原因导致所交货物的质量达不到合同规定的质量标准，买方可以拒绝接收货物，买方不承担所带来的损失，包括但不限于货物的运输费、保险费，仓储费。</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">三、交货或提货时间：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">{!! !empty($data[4]) ? $data[4] : '' !!}卖方无论因何原因延迟交货且买方没有选择终止合同的情况下，卖方都应该承担延期交货的责任，买方有权按照实际延期交货的天数，以{!! !empty($data[5]) ? $data[5] : '元/天·吨' !!}向卖方主张因延期交货所带来的责任。</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">四、交（提）货地点、方式：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">{!! !empty($data[6]) ? $data[6] : '' !!}</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">五、运输：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">①运输方式和费用承担方式：{!! !empty($data[7]) ? $data[7] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">②货物保险的办理及费用承担：{!! !empty($data[8]) ? $data[8] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">③风险承担：货物损毁灭失的风险，在货物交付买方之前由卖方承担，交付买方之后由买方承担，交付以买方签收并实际占有为准。货交第三方承运的不视为实际占有。</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">六、验收：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">①合理损耗和计算方法：{!! !empty($data[9]) ? $data[9] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">②交货重量：{!! !empty($data[10]) ? $data[10] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">③验收标准、方法及提出异议期限：{!! !empty($data[11]) ? $data[11] : '' !!}</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">七、结算支付：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">①{!! !empty($data[12]) ? $data[12] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">②付款方式：{!! !empty($data[13]) ? $data[13] : '' !!}</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">③其他：{!! !empty($data[14]) ? $data[14] : '无' !!}</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">八、违约责任：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">①卖方逾期交货或未按约定的质量交货的，买方有权拒收货物、解除合同，由此产生的损失及费用由卖方承担。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">②卖方逾期交货，而买方仍同意收货的：在交货时遇货物市场价格上涨的，买方仍按本合同约定价格结算，在交货时遇货物市场价格下降的，买方按交货时的市场价格结算，或另以补充合同签订确定。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">③卖方未按约定的质量交货的，而买方仍同意收货的：买方有权选择按照卖方交货产品质量结算或在没有给买方带来经济损失的前提下要求卖方在指定期限内交换货；买方正确使用卖方产品而给买方造成损失的，由卖方承担赔偿责任，包括但不限于使用卖方产品生产成品不合格的责任、买方不能正常向自己的客户交付产品的责任。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">④卖方未按约定重量交货的，买方有权选择按实际交货重量结算或要求卖方在指定期限内按照约定重量交货，若卖方不同意，买方有权解除合同，由此带来的损失由卖方自行承担。</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">九、不可抗拒力：由于战争、地震、水灾、火灾、暴风雪或其他不可抗力原因而不能履行合同的一方不负有违约责任，但应于不可抗因素消除后5日内向对方提交相关证明文件。卖方未能与承运人达成运输协议而无法按时交货的，不视为不可抗拒力，不能免除违约责任，仍然要承担延期交货及{!! !empty($data[15]) ? $data[15] : '元/天·吨' !!}的违约责任。</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">十、争议的处理：因本合同履行过程中出现的任何争议，应本着友好协商的原则解决，如协商不成，由签约地所在的人民法院仲裁。</td>
        </tr>
    </table>
    <br/><br/>
    <table width="100%">
        <tr>
            <td width="492" colspan="2">十一、其他约定：</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">①本合同的约定条款是买卖双方自愿协商的结果，其任何条款并不构成任一方的格式条款。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">②若超过合同约定有限期限未执行完毕，余下未执行部分必须双方再确认后，方能生效。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">③合同中价格约定后，无特殊情况双方均不得提出调价。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">④本合同在约定有效期限内有效，逾期作废。</td>
        </tr>
        <tr>
            <td width="20"></td>
            <td width="468">⑤其他：{!! !empty($data[16]) ? $data[16] : '无' !!}</td>
        </tr>
    </table>
    <br /><br />
    <table width="100%">
        <tr>
            <td width="492" colspan="2">十二、本合同一经签订，双方此前的就买卖货物所签署的任何形式的文件、传真、电子数据自动失效，一切以本合同为准。本合同一式肆份，买卖双方各执贰份，具有同等法律效力。合同以传真方式签署同样有效。本合同自双方签字盖章后方能生效。</td>
        </tr>
    </table>
    <br /><br />
    <br /><br />
    <table>
        <tr>
            <td style="line-height: 26px;" width="30" align="right">买方：</td>
            <td style="line-height: 26px;" width="240">{{ $company->name }}</td>
            <td style="line-height: 26px;" width="50" align="right">卖方：</td>
            <td style="line-height: 26px;" >{{ $offer->supplier->supplier->name }}</td>
        </tr>
        <tr>
            <td style="line-height: 26px;" align="right">代表：</td>
            <td style="line-height: 26px;">{{ $company->contract_contact }}</td>
            <td style="line-height: 26px;" align="right">代表：</td>
            <td style="line-height: 26px;">{{ $offer->supplier->supplier->contact }}</td>
        </tr>
        <tr>
            <td style="line-height: 26px;" align="right">盖章：</td>
            <td style="line-height: 26px;"></td>
            <td style="line-height: 26px;" align="right">盖章：</td>
            <td style="line-height: 26px;"></td>
        </tr>
        <tr>
            <td style="line-height: 26px;" colspan="4">合同生效日期：{!! !empty($data[17]) ? $data[17] : date('Y年m月d日') !!}</td>
        </tr>
    </table>
</div>