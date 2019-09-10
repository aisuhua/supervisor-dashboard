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
            <a class="btn btn-primary navbar-btn refresh" href="/supervisor/readLog?server_id={{ server.id }}" data-nopjax>
                自动刷新
            </a>
            <a class="btn btn-default navbar-btn clear_log" href="/supervisor/clearLog?server_id={{ server.id }}">
                清理日志
            </a>
        </div>
    </div>
</nav>

{% include "partials/logCommon.volt" %}