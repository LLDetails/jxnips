@extends('layout.frame')

@section('search')
    <form class="form-inline" method="get" action="{{ route('user.staff.manage') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">帐号</div>
                <input type="text" style="width: 140px" class="form-control input-sm" name="username" value="{{ Input::get('username') }}" placeholder="帐号">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-addon">名称</div>
                <input type="text" style="width: 140px" class="form-control input-sm" name="realname" value="{{ Input::get('realname') }}" placeholder="名称">
            </div>
            <div class="input-group input-group-sm">
                <select name="area_id" class="form-control input-sm">
                    <option value="">不限地区</option>
                    @foreach ($areas as $area)
                        <option @if(Input::get('area_id') == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group input-group-sm">
                <select name="role_id" class="form-control input-sm">
                    <option value="">不限角色</option>
                    @foreach ($roles as $role)
                        <option @if(Input::get('role_id') == $role->id) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查找</button>
        <a href="{{ route('user.staff.manage') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
        {!! IPSHelper::showButton(['permission'=>'user.staff.add', 'type'=>'frame', 'title'=>'新建用户', 'src'=>route('user.staff.add'), 'css'=>'btn btn-success btn-sm pull-right', 'text'=>'<span class="fa fa-plus-circle"></span> 新建用户']) !!}
        {!! IPSHelper::showButton(['permission'=>'user.staff.addition.index', 'type'=>'frame', 'title'=>'用户额外属性', 'src'=>route('user.staff.addition.index'), 'css'=>'btn btn-warning btn-sm pull-right', 'style'=>'margin-bottom: 10px; margin-right:10px;', 'text'=>'<span class="fa fa-cog"></span> 用户额外属性']) !!}
    </form>
@endsection

@section('main')
    <table class="list table table-striped table-bordered table-hover" style="margin-top:10px;">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>账号</th>
            <th>名称</th>
            <th>角色</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($users) > 0)
            @foreach ($users as $k => $user)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="260">
                        {!! IPSHelper::showButton(['permission'=>'user.staff.view', 'type'=>'frame', 'title'=>'查看用户信息', 'src'=>route('user.staff.view', ['user' => $user->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                        {!! IPSHelper::showButton(['permission'=>'user.staff.edit', 'type'=>'frame', 'title'=>'编辑用户', 'src'=>route('user.staff.edit', ['user' => $user->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        @if (auth()->user()->id != $user->id)
                        {!! IPSHelper::showButton(['permission'=>'user.staff.disable', 'type'=>'link', 'css'=>'btn btn-'. ($user->allow_login ? 'danger' : 'success').' btn-xs', 'confirm'=>'确定'.($user->allow_login ? '禁用' : '启用').'该帐号？', 'href'=>route('user.staff.disable', ['user' => $user->id]), 'text'=>($user->allow_login ? '禁用' : '启用')]) !!}
                        @endif
                    </td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->staff->realname }}</td>
                    <td>{{ $user->role->name }}</td>
                    <td>{{ $user->allow_login ? '正常' : '禁用' }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->edit_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">没有找到用户</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection