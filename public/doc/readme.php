<?php
    $index_data = file_get_contents(__DIR__.'/index.txt');
    $index_data = str_replace("\r",'',$index_data);
    $index_data = explode("\n",$index_data);
    $index = [];
    $default_title = null;
    foreach ($index_data as $key => $item) {
        $item = trim($item);
        list($v, $k) = explode('@@@', $item);
        $index[$v] = $k;
        if ($key == 0) {
            $default_title = $v;
            $default_content = $k;
        }
    }
    if (isset($_GET['content'])) {
        $title = trim($_GET['content']);
    }
    if (empty($title) or !in_array($title, array_keys($index))) {
        $title = $default_title;
    }
    $content_data = file_get_contents(__DIR__.'/'.$index[$title]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>易诚荟在线交易平台 - <?php echo $title ?></title>
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
        #sticky > a.active {
            z-index:0;
        }
        .content * {
            line-height: 24px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row" style="margin-top: 30px; margin-bottom:20px;">
        <div class="col-xs-10 col-md-10">
            <img height="40" src="/asset/images/logo.jpg" />
            <h4>诚邀您加入易诚荟交易平台</h4>
        </div>
        <div class="col-xs-2 col-md-2">
            <a style="margin-top:45px;" href="/login" class="btn btn-larger btn-primary pull-right"><span class="fa fa-user"></span> 登录</a>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 col-md-3">
            <div class="list-group" id="sticky">
                <?php foreach($index as $m_title => $f): ?>
                <a href="/doc/readme.php?content=<?php echo urlencode($m_title) ?>" class="list-group-item <?php if($title == $m_title):?>active<?php endif ?>"><?php echo $m_title ?></a>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col-xs-9 col-md-9">
            <div class="panel panel-default">
                <div class="panel-body content">
                    <h3 class="text-center" style="margin-bottom:30px;">易诚荟交易平台 — <?php echo $title ?></h3>
                    <?php echo $content_data ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var obj = $('#sticky');
        var width = obj.parent().width();
        //obj.width(width);
        var offset = obj.offset();
        var topOffset = offset.top;
        var marginTop = 0;

        $(window).scroll(function() {
            var scrollTop = $(window).scrollTop();

            if (scrollTop >= topOffset){

                obj.css({
                    marginTop: 0,
                    marginLeft: 0,
                    top:0,
                    width:width,
                    position: 'fixed'
                });
            }

            if (scrollTop < topOffset){

                obj.css({
                    marginTop: marginTop,
                    marginLeft: 0,
                    width:'auto',
                    position: 'relative'
                });
            }
        });
    });
</script>
</html>