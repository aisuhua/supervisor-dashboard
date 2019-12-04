<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Supervisor</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                {% for id, name in menus %}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ name }} <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        {% if menu_servers[id] is not empty %}
                            {% for menu_server in menu_servers[id] %}
                                <li><a href="/process?server_id={{ menu_server['id'] }}&ip={{ menu_server['ip'] }}&port={{ menu_server['port'] }}">{{ menu_server['ip'] }}:{{ menu_server['port'] }}</a></li>
                            {% endfor %}

                            <li role="separator" class="divider"></li>
                            <li><a href="/server?group_id={{ id }}">服务器管理</a></li>
                        {% else %}
                            <li><a href="/server/create">添加服务器</a></li>
                        {% endif %}
                    </ul>
                </li>
                {% else %}

                {% endfor %}
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">管理功能 <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/server-group">分组管理</a></li>
                        <li><a href="/server">服务器管理</a></li>
                        <li><a href="/process/all">所有进程</a></li>
                        <li><a href="/cron/all">所有定时任务</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>