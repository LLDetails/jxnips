@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'company.addition.add', 'type'=>'frame', 'title'=>'新增公司额外属性', 'src'=>route('company.addition.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 新增公司额外属性']) !!}
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th colspan="5">公司额外属性</th>
        </tr>
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>名称</th>
            <th>标识</th>
            <th>类型</th>
        </tr>
        @if (count($addition) > 0)
            @foreach ($addition as $k => $field)
                <tr>
                    <td width="30">{{ $k + 1 }}</td>
                    <td class="text-center" width="100">
                        {!! IPSHelper::showButton(['permission'=>'company.addition.edit', 'type'=>'frame', 'title'=>'编辑公司额外属性', 'src'=>route('company.addition.edit', ['addition_id' => $k]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'company.addition.delete', 'type'=>'link', 'css'=>'btn btn-danger btn-xs', 'confirm'=>'确定删除该属性？此操作不可逆！', 'href'=>route('company.addition.delete', ['addition_id' => $k]), 'text'=>'删除']) !!}
                    </td>
                    <td>{{ $field->display }}</td>
                    <td>{{ $field->name }}</td>
                    <td>{{ $type[$field->tpl] }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="7">没有额外属性</td>
            </tr>
        @endif
    </table>
@stop
