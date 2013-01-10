
if (navigator.appName != "Microsoft Internet Explorer") {
    window.addEventListener('load', function() {
        setTimeout(scrollTo, 0, 0, 1);
    }, false);
    window.addEventListener('load', setOrient, false);
    window.addEventListener('orientationchange', setOrient, false);
}
function setOrient() {
    var orient = Math.abs(window.orientation) === 90 ? 'land' : 'up';
    var cl = document.body.className;
    cl = cl.replace(/up|land/, orient);
    document.body.className = cl;
}
function loadpage(filename) {
    var main_nav = $('#main-nav');
    var page = $('#page');
    var ajax = $('#ajax-loading');
    main_nav.fadeOut(100);
    page.fadeOut(100, function() {
        ajax.show();
        page.load(filename, function() {
			$('#statusajax').empty();
			$('#formulario').empty();
			$('#pruebajax').empty();
            ajax.hide();
            page.fadeIn();
            main_nav.fadeIn();
        });
    });
    return false;
}

function loaddashboard(filename) {
	var _pagina='index.php';
    var main_nav = $('#main-nav');
    var page = $('#dashboard');
    var ajax = $('#ajax-loading');
	$('#statusajax').empty();
	$('#formulario').empty();
	$('#pruebajax').empty();
    main_nav.fadeOut(100);
    page.fadeOut(100, function() {
        ajax.show();
        page.load(_pagina+'?page='+filename+"&req=1", function() {
            ajax.hide();
            page.fadeIn();
            main_nav.fadeIn();
        });
    });
    return false;
}

function tabs(tab) {
    $('#tabs li a').removeClass("active");
    tab.addClass("active");
    $('.istab').hide();
    $('#' + tab.attr("rel")).show();
}(function($) {
    $.fn.extend({
        limit: function(limit, element) {
            var interval, f;
            var self = $(this);
            $(this).focus(function() {
                interval = window.setInterval(substring, 100)
            });
            $(this).blur(function() {
                clearInterval(interval);
                substring()
            });
            substringFunction = "function substring(){ var val = $(self).val();var length = val.length;if(length > limit){$(self).val($(self).val().substring(0,limit));}";
            if (typeof element != 'undefined') substringFunction += "if($(element).html() != limit-length){$(element).html((limit-length<=0)?'0':limit-length);}";
            substringFunction += "}";
            eval(substringFunction);
            substring()
        }
    })
})(jQuery);
(function($) {
    $.pop = function(options) {
        var settings = {
            pop_class: '.pop',
            pop_toggle_text: ''
        }

        function initpops() {
            $(settings.pop_class).each(function() {
                var pop_classes = $(this).attr("class");
                $(this).addClass("pop_menu");
                $(this).wrap("<div class='" + pop_classes + "'></div>");
                $(".pop_menu").attr("class", "pop_menu");
                $(this).before(" \
<div class='pop_toggle'>" + settings.pop_toggle_text + "</div> \
");
            });
        }
        initpops();
        var totalpops = $(settings.pop_class).size() + 1000;
        $(settings.pop_class).each(function(i) {
            var popzindex = totalpops - i;
            $(this).css({
                zIndex: popzindex
            });
        });
        activePop = null;

        function closeInactivePop() {
            $(settings.pop_class).each(function(i) {
                if ($(this).hasClass('active') && i != activePop) {
                    $(this).removeClass('active');
                }
            });
            return false;
        }
        $(settings.pop_class).mouseover(function() {
            activePop = $(settings.pop_class).index(this);
        });
        $(settings.pop_class).mouseout(function() {
            activePop = null;
        });
        $(document.body).click(function() {
            closeInactivePop();
        });
        $(".pop_toggle").click(function() {
            $(this).parent(settings.pop_class).toggleClass("active");
        });
    }
})(jQuery);