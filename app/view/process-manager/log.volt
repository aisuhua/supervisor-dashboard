<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="">{{ name }} - 进程日志</a>
        </div>
        <div class="collapse navbar-collapse navbar-right">
            {% if running %}
            <a class="btn btn-primary btn-sm navbar-btn" id="refresh" href="/process-manager/tail?server_id={{ server.id }}&group={{ group }}&name={{ name }}&stderr={{ stderr }}" data-nopjax>自动刷新</a>
            {% endif %}
            <a class="btn btn-default btn-sm navbar-btn" href="/process-manager/download?server_id={{ server.id }}&group={{ group }}&name={{ name }}&stderr={{ stderr }}" data-nopjax>下载日志</a>
        </div>
    </div>
</nav>

{% include "partials/logCommon.volt" %}