@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'company.delivery_mode.add', 'type'=>'frame', 'title'=>'新增到货方式', 'src'=>route('company.delivery_mode.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 新增到货方式']) !!}
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>到货方式</th>
            <th>价格（元/吨）</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($delivery_modes) > 0)
            @foreach ($delivery_modes as $k => $delivery_mode)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        {!! IPSHelper::showButton(['permission'=>'company.delivery_mode.edit', 'type'=>'frame', 'title'=>'编辑到货方式', 'src'=>route('company.delivery_mode.edit', ['delivery_mode' => $delivery_mode->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                    </td>
                    <td>{{ $delivery_mode->mode }}</td>
                    <td>{{ $delivery_mode->costs }}</td>
                    <td>{{ $delivery_mode->created_at }}</td>
                    <td>{{ $delivery_mode->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">还没有设置到货方式</td>
            </tr>
        @endif
    </table>
@stop