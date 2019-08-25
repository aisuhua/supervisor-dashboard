<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="">Supervisor 服务日志</a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            <a class="btn btn-primary navbar-btn refresh" href="/server/{{ server.id }}/supervisor/readlog?ip={{ server.ip }}&port={{ server.port }}">
                自动刷新
            </a>
            <a class="btn btn-default navbar-btn clear_log" href="/server/{{ server.id }}/supervisor/clearlog?ip={{ server.ip }}&port={{ server.port }}">
                清理日志
            </a>
        </div>
    </div>
</nav>

<style>
samp {
    word-wrap: break-word;
    white-space: pre-wrap;
    padding-bottom: 20px;
}
</style>

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

    var refresh_url = $(this).attr('href');

    function refresh() {
        $.pjax({
            url: refresh_url,
            container: '#pjax-container',
            push: false
        }).done(function() {
            $('html, body').scrollTop(function() {
                return $(this).height();
            });
        });
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

       refresh();
       timerId = setTimeout(function run() {
           refresh();
           timerId = setTimeout(run, 1000);
       }, 1000);

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