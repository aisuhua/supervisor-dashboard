<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">{{ name }} - 进程日志</a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            <a class="btn btn-primary navbar-btn refresh" href="/server/{{ server.id }}/process/{{ group }}:{{ name }}/taillog?ip={{ server.ip }}&port={{ server.port }}">
                自动刷新
            </a>
        </div>
    </div>
</nav>

<div id="pjax-container">
    {{ content() }}
</div>

<style>
    .nprogress-busy  {
        cursor: wait;

        body {
            pointer-events: none;
        }
    }
</style>

<script>
$(function() {

    $(document).off('pjax:send');
    $(document).off('pjax:complete');
    $(document).off('pjax:end');
    $(document).off('ajaxStart');
    $(document).off('ajaxStop');

    var intervalId;

   $('.refresh').click(function() {
       event.stopPropagation();

       if (intervalId) {
           clearInterval(intervalId);
       }

       if ($(this).hasClass('refreshing')) {
            $(this).removeClass('refreshing').html('自动刷新');
            return false;
       }

       $(this).addClass('refreshing').html('停止刷新 <i class="fa fa-spinner fa-pulse fa-fw"></i>');

       $('html, body').scrollTop(function() {
           return $(this).height();
       });

       var url = $(this).attr('href');
       $.pjax({
           url: url,
           container: '#pjax-container'
       }).done(function() {
           $('html, body').scrollTop(function() {
               return $(this).height();
           });
       });

       $('#loading').hide();

       intervalId = setInterval(function() {
           $.pjax({
               url: url,
               container: '#pjax-container',
               push: false
           }).done(function() {
               $('html, body').scrollTop(function() {
                   return $(this).height();
               });
           });
       }, 1000);

       return false;
   });
});
</script>