<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href=""><small>{{ command['command'] }} - 命令执行日志</small></a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            {% if running %}
            <a class="btn btn-primary navbar-btn refresh" href="/command/tail/{{ command['id'] }}?server_id={{ server.id }}" data-nopjax>
                自动刷新
            </a>
            {% else %}
            <a class="btn btn-default navbar-btn" href="/command/download/{{ command['id'] }}?server_id={{ server.id }}" data-nopjax>
                下载完整日志
            </a>
            {% endif %}
        </div>
    </div>
</nav>

{% include "partials/logCommon.volt" %}