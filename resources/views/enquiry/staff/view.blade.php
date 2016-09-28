@extends('layout.frame')

@section('main')
<div class="panel panel-primary">
	<div class="panel-heading" style="height: 46px;">
        <h3 class="panel-title">
            <strong class="pull-left" style="margin-top:6px;">询价单: {{ $enquiry->title }}</strong>
            <span class="pull-right time-loading" style="padding-top:5px;">系统时间获取中...</span>
            <span class="pull-right time-loaded" style="display:none;padding-top:5px;">询价倒计时：<span class="colockbox" id="colockbox-global"><span class="day">0</span>天 <span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span></span></span>
        </h3>
    </div>
	<table class="table">
        <tr>
            <td colspan="3">质量要求：{{ $enquiry->quality }}</td>
        </tr>
		<tr>
			<td>采购量：{{ strval((float)$enquiry->quantity) }} 吨</td>
			<td>船期：{{ $enquiry->sailing_date }}</td>
			<td>询价截止：{{ $enquiry->stop_at }}</td>
		</tr>
		<tr>
			<td colspan="3">交货地点及方式：{{ $enquiry->terms_of_delivery }}</td>
		</tr>
  	</table>
</div>

<table class="table table-bordered table-striped">
    <tr>
        <th>#</th>
        <th>报价（元/吨）</th>
        <th>报价有效期</th>
        <th>供应商</th>
        <th>质量修改意见</th>
        <th>付款方式</th>
        <th>补充说明</th>
        <th>报价时间</th>
    </tr>
    @if (!empty($replies) and count($replies) > 0)
        @foreach ($replies as $k => $reply)
            <tr>
                <td>{{ $k+1 }}</td>
                <td style="color:red; font-weight:bold">
                    @if ($enquiry->stop_at > $datetime)
                        询价截止可见
                    @else
                        {{ strval((float)$reply->price) }}
                    @endif
                </td>
                <td style="color:red; font-weight:bold">
                    @if ($enquiry->stop_at > $datetime)
                        询价截止可见
                    @else
                        询价截止后 {{ $reply->price_validity }} 小时
                    @endif
                </td>
                <td>{{ $reply->supplier->supplier->name }}</td>
                <td>
                    @if ($enquiry->stop_at > $datetime)
                        询价截止可见
                    @else
                        {{ $reply->quality }}
                    @endif
                </td>
                <td>
                    @if ($enquiry->stop_at > $datetime)
                        询价截止可见
                    @else
                        {{ $reply->payment }}
                    @endif
                </td>
                <td>
                    @if ($enquiry->stop_at > $datetime)
                        询价截止可见
                    @else
                        {{ $reply->remark }}
                    @endif
                </td>
                <td>{{ $reply->created_at }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8">暂无报价</td>
        </tr>
    @endif
</table>
@stop

@section('js')
<script type="text/javascript">
	function countDown(end,start,id){
        var day_elem = $(id).find('.day');
        var hour_elem = $(id).find('.hour');
        var minute_elem = $(id).find('.minute');
        var second_elem = $(id).find('.second');
        var end_time = new Date(end).getTime(),//月份是实际月份-1
        sys_second = (end_time-new Date(start).getTime())/1000;
        var timer = setInterval(function(){
            if (sys_second > 1) {
                sys_second -= 1;
                var day = Math.floor((sys_second / 3600) / 24);
                var hour = Math.floor((sys_second / 3600) % 24);
                var minute = Math.floor((sys_second / 60) % 60);
                var second = Math.floor(sys_second % 60);
                day_elem && $(day_elem).text(day);//计算天
                $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
                $(minute_elem).text(minute<10?"0"+minute:minute);//计算分钟
                $(second_elem).text(second<10?"0"+second:second);//计算秒杀
            } else {
                clearInterval(timer);
            }
        }, 1000);
    }
    $(document).ready(function() {
    	$.get('{{ route('system.datetime') }}', function(datetime) {
    		countDown('{{ str_replace('-', '/', $enquiry->stop_at) }}', datetime.replace(/-/g, '/'), '#colockbox-global');
    		setTimeout(function() {
    			$('.time-loading').hide();
    			$('.time-loaded').show();
    		}, 1000);
        });
    });
</script>
@stop