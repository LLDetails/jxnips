@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'setting.assign_rule.add', 'type'=>'frame', 'title'=>'新增分配规则', 'src'=>route('setting.assign_rule.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 新增分配规则']) !!}
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>规则名称</th>
            <th>详细</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($assign_rules) > 0)
            @foreach ($assign_rules as $k => $rule)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="120">
                        {!! IPSHelper::showButton(['permission'=>'setting.assign_rule.edit', 'type'=>'frame', 'title'=>'编辑分配规则', 'src'=>route('setting.assign_rule.edit', ['rule' => $rule->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'setting.assign_rule.delete', 'type'=>'link', 'css'=>'btn btn-xs btn-danger', 'confirm'=>'确定删除该规则？', 'href'=>route('setting.assign_rule.delete', ['rule' => $rule->id]), 'text'=>'删除']) !!}
                    </td>
                    <td>{{ $rule->name }}</td>
                    <td>{{ implode('%；',json_decode($rule->rules, true)).'%' }}</td>
                    <td>{{ $rule->created_at }}</td>
                    <td>{{ $rule->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="5">没有找分配规则</td>
            </tr>
        @endif
    </table>
@stop