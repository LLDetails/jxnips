<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>易诚荟在线交易平台 - 登录</title>
    <link href="{{ asset('asset/favicon.ico') }}" type="image/x-icon" rel="icon">
    <link href="{{ asset('asset/favicon.ico') }}" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/ionicons/css/ionicons.min.css') }}">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('asset/vendor/html5shiv/dist/html5shiv.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/respond/dist/respond.min.js') }}"></script>
    <![endif]-->
    <script type="text/javascript" src="{{ asset('asset/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('asset/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/vendor/animate.css/animate.css') }}">
    <style type="text/css">

    </style>
</head>
<body>
<div style="position: absolute; z-index:100; height: 240px; top: 50%; margin-top: -140px; left:50%; width: 340px; margin-left: -170px;">
    <div class="panel panel-default" id="login-wrapper" style="border: none">
        <div class="panel-body">
            <div style="font-size: 20px; margin-bottom: 20px;"><img style="vertical-align: middle; width:auto; height: 40px;" src="{{ asset('asset/images/logo.jpg') }}" alt="易诚荟在线交易平台" /> <span style="vertical-align: middle">在线交易平台 - 登录</span></div>
            @if ($errors->has('msg'))
                <div class="alert alert-dismissible alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>抱歉！</strong> {{ $errors->first('msg') }}
                </div>
            @endif
            <form method="post" action="{{ URL::full() }}">
                <p class="text-info"><span class="fa fa-info"></span> 验证码短信已发送至尾号 {{ substr(session('forget_user')->phone, -4) }} 的手机</p>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">验证码</span>
                        <input type="text" name="vcode" value="{{ old('vcode') }}" class="form-control" placeholder="请填写短信中收到的验证码">
                    </div>
                </div>
                <input type="hidden" name="step" value="2" />
                {!! csrf_field() !!}
                <button id="login-btn" type="submit" class="btn btn-info btn-block" data-loading-text="<span class='fa-spin ion-load-c'></span> 提交中...">下一步</button>
                <a href="{{ route('auth.login') }}" class="btn btn-warning btn-block" >返回登录</a>
            </form>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        if (window.top != window) {
            window.top.location.href = window.location.href;
        }
        $('#login-btn').click(function() {
            $(this).button('loading');
        });
    });
</script>
</html>