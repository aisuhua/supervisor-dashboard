<ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li><a href="/">默认分组</a></li>
    <li class="active">{{ server.ip }}:{{ server.port }}</li>
</ol>

<div style="margin-bottom: 20px;">
    <div class="btn-group" role="group">
        <a href="/program" class="btn btn-default">添加任务</a>
        <a href="#" class="btn btn-default">更新配置</a>
        <a href="#" class="btn btn-default">刷新</a>
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

<table class="table table-bordered ">
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
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
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
            <button type="button" class="btn btn-xs btn-warning">重启</button>&nbsp;
            <button type="button" class="btn btn-xs btn-warning">启动</button>&nbsp;
            <button type="button" class="btn btn-xs btn-warning">停止</button>
            <button type="button" class="btn btn-xs btn-warning">修改</button>&nbsp;
            <button type="button" class="btn btn-xs btn-warning">删除</button>&nbsp;
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
                    <a href="#">重启</a>&nbsp;&nbsp;
                    <a href="#">启动</a>&nbsp;&nbsp;
                    <a href="#">停止</a>&nbsp;&nbsp;
                    <a href="#">清理日志</a>&nbsp;&nbsp;
                    <a href="#">查看日志</a>
                </td>
            </tr>
            {% endif %}
        {% endfor %}
    {% else %}
    {% endfor %}
    </tbody>
</table>