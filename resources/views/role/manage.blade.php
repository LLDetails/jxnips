@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'role.add', 'type'=>'frame', 'title'=>'新增角色', 'src'=>route('role.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 新增角色']) !!}
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>角色名</th>
            <th>级别</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($roles) > 0)
            @foreach ($roles as $k => $role)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        {!! IPSHelper::showButton(['permission'=>'role.edit', 'type'=>'frame', 'title'=>'编辑角色组', 'src'=>route('role.edit', ['role' => $role->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'role.permission', 'type'=>'frame', 'title'=>'权限绑定', 'src'=>route('role.permission', ['role' => $role->id]), 'css'=>'btn btn-warning btn-xs', 'text'=>'权限绑定']) !!}
                        {!! IPSHelper::showButton(['permission'=>'role.delete', 'type'=>'link', 'css'=>'btn btn-xs btn-danger', 'confirm'=>'确定删除该角色？', 'href'=>route('role.delete', ['role' => $role->id]), 'text'=>'删除']) !!}
                    </td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->level }}</td>
                    <td>{{ $role->created_at }}</td>
                    <td>{{ $role->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="6">没有找到角色</td>
            </tr>
        @endif
    </table>
@stop