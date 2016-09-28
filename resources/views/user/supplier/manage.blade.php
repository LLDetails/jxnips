@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>

@endsection

@section('search')
    <form class="form-inline" method="get" action="{{ route('user.supplier.manage') }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">帐号</div>
                <input style="width:140px;" type="text" class="form-control input-sm" name="username" value="{{ Input::get('username') }}" placeholder="帐号">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-addon">名称</div>
                <input style="width:140px;" type="text" class="form-control input-sm" name="name" value="{{ Input::get('name') }}" placeholder="名称">
            </div>
            <div class="input-group">
                <select name="allow_login" class="form-control input-sm">
                    <option value="">状态不限</option>
                    <option @if(Input::get('allow_login') == 'true') selected @endif value="true">已启用</option>
                    <option @if(Input::get('allow_login') == 'false') selected @endif value="false">被禁用</option>
                </select>
            </div>
            @if ( ! empty($areas))
                <div class="input-group">
                    <select name="area_id" class="form-control input-sm">
                        <option value="">所有地区</option>
                        @foreach ($areas as $area)
                            <option @if(Input::get('area_id') == $area->id) selected @endif value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <select name="goods_id" class="form-control input-sm select2">
                <option value="">请选择采购商品</option>
                @foreach ($goods_data as $goods_item)
                    <option @if(Input::get('goods_id') == $goods_item->id) selected @endif value="{{ $goods_item->id }}">[{{ $goods_item->code }}] {{ $goods_item->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查找</button>
        <a href="{{ route('user.supplier.manage') }}" class="btn btn-info btn-sm"><span class="fa fa-list"></span> 显示全部</a>
        {!! IPSHelper::showButton(['permission'=>'user.supplier.add', 'type'=>'frame', 'title'=>'新建供应商', 'src'=>route('user.supplier.add'), 'css'=>'btn btn-success btn-sm pull-right', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 新建供应商']) !!}
    </form>
@endsection

@section('main')
    <table class="list table table-striped table-bordered table-hover" style="margin-top: 10px;">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>账号</th>
            <th>名称</th>
            <th width="70">归属地区</th>
            <th width="240">供应原料</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($users) > 0)
            <?php $auth_user_id = auth()->user()->id ?>
            @foreach ($users as $k => $user)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="160">
                        {!! IPSHelper::showButton(['permission'=>'user.supplier.view', 'type'=>'frame', 'title'=>'查看供应商信息', 'src'=>route('user.supplier.view', ['user' => $user->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'查看']) !!}
                        {!! IPSHelper::showButton(['permission'=>'user.supplier.edit', 'type'=>'frame', 'title'=>'编辑供应商', 'src'=>route('user.supplier.edit', ['user' => $user->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'编辑']) !!}
                        {!! IPSHelper::showButton(['permission'=>'user.supplier.disable', 'type'=>'link', 'css'=>'btn btn-'. ($user->allow_login ? 'danger' : 'success').' btn-xs', 'confirm'=>'确定'.($user->allow_login ? '禁用' : '启用').'该供应商？', 'href'=>route('user.supplier.disable', ['user' => $user->id]), 'text'=>($user->allow_login ? '禁用' : '启用')]) !!}
                    </td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->supplier->name }}</td>
                    <td>{{ $user->area->name }}</td>
                    <?php
                        $supply_goods = $user->supplier->goods;
                        $supply_goods = json_decode($supply_goods);
                        $supply_goods_name = \App\Goods::whereIn('id', $supply_goods)->where('is_available', true)->whereNull('deleted_at')->lists('name');
                        if ( ! empty($supply_goods_name)) {
                            $supply_goods_name = $supply_goods_name->toArray();
                        } else {
                            $supply_goods_name = [];
                        }
                    ?>
                    <td>{{ implode('；', $supply_goods_name) }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->edit_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">没有找到供应商</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection

@section('js')
    <script type="text/javascript">
        $(".select2").select2({
            language: "zh-CN"
        });
    </script>
@endsection