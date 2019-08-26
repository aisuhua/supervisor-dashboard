{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    <li class="active">{{ server.ip }}:{{ server.port }}</li>
</ol>

<div style="margin-bottom: 20px;">
    <div class="btn-group" role="group">
        <a href="/server/{{ server.id }}/process/create?ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default add_process">修改配置</a>
        <a href="#" class="btn btn-default">更新配置</a>
        {#<a href="/server/{{ server.id }}/supervisor/readlog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="btn btn-default read_log">服务日志</a>#}
        {#<a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}" class="btn btn-default">刷新页面</a>#}
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                更多 <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="/server/{{ server.id }}/process/restartall" class="restartall">重启所有进程</a></li>
                <li><a href="/server/{{ server.id }}/process/stopall" class="stopall">停止所有进程</a></li>
                <li><a href="/server/{{ server.id }}/supervisor/restart" class="restart_supervisor">重启 Supervisor</a></li>
                <li><a href="/server/{{ server.id }}/supervisor/readlog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="read_log">查看 Supervisor 日志</a></li>
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
            <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/start" class="start">启动</a>&nbsp;
            <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/taillog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="tail_log">查看日志</a>
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
            <a class="btn btn-xs btn-warning restart" href="/server/{{ server.id }}/process/{{ processGroup }}/restart">重启</a>&nbsp;
            <a class="btn btn-xs btn-warning start" href="/server/{{ server.id }}/process/{{ processGroup }}/start">启动</a>&nbsp;
            <a class="btn btn-xs btn-warning stop" href="/server/{{ server.id }}/process/{{ processGroup }}/stop">停止</a>&nbsp;
            {#<a class="btn btn-xs btn-warning">修改</a>&nbsp;#}
            {#<a class="btn btn-xs btn-warning">复制</a>&nbsp;#}
            {#<a class="btn btn-xs btn-warning">删除</a>#}
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
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/restart" class="restart">重启</a>&nbsp;
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/start" class="start">启动</a>&nbsp;
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/stop" class="stop">停止</a>&nbsp;
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/clearlog" class="clear_log">清理日志</a>&nbsp;
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/taillog?ip={{ server.ip }}&port={{ server.port }}" target="_blank" class="tail_log">查看日志</a>
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

    // 超过 50 个进程，则采用异步重启方式
    $('.restartall, .stopall, .restart_supervisor').click(function() {
        event.stopPropagation();

        if (!confirm("真的要" + $(this).text() + '吗？')) {
            event.stopPropagation();
            return false;
        }

        var url = $(this).attr('href');

        var size = $('table.table-striped tr:not(:has(th))').size();
        if (size > 0) {
            url += '?wait=0';
            $.get(url, function(data) {
                if (data.state) {
                    success(data.message);
                } else {
                    error(data.message);
                }
            });
        } else {
            $.pjax({
                url: url,
                container: '#pjax-container',
                push: false
            });
        }

        return false;
    });

    var $links = $('.start, .restart, .stop, .clear_log').unbind();
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
});
</script>