{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

{#<ol class="breadcrumb">#}
    {#<li class="active">{{ server.ip }}:{{ server.port }}</li>#}
{#</ol>#}

<div style="margin-bottom: 20px;">
    <div class="btn-group btn-group-xs" role="group">
        {#<a href="/server/{{ server.id }}/config?ip={{ server.ip }}&port={{ server.port }}#form-create" class="btn btn-default form-create">添加配置</a>#}
        {#<a href="/server/{{ server.id }}/config?ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default form-edit">修改配置</a>#}

        <a href="/supervisor/readLog?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" target="_blank" data-nopjax class="btn btn-default read_log">服务日志</a>
        {#<a href="/server/{{ server.id }}/supervisor/readlog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="btn btn-default read_log">服务日志</a>#}
        {#<a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default">刷新页面</a>#}
        <div class="btn-group btn-group-xs">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                更多 <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="/process/reloadConfig?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" class="update-config" title="">同步配置</a></li>
                <li><a href="/process/restartAll?server_id={{ server.id }}" class="restartall" data-nopjax data-confirm="真的要重启所有进程吗？">重启所有进程</a></li>
                <li><a href="/process/stopAll?server_id={{ server.id }}" class="stopall" data-nopjax data-confirm="真的要停止所有进程吗？">停止所有进程</a></li>
                <li><a href="/supervisor/restart?server_id={{ server.id }}" class="restart_supervisor" data-nopjax data-confirm="真的要重启 Supervisor 服务吗？">重启 Supervisor 服务</a></li>
                {#<li><a href="/server/{{ server.id }}/supervisor/shutdown">停止服务</a></li>#}
            </ul>
        </div>
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
            <a href="/process/start?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="start">启动</a>&nbsp;
            <a href="/process/tailLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}&ip={{ server.ip }}&port={{ server.port }}" data-nopjax target="_blank" class="tail_log">查看日志</a>
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

    {% for group in process_groups %}
        {% if group['id'] is not empty %}
            {% set btn_class = '' %}
        {% else %}
            {% set btn_class = 'disabled' %}
        {% endif %}
    <tr>
        <th colspan="4">
            <span class="label label-info" style="font-size:14px">{{ group['program'] }}</span>&nbsp;
        </th>
        <th>
            <a class="btn btn-xs btn-warning restart" href="/process/restartGroup?server_id={{ server.id }}&group={{ group['program'] }}" data-nopjax>重启</a>&nbsp;
            <a class="btn btn-xs btn-warning start" href="/process/startGroup?server_id={{ server.id }}&group={{ group['program'] }}" data-nopjax>启动</a>&nbsp;
            <a class="btn btn-xs btn-warning stop" href="/process/stopGroup?server_id={{ server.id }}&group={{ group['program'] }}" data-nopjax>停止</a>&nbsp;
            {% if group['id'] is not empty %}
                <a class="btn btn-xs btn-warning {{ btn_class }}" href="/process/edit/{{ group['id'] }}?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}">修改</a>&nbsp;
                {#<a class="btn btn-xs btn-warning {{ btn_class }}" href="#">复制</a>&nbsp;#}
                <a class="btn btn-xs btn-warning delete {{ btn_class }}" href="/process/delete/{{ group['id'] }}?server_id={{ server.id }}" data-confirm="真的要删除 {{ group['program'] }} 吗？" data-nopjax>删除</a>
            {% else %}
                <a class="btn btn-xs btn-warning {{ btn_class }}" href="">修改</a>&nbsp;
                {#<a class="btn btn-xs btn-warning {{ btn_class }}" href="#">复制</a>&nbsp;#}
                <a class="btn btn-xs btn-warning delete {{ btn_class }}" href="" data-confirm="真的要删除 {{ group['program'] }} 吗？" data-nopjax>删除</a>
            {% endif %}

        </th>
    </tr>
        {% for process in processes %}
            {% if process['group'] == group['program'] %}
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
                    <a href="/process/restart?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="restart">重启</a>&nbsp;
                    <a href="/process/start?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="start">启动</a>&nbsp;
                    <a href="/process/stop?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="stop">停止</a>&nbsp;
                    <a href="/process/clearLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="clear_log">清理日志</a>&nbsp;
                    <a href="/process/tailLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}&ip={{ server.ip }}&port={{ server.port }}" data-nopjax target="_blank" class="tail_log">查看日志</a>
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

    $('.restartall, .stopall, .restart_supervisor, .delete').click(function() {
        if (!confirm($(this).attr('data-confirm'))) {
            return false;
        }

        var url = $(this).attr('href');
        $.get(url, function(data) {
            if (data.state) {
                success(data.message);

                if (typeof data.reload_config != 'undefined') {
                    reloadConfig();
                }
            } else {
                error(data.message);
            }
        });

        return false;
    });

    var $links = $('.start, .restart, .stop, .clear_log, .update-config');
    $links.click(function() {
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
});
</script>