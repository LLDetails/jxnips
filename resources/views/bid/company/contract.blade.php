<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style type="text/css">
        * {margin:0; padding:0}
        body {
            background: #F6F6F7;
        }

        #ctrl {
            position: fixed;
            z-index: 100;
            width: 100%;
            top: 0;
            left: 0;
            text-align: center;
            height: 40px;
            line-height: 40px;
            font-size:12px;
            background: #000000;
            opacity: 0.7;
        }

        #ctrl button {
            border: 0;
            background: #00B349;
            color:#FFFFFF;
            padding: 3px 5px;
            cursor: pointer;
            margin-left: 20px;
        }
        #ctrl button:hover {
            background: #039444;
        }

        #paper {
            font-family: "宋体",SimSun,STSong,BitmapSong;
            border: 1px solid #CCCCCC;
            -webkit-box-shadow:0 0 10px rgba(204, 204, 204, .5);
            -moz-box-shadow:0 0 10px rgba(204, 204, 204, .5);
            box-shadow:0 0 10px rgba(204, 204, 204, .5);
            width: 17.6cm;
            padding: 2cm 1.7cm 1.5cm 1.7cm;
            margin: 60px auto 20px auto;
            background: #FFFFFF;
            font-size: 10.5pt;
            line-height: 2.0;
        }

        #paper h1 {
            text-align: center;
            font-weight: bold;
            font-size: 18pt;
        }

        #paper table {margin:0 auto; width:100%; border-collapse:collapse;border:none;}
        #paper table.bordered td, #paper table.bordered th {border:solid #000 1px;}
        #paper table.padded td, #paper table.padded th {padding: 0pt 4pt 0pt 4pt;}

        #paper p {text-align: justify}
        #paper p.indent {text-indent: 21pt;}
        #paper p.padding {padding-left:21pt;}

        #paper h2 {
            text-align: justify;
            font-size: 10.5pt;
            font-weight: normal;
        }

        #paper strong.warning {
            color: #F50000;
            font-weight: bolder;
        }

        #paper .editable {
            /*border: 1px dashed #5C74E7;*/
            /*background: #DFF2FC;*/
            color: red;
        }
    </style>
</head>
<body>
<form style="display: none" id="contract-form" method="post" action="{{ URL::full() }}">
    {!! csrf_field() !!}
    <div class="box"></div>
</form>
<div id="ctrl">
    <span style="color:#FFFFFF">合同中的红色文字的内容可以编辑</span>
    <button type="button" id="generate">✓生成合同</button>
