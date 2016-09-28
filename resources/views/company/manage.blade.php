@extends('layout.frame')

@section('search')
    <form style="margin-bottom:15px;" class="form-inline" method="get" action="{{ route('company.manage') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">名称</div>
                <input type="text" class="form-control input-sm" name="name" value="{{ Input::get('name') }}" placeholder="名称">
            </div>
            <div class="input-group">
                <select name="area_id" class="form-control input-sm">
                    <option value="">不限地区</option>
                    @foreach ($areas as $area)
                        <option @if(Input::get('area_id') == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查找</button>
        <a href="{{ route('company.manage') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
        {!! IPSHelper::showButton(['permission'=>'company.add', 'type'=>'frame', 'title'=>'添加分公司', 'src'=>route('company.add'), 'css'=>'btn btn-success btn-sm pull-right', 'text'=>'<span class="fa fa-plus-circle"></span> 添加分公司']) !!}
        {!! IPSHelper::showButton(['permission'=>'company.addition.index', 'type'=>'frame', 'title'=>'公司额外属性', 'src'=>route('company.addition.index'), 'css'=>'btn btn-warning btn-sm pull-right', 'style'=>'margin-bottom: 10px; margin-right:10px;', 'text'=>'<span class="fa fa-cog"></span> 公司额外属性']) !!}
    </form>
@stop

@section('main')
    <table class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>公司名</th>
            <th>合同代号</th>
            <th>归属地区</th>
            <th>交付地址</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($companies) > 0)
            @foreach ($companies as $k => $company)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="140">
                        {!! IPSHelper::showButton(['permission'=>'company.edit', 'type'=>'frame', 'title'=>'编辑分公司', 'src'=>route('company.edit', ['company' => $company->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'company.delete', 'type'=>'link', 'css'=>'btn btn-xs btn-danger', 'confirm'=>'确定删除该公司？', 'href'=>route('company.delete', ['company' => $company->id]), 'text'=>'删除']) !!}
                    </td>
                    <td>{{ $company->name }}</td>
                    <td><code>{{ $company->code }}</code></td>
                    <td>{{ $company->area->name }}</td>
                    <td>{{ $company->delivery_address }}</td>
                    <td>{{ $company->created_at }}</td>
                    <td>{{ $company->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">没有找到角色</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection