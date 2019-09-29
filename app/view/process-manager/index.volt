{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<div style="margin-bottom: 20px;">
    <div class="btn-group btn-group-xs" role="group">
        <a href="/supervisor/readLog?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" target="_blank" data-nopjax class="btn btn-default read_log">服务日志</a>
        <div class="btn-group btn-group-xs">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                更多 <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="/process-manager/reloadConfig?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" class="update-config" title="">同步配置</a></li>
                <li><a href="/process?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}&show_sys=1">显示系统进程</a></li>
                <li><a href="/process-manager/restartAll?server_id={{ server.id }}" class="restartall" data-nopjax data-confirm="警告：如果有定时任务正在运行，也会被重启。真的要重启所有进程吗？">重启所有进程</a></li>
                <li><a href="/process-manager/stopAll?server_id={{ server.id }}" class="stopall" data-nopjax data-confirm="警告：如果有定时任务正在运行，也会被停止。真的要停止所有进程吗？">停止所有进程</a></li>
                <li><a href="/supervisor/restart?server_id={{ server.id }}" class="restart_supervisor" data-nopjax data-confirm="警告：重启期间所有进程都将停止运行。真的要重启 Supervisor 服务吗？">重启 Supervisor 服务</a></li>
            </ul>
        </div>
    </div>
</div>

{% if process_warnings is not empty %}

<div class="alert alert-danger">
    有 {{ process_warnings | length }} 个进程状态异常，请处理。
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
            <a href="/process-manager/start?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="start">启动</a>&nbsp;
            <a href="/process-manager/log?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}&ip={{ server.ip }}&port={{ server.port }}" data-nopjax target="_blank" class="tail_log">查看日志</a>
        </td>
    </tr>
{% endfor %}
</table>

{% endif %}

<table class="table table-bordered table-hover">
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
            <a id="{{ group['program'] }}" class="invisible">{{ group['program'] }}</a>
        </th>
        <th>
            <a class="btn btn-xs btn-warning restart" href="/process-manager/restartGroup?server_id={{ server.id }}&group={{ group['program'] }}" data-nopjax>重启</a>&nbsp;
            <a class="btn btn-xs btn-warning start" href="/process-manager/startGroup?server_id={{ server.id }}&group={{ group['program'] }}" data-nopjax>启动</a>&nbsp;
            <a class="btn btn-xs btn-warning stop" href="/process-manager/stopGroup?server_id={{ server.id }}&group={{ group['program'] }}" data-nopjax>停止</a>&nbsp;
            {% if group['id'] is not empty %}
                <a class="btn btn-xs btn-warning {{ btn_class }}" href="/process/edit/{{ group['id'] }}?server_id={{ server.id }}">修改</a>&nbsp;
                <a class="btn btn-xs btn-warning delete {{ btn_class }}" href="/process/delete/{{ group['id'] }}?server_id={{ server.id }}" data-confirm="真的要删除 {{ group['program'] }} 吗？" data-nopjax>删除</a>
            {% else %}
                <a class="btn btn-xs btn-warning {{ btn_class }}" href="">修改</a>&nbsp;
                {#<a class="btn btn-xs btn-warning {{ btn_class }}" href="#">复制</a>&nbsp;#}
                <a class="btn btn-xs btn-warning delete {{ btn_class }}" href="">删除</a>
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
                    <a href="/process-manager/restart?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="restart">重启</a>&nbsp;
                    <a href="/process-manager/start?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="start">启动</a>&nbsp;
                    <a href="/process-manager/stop?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="stop">停止</a>&nbsp;
                    <a href="/process/log?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}&ip={{ server.ip }}&port={{ server.port }}" data-nopjax target="_blank" class="tail_log">查看日志</a>&nbsp;
                    <a href="/process-manager/clearLog?server_id={{ server.id }}&group={{ process['group'] }}&name={{ process['name'] }}" data-nopjax class="clear_log">清理日志</a>&nbsp;
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

    var location_hash = window.location.hash;
    if (location_hash) {
        var $anchor = $(location_hash);
        if ($anchor.size() > 0) {
            $('html, body').animate({
                    scrollTop: $anchor.offset().top - 63
                }, 'fast'
            );

            var $tr = $anchor.closest('tr');
            $tr.addClass('anchor-out');
            $tr.addClass('anchor-hover');
            setTimeout(function() {
                $tr.removeClass('anchor-hover');
            }, 1000);
        }
    }

    $('.restartall, .stopall, .restart_supervisor, .delete').click(function() {
        if (!confirm($(this).attr('data-confirm'))) {
            return false;
        }

        var url = $(this).attr('href');
        $.get(url, function(data) {
            if (data.state) {
                success(data.message);

                if (typeof data.reload != 'undefined') {
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