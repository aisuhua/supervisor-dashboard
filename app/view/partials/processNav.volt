<ul id="" class="nav nav-tabs my-tabs1" role="tablist" style="margin-bottom: 20px;">

    {% set index_class = '' %}
    {% set create_class = '' %}
    {% set ini_class = '' %}
    {% set clone_class = '' %}

    {% if dispatcher.getActionName() == 'index' %}
        {% set index_class = 'active' %}
    {% elseif dispatcher.getActionName() == 'create' or
        dispatcher.getActionName() == 'edit' or
        dispatcher.getActionName() == 'createIni' or
        dispatcher.getActionName() == 'editIni'
    %}
        {% set create_class = 'active' %}
    {% elseif dispatcher.getActionName() == 'ini' %}
        {% set ini_class = 'active' %}
    {% elseif dispatcher.getActionName() == 'clone' %}
        {% set clone_class = 'active' %}
    {% endif %}

    <li role="presentation" class="{{ index_class }}"><a href="/process?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}">进程列表</a></li>
    <li role="presentation" class="{{ create_class }}"><a href="/process/create?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}">添加/修改进程</a></li>
    <li role="presentation" class="{{ ini_class }}"><a href="#"> 编辑 ini 配置</a></li>
    <li role="presentation" class="{{ clone_class }}"><a href="#">克隆配置</a></li>
</ul>