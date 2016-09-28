@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'area.add', 'type'=>'frame', 'title'=>'添加地区', 'src'=>route('area.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 添加地区']) !!}
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>地区</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($areas) > 0)
            @foreach ($areas as $k => $area)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        {!! IPSHelper::showButton(['permission'=>'area.edit', 'type'=>'frame', 'title'=>'编辑地区', 'src'=>route('area.edit', ['area' => $area->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'area.delete', 'type'=>'link', 'css'=>'btn btn-xs btn-danger', 'confirm'=>'确定删除该地区？', 'href'=>route('area.delete', ['area' => $area->id]), 'text'=>'删除']) !!}
                    </td>
                    <td>{{ $area->name }}</td>
                    <td>{{ $area->created_at }}</td>
                    <td>{{ $area->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">没有地区数据</td>
            </tr>
        @endif
    </table>
@stop