define('more', ['jquery'], function($){

    var More = function(opt) {

        opt = $.extend({
            start: 0
        }, opt || {});

        var $root = $(opt.root);
        var $loading = $root.children('.loading');

        var _isLoading = false;

        function _loadMore() {
            _isLoading = true;

            $root.append($loading);
            $
            .ajax({
                url: $.isFunction(opt.url) ? opt.url.apply(opt) : opt.url,
                data: $.isFunction(opt.data) ? opt.data.apply(opt) : (opt.data || {}),
                type: 'POST'
            })
            .done(function(rows){
                $loading.detach();
                
                var $rows = $(rows);
                $rows.appendTo($root);
                 
                var $more = $rows.filter('[data-more]');
                if ($more.length) {
                    opt.start = parseInt($more.data('more'));
                    $more.remove();
                }
                else {
                    opt.start = -1;
                }

                _isLoading = false;
                
                $root.trigger('load.more');
            });         
        }
 
        if (opt.mode == 'nonstop') {
            $root.on('load.more', function(){
                if (opt.start >= 0) {
                    _loadMore();
                }
            })
        }
        else {
            $(window).scroll(function () {
                if ($(window).height() + $(window).scrollTop() >= $(document).height()) {
                    if (opt.start >= 0 && !_isLoading) {
                        _loadMore();
                    }
                }

            });
        }

        _loadMore();

        return this;
    };

    return More;    
});