</div>
<div id="paper">
    <h1>购销合同<br /><br /></h1>
    <table>
        <tr>
            <td colspan="4" align="right">合同编号：[稍后由系统生成]</td>
        </tr>
        <tr>
            <td width="100">签订时间：</td>
            <td></td>
            <td align="right">签订地点：</td>
            <td><span class="editable" contenteditable="true">深圳市</span></td>
        </tr>
        <tr>
            <td width="60" align="right">买方：</td>
            <td>{{ $company->name }}</td>
            <td align="right">卖方：</td>
            <td>{{ $offer->supplier->supplier->name }}</td>
        </tr>
        <tr>
            <td width="60" align="right">地址：</td>
            <td>{{ $company->delivery_address }}</td>
            <td align="right">地址：</td>
            <td>{{ $offer->supplier->supplier->address }}</td>
        </tr>
        <tr>
            <td width="60" align="right">电话：</td>
            <td>{{ $company->contract_tel }}</td>
            <td align="right">电话：</td>
            <td>{{ isset(json_decode($offer->supplier->supplier->tel,true)[0])?json_decode($offer->supplier->supplier->tel,true)[0]:'' }}</td>
        </tr>
        <tr>
            <td width="60" align="right">传真：</td>
            <td>{{ $company->contract_fax }}</td>
            <td align="right">传真：</td>
            <td>{{ $offer->supplier->supplier->fax }}</td>
        </tr>
    </table>
    <br />
    <p class="indent">买卖双方经平等、自愿协商，根据《中华人民共和国合同法》的有关规定，同意按下列条款签订本合同。</p>
    <br />
    <h2>一、产品名称、包装、规格、数量、金额</h2>
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
            <td align="center"><span class="editable" contenteditable="true">无</span></td>
            <td align="center">{{ $goods->unit }}</td>
            <td align="center">{{ strval($offer->price + $offer->delivery_costs) }}</td>
            <td align="center">{{ strval((float)$offer->quantity) }}</td>
            <td align="center">{{ number_format(($offer->price + $offer->delivery_costs) * $offer->quantity) }}</td>
        </tr>
        <tr>
            <td>合计</td>
            <td colspan="5">{{ number_format(($offer->price + $offer->delivery_costs) * $offer->quantity) }} 元（大写：{{ $amount }}）</td>
        </tr>
        <tr>
            <td colspan="6">包装物不计重，不计价，不返还，每件包装物扣重<span class="editable" contenteditable="true">0g</span></td>
        </tr>
    </table>
    <h2>二、质量要求、技术标准、供方对质量负责的条件和期限：</h2>
    <p class="padding"><span class="editable" contenteditable="true">{{ $goods->quality_standard }}</span></p>
    <p class="padding">无论何种原因导致所交货物的质量达不到合同规定的质量标准，买方可以拒绝接收货物，买方不承担所带来的损失，包括但不限于货物的运输费、保险费，仓储费。</p>
    <h2>三、交货或提货时间：</h2>
    <p class="padding"><span class="editable" contenteditable="true">[交（提）货时间]</span>卖方无论因何原因延迟交货且买方没有选择终止合同的情况下，卖方都应该承担延期交货的责任，买方有权按照实际延期交货的天数，以<span class="editable" contenteditable="true">元/天·吨</span>向卖方主张因延期交货所带来的责任。</p>
    <h2>四、交（提）货地点、方式：</h2>
    <p class="padding"><span class="editable" contenteditable="true">[请编写交（提）货地点、方式]</span></p>
    <h2>五、运输：</h2>
    <p class="padding">①运输方式和费用承担方式：<span class="editable" contenteditable="true">[请编写运输方式和费用承担方式]</span></p>
    <p class="padding">②货物保险的办理及费用承担：<span class="editable" contenteditable="true">有；无；费用由买方; 卖方承担</span></p>
    <p class="padding">③风险承担：货物损毁灭失的风险，在货物交付买方之前由卖方承担，交付买方之后由买方承担，交付以买方签收并实际占有为准。货交第三方承运的不视为实际占有。</p>
    <h2>六、验收：</h2>
    <p class="padding">①合理损耗和计算方法：<span class="editable" contenteditable="true">[请编写损耗和计算方法]</span></p>
    <p class="padding">②交货重量：<span class="editable" contenteditable="true">以买方；卖方；双方认可的第三方（  ）的地磅所称重量为准。</span></p>
    <p class="padding">③验收标准、方法及提出异议期限：<span class="editable" contenteditable="true">[请编写验收标准、方法及提出异议期限]</span></p>
    <h2>七、结算支付：</h2>
    <p class="padding">①<span class="editable" contenteditable="true">买方于货到验收合格后   日内付款  %，收到发票后付清尾款；买方于货到验收合格并收到卖方发票后   日内付款  ；预付款</span></p>
    <p class="padding">②付款方式：<span class="editable" contenteditable="true">电汇；承兑汇票；其他</span></p>
    <p class="padding">③其他：<span class="editable" contenteditable="true">无</span></p>
    <h2>八、违约责任：</h2>
    <p class="padding">①卖方逾期交货或未按约定的质量交货的，买方有权拒收货物、解除合同，由此产生的损失及费用由卖方承担。</p>
    <p class="padding">②卖方逾期交货，而买方仍同意收货的：在交货时遇货物市场价格上涨的，买方仍按本合同约定价格结算，在交货时遇货物市场价格下降的，买方按交货时的市场价格结算，或另以补充合同签订确定。</p>
    <p class="padding">③卖方未按约定的质量交货的，而买方仍同意收货的：买方有权选择按照卖方交货产品质量结算或在没有给买方带来经济损失的前提下要求卖方在指定期限内交换货；买方正确使用卖方产品而给买方造成损失的，由卖方承担赔偿责任，包括但不限于使用卖方产品生产成品不合格的责任、买方不能正常向自己的客户交付产品的责任。</p>
    <p class="padding">④卖方未按约定重量交货的，买方有权选择按实际交货重量结算或要求卖方在指定期限内按照约定重量交货，若卖方不同意，买方有权解除合同，由此带来的损失由卖方自行承担。</p>
    <h2>九、不可抗拒力：由于战争、地震、水灾、火灾、暴风雪或其他不可抗力原因而不能履行合同的一方不负有违约责任，但应于不可抗因素消除后5日内向对方提交相关证明文件。卖方未能与承运人达成运输协议而无法按时交货的，不视为不可抗拒力，不能免除违约责任，仍然要承担延期交货及<span class="editable" contenteditable="true">元/天·吨</span>的违约责任。</h2>
    <h2>十、争议的处理：因本合同履行过程中出现的任何争议，应本着友好协商的原则解决，如协商不成，由签约地所在的人民法院仲裁。</h2>
    <h2>十一、其他约定：</h2>
    <p class="padding">①本合同的约定条款是买卖双方自愿协商的结果，其任何条款并不构成任一方的格式条款。</p>
    <p class="padding">②若超过合同约定有限期限未执行完毕，余下未执行部分必须双方再确认后，方能生效。</p>
    <p class="padding">③合同中价格约定后，无特殊情况双方均不得提出调价。</p>
    <p class="padding">④本合同在约定有效期限内有效，逾期作废。</p>
    <p class="padding">⑤其他：<span class="editable" contenteditable="true">无</span></p>
    <h2>十二、本合同一经签订，双方此前的就买卖货物所签署的任何形式的文件、传真、电子数据自动失效，一切以本合同为准。本合同一式肆份，买卖双方各执贰份，具有同等法律效力。合同以传真方式签署同样有效。本合同自双方签字盖章后方能生效。</h2>
    <br />
    <br />
    <table>
        <tr>
            <td width="50" align="right">买方：</td>
            <td width="260">{{ $company->name }}</td>
            <td width="50" align="right">卖方：</td>
            <td>{{ $offer->supplier->supplier->name }}</td>
        </tr>
        <tr>
            <td width="50" align="right">代表：</td>
            <td>{{ $company->contract_contact }}</td>
            <td width="50" align="right">代表：</td>
            <td>{{ $offer->supplier->supplier->contact }}</td>
        </tr>
        <tr>
            <td width="50" align="right">盖章：</td>
            <td></td>
            <td width="50" align="right">盖章：</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">合同生效日期：<span class="editable" contenteditable="true">{{ date('Y年m月d日') }}</span></td>
        </tr>
    </table>
