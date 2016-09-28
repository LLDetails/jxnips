@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>

    <script type="text/javascript" src="{{ asset('asset/vendor/rater-star/jquery.rater.js') }}"></script>
    <link href="{{ asset('asset/vendor/rater-star/jquery.rater.css') }}" rel="stylesheet"/>
@endsection

@section('main')
    <table style="margin-top: 10px;" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>合同编号</th>
            <th>状态</th>
            <th>对应需求记录</th>
            <th>物料</th>
            <th>供应商</th>
            <th>供应量</th>
            <th>生成时间</th>
        </tr>
        @if (count($contracts) > 0)
            @foreach ($contracts as $k => $contract)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="220">
                        @if (!$contract->offline)
                            @if ($contract->state == 'refused')
                                {!! IPSHelper::showButton(['permission'=>'contract.company.edit', 'type'=>'frame', 'title'=>'修改合同', 'src'=>route('contract.company.edit', ['contract' => $contract->id]), 'css'=>'btn btn-warning btn-xs', 'text'=>'修改合同']) !!}
                            @else
                                {!! IPSHelper::showButton(['permission'=>'contract.company.view', 'type'=>'frame', 'title'=>'查看合同', 'src'=>route('contract.company.view', ['contract' => $contract->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看合同']) !!}
                            @endif
                        @else
                            {!! IPSHelper::showButton(['permission'=>'contract.attachment', 'type'=>'frame', 'title'=>'合同附件', 'src'=>route('contract.attachment', ['contract' => $contract->id]), 'css'=>'btn '.($contract->state == 'pending' ? 'btn-success' : 'btn-primary').' btn-xs', 'text'=>'合同附件']) !!}
                        @endif
                        @if ($contract->state == 'finished')
                            @if (IPSHelper::hasPermission('contract.company.grade'))
                                @if (empty($contract->grade) or empty($contract->grade->company_graded_at))
                                    <button data-url="{{ route('contract.company.grade', ['contract' => $contract->id]) }}" data-toggle="modal" data-target="#grade-model" type="button" class="btn btn-warning btn-xs grade">评价合同</button>
                                @else
                                    @if (!empty($contract->grade->company_graded_at) and !empty($contract->grade->supplier_graded_at))
                                        <button data-supplier_grade_1="{{ $contract->grade->supplier_grade_1 }}" data-supplier_grade_2="{{ $contract->grade->supplier_grade_2 }}" data-supplier_grade_3="{{ $contract->grade->supplier_grade_3 }}" data-company_grade_1="{{ $contract->grade->company_grade_1 }}" data-company_grade_2="{{ $contract->grade->company_grade_2 }}" data-toggle="modal" data-target="#grade-display-model" type="button" class="btn btn-default btn-xs display-grade">查看评价</button>
                                    @else
                                        <button data-company_grade_1="{{ $contract->grade->company_grade_1 }}" data-company_grade_2="{{ $contract->grade->company_grade_2 }}" data-toggle="modal" data-target="#grade-show-model" type="button" class="btn btn-default btn-xs show-grade">查看评价</button>
                                    @endif
                                @endif
                            @endif
                        @elseif ($contract->state == 'confirmed')
                            {!! IPSHelper::showButton(['permission'=>'contract.company.finish', 'type'=>'link', 'css'=>'btn btn-xs btn-danger', 'confirm'=>'要确合同已完成吗？确认后无法再修改。', 'href'=>route('contract.company.finish', ['contract' => $contract->id]), 'text'=>'合同完成']) !!}
                        @endif
                    </td>
                    <td>{{ $contract->code }}</td>
                    <td>{{ $contract_states[$contract->state] }}</td>
                    <td>{{ $contract->offer->demand->basket->name }}</td>
                    <?php
                        $goods = json_decode($contract->offer->demand->goods_static);
                    ?>
                    <td>{{ $goods->name }}</td>
                    <td>{{ $contract->offer->supplier->supplier->name }}</td>
                    <td>{{ strval((float)$contract->offer->quantity) }} {{ $goods->unit }}</td>
                    <td>{{ $contract->created_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="9">没有找到记录</td>
            </tr>
        @endif
    </table>

    <div id="grade-model" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">评价</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-dismissible alert-danger hide">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong id="grade-err-msg"></strong>
                    </div>
                    <table style="width:100%">
                        <tr>
                            <td>原料合格率</td>
                            <td><div id="company_grade_1"></div></td>
                        </tr>
                        <tr>
                            <td>合同执行率</td>
                            <td><div id="company_grade_2"></div></td>
                        </tr>
                    </table>
                    <input type="hidden" id="company_grade_1_value" name="company_grade_1">
                    <input type="hidden" id="company_grade_2_value" name="company_grade_2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button data-token="{{ csrf_token() }}" type="button" class="btn btn-primary" data-url="" id="submit-grade">提交评价</button>
                </div>
            </div>
        </div>
    </div>

    <div id="grade-show-model" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">评价结果</h4>
                </div>
                <div class="modal-body">
                    <table style="width:100%">
                        <tr>
                            <td>原料合格率</td>
                            <td><div id="company_show_grade_1"></div></td>
                        </tr>
                        <tr>
                            <td>合同执行率</td>
                            <td><div id="company_show_grade_2"></div></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

    <div id="grade-display-model" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">评价结果</h4>
                </div>
                <div class="modal-body">
                    <h3>供方评价：</h3>
                    <table style="width:100%">
                        <tr>
                            <td>付款及时率</td>
                            <td><div id="supplier_display_grade_1"></div></td>
                        </tr>
                        <tr>
                            <td>卸货及时率</td>
                            <td><div id="supplier_display_grade_2"></div></td>
                        </tr>
                        <tr>
                            <td>化验及时率</td>
                            <td><div id="supplier_display_grade_3"></div></td>
                        </tr>
                    </table>
                    <hr />
                    <h3>需方评价：</h3>
                    <table style="width:100%">
                        <tr>
                            <td>原料合格率</td>
                            <td><div id="company_display_grade_1"></div></td>
                        </tr>
                        <tr>
                            <td>合同执行率</td>
                            <td><div id="company_display_grade_2"></div></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('page')
    {!! $pages !!}
@endsection

@section('js')
    <script type="text/javascript">

        function initStar(url) {

            $('#submit-grade').attr('data-url', url);

            var options = {
                image: '{{ asset('asset/vendor/rater-star/star.gif')}}',
                max: 5
            };
            options.after_click = function(ret) {
                $('#company_grade_1_value').val(ret.number);
            };
            $('#company_grade_1').html('');
            $('#company_grade_1').rater(options);
            options.after_click = function(ret) {
                $('#company_grade_2_value').val(ret.number);
            };
            $('#company_grade_2').html('');
            $('#company_grade_2').rater(options);
        }

        function showStar(g1, g2) {
            var options = {
                image: '{{ asset('asset/vendor/rater-star/star.gif')}}',
                max: 5,
                enabled: false
            }
            $('#company_show_grade_1').html('');
            options.value = g1;
            $('#company_show_grade_1').rater(options);
            $('#company_show_grade_2').html('');
            options.value = g2;
            $('#company_show_grade_2').rater(options);
        }

        function displayStar(gs1, gs2, gs3, gc1, gc2) {
            var options = {
                image: '{{ asset('asset/vendor/rater-star/star.gif')}}',
                max: 5,
                enabled: false
            }
            $('#supplier_display_grade_1').html('');
            options.value = gs1;
            $('#supplier_display_grade_1').rater(options);
            $('#supplier_display_grade_2').html('');
            options.value = gs2;
            $('#supplier_display_grade_2').rater(options);
            $('#supplier_display_grade_3').html('');
            options.value = gs3;
            $('#supplier_display_grade_3').rater(options);

            $('#company_display_grade_1').html('');
            options.value = gc1;
            $('#company_display_grade_1').rater(options);
            $('#company_display_grade_2').html('');
            options.value = gc2;
            $('#company_display_grade_2').rater(options);
        }

        $(document).ready(function() {
            $('.grade').click(function() {
                var url = $(this).attr('data-url');
                initStar(url);
            });

            $('.show-grade').click(function() {
                var g1 = $(this).attr('data-company_grade_1');
                var g2 = $(this).attr('data-company_grade_2');
                showStar(g1, g2);
            });

            $('.display-grade').click(function() {
                var gs1 = $(this).attr('data-supplier_grade_1');
                var gs2 = $(this).attr('data-supplier_grade_2');
                var gs3 = $(this).attr('data-supplier_grade_3');
                var gc1 = $(this).attr('data-company_grade_1');
                var gc2 = $(this).attr('data-company_grade_2');
                displayStar(gs1, gs2, gs3, gc1, gc2);
            });

            $('#submit-grade').click(function() {
                var url = $(this).attr('data-url');
                var token = $(this).attr('data-token');
                var data = {
                    "_token": token,
                    "company_grade_1": $('#company_grade_1_value').val(),
                    "company_grade_2": $('#company_grade_2_value').val()
                };
                $.post(url, data, function(res) {
                    if (typeof res['state'] == 'undefined') {
                        $('#grade-err-msg').html('服务器繁忙，请稍候再试');
                        $('#grade-err-msg').parent().show();
                    } else {
                        if (res['state'] == 'error') {
                            $('#grade-err-msg').html(res['msg']);
                            $('#grade-err-msg').parent().show();
                        } else {
                            window.location.href = window.location.href;
                        }
                    }
                });
            });
        });
    </script>
@endsection
