(function() {
    (function($) {
        return $.fn.fixedHeader = function(options) {
            var config;
            config = {
                topOffset: 0,
                bgColor: "#EEEEEE"
            };
            if (options) {
                $.extend(config, options);
            }
            return this.each(function() {
                var $head, $win, headTop, isFixed, o, processScroll, ww;
                processScroll = function() {
                    var headTop, i, isFixed, scrollTop, t;
                    if (!o.is(":visible")) {
                        return;
                    }
                    i = void 0;
                    scrollTop = $win.scrollTop();
                    t = $head.length && $head.offset().top - config.topOffset;
                    if (!isFixed && headTop !== t) {
                        headTop = t;
                    }
                    if (scrollTop >= headTop && !isFixed) {
                        isFixed = 1;
                    } else {
                        if (scrollTop <= headTop && isFixed) {
                            isFixed = 0;
                        }
                    }
                    if (isFixed) {
                        return $("thead.header-copy", o).removeClass("hide");
                    } else {
                        return $("thead.header-copy", o).addClass("hide");
                    }
                };
                o = $(this);
                $win = $(window);
                $head = $("thead.header", o);
                isFixed = 0;
                headTop = $head.length && $head.offset().top - config.topOffset;
                $win.on("scroll", processScroll);
                $head.on("click", function() {
                    if (!isFixed) {
                        return setTimeout((function() {
                            return $win.scrollTop($win.scrollTop() - 47);
                        }), 10);
                    }
                });
                var copiedHead = $head.clone(true).
                    removeClass("header").
                    addClass("header-copy header-fixed");

                copiedHead.find('tr').each(function(i, tr) {
                    var tds = $(tr).find('th');
                    if (tds.length == 1) {
                        tds.css('border-left', 0).css('border-right', 0);
                    } else {
                        $(tr).find('th:first').css('border-left', 0);
                        $(tr).find('th:last').css('border-right', 0);
                    }
                });

                copiedHead.appendTo(o);
                ww = [];
                o.find("thead.header > tr > th").each(function(i, h) {
                    var _width = $(h).width();
                    var _paddingLeft = parseInt($(h).css('padding-left'));
                    var _paddingRight = parseInt($(h).css('padding-right'));

                    var parentTr = $(h).parent();
                    var thLength = parentTr.find('th').length;
                    var index = parentTr.index($(h));

                    var _borderLeftWidth = parseInt($(h).css('border-left-width'));
                    var _borderRightWidth = parseInt($(h).css('border-right-width'));

                    var width = _width + _paddingLeft + _paddingRight;
                    if (index != 0) {
                        width += _borderLeftWidth;
                    }
                    if (index != (thLength - 1)) {
                        width += _borderRightWidth;
                    }
                    //width = width - 5;
                    return ww.push(width);
                });
                $.each(ww, function(i, w) {
                    return o.find("thead.header-copy > tr > th:eq(" + i + ")").css({
                        width: w
                    });
                });
                //var _bodyPaddingTop = $('body').css('padding-top');
                o.find("thead.header-copy").css({
                    margin: "0 auto",
                    width: o.width(),
                    "background-color": config.bgColor
                });
                return processScroll();
            });
        };
    })(jQuery);

}).call(this);

$(document).ready(function() {


    // make the header fixed on scroll
    $('.table-fixed-header').fixedHeader();

    $(window).resize(function() {
        $('.header-copy').remove();
        $('.table-fixed-header').fixedHeader();
    });
});