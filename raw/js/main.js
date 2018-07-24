requirejs.config({
    baseUrl: 'assets/js',
    shim: {
        'bootstra.bundle': ['jquery'],
        'locales/bootstrap-datepicker.zh-CN': ['bootstrap-datepicker'],
        'theme/fa/theme': ['fileinput'],
        'locales/zh': ['fileinput']
    }
});

define('main', ['jquery', 'bootstrap'], function($, b){return $});

require(['main'], function($) {

    // $('[data-toggle="tooltip"]').tooltip()

    //jquery1.9以后不支持$.browser
    //让ie支持 表单外按钮提交表单
    //1.9以后用on方法替代了live
    require(['bootstrap-select', 'css!../css/bootstrap-select'], function(){
        $('.selectpicker').selectpicker()
    })

    if (
        /msie/.test(navigator.userAgent.toLowerCase()) ||
        navigator.userAgent.match(/Trident.*rv[ :]*11\./)
        ) {
        $(document).on('click', 'button[form]', function() {
            var form_id = $(this).attr('form');
            $('#' + form_id).submit();
        });
    }

	$('body').on('click', '[href^="ajax:"]', function(e) {
        var $link = $(this);
		if ($link.data('delegated')) return false;
        $.getScript($(this).attr('href').substring(5));
		return false;
	});

	$('body').on('click', '[href^="gini-ajax:"]', function(e) {
        var $link = $(this);
		if ($link.data('delegated')) return false;

        e.preventDefault();

        $link.trigger('ajax-before');

        $.ajax({
		    type: "GET",
            url: $link.attr('href').substring(10),
            success: function(html) {
                $link.trigger('ajax-success', html);
    		    $('body').append(html).find('script[data-ajax]').remove();
    		},
            complete: function() {
                $link.trigger('ajax-complete');
            }
		});

		return false;
	});

	$('body').on('submit', 'form[action^="gini-ajax:"]', function(e) {
		if ($(this).data('delegated')) return false;

        e.preventDefault();

		var $form = $(this);

        $form.trigger('ajax-before');

		$.ajax({
			type: $form.attr('method') || "POST",
			url: $form.attr('action').substring(10),
			data: $form.serialize(),
			success: function(html) {
                $form.trigger('ajax-success', html);
    		    $('body').append(html).find('script[data-ajax]').remove();
			},
            complete: function() {
                $form.trigger('ajax-complete');
            }
		});

		return false;
	});


    if (window.devicePixelRatio >= 2) {

        $.fn.setRetinaImage = function() {

            return this.each(function(){
                var $img = $(this);

                $img.addClass('retina-ready');

                var image = new Image();
                image.src = $img.data('retina-src');

                function _img_loaded(img) {
                    if (!img.complete) return false;
                    if (typeof img.naturalWidth != "undefined" && img.naturalWidth == 0) {
                        return false;
                    }
                    return true;
                }

                $(image).load(function() {

                    function _replace_image() {
                        $img.attr('width', $img.width());
                        $img.attr('height', $img.height());
                        $img.attr('src', image.src);
                    }

                    if (_img_loaded($img[0])) {
                        _replace_image();
                    }
                    else {
                        $img.load(_replace_image);
                    }

                })

            });
        };

        $(document).ajaxSuccess(function () {
            $('img[data-retina-src]:not(.retina-ready)').setRetinaImage();
        });

        $('img[data-retina-src]:not(.retina-ready)').setRetinaImage();

    }
})
