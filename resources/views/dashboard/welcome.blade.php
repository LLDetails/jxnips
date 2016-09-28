@extends('layout.frame')

@section('main')
    <div class="row">
        @if (!empty($enquiries))
            <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待报价的询价单</div>
                    <table class="table">
                        @if (count($enquiries) > 0)
                            @foreach($enquiries as $enquiry)
                            <tr>
                                <td><a data-frame-title="询价单报价" data-frame-src="{{ route('enquiry.supplier.reply', ['enquiry' => $enquiry->id]) }}" href="javascript:void(0);" class="frame-link">{{ $enquiry->title }} - {{ strval((float)$enquiry->quantity) }} 吨</a></td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>待报价的询价单</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($check_basket))
            <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待审核的需求</div>
                    <table class="table">
                        @if (count($check_basket) > 0)
                            @foreach($check_basket as $basket)
                            <tr>
                                <td><a data-frame-title="审核采购需求" data-frame-src="{{ route('demand.check.action', ['basket' => $basket->id]) }}" href="javascript:void(0);" class="frame-link">{{ $basket->name }}</a></td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待审核的需求</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($collect_baskets))
            <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待汇总的需求</div>
                    <table class="table">
                        @if (count($collect_baskets) > 0)
                            @foreach($collect_baskets as $basket)
                                <tr>
                                    @if ($basket->state != 'refused')
                                        <td><a data-frame-title="采购需求汇总" data-frame-src="{{ route('bid.demand.collect', ['basket' => $basket->id]) }}" href="javascript:void(0);" class="frame-link">{{ $basket->name }}</a></td>
                                    @else
                                        <td><a data-frame-title="采购需求汇总" data-frame-src="{{ route('bid.demand.edit', ['basket' => $basket->id]) }}" href="javascript:void(0);" class="frame-link">{{ $basket->name }}</a></td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待汇总的需求</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($bid_baskets))
                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待审核的汇总</div>
                    <table class="table">
                        @if (count($bid_baskets) > 0)
                            @foreach($bid_baskets as $basket)
                                <tr>
                                    <td><a data-frame-title="标书审核" data-frame-src="{{ route('bid.check.view', ['basket' => $basket->id]) }}" href="javascript:void(0);" class="frame-link">{{ $basket->name }}</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待审核的汇总</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($offers))
            <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待生成的合同</div>
                    <table class="table">
                        @if (count($offers) > 0)
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{ $offer->demand->goods->name }}</td>
                                    <td>￥{{ strval((float)$offer->price) }}</td>
                                    <td>{{ strval((float)$offer->quantity) }} 吨</td>
                                    <td>[<a data-frame-title="生成合同" data-frame-src="{{ route('bid.company.offer', ['offer' => $offer->id]) }}" href="javascript:void(0);" class="frame-link">标准合同</a>]</td>
                                    <td>[<a style="color: #F00000" data-confirm="确定要生成附件合同？此操作不可逆！" data-frame-title="生成合同" data-frame-src="{{ route('bid.company.upload_contract', ['offer' => $offer->id]) }}" href="javascript:void(0);" class="frame-link">附件合同</a>]</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待生成的合同</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($bids))
                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待报价的标书</div>
                    <table class="table">
                        @if (count($bids) > 0)
                            @foreach($bids as $bid)
                                <tr>
                                    <td><a data-frame-title="标书报价" data-frame-src="{{ route('bid.supplier.offer', ['bid' => $bid->id]) }}" href="javascript:void(0);" class="frame-link">{{ $bid->code }}</a></td>
                                    <td>{{ $bid->goods->name }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待报价的标书</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($contracts))
            <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待确认的合同</div>
                    <table class="table">
                        @if (count($contracts) > 0)
                            @foreach($contracts as $contract)
                                <tr>
                                    <td><a data-frame-title="查看合同" data-frame-src="{{ route('contract.supplier.view', ['contract' => $contract->id]) }}" href="javascript:void(0);" class="frame-link">{{ $contract->code }}</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待确认的合同</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif

        @if ( ! empty($edit_contracts))
            <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">待修改的合同</div>
                    <table class="table">
                        @if (count($edit_contracts) > 0)
                            @foreach($edit_contracts as $contract)
                                <tr>
                                    <td><a data-frame-title="修改合同" data-frame-src="{{ route('contract.company.edit', ['contract' => $contract->id]) }}" href="javascript:void(0);" class="frame-link">{{ $contract->code }}</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">暂无待修改的合同</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var _$ = top.$;
            _$('.loading-box').hide();
        });
    </script>
@stop