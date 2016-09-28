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
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/styles/frame.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/styles/bootstrap-table-fixed.css') }}" />
    <script type="text/javascript" src="{{ asset('asset/javascripts/bootstrap-table-fixed.js') }}"></script>
    <style type="text/css">
        .form-inline .form-group {
            display: inline-block;
            *zoom: 1;
            *display: inline;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .form-inline .form-control {
            display: inline-block;
            *zoom: 1;
            *display: inline;
            width: auto;
            vertical-align: middle;
        }
        .form-inline .form-control-static {
            display: inline-block;
            *zoom: 1;
            *display: inline;
        }
        .form-inline .input-group {
            display: inline-table;
            vertical-align: middle;
        }
        .form-inline .input-group .input-group-addon,
        .form-inline .input-group .input-group-btn,
        .form-inline .input-group .form-control {
            width: auto;
        }
        .form-inline .input-group > .form-control {
            width: 100%;
        }
        .form-inline .control-label {
            margin-bottom: 0;
            vertical-align: middle;
        }
        .form-inline .radio,
        .form-inline .checkbox {
            display: inline-block;
            *zoom: 1;
            *display: inline;
            margin-top: 0;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .form-inline .radio label,
        .form-inline .checkbox label {
            padding-left: 0;
        }
        .form-inline .radio input[type="radio"],
        .form-inline .checkbox input[type="checkbox"] {
            position: relative;
            margin-left: 0;
        }
        .form-inline .has-feedback .form-control-feedback {
            top: 0;
        }

        .radio-inline,
        .checkbox-inline {
            position: relative;
            display: inline-block;
            *zoom: 1;
            *display: inline;
            padding-left: 20px;
            margin-bottom: 0;
            font-weight: normal;
            vertical-align: middle;
            cursor: pointer;
        }
    </style>
    @yield('style')
</head>
<body>
<div class="container-fluid">

    @if (session()->has('message.state') and session('message.state') == 'success')
        <div id="success-message-box" class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <span class="fa fa-check-circle"></span> {{ session('message.content') }}
        </div>
    @endif

    @yield('search')
    @yield('main')
    @yield('page')

</div>
</body>

@section('frame-js')
<script type="text/javascript">
    $('.normal-load-btn').on('click', function () {
        var $btn = $(this).button('loading');
    })
    function turnBackFrame(go, hold, src) {
        var _$ = top.$;
        var referFrameSrc = $(window.frameElement).attr('data-refer-frame');
        if (go) {
            referFrameSrc = go;
        } else if (src) {
            referFrameSrc = $(window.frameElement).attr('src');
        }
        var referTitle = $(window.frameElement).attr('data-refer-title');
        var frameId = $(window.frameElement).attr('data-frame');

        if ( !! referTitle) {
            var existsFrame = _$('.content > .frameset > iframe[data-frame="'+referTitle+'"]');
            if (existsFrame.length > 0) {
                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + referTitle + '"]').trigger('click');
                }
                _$('.loading-box').show();
                existsFrame.attr('src', referFrameSrc);
                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + frameId + '"]').remove();
                    $(window.frameElement).remove();
                }
            } else {
                existsFrame = _$('.content > .frameset > iframe[data-frame="home"]').clone(true, true);
                existsFrame.attr('data-frame', referTitle);
                existsFrame.attr('src', referFrameSrc);
                existsFrame.load(function() {
                    _$('.loading-box').hide();
                });
                existsFrame.appendTo(_$('.content > .frameset'));

                var tab = _$('<li class="pull-left frame-tab active"><a href="javascript:void(0)">' + referTitle + '</a> <span class="fa fa-times-circle close-frame"></span></li>');
                tab.attr('data-frame', referTitle);
                tab.appendTo(_$('.content > .tab > ul'));
                tab.click(parent.switchFrame);
                //_$('.content .frame-tab[data-frame="'+frameId+'"]').find('.close-frame').trigger('click');

                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + referTitle + '"]').trigger('click');
                }
                _$('.loading-box').show();
                existsFrame.attr('src', referFrameSrc);
                if ( ! hold) {
                    _$('.content .frame-tab[data-frame="' + frameId + '"]').remove();
                    $(window.frameElement).remove();
                }
            }
        }
    }

    $('.frame-back-link').click(function() {
        var _$ = top.$;
        var referFrameSrc = $(window.frameElement).attr('data-refer-frame');
        var referTitle = $(window.frameElement).attr('data-refer-title');
        var frameId = $(window.frameElement).attr('data-frame');
        if ( !! referTitle) {
            var existsFrame = _$('.content > .frameset > iframe[data-frame="'+referTitle+'"]');
            if (existsFrame.length > 0) {
                _$('.content .frame-tab[data-frame="'+referTitle+'"]').trigger('click');
                _$('.loading-box').show();
                existsFrame.attr('src', referFrameSrc);
                _$('.content .frame-tab[data-frame="'+frameId+'"]').remove();
                $(window.frameElement).remove();
            } else {
                existsFrame = _$('.content > .frameset > iframe[data-frame="home"]').clone(true, true);
                existsFrame.attr('data-frame', referTitle);
                existsFrame.attr('src', referFrameSrc);
                existsFrame.load(function() {
                    _$('.loading-box').hide();
                });
                existsFrame.appendTo(_$('.content > .frameset'));

                var tab = _$('<li class="pull-left frame-tab active"><a href="javascript:void(0)">' + referTitle + '</a> <span class="fa fa-times-circle close-frame"></span></li>');
                tab.attr('data-frame', referTitle);
                tab.appendTo(_$('.content > .tab > ul'));
                tab.click(parent.switchFrame);
                //_$('.content .frame-tab[data-frame="'+frameId+'"]').find('.close-frame').trigger('click');

                _$('.content .frame-tab[data-frame="'+referTitle+'"]').trigger('click');
                _$('.loading-box').show();
                existsFrame.attr('src', referFrameSrc);
                _$('.content .frame-tab[data-frame="'+frameId+'"]').remove();
                $(window.frameElement).remove();
            }
        } else {
            _$('.content .frame-tab[data-frame="'+frameId+'"]').find('.close-frame').trigger('click');
        }
    });

