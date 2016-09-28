<?php
    $index_data = file_get_contents(base_path('public').'/doc/index.txt');
    $index_data = str_replace("\r",'',$index_data);
    $index_data = explode("\n",$index_data);
    $index = [];
    foreach ($index_data as $key => $item) {
        $item = trim($item);
        list($v, $k) = explode('@@@', $item);
        $index[$v] = file_get_contents(base_path('public').'/doc/'.$k);
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>易诚荟在线交易平台 - 用户须知</title>
    <link href="/asset/favicon.ico" type="image/x-icon" rel="icon">
    <link href="/asset/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/asset/vendor/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/asset/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/asset/vendor/ionicons/css/ionicons.min.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/asset/vendor/html5shiv/dist/html5shiv.min.js"></script>
    <script type="text/javascript" src="/asset/vendor/respond/dist/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/asset/vendor/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="/asset/vendor/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/asset/vendor/animate.css/animate.css">
    <style type="text/css">
        .content * {
            line-height: 24px;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="row" style="margin-top:30px;">
        <div class="col-xs-12 col-md-12">
            <ul class="nav nav-tabs nav-justified">
                <?php $key = 0 ?>
                @foreach($index as $title => $data)
                <li role="presentation" @if($key==0)class="active"@endif><a class="switch" data-key="{{ $key }}" href="javascript:void(0)">{{ $title }}</a></li>
                <?php $key += 1; ?>
                @endforeach
            </ul>

            <?php $dkey = 0 ?>
            @foreach($index as $title => $data)
            <div @if($dkey>0)style="display:none"@endif class="panel panel-default" id="d-{{ $dkey }}">
                <div class="panel-body content" style="height:500px; overflow-y:scroll">
                    <h3 class="text-center" style="margin-bottom:30px;">易诚荟交易平台 — {{ $title }}</h3>
                    {!! $data !!}
                </div>
            </div>
            <?php $dkey += 1; ?>
            @endforeach
            <form method="post" action="{{ URL::current() }}">
                {!! csrf_field() !!}
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" style="margin-right: 30px;">同意条款</button>
                    <a href="{{ route('auth.logout') }}" class="btn btn-default">拒绝</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        $('a.switch').click(function() {
            $('ul.nav li.active').removeClass('active');
            $(this).parent().addClass('active');
            var key = $(this).attr('data-key');
            $('.panel').hide();
            $('#d-'+key).show();
        });
    });
</script>
</html>