</div>
</body>
{{--<script type="text/javascript" src="{{ asset('asset/vendor/ckeditor/ckeditor.js') }}"></script>--}}
{{--<script type="text/javascript">--}}
{{--CKEDITOR.on( 'instanceCreated', function( event ) {--}}
{{--var editor = event.editor,--}}
{{--element = editor.element;--}}
{{--editor.config.skin = 'office2013';--}}
{{--editor.on( 'configLoaded', function() {--}}

{{--// Remove unnecessary plugins to make the editor simpler.--}}
{{--editor.config.removePlugins = 'colorbutton,find,flash,font,' +--}}
{{--'forms,iframe,image,newpage,removeformat,' +--}}
{{--'smiley,specialchar,stylescombo,templates';--}}

{{--// Rearrange the layout of the toolbar.--}}
{{--editor.config.toolbarGroups = [--}}
{{--{ name: 'editing',		groups: [ 'basicstyles'] },--}}
{{--{ name: 'undo' },--}}
{{--{ name: 'clipboard',	groups: [ 'selection', 'clipboard' ] }--}}
{{--];--}}
{{--});--}}

{{--});--}}
{{--</script>--}}
<script type="text/javascript" src="{{ asset('asset/vendor/jquery/dist/jquery.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#generate').click(function() {
            var editables = $('.editable');
            var html= '';
            for (var i = 0; i < editables.length; i += 1) {
                html += '<textarea name="data[]">'+editables.get(i).innerHTML+'</textarea>';
            }
            $('#contract-form .box').html(html);
            $('#contract-form').submit();
        });
    });
</script>
<script type="text/javascript">
    function turnBackFrame(go, hold) {
        var _$ = top.$;
        var referFrameSrc = $(window.frameElement).attr('data-refer-frame');
        if (go) {
            referFrameSrc = go;
        }
        var referTitle = $(window.frameElement).attr('data-refer-title');
        var frameId = $(window.frameElement).attr('data-frame');

        if ( !! referTitle) {
            var existsFrame = _$('.content > .frameset > iframe[data-frame="'+referTitle+'"]');
            if (existsFrame.length > 0) {
                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + referTitle + '"]').trigger('click');
                }
                _$('.loading-box').show();
                existsFrame.attr('src', referFrameSrc);
                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + frameId + '"]').remove();
                    $(window.frameElement).remove();
                }
            } else {
                existsFrame = _$('.content > .frameset > iframe[data-frame="home"]').clone(true, true);
                existsFrame.attr('data-frame', referTitle);
                existsFrame.attr('src', referFrameSrc);
                existsFrame.load(function() {
                    _$('.loading-box').hide();
                });
                existsFrame.appendTo(_$('.content > .frameset'));

                var tab = _$('<li class="pull-left frame-tab active"><a href="javascript:void(0)">' + referTitle + '</a> <span class="fa fa-times-circle close-frame"></span></li>');
                tab.attr('data-frame', referTitle);
                tab.appendTo(_$('.content > .tab > ul'));
                tab.click(parent.switchFrame);
                //_$('.content .frame-tab[data-frame="'+frameId+'"]').find('.close-frame').trigger('click');

                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + referTitle + '"]').trigger('click');
                }
                _$('.loading-box').show();
                existsFrame.attr('src', referFrameSrc);
                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + frameId + '"]').remove();
                    $(window.frameElement).remove();
                }
            }
        }
    }
</script>
@if (session()->has('tip_message'))
    <script type="text/javascript">
        top.showTip('{{ session('tip_message.content') }}', '{{ session('tip_message.state') }}');
        @if (session('tip_message.hold'))
            turnBackFrame(null, true);
        @else
            @if (session('tip_message.go'))
                turnBackFrame('{{ session('tip_message.go') }}', false);
        @else
            turnBackFrame(null, false);
        @endif
        @endif
    </script>
@endif
</html>