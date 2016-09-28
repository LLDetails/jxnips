@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'feature.add', 'type'=>'frame', 'title'=>'添加功能', 'src'=>route('feature.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 添加功能']) !!}
@stop

@section('main')
    <table class="list table table-bordered table-hover">
        @foreach ($feature_data as $group => $data)
            <tr>
                <td width="120">{{ $group }}</td>
                <td>
                    @foreach ($data as $feature)
                        <span style="display: inline-block; width: 180px; white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <span class="ion-key" style="font-size: 16px"></span>
                            {{ $feature->name }}
                            {!! IPSHelper::showButton(['permission'=>'feature.edit', 'type'=>'frame', 'title'=>'编辑功能', 'src'=>route('feature.edit', ['feature' => $feature->id]), 'css'=>'', 'text'=>' <span class="fa fa-edit"></span> ']) !!}
                            {!! IPSHelper::showButton(['permission'=>'feature.delete', 'type'=>'link', 'css'=>'', 'confirm'=>'确定删除该功能？', 'href'=>route('feature.delete', ['feature' => $feature->id]), 'text'=>' <span class="fa fa-trash"></span> ']) !!}
                        </span>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </table>
@stop