<ol class="breadcrumb">
    <li class="active">{{ server.serverGroup.name }}</li>
    <li class="active">{{ server.ip }}:{{ server.port }}</li>
</ol>

<ul id="" class="nav nav-tabs my-tabs1" role="tablist" style="margin-bottom: 20px;">
    {% set index_class = '' %}
    {% set create_class = '' %}
    {% set ini_class = '' %}
    {% set cron_class = '' %}
    {% set cron_create_class = '' %}
    {% set cron_log_class = '' %}

    {% if dispatcher.getControllerName() == 'process' %}
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
        {% endif %}

    {% elseif dispatcher.getControllerName() == 'cron' %}
        {% if dispatcher.getActionName() == 'index' %}
            {% set cron_class = 'active' %}
        {% elseif dispatcher.getActionName() == 'create' or
            dispatcher.getActionName() == 'edit'
        %}
            {% set cron_create_class = 'active' %}
        {% endif %}
    {% elseif dispatcher.getControllerName() == 'cron-log' %}
        {% if dispatcher.getActionName() == 'index' %}
            {% set cron_log_class = 'active' %}
        {% endif %}
    {% endif %}

    <li role="presentation" class="{{ index_class }}"><a href="/process?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}">进程列表</a></li>
    <li role="presentation" class="{{ create_class }}"><a href="/process/create?server_id={{ server.id }}">添加/修改进程</a></li>
    <li role="presentation" class="{{ ini_class }}"><a href="/process/ini?server_id={{ server.id }}">进程配置</a></li>
    <li role="presentation" class="{{ cron_class }}"><a href="/cron?server_id={{ server.id }}">定时任务</a></li>
    <li role="presentation" class="{{ cron_create_class }}"><a href="/cron/create?server_id={{ server.id }}">添加/修改任务</a></li>
    <li role="presentation" class="{{ cron_log_class }}"><a href="/cron-log/?server_id={{ server.id }}">执行日志</a></li>
    {#<li role="presentation" class="{{ clone_class }}"><a href="#">克隆配置</a></li>#}
</ul>

<script>
function reloadConfig() {
    $.get('/process/reloadConfig?server_id={{ server.id }}');
}

$(function() {

    var ini_editor = document.getElementById('ini');
    if (ini_editor)
    {
        // code editor
        var editor = CodeMirror.fromTextArea(document.getElementById('ini'), {
            mode: "properties",
            lineNumbers: true,
            lineWrapping: true,
            indentUnit: 0,
            autoRefresh: true,
            automaticLayout: true
        });

        editor.setSize('100%', '100%');

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            editor.refresh();
        });
    }
});
</script>

{% if reload_config is not empty %}
<script>reloadConfig();</script>
{% endif %}

