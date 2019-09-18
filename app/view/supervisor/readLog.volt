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

        </div>
    </div>
</nav>

<samp id="log">{{ log | escape | default("没有任何日志记录") }}</samp>