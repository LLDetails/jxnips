@extends('layout.frame')

@section('search')
    <form class="form-inline" method="get" action="{{ URL::full() }}">
        {!! IPSHelper::showButton(['permission'=>'sms.add', 'type'=>'frame', 'title'=>'新建短信模板', 'src'=>route('sms.add'), 'css'=>'btn btn-success btn-sm', 'text'=>'<span class="fa fa-plus-circle"></span> 新建短信模板']) !!}
    </form>
@endsection

@section('main')
    <br />
    <table class="list table table-striped table-bordered table-hover" style="margin-top:10px;">
        <tr>
            <th class="text-center">操作</th>
            <th>模板标题</th>
            @if(IPSHelper::hasPermission('sms.check'))
                <th width="260">模板内容</th>
                <th>模板授权ID</th>
            @endif
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($templates) > 0)
            @foreach ($templates as $k => $template)
                <tr>
                    <td class="text-center" width="260">
                        @if ($template->enable)
                            {!! IPSHelper::showButton(['permission'=>'sms.send.supplier', 'type'=>'frame', 'title'=>'发给供应商', 'src'=>route('sms.send.supplier', ['sms' => $template->id]), 'css'=>'btn btn-primary btn-xs', 'text'=>'发给供应商']) !!}
                            {!! IPSHelper::showButton(['permission'=>'sms.send.staff', 'type'=>'frame', 'title'=>'发给采购方', 'src'=>route('sms.send.staff', ['sms' => $template->id]), 'css'=>'btn btn-success btn-xs', 'text'=>'发给采购方']) !!}
                        @endif
                        {!! IPSHelper::showButton(['permission'=>'sms.check', 'type'=>'frame', 'title'=>'短信模板审核授权', 'src'=>route('sms.check', ['sms' => $template->id]), 'css'=>'btn btn-danger btn-xs', 'text'=>'审核授权']) !!}
                    </td>
                    <td>{{ $template->title }}</td>
                    @if(IPSHelper::hasPermission('sms.check'))
                        <td>{{ $template->txt }}</td>
                        <td>{{ $template->ali_code }}</td>
                    @endif
                    <td>{!! $template->enable ? '<span class="text-success">可以使用</span>' : '<span class="text-danger">申请授权</span>' !!}</td>
                    <td>{{ $template->created_at }}</td>
                    <td>{{ $template->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" @if(IPSHelper::hasPermission('sms.check')) colspan="7" @else colspan="5" @endif>没有找到数据</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection