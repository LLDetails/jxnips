@extends('layout.frame')

@section('main')
    <table class="list table table-striped table-bordered table-hover">
        <tr>
            <th>#</th>
            <th class="text-center">操作</th>
            <th>标题</th>
            <th>品种</th>
            <th>数量（吨）</th>
            <th>报价截止</th>
            <th>状态</th>
        </tr>
        @if (count($enquiries) > 0)
            @foreach ($enquiries as $k => $enquiry)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <?php
                        $replied = $enquiry->replies()->where('supplier_id', auth()->user()->id)->exists();
                    ?>
                    <td class="text-center" width="60">
                        @if ($enquiry->stop_at > $datetime and !$replied)
                            {!! IPSHelper::showButton(['permission'=>'enquiry.supplier.reply', 'type'=>'frame', 'title'=>'报价单报价', 'src'=>route('enquiry.supplier.reply', ['enquiry' => $enquiry->id]), 'css'=>'btn btn-info btn-xs', 'text'=>'报价']) !!}
                        @else
                            {!! IPSHelper::showButton(['permission'=>'enquiry.supplier.reply', 'type'=>'frame', 'title'=>'查看报价单', 'src'=>route('enquiry.supplier.reply', ['enquiry' => $enquiry->id]), 'css'=>'btn btn-default btn-xs', 'text'=>'详情']) !!}
                        @endif
                    </td>
                    <td>{{ $enquiry->title }}</td>
                    <td>{{ $enquiry->goods->name }}</td>
                    <td>{{ strval((float)$enquiry->quantity) }}</td>
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
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="7">没有报价单</td>
            </tr>
        @endif
    </table>
@stop

@section('page')
    {!! $pages !!}
@endsection