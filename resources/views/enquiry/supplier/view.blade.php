@extends('layout.frame')

@section('main')
@if ($show_form)
<form method="post" action="{{ URL::full() }}">
{!! csrf_field() !!}
@endif
<div class="panel panel-primary">
	<div class="panel-heading" style="height: 46px;">
        <h3 class="panel-title">
            <strong class="pull-left" style="margin-top:6px;">重要提示：提交报价前请确认能满足各项信息要求！</strong>
            <span class="pull-right time-loading" style="padding-top:5px;">系统时间获取中...</span>
            <span class="pull-right time-loaded" style="display:none;padding-top:5px;">报价倒计时：<span class="colockbox" id="colockbox-global"><span class="day">0</span>天 <span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span></span></span>
        </h3>
    </div>
	<table class="table">
		<tr>
			<td width="130">物料名称：</td>
            <td>{{ $enquiry->title }}</td>
		</tr>
        <tr>
            <td>质量要求：</td>
            <td>
                {{ $enquiry->quality }}
            </td>
        </tr>
        <tr>
            <td>质量修改意见：</td>
            <td>
                @if ($show_form)
                    <textarea name="quality" style="width:100%; height:50px;">{{ old('quality') }}</textarea>
                    @if ($errors->has('quality'))
                        <p style="color:red">{{ $errors->first('quality') }}</p>
                    @endif
                @else
                    <span style="color:blue">{{ empty($my_reply->quality) ? '无' : $my_reply->quality }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>数量：{{ strval((float)$enquiry->quantity) }} 吨</td>
            <td>船期：{{ $enquiry->sailing_date }}</td>
        </tr>
        <tr>
            <td>交货地点及方式：</td>
            <td>{{ $enquiry->terms_of_delivery }}</td>
        </tr>
        <tr>
            <td>付款方式：</td>
            <td>
                @if ($show_form)
                    <textarea name="payment" style="width:100%; height:30px;">{{ old('payment') }}</textarea>
                    @if ($errors->has('payment'))
                        <p style="color:red">{{ $errors->first('payment') }}</p>
                    @endif
                @else
                    <span style="color:blue">{{ empty($my_reply->payment) ? '无' : $my_reply->payment }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>价格（人民币）：</td>
            <td>
                @if ($show_form)
                    <input type="text" name="price" value="{{ old('price') }}" /> 元/吨
                    @if ($errors->has('price'))
                        <p style="color:red">{{ $errors->first('price') }}</p>
                    @endif
                @else
                    <span style="color:blue">{{ empty($my_reply->price) ? '无' : strval((float)$my_reply->price).' 元/吨' }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>报价有效期：</td>
            <td>
                @if ($show_form)
                    报价截止后 <input type="text" name="price_validity" value="{{ old('price_validity') }}" /> 小时
                    @if ($errors->has('price_validity'))
                        <p style="color:red">{{ $errors->first('price_validity') }}</p>
                    @endif
                @else
                    <span style="color:blue">{{ empty($my_reply->price_validity) ? '无' : '报价截止后 '.strval((float)$my_reply->price_validity).' 小时' }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>补充说明：</td>
            <td>
                @if ($show_form)
                    <textarea name="remark" placeholder="若没有请留空" style="width:100%; height:40px;">{{ old('remark') }}</textarea>
                    @if ($errors->has('remark'))
                        <p style="color:red">{{ $errors->first('remark') }}</p>
                    @endif
                @else
                    <span style="color:blue">{{ empty($my_reply->remark) ? '无' : $my_reply->remark }}</span>
                @endif
            </td>
        </tr>
        @if ($show_form)
        <tr>
            <td colspan="2" class="text-center"><button class="btn btn-success" type="submit"><span class="ion-speakerphone"></span> 提交报价</button></td>
        </tr>
        @endif
  	</table>
</div>
@if ($show_form)
</form>
@endif
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