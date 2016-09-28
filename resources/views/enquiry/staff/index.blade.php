@extends('layout.frame')

@section('search')
    {!! IPSHelper::showButton(['permission'=>'enquiry.staff.add', 'type'=>'frame', 'title'=>'发布询价单', 'src'=>route('enquiry.staff.add'), 'css'=>'btn btn-success btn-xs', 'style'=>'margin-bottom: 10px;', 'text'=>'<span class="fa fa-plus-circle"></span> 发布询价单']) !!}
@stop

@section('main')
    <table class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>标题</th>
            <th>品种</th>
            <th>报价截止</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
        </tr>
        @if (count($enquiries) > 0)
            @foreach ($enquiries as $k => $enquiry)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td class="text-center" width="60">
                        {!! IPSHelper::showButton(['permission'=>'enquiry.staff.view', 'type'=>'frame', 'title'=>'查看询价单', 'src'=>route('enquiry.staff.view', ['enquiry' => $enquiry->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'查看']) !!}
                    </td>
                    <td>{{ $enquiry->title }}</td>
                    <td>{{ $enquiry->goods->name }}</td>
                    <td>{{ $enquiry->stop_at }}</td>
                    <td>
                        @if($enquiry->stop_at > $datetime and $enquiry->start_at < $datetime)
                            <span style="color:red">询价中</span>
                        @elseif ($enquiry->start_at > $datetime)
                            <span>未开始</span>
                        @elseif ($enquiry->stop_at < $datetime)
                            <span style="color:blue">已截止</span>
                        @endif
                    </td>
                    <td>{{ $enquiry->created_at }}</td>
                    <td>{{ $enquiry->updated_at }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="9">没有询价单</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection