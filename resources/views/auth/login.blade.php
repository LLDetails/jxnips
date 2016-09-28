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
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/styles/login.css') }}">
</head>
<body>
<div style="position: absolute; z-index:100; height: 240px; top: 50%; margin-top: -140px; left:50%; width: 340px; margin-left: -170px;">
    <div class="panel panel-default animated bounce" id="login-wrapper">
        <div class="panel-body">
            <div style="font-size: 20px; margin-bottom: 20px;"><img style="vertical-align: middle; width:auto; height: 40px;" src="{{ asset('asset/images/logo.jpg') }}" alt="易诚荟在线交易平台" /> <span style="vertical-align: middle">在线交易平台 - 登录</span></div>
            @if ($errors->has('message'))
                <div class="alert alert-dismissible alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>抱歉！</strong> {{ $errors->first('message') }}
                </div>
            @endif
            <form method="post" action="{{ URL::full() }}">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">帐号</span>
                        <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="请填写登录帐号">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">密码</span>
                        <input type="password" name="password" class="form-control" placeholder="请填写登录密码">
                    </div>
                </div>
                {!! csrf_field() !!}
                <button id="login-btn" type="submit" class="btn btn-primary btn-block" data-loading-text="<span class='fa-spin ion-load-c'></span> 登录中...">登 录</button>
                <p style="margin-top: 15px;">
                    <a target="_blank" class="pull-left" href="/doc/readme.php">点击阅读用户须知</a>
                    <a href="{{ route('auth.password.reset') }}" class="pull-right">找回密码</a>
                </p>
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