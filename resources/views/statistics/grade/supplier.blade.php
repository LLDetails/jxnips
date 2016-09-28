@extends('layout.frame')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/datetimepicker/datetimepicker.css') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/datetimepicker/datetimepicker.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/select2/dist/css/select2.min.css?v=1') }}">
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/select2/dist/js/i18n/zh-CN.js') }}"></script>
@endsection

@section('search')
    <form class="form-inline" method="get" action="{{ URL::full() }}">
        <div class="form-group">
            <div class="input-group input-group-sm">
                <div class="input-group-addon">月份</div>
                <input type="text" style="width: 140px" class="form-control input-sm date" name="date" value="{{ Input::get('date') }}" placeholder="起始月份">
                <div class="input-group-addon">往后6个月</div>
            </div>
            <div class="input-group input-group-sm">
                <select name="type" class="form-control input-sm">
                    <option value="">请选择查询对象</option>
                    @foreach ($grade_types as $k=>$type)
                        <option @if(Input::get('type') == $k) selected @endif value="{{ $k }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><span class="fa fa-search"></span> 查询</button>
    </form>
@endsection

@section('main')
    <table style="margin-top: 10px;" class="list table table-striped table-bordered table-hover">
        <tr>
            <th>供应商</th>
            <th>付款及时率</th>
            <th>卸货及时率</th>
            <th>化验及时率</th>
        </tr>
        @foreach ($data as $k=>$item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ strval((float)$item->sgv1) }} 星</td>
                <td>{{ strval((float)$item->sgv2) }} 星</td>
                <td>{{ strval((float)$item->sgv3) }} 星</td>
            </tr>
        @endforeach
    </table>
@stop


@section('js')
    <script type="text/javascript">
        $('input.date').datetimepicker({
            lang: 'ch',
            format:'Y-m'
        });
        $(document).ready(function() {
            $(".select2").select2({
                language: "zh-CN"
            });
        });
    </script>
@stop