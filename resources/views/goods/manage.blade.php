@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>
@endsection

@section('search')
    <form style="margin-bottom:15px;" class="form-inline" method="get" action="{{ route('goods.manage') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">名称</div>
                <input type="text" class="form-control input-sm" name="name" value="{{ Input::get('name') }}" placeholder="名称">
            </div>
            <select name="category_id" class="form-control input-sm select2">
                <option value="">所有类别</option>
                @foreach ($categories as $category)
                    <option @if(Input::get('category_id') == $category->id) selected @endif value="{{ $category->id }}">【{{$category->code}}】{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group">
            <select name="is_available" class="form-control input-sm">
                <option value="">状态不限</option>
                <option @if(Input::get('is_available') == 'true') selected @endif value="true">已启用</option>
                <option @if(Input::get('is_available') == 'false') selected @endif value="false">被禁用</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查找</button>
        <a href="{{ route('goods.manage') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
        {!! IPSHelper::showButton(['permission'=>'goods.add', 'type'=>'frame', 'title'=>'添加商品', 'src'=>route('goods.add'), 'css'=>'btn btn-success btn-sm pull-right', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 添加商品']) !!}
    </form>
@stop

@section('main')
    <table class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th width="200" class="text-center">操作</th>
            <th>商品名称</th>
            <th>代号</th>
            <th>计量单位</th>
            <th>归属分类</th>
            <th>报价有效期</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($goods_records) > 0)
            @foreach ($goods_records as $k => $goods)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="200">
                        {!! IPSHelper::showButton(['permission'=>'goods.view', 'type'=>'frame', 'title'=>'查看商品', 'src'=>route('goods.view', ['goods' => $goods->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                        {!! IPSHelper::showButton(['permission'=>'goods.edit', 'type'=>'frame', 'title'=>'编辑商品', 'src'=>route('goods.edit', ['goods' => $goods->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'goods.disable', 'type'=>'link', 'css'=>'btn btn-'. ($goods->is_available ? 'danger' : 'success').' btn-xs', 'confirm'=>'确定'.($goods->is_available ? '停用' : '启用').'该商品？', 'href'=>route('goods.disable', ['goods' => $goods->id]), 'text'=>($goods->is_available ? '停用' : '启用')]) !!}
                    </td>
                    <td>{{ $goods->name }}</td>
                    <td><code>{{ $goods->code }}</code></td>
                    <td>{{ $goods->unit }}</td>
                    <td>{{ $goods->category->name }}</td>
                    <td>招投标结束后{{ $goods->price_validity }}小时</td>
                    <td>{{ $goods->is_available ? '可用' : '停用' }}</td>
                    <td>{{ $goods->created_at }}</td>
                    <td>{{ $goods->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="9">没有找到商品</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".select2").select2({
                language: "zh-CN"
            });
        });
    </script>
@stop