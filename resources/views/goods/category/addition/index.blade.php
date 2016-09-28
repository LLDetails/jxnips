@extends('layout.frame')

@section('search')
    <a style="margin-bottom: 10px;" data-frame-title="新增商品{{ $deal_addition == 1 ? '交易' : '分类' }}额外属性" data-frame-src="{{ route('goods.category.addition.add', ['category' => $category->id, 'deal_addition'=>$deal_addition]) }}" href="javascript:void(0)" class="btn btn-success btn-xs frame-link"><span class="fa fa-plus-circle"></span> 新增商品{{ $deal_addition == 1 ? '交易' : '分类' }}额外属性</a>
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th colspan="5">新增商品（{{ $category->name }}）{{ $deal_addition == 1 ? '交易' : '分类' }}额外属性</th>
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
                        <a data-frame-title="编辑商品分类额外属性" data-frame-src="{{ route('goods.category.addition.edit', ['category' => $category->id, 'addition_id' => $k, 'deal_addition'=>$deal_addition]) }}" href="javascript:void(0);" class="btn btn-info btn-xs frame-link">编辑</a>
                        <a onclick="if(!confirm('确定删除该属性？此操作不可逆！')){return false}" href="{{ route('goods.category.addition.delete', ['category' => $category->id, 'addition_id' => $k, 'deal_addition'=>$deal_addition]) }}" class="btn btn-danger btn-xs">删除</a>
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
