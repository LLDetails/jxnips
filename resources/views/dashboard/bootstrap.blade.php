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
        <link rel="stylesheet" type="text/css" href="{{ asset('asset/styles/dashboard.css') }}">
        <style type="text/css">
            @keyframes rotation {
                0% {transform:rotate(0deg);}
                100% {transform:rotate(360deg);}
            }
            @-webkit-keyframes rotation {
                0% {-webkit-transform:rotate(0deg);}
                100% {-webkit-transform:rotate(360deg);}
            }
            @-moz-keyframes rotation {
                0% {-moz-transform:rotate(0deg);}
                100% {-moz-transform:rotate(360deg);}
            }

            .loading-box {
                width: 200px;
                height: 100px;
                margin-top: -50px;
                margin-left: -100px;
                z-index: 1000;
                top: 50%;
                left: 50%;
                position: absolute;
                display: none;
            }

            .loading {
                animation:rotation 2.5s linear infinite;
                -webkit-animation:rotation 2.5s linear infinite;
                -moz-animation:rotation 2.5s linear infinite;
            }
        </style>
    </head>
    <body>
        <table id="main" style="border:0">
            <tr class="top">
                <td class="nav">
                    <img style="margin-top:10px; margin-left:10px;" height="60" src="{{ asset('asset/images/dlogo.jpg') }}" />
                </td>
                <td class="nav">
                    <div class="site-name pull-left">
                        <h3>易诚荟</h3>
                        <p>在线交易平台</p>
                    </div>
                    <div class="user-info pull-right">
                        <div class="media dropdown-toggle" data-toggle="dropdown" role="button">
                            <div class="media-left">
                                <a href="#">
                                    <img class="media-object" height="40" src="{{ asset('asset/images/avatar.png') }}" alt="avatar">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    @if (auth()->user()->type == 'staff')
                                        {{ session('user_profile')->realname }}
                                    @else
                                        {{ session('user_profile')->name }}
                                    @endif
                                </h4>
                                {{ auth()->user()->role->name }}
                                @if ( ! empty($company))
                                    <br />{{ $company->name }}
                                @endif
                            </div>
                            <div class="media-right">
                                <span class="fa fa-caret-down"></span>
                            </div>
                        </div>
                        <ul class="dropdown-menu" role="menu" style="top:78px">
                            <div class="arrow"></div>
                            {{--<li><a href="#"><span class="fa fa-user"></span> 帐号信息</a></li>--}}
                            {{--<li class="divider"></li>--}}
                            <li><a id="reset-password" href="javascript:void(0)"><span class="fa fa-key"></span> 修改密码</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ route('auth.logout') }}"><span class="fa fa-sign-out"></span> 安全退出</a></li>
                        </ul>
                    </div>
                    @if (false)
                    <div class="notification pull-right">
                        <span class="ion-ios-bell-outline" data-toggle="dropdown" style="font-size:34px; margin-top:5px;"></span>
                        <span class="badge" data-toggle="dropdown" style="background:#F50000; margin-left:-15px;">0</span>
                        <ul class="dropdown-menu" role="menu" style="top:78px; right:-85px">
                            <div class="arrow"></div>
                            <li>
                                <a data-frame-title="需求管理" data-frame-src="#" href="javascript:void(0);" class="frame-link">
                                    <div class="media">
                                        <div class="media-left">
                                            <span class="iconfont" style="font-size:40px; color:#00D04E">&#xe602;</span>
                                        </div>
                                        <div class="media-body" style="padding-top:10px;">
                                            受理采购需求
                                            <p>有 <span style="color:#f50000">0</span> 条新的采购需求</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a data-frame-title="采购管理" data-frame-src="#" href="javascript:void(0);" class="frame-link">
                                    <div class="media">
                                        <div class="media-left">
                                            <span class="iconfont" style="font-size:40px; color:#5D74E6">&#xe603;</span>
                                        </div>
                                        <div class="media-body" style="padding-top:10px;">
                                            采购信息
                                            <p>有 <span style="color:#f50000">0</span> 条采购信息需处理</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                    <div class="notification pull-right" id="phone" data-toggle="modal" data-target="#phoneModal">
                        <span class="ion-iphone" style="font-size:36px; margin-top:4px;"></span>
                        @if (empty(auth()->user()->phone))
                            <span class="fa fa-exclamation-triangle" style="color:#fcce00;"></span>
                        @else
                            <span class="fa fa-check-circle" style="color:#00b64a;"></span>
                        @endif
                        绑定手机
                    </div>
                    <div class="notification pull-right" style="padding-top:40px;">
                        <a class="btn btn-warning btn-xs" target="_blank" href="/doc/readme.php"><span class="glyphicon glyphicon-warning-sign"></span> 用户须知</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="side">
                    <ul>
                        @if ( ! empty($menu))
                            @foreach($menu as $item)
                                @if ($item['type'] == 'parent')
                                    <li>
                                        <a href="javascript:void(0)">
                                            {!! $item['icon'] !!}
                                            <p>{{ $item['title'] }}</p>
                                        </a>
                                        <div class="sub-menu">
                                            <ul>
                                                @if ( ! empty($item['item']) and count($item['item']) > 0)
                                                    @foreach($item['item'] as $sub_item)
                                                        <li>
                                                            <a
                                                                @if (Route::has($sub_item['route']))
                                                                data-href="{{ route($sub_item['route']) }}"
                                                                class="frame-entry"
                                                                data-frame="{{ route($sub_item['route']) }}"
                                                                href="javascript:void(0)"
                                                                @else
                                                                href="javascript:void(0)"
                                                                @endif
                                                            >
                                                                {!! $sub_item['icon'] !!}
                                                                <p>{{ $sub_item['title'] }}</p>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </li>
                                @else
                                    <li>
                                        <a
                                            @if (Route::has($item['route']))
                                            data-href="{{ route($item['route']) }}"
                                            class="frame-entry"
                                            data-frame="{{ route($item['route']) }}"
                                            href="javascript:void(0)"
                                            @else
                                            href="javascript:void(0);"
                                            @endif
                                        >
                                            {!! $item['icon'] !!}
                                            <p>{{ $item['title'] }}</p>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </td>
                <td class="content" valign="top">
                    <div class="tab">
                        <div class="ctrl ctrl-left"><a href="#"><span class="fa fa-angle-left"></span></a></div>
                        <div class="ctrl ctrl-right"><a href="#"><span class="fa fa-angle-right"></span></a></div>
                        <ul>
                            <li class="pull-left frame-tab active" data-frame="home"><a href="javascript:void(0)">首页</a></li>
                            {{--<li class="pull-left active"><a href="#">供应信息</a> <span class="fa fa-times-circle"></span></li>--}}
                        </ul>
                    </div>
                    <div class="loading-box">
                        <img width="40" class="loading" src="{{ asset('asset/images/logo.png') }}" />
                        加载中,请稍后...
                    </div>
                    <div class="frameset">
                        <iframe frameborder="0" data-frame="home" src="{{ route('dashboard.welcome') }}"></iframe>
                    </div>
                </td>
            </tr>
        </table>

        <form method="post" action="{{ route('auth.password') }}">
            {!! csrf_field() !!}
            <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">修改登录密码</h4>
                        </div>
                        <div class="modal-body">
                            @if (session()->has('message.state') and session('message.state') == 'error' and session('action') == 'password')
                                <p class="text-danger"><span class="fa fa-info-circle"></span> {{ session('message.content') }}</p>
                            @endif
                            @if ($errors->has('old_password') and session('action') == 'password')
                                <div class="form-group has-error">
                                    <label class="text-danger" for="old_password">原密码</label>
                                    <input type="password" name="old_password" value="" id="old_password" class="form-control" placeholder="原登录密码">
                                    <span id="helpBlock" class="help-block text-danger">{{ $errors->first('old_password') }}</span>
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="old_password">原密码</label>
                                    <input type="password" name="old_password" id="old_password" class="form-control" placeholder="原登录密码">
                                </div>
                            @endif
                            @if ($errors->has('password') and session('action') == 'password')
                                <div class="form-group has-error">
                                    <label class="text-danger" for="password">新密码</label>
                                    <input type="password" name="password" value="" id="password" class="form-control" placeholder="新登录密码">
                                    <span id="helpBlock" class="help-block text-danger">{{ $errors->first('password') }}</span>
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="password">新密码</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="新登录密码">
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="password_confirmation">确认密码</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="再次输入密码">
                            </div>
                            <input type="hidden" name="id" value="{{ old('id') }}" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="submit" class="btn btn-primary">修改密码</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form>
            <div class="modal fade" id="phoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">绑定手机号码</h4>
                        </div>
                        <div class="modal-body">
                            @if (session()->has('message.state') and session('message.state') == 'error' and session('action') == 'password')
                                <p class="text-danger"><span class="fa fa-info-circle"></span> {{ session('message.content') }}</p>
                            @endif
                            @if ($errors->has('phone') and session('action') == 'phone')
                                <div class="form-group has-error">
                                    <label class="text-danger" for="phone-field">手机号码</label>
                                    <input type="text" name="phone" value="{{ auth()->user()->phone }}" id="phone-field" class="form-control" placeholder="手机号码">
                                    <span id="helpBlock" class="help-block text-danger">{{ $errors->first('phone') }}</span>
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="phone-field">手机号码</label>
                                    <input type="text" name="phone" value="{{ auth()->user()->phone }}" id="phone-field" class="form-control" placeholder="手机号码">
                                </div>
                            @endif
                            @if ($errors->has('phone_vcode') and session('action') == 'phone')
                                <div class="form-group has-error">
                                    <label class="text-danger" for="phone-vcode">短信验证码</label>
                                    <div class="input-group">
                                        <input type="text" name="phone_vcode" value="" id="phone-vcode" class="form-control" placeholder="短信验证码">
                                <span class="input-group-btn">
                                    <button id="send" data-loading-text="发送中..." data-url="{{ route('user.phone.vcode') }}" class="btn btn-info" type="button">发送短信验证码</button>
                                </span>
                                        <span style="display: none" class="input-group-addon"><strong id="second">60</strong>秒后可重新发送</span>
                                    </div>
                                    <span id="helpBlock" class="help-block text-danger">{{ $errors->first('phone_vcode') }}</span>
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="phone-vcode">短信验证码</label>
                                    <div class="input-group">
                                        <input type="text" name="phone_vcode" value="" id="phone-vcode" class="form-control" placeholder="短信验证码">
                                <span class="input-group-btn">
                                    <button id="send" data-loading-text="发送中..." data-url="{{ route('user.phone.vcode') }}" class="btn btn-info" type="button">获取短信验证码</button>
                                </span>
                                        <span id="sent-sms-msg" class="input-group-addon"><strong id="second">60</strong>秒后可重新发送</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="unbind" data-url="{{ route('user.phone.unbind') }}" class="btn btn-danger pull-left">解除绑定</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="button" id="bind-phone" data-token="{{ csrf_token() }}" data-loading-text="验证中..." data-url="{{ route('user.phone.bind') }}" class="btn btn-primary">绑定手机</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </body>
    <script type="text/javascript">

        function compareTabWidth() {
            var wrapperWidth = $('.tab').width() - 30;
            var frameTabs = $('.frame-tab');
            var frameTabsWidth = 0;
            $.each(frameTabs, function(index, ele) {
                frameTabsWidth += 2 + $(ele).width() + 30;
            });

            var disparities = wrapperWidth - frameTabsWidth;
            if (disparities < 0) {
                $('.tab > ul').css('margin-left', disparities);
            }
        }

        function showTip(content, state) {
            var tpl = '<div class="msg-tip alert alert-dismissible alert-'+state+'">' +
                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
                        content +
                      '</div>';
            //var p = $('<p class="msg-tip bg-'+state+'"></p>');
            var p = $(tpl);
            p.html(content);
            var width = p.width();
            p.css('margin-left', -width/2);
            p.appendTo($('body'));

            setTimeout(function() {
                p.remove();
            }, 3000);
        }

        //showTip('测试Tips', 'success');

        function closeFrame() {
            var id = $(this).parent().attr('data-frame');

            var closedTab = $('.frame-tab[data-frame="'+id+'"]');
            closedTab.remove();
            $('.content > .frameset > iframe[data-frame="'+id+'"]').remove();

            $('.frame-tab').removeClass('active');
            var actTab = $('.frame-tab').last();
            actTab.addClass('active');
            $('.content > .frameset > iframe').hide();
            $('.content > .frameset > iframe[data-frame="'+actTab.attr('data-frame')+'"]').show();

            compareTabWidth();
        }

        function switchFrame() {
            var id = $(this).attr('data-frame');
            var actFrame = $('.content > .frameset > iframe[data-frame="'+id+'"]');
            var actTab = $('.frame-tab[data-frame="'+id+'"]');

            $('.frame-tab').removeClass('active');
            $('.content > .frameset > iframe').hide();

            actFrame.show();
            if (id == 'home') {
                actFrame.get(0).src = actFrame.get(0).src;
            }
            actTab.addClass('active');
        }

        $(document).ready(function() {

            $('#sent-sms-msg').hide();

            $('#send').click(function() {
                var $btn = $(this).button('loading');
                var url = $(this).attr('data-url');
                var phone = $('#phone-field').val();
                var self = $(this);
                $.get(url, {"phone": phone}, function(res) {
                    $btn.button('reset');
                    if (typeof res['state'] == 'undefined') {
                        alert(res);
                        return false;
                    }
                    if (res['state'] == 'error') {
                        alert(res['message'] + res['code']);
                        return false;
                    }
                    self.parent().hide();
                    var s = 60;
                    function secondDown() {
                        if (s == 0) {
                            $('#second').parent().hide();
                            $('#send').parent().show();
                        } else {
                            $('#second').html(s);
                            s -= 1;
                            setTimeout(secondDown, 1000);
                        }
                    }
                    $('#second').parent().show(secondDown);
                });
            });

            $('#bind-phone').click(function() {
                var $btn = $(this).button('loading');
                var phone = $('#phone-field').val();
                var phone_vcode = $('#phone-vcode').val();
                var url = $(this).attr('data-url');
                var token = $(this).attr('data-token');
                $.post(url, {"phone": phone, "phone_vcode": phone_vcode, "_token": token}, function(res) {
                    $btn.button('reset');
                    if (typeof res['state'] == 'undefined') {
                        alert(res);
                        return false;
                    }
                    if (res['state'] == 'error') {
                        alert(res['msg']);
                        return false;
                    }
                    alert(res['msg']);
                    window.location.href = window.location.href;
                });
            });

            $('#unbind').click(function() {
                var url = $(this).attr('data-url');
                $.get(url, function(res) {
                    if (typeof res['state'] == 'undefined') {
                        alert(res);
                        return false;
                    }
                    if (res['state'] == 'error') {
                        alert(res['msg']);
                        return false;
                    }
                    alert(res['msg']);
                    window.location.href = window.location.href;
                });
            });

            compareTabWidth();

            $('div.ctrl-left').click(function() {
                var leftOffset = $('.tab > ul').css('margin-left');
                var rightOffset = $('.tab > ul').css('margin-right');
                leftOffset = parseInt(leftOffset);
                leftOffset = Number(leftOffset);
                rightOffset = parseInt(rightOffset);
                rightOffset = Number(rightOffset);
                if (isNaN(leftOffset)) {
                    leftOffset = 0;
                }
                if (isNaN(rightOffset)) {
                    rightOffset = 0;
                }
                if (leftOffset < 0) {
                    var newLeftOffset = leftOffset + 40;
                    if (newLeftOffset > 0) {
                        newLeftOffset = 0;
                    }
                    $('.tab > ul').css('margin-left', newLeftOffset);
                    $('.tab > ul').css('margin-right', rightOffset - 40);
                }
            });

            $('div.ctrl-right').click(function() {
                var leftOffset = $('.tab > ul').css('margin-left');
                var rightOffset = $('.tab > ul').css('margin-right');
                leftOffset = parseInt(leftOffset);
                leftOffset = Number(leftOffset);
                rightOffset = parseInt(rightOffset);
                rightOffset = Number(rightOffset);
                if (isNaN(leftOffset)) {
                    leftOffset = 0;
                }
                if (isNaN(rightOffset)) {
                    rightOffset = 0;
                }
                if (rightOffset < 0) {
                    var newRightOffset = rightOffset + 40;
                    if (newRightOffset > 0) {
                        newRightOffset = 0;
                    }
                    $('.tab > ul').css('margin-right', newRightOffset);
                    $('.tab > ul').css('margin-left', leftOffset - 40);
                }
            });

            $('.side ul li').mouseenter(function() {

                var width = $(this).width(),
                        height = $(this).height(),
                        body = $('body'),
                        subMenu = $(this).find('.sub-menu');

                var arrow = subMenu.find('.arrow');
                if (arrow.length == 0) {
                    arrow = $('<div class="arrow"></div>');
                    arrow.appendTo(subMenu);
                }

                var selfPosition = $(this).offset();
                var subMenuHeight = subMenu.height();
                if (selfPosition.top +  subMenuHeight <= body.height()) {
                    subMenu.css('left', width).show();
                    arrow.css('top', Math.round(height / 2 - arrow.height()/2));
                } else {
                    subMenu.css({'left': width, 'top': 0 - subMenuHeight + height}).show();
                    arrow.css('top', Math.round(subMenuHeight - height / 2 + arrow.height()/2));
                }
            }).mouseleave(function() {
                $(this).find('.sub-menu').hide();
            });

            function resizeIFrame() {
                var _height = $('.content > .tab').height();
                var width = $('.content').width();
                var height = $('.content').height();
                $('.content > .frameset > iframe').height(height - _height - 10).width(width).attr('frameborder', 0);
            }
            resizeIFrame();
            $(window).resize(resizeIFrame);

            $('.frame-tab').click(switchFrame);

            $('a.frame-entry').click(function() {

                var title = $(this).find('p').text();
                //var id = $(this).attr('data-frame');
                //var src = $(this).attr('data-frame');
                var src = $(this).attr('data-href');

                var existsFrame = $('.content > .frameset > iframe[data-frame="'+title+'"]');
                $('.loading-box').show();
                if (existsFrame.length == 0) {
                    existsFrame = $('.content > .frameset > iframe[data-frame="home"]').clone(true, true);
                    existsFrame.attr('data-frame', title);
                    existsFrame.attr('src', src);
                    existsFrame.load(function() {
                        $('.loading-box').hide();
                    });
                    existsFrame.appendTo($('.content > .frameset'));

                    var tab = $('<li class="pull-left frame-tab active"><a href="javascript:void(0)">' + title + '</a> <span class="fa fa-times-circle close-frame"></span></li>');
                    tab.attr('data-frame', title);
                    tab.appendTo($('.content > .tab > ul'));
                    tab.click(switchFrame);
                    tab.find('.close-frame').click(closeFrame);
                }

                $('.frame-tab').removeClass('active');
                $('.frame-tab[data-frame="'+title+'"]').addClass('active');
                $('.content > .frameset > iframe').hide();
                existsFrame.show();
                if (src == existsFrame.get(0).src) {
                    existsFrame.get(0).src = existsFrame.get(0).src;
                } else {
                    existsFrame.get(0).src = src;
                }
                compareTabWidth();
                return false;
            });

            $('.frame-link').click(function() {

                var confirmData = $(this).attr('data-confirm');
                if (confirmData) {
                    if(!confirm(confirmData)) {
                        return false;
                    }
                }

                var title = $(this).attr('data-frame-title');
                var src = $(this).attr('data-frame-src');

                var existsFrame = $('.content > .frameset > iframe[data-frame="'+title+'"]');
                $('.loading-box').show();
                if (existsFrame.length == 0) {
                    existsFrame = $('.content > .frameset > iframe[data-frame="home"]').clone(true, true);
                    existsFrame.attr('data-frame', title);
                    existsFrame.attr('src', src);
                    existsFrame.load(function() {
                        $('.loading-box').hide();
                    });
                    existsFrame.appendTo($('.content > .frameset'));

                    var tab = $('<li class="pull-left frame-tab active"><a href="javascript:void(0)">' + title + '</a> <span class="fa fa-times-circle close-frame"></span></li>');
                    tab.attr('data-frame', title);
                    tab.appendTo($('.content > .tab > ul'));
                    tab.click(switchFrame);
                    tab.find('.close-frame').click(closeFrame);
                }

                $('.frame-tab').removeClass('active');
                $('.frame-tab[data-frame="'+title+'"]').addClass('active');
                $('.content > .frameset > iframe').hide();
                existsFrame.show();
                existsFrame.get(0).src = existsFrame.get(0).src;
                compareTabWidth();
                return false;
            });

            $('#reset-password').click(function() {
                $('#passwordModal').modal('show');
            });

        });
    </script>
    @if ((count($errors->all()) > 0 or session('message.state') == 'error') and session()->has('action'))
        <script type="text/javascript">
            $('#'+'{{ session('action') }}'+'Modal').modal('show');
        </script>
    @endif
    @if (session()->has('message.state') and session('message.state') == 'success')
        <script type="text/javascript">
            alert('{{ session('message.content') }}');
            window.location.href = '{{ route('auth.logout') }}';
        </script>
    @endif
</html>
