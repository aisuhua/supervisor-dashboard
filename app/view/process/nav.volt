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
    <li role="presentation" class="{{ ini_class }}"><a href="/process/ini?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}">ini 配置</a></li>
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
            automaticLayout: true,
            extraKeys: {
                "F11": function(cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function(cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
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

