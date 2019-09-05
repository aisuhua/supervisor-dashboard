{{ content() }}
{{ flashSession.output() }}
{% include 'partials/processNav.volt' %}

{#<ol class="breadcrumb">#}
    {#<li class="active">{{ server.ip }}:{{ server.port }}</li>#}
{#</ol>#}

<div style="margin-bottom: 20px;">
    <div class="btn-group btn-group-sm" role="group">
        {#<a href="/server/{{ server.id }}/config?ip={{ server.ip }}&port={{ server.port }}#form-create" class="btn btn-default form-create">添加配置</a>#}
        {#<a href="/server/{{ server.id }}/config?ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default form-edit">修改配置</a>#}
        <a href="/process/reloadConfig?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default update-config">同步配置</a>
        <a href="/process/restartall?server_id={{ server.id }}" class="btn btn-default restartall" data-confirm="真的要重启所有进程吗？">重启所有</a>
        <a href="/process/stopall?server_id={{ server.id }}" class="btn btn-default stopall" data-confirm="真的要停止所有进程吗？">停止所有</a>
        <a href="/supervisor/restart?server_id={{ server.id }}" class="btn btn-default restart_supervisor" data-confirm="真的要重启 Supervisor 服务吗？">重启服务</a>
        <a href="/supervisor/readLog?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="btn btn-default read_log">服务日志</a>
        {#<a href="/server/{{ server.id }}/supervisor/readlog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="btn btn-default read_log">服务日志</a>#}
        {#<a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default">刷新页面</a>#}
        {#<div class="btn-group btn-group-sm">#}
            {#<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">#}
                {#更多 <span class="caret"></span>#}
            {#</button>#}
            {#<ul class="dropdown-menu">#}
                {#<li><a href="/server/{{ server.id }}/process/restartall" class="restartall">重启所有进程</a></li>#}
                {#<li><a href="/server/{{ server.id }}/process/stopall" class="stopall">停止所有进程</a></li>#}
                {#<li><a href="/server/{{ server.id }}/supervisor/restart" class="restart_supervisor">重启 Supervisor</a></li>#}
                {#<li><a href="/server/{{ server.id }}/supervisor/readlog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="read_log">查看 Supervisor 日志</a></li>#}
                {#<li><a href="/server/{{ server.id }}/supervisor/shutdown">停止服务</a></li>#}
            {#</ul>#}
        {#</div>#}
    </div>
</div>

{% if process_warnings is not empty %}

<div class="alert alert-danger">
    您有 {{ process_warnings | length }} 个进程异常，请联系相关人员进行处理。
</div>

<table class="table table-bordered">
<tr>
    <th>进程号</th>
    <th>进程名称</th>
    <th>进程描述</th>
    <th>进程状态</th>
    <th>操作</th>
</tr>

{% for process in process_warnings %}
    <tr>
        <td>{{ process['pid'] }}</td>
        <td>{{ process['name'] }}</td>
        <td>{{ process['description'] }}</td>
        <td>
            {% if process['statename'] == "RUNNING" %}
                {% set label_name = "success" %}
            {% elseif process['statename'] == "STARTING" %}
                {% set label_name = "danger" %}
            {% elseif process['statename'] == "STOPPED" %}
                {% set label_name = "danger" %}
            {% else %}
                {% set label_name = "default" %}
            {% endif %}
            <span class="label label-{{ label_name }}">{{ process['statename'] }}</span>
        </td>
        <td>
            <a href="/process/start?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" class="start">启动</a>&nbsp;
            <a href="/process/tailLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}&ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="tail_log">查看日志</a>
        </td>
    </tr>
{% endfor %}
</table>

{% endif %}

<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>进程号</th>
        <th>进程名称</th>
        <th>进程描述</th>
        <th>进程状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>

    {% for processGroup in processGroups %}
    <tr>
        <th colspan="4">
            <span class="label label-info" style="font-size:14px">{{ processGroup }}</span>&nbsp;
            {#<span class="process-group-tip">(没有负责人，请<a href="#">设置</a>)</span>#}
        </th>
        <th>
            <a class="btn btn-xs btn-warning restart" href="/process/restartGroup?server_id={{ server.id }}&group={{ processGroup }}">重启</a>&nbsp;
            <a class="btn btn-xs btn-warning start" href="/process/startGroup?server_id={{ server.id }}&group={{ processGroup }}">启动</a>&nbsp;
            <a class="btn btn-xs btn-warning stop" href="/process/stopGroup?server_id={{ server.id }}&group={{ processGroup }}">停止</a>&nbsp;
            <a class="btn btn-xs btn-warning" href="#">修改</a>&nbsp;
            <a class="btn btn-xs btn-warning" href="#">复制</a>&nbsp;
            <a class="btn btn-xs btn-warning delete" href="/process/delete?server_id={{ server.id }}&group={{ processGroup }}" data-confirm="真的要删除 {{ processGroup }} 吗？">删除</a>
        </th>
    </tr>
        {% for process in processes %}
            {% if process['group'] == processGroup %}
            <tr>
                <td>{{ process['pid'] }}</td>
                <td>{{ process['name'] }}</td>
                <td>{{ process['description'] }}</td>
                <td>
                    {% if process['statename'] == "RUNNING" %}
                        {% set label_name = "success" %}
                    {% elseif process['statename'] == "STARTING" %}
                        {% set label_name = "danger" %}
                    {% elseif process['statename'] == "STOPPED" %}
                        {% set label_name = "danger" %}
                    {% else %}
                        {% set label_name = "default" %}
                    {% endif %}
                    <span class="label label-{{ label_name }}">{{ process['statename'] }}</span>
                </td>
                <td>
                    <a href="/process/restart?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" class="restart">重启</a>&nbsp;
                    <a href="/process/start?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" class="start">启动</a>&nbsp;
                    <a href="/process/stop?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" class="stop">停止</a>&nbsp;
                    <a href="/process/clearLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" class="clear_log">清理日志</a>&nbsp;
                    <a href="/process/tailLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}&ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="tail_log">查看日志</a>
                </td>
            </tr>
            {% endif %}
        {% endfor %}
    {% else %}
    {% endfor %}
    </tbody>
</table>

<script>
$(function () {

    // 防止 pjax 执行
    $('.tail_log, .read_log').unbind().click(function() {
        event.stopPropagation();
    });

    $('.restartall, .stopall, .restart_supervisor, .delete').click(function() {
        event.stopPropagation();

        if (!confirm($(this).attr('data-confirm'))) {
            event.stopPropagation();
            return false;
        }

        var url = $(this).attr('href');

        $.get(url, function(data) {
            if (data.state) {
                success(data.message);

                if (typeof data.reload_config != 'undefined') {
                    $.ajax({
                        url: '/process/reloadConfig?server_id={{ server.id }}',
                        success: function(data) {},
                        timeout: 10000
                    });
                }
            } else {
                error(data.message);
            }
        });

        return false;
    });

    var $links = $('.start, .restart, .stop, .clear_log, .update-config');
    $links.click(function(event) {
        event.stopPropagation();

        var url = $(this).attr('href');
        $.get(url, function(data) {
            if (data.state) {
                success(data.message);
            } else {
                error(data.message);
            }
        });

        return false;
    });

//    $('.update-config').click(function() {
//        event.stopPropagation();
//
//        var url = $(this).attr('href');
//        $.get(url, function(data) {
//            if (data.state == 1) {
//                success(data.message);
////                $.pjax({
////                    url: window.location.pathname + window.location.search,
////                    container: '#pjax-container',
////                    push: true
////                });
//                window.locationre
//            } else if (data.state == 2) {
//                success(data.message);
//            } else {
//                error(data.message);
//            }
//        });
//
//        return false;
//    });
});
</script>