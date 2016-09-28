@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>
@endsection

@section('search')
    <form class="form-inline" method="get" action="{{ route('offer.information.index') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">开始日期</div>
                <input type="text" style="width: 140px" class="form-control input-sm date" name="date_start" value="{{ Input::get('date_start') }}" placeholder="开始日期">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-addon">截止日期</div>
                <input type="text" style="width: 140px" class="form-control input-sm date" name="date_stop" value="{{ Input::get('date_stop') }}" placeholder="截止日期">
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查找</button>
        <a href="{{ route('offer.information.index') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
        {!! IPSHelper::showButton(['permission'=>'offer.information.create', 'type'=>'link', 'title'=>'发布需求', 'href'=>route('offer.information.create'), 'css'=>'btn btn-success btn-sm pull-right', 'text'=>'<span class="fa fa-plus-circle"></span> 创建清单']) !!}
    </form>
@stop

@section('main')
    <table style="margin-top: 10px;" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>日期</th>
        </tr>
        @if (count($offer_baskets) > 0)
            @foreach ($offer_baskets as $k => $basket)
                <?php
                    $total_count = $basket->information()->where('supplier_id', auth()->user()->id)->count();
                    $edit_count = $basket->information()->where('supplier_id', auth()->user()->id)->where('state', 'created')->count();
                ?>
                @if ($total_count == 0 and $basket->name < date('Y-m-d'))

                @else
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td class="text-center" width="240">
                            @if($basket->name == date('Y-m-d'))
                                {!! IPSHelper::showButton(['permission'=>'offer.information.list', 'type'=>'frame', 'title'=>'编辑日常报价', 'src'=>route('offer.information.list', ['basket' => $basket->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                            @else
                                {!! IPSHelper::showButton(['permission'=>'offer.information.list', 'type'=>'frame', 'title'=>'查看日常报价', 'src'=>route('offer.information.list', ['basket' => $basket->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                            @endif
                        </td>
                        <td>{{ $basket->name }}</td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">没有找到记录</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection

@section('js')
    <script type="text/javascript">
        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m-d'
        });
    </script>
@stop