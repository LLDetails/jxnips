@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'goods.category.add', 'type'=>'frame', 'title'=>'新增商品分类', 'src'=>route('goods.category.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 新增商品分类']) !!}
@stop

@section('main')
    <table style="width:800px" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>分类名</th>
            <th>代号</th>
            <th>状态</th>
            <th>排序</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($categories) > 0)
            @foreach ($categories as $k => $category)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="240">
                        {!! IPSHelper::showButton(['permission'=>'goods.category.edit', 'type'=>'frame', 'title'=>'编辑商品分类', 'src'=>route('goods.category.edit', ['category' => $category->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'goods.category.addition.index', 'type'=>'frame', 'title'=>'商品分类额外属性', 'src'=>route('goods.category.addition.index', ['category' => $category->id]), 'css'=>'btn btn-warning btn-xs', 'text'=>'额外属性']) !!}
                        {!! IPSHelper::showButton(['permission'=>'goods.category.addition.index', 'type'=>'frame', 'title'=>'商品分类交易属性', 'src'=>route('goods.category.addition.index', ['category' => $category->id, 'deal_addition' => 1]), 'css'=>'btn btn-primary btn-xs', 'text'=>'交易属性']) !!}
                        {!! IPSHelper::showButton(['permission'=>'goods.category.disable', 'type'=>'link', 'css'=>'btn btn-'. ($category->is_available ? 'danger' : 'success').' btn-xs', 'confirm'=>'确定'.($category->is_available ? '停用' : '启用').'该分类？', 'href'=>route('goods.category.disable', ['category' => $category->id]), 'text'=>($category->is_available ? '停用' : '启用')]) !!}
                    </td>
                    <td>{{ $category->name }}</td>
                    <td><code>{{ $category->code }}</code></td>
                    <td>{{ $category->is_available ? '正常' : '停用' }}</td>
                    <td>{{ $category->display_order }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>{{ $category->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">没有找到商品分类</td>
            </tr>
        @endif
    </table>
@stop