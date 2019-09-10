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
            <a class="btn btn-primary navbar-btn refresh" href="/process/tailLog?server_id={{ server.id }}&group={{ group }}&name={{ name }}&ip={{ server.ip }}&port={{ server.port }}">
                自动刷新
            </a>
            <a class="btn btn-default navbar-btn clear_log" href="/process/clearLog?server_id={{ server.id }}&group={{ group }}&name={{ name }}">
                清理日志
            </a>
        </div>
    </div>
</nav>

{% include "partials/logCommon.volt" %}