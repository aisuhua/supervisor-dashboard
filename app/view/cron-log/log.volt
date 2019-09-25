<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href=""><small>{{ cronLog.command }} - 定时任务日志</small></a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            {% if running %}
                <a class="btn btn-primary btn-sm navbar-btn" id="refresh" href="/process/tail?server_id={{ server.id }}&group={{ group }}&name={{ name }}" data-nopjax>自动刷新</a>
            {% else %}
                <a class="btn btn-default navbar-btn" href="/cron-log/download/{{ cronLog.id }}?server_id={{ server.id }}" data-nopjax>下载日志</a>
            {% endif %}
        </div>
    </div>
</nav>

{% include "partials/logCommon.volt" %}