//    $(window.frameElement).load(function() {
//        //console.log($(this));
//
//    });

    $('.frame-link').click(function() {

        var confirmData = $(this).attr('data-confirm');
        if (confirmData) {
            if(!confirm(confirmData)) {
                return false;
            }
        }

        var _$ = top.$;
        var url = $(this).attr('data-frame-src');
        var title = $(this).attr('data-frame-title');
        var id = title;

        //var currentFrameSrc = $(window.frameElement).attr('src');
        var currentFrameSrc =  $(window.frameElement).get(0).contentWindow.location.href;
        var currentFrameTitle = $(window.frameElement).attr('data-frame');

        var parentDocument = _$(parent.document);

        var existsFrame = parentDocument.find('.content > .frameset > iframe[data-frame="'+id+'"]');
        if (existsFrame.length == 0) {
            parentDocument.find('.loading-box').show();
            existsFrame = parentDocument.find('.content > .frameset > iframe[data-frame="home"]').clone(true, true);
            existsFrame.attr('data-frame', id);
            existsFrame.attr('src', url);
            existsFrame.attr('data-refer-frame', currentFrameSrc);
            existsFrame.attr('data-refer-title', currentFrameTitle);
            existsFrame.appendTo(parentDocument.find('.content > .frameset'));
            existsFrame.load(function() {
                parentDocument.find('.loading-box').hide();
            });

            var tab = _$('<li class="pull-left frame-tab active"><a href="javascript:void(0)">' + title + '</a> <span class="fa fa-times-circle close-frame"></span></li>');
            tab.attr('data-frame', id);
            tab.appendTo(parentDocument.find('.content > .tab > ul'));
            tab.click(parent.switchFrame);
            tab.find('.close-frame').click(parent.closeFrame);
        }

        parentDocument.find('.frame-tab').removeClass('active');
        parentDocument.find('.frame-tab[data-frame="'+id+'"]').addClass('active');
        parentDocument.find('.content > .frameset > iframe').hide();
        existsFrame.show();
        parentDocument.find('.loading-box').show();
        existsFrame.get(0).src = url;
        top.compareTabWidth();
        return false;
    });
</script>
@if (session()->has('tip_message'))
    <script type="text/javascript">
        top.showTip('{{ session('tip_message.content') }}', '{{ session('tip_message.state') }}');
        @if (session('tip_message.hold'))
            turnBackFrame(null, true);
        @else
            @if (session('tip_message.go'))
                turnBackFrame('{{ session('tip_message.go') }}', false);
            @else
                turnBackFrame(null, false);
            @endif
        @endif
    </script>
@endif
@show
@yield('js')
@yield('extjs')
</html>