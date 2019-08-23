{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    <li class="active">{{ server.ip }}:{{ server.port }}</li>
</ol>

{#<p class="bg-success" style="padding:10px;">#}

{#</p>#}

{#<div class="alert alert-success" role="alert" style="height: 40px; padding-top: 9px;">#}
    {#{{ server.ip }}:{{ server.port }}#}
{#</div>#}

{#<div class="panel panel-default">#}
    {#<div class="panel-body">#}
        {#{{ server.ip }}:{{ server.port }}#}
    {#</div>#}
{#</div>#}

<div style="margin-bottom: 20px;">
    <div class="btn-group" role="group">
        <a href="/program" class="btn btn-default">添加任务</a>
        <a href="#" class="btn btn-default">更新配置</a>
        <a href="/server/{{ server.id }}/process" class="btn btn-default">刷新</a>
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                更多 <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="#">重启所有任务</a></li>
                <li><a href="#">停止所有任务</a></li>
                <li><a href="#">查看日志</a></li>
                <li><a href="#">重启服务</a></li>
                <li><a href="#">停止服务</a></li>
            </ul>
        </div>
    </div>
</div>

{% if process_warnings is not empty %}

<div class="alert alert-danger">
    您有 {{ process_warnings | length }} 个任务异常，请联系相关人员进行处理。
</div>

<table class="table table-bordered">
<tr>
    <th>进程号</th>
    <th>任务名称</th>
    <th>任务描述</th>
    <th>任务状态</th>
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
            <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/start" class="start">启动</a>&nbsp;&nbsp;
            <a href="#" class="clear_log">清理日志</a>&nbsp;&nbsp;
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
        <th>任务名称</th>
        <th>任务描述</th>
        <th>任务状态</th>
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
            <a class="btn btn-xs btn-warning stop" href="/server/{{ server.id }}/process/{{ processGroup }}/stop">停止</a>
            <a class="btn btn-xs btn-warning">修改</a>&nbsp;
            <a class="btn btn-xs btn-warning">删除</a>&nbsp;
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
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/restart" class="restart">重启</a>&nbsp;&nbsp;
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/start" class="start">启动</a>&nbsp;&nbsp;
                    <a href="/server/{{ server.id }}/process/{{ process['group'] }}:{{ process['name'] }}/stop" class="stop">停止</a>&nbsp;&nbsp;
                    <a href="#" class="clear_log">清理日志</a>&nbsp;&nbsp;
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
    $('.tail_log').unbind().click(function() {
        event.stopPropagation();
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