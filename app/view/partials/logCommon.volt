<samp id="pjax-container">{{ content() }}</samp>

<script>
    $(function() {

        $(document).off('pjax:send');
        $(document).off('pjax:complete');
        $(document).off('pjax:end');
        $(document).off('ajaxStart');
        $(document).off('ajaxStop');

        var timerId;

        // F5 刷新时先将自动刷日志停止
        // https://stackoverflow.com/questions/14707602/capturing-f5-keypress-event-in-javascript-using-window-event-keycode-in-window-o
        document.onkeydown = fkey;
        document.onkeypress = fkey;
        document.onkeyup = fkey;

        var wasPressed = false;
        function fkey(e){
            e = e || window.event;
            if( wasPressed ) return;

            if (e.keyCode == 116) {
                wasPressed = true;
                if (timerId) {
                    clearTimeout(timerId);
                }
            }
        }

        $('.refresh').click(function() {
            event.stopPropagation();

            if (timerId) {
                clearTimeout(timerId);
            }

            if ($(this).hasClass('refreshing')) {
                $(this).removeClass('refreshing').html('自动刷新');
                return false;
            }

            $(this).addClass('refreshing').html('停止刷新 <i class="fa fa-spinner fa-pulse fa-fw"></i>');
            $('html, body').scrollTop(function() {
                return $(this).height();
            });

            var refresh_url = $(this).attr('href');

            function refresh() {
                var r = 'random=' + Math.random();
                refresh_url += refresh_url.indexOf('?') >= 0 ? ('&' + r) : ('?' + r);

                $.pjax({
                    url: refresh_url,
                    container: '#pjax-container',
                    push: false,
                    timeout: 180000
                }).done(function() {
                    $('html, body').scrollTop(function() {
                        return $(this).height();
                    });
                });
            }

            refresh();
            timerId = setTimeout(function run() {
                refresh();
                timerId = setTimeout(run, 2000);
            }, 2000);

            return false;
        });

        $('.clear_log').click(function() {
            event.stopPropagation();
            NProgress.start();

            var $that = $(this);
            var url = $that.attr('href');

            $.get(url, function(data) {
                NProgress.done();

                if (data.state) {
                    // success(data.message, {delay: 1000});
                    $("#pjax-container").html('没有任何日志记录');
                } else {
                    error(data.message);
                }
            });
            return false;
        });
    });
</script>