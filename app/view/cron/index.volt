{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<table id="cron-list" class="table table-bordered table-hover ">
    <thead>
    <tr>
        <th>ID</th>
        <th>用户</th>
        <th>时间</th>
        <th>命令</th>
        <th>状态</th>
        <th>下次执行时间</th>
        <th>上次执行时间</th>
        <th>更新时间</th>
        <th>备注</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        {% for cron in cron_arr %}
        <tr id="{{ cron['id'] }}">
            <td>{{ cron['id'] }}</td>
            <td>{{ cron['user'] }}</td>
            <td><code>{{ cron['time'] }}</code></td>
            <td><code>{{ cron['command'] }}</code></td>
            <td>
                {% if cron['status'] == 1 %}
                    <span class="text-success">启用</span>
                {% else %}
                    <span class="text-danger">停用</span>
                {% endif %}
            </td>
            <td>
                {% if cron['status'] == 1 %}
                    {{ date ('Y-m-d H:i', cron['next_time']) }}
                {% else %}

                {% endif %}
            </td>
            <td>
                {% if cron['last_time'] %}
                    {{ date ('Y-m-d H:i', cron['last_time']) }}
                {% else %}

                {% endif %}
            </td>
            <td>
                {% if time() - cron['update_time'] < 60 %}
                    <span class="text-success">几秒前</span>
                {% else %}
                        {{ date ('Y-m-d H:i', cron['update_time']) }}
                {% endif %}
            </td>
            <td>{{ cron['description'] }}</td>
            <td>
                <a href="/cron/edit/{{ cron['id'] }}?server_id={{ server.id }}">修改</a>
                <span class="text-muted"> | </span>
                <a href="/cron/delete/{{ cron['id'] }}?server_id={{ server.id }}" onclick="return confirm('真的要删除吗？');" data-nopush class="delete">删除</a>
                <span class="text-muted"> | </span>
                <a href="/cron-log?server_id={{ server.id }}&cron_id={{ cron['id'] }}">查看日志</a>
            </td>
        </tr>
        {% else %}
        <tr><td colspan="9" class="text-center">暂无数据</td></tr>
        {% endfor %}
    </tbody>
</table>

<script>
$(function() {
    var location_hash = window.location.hash;
    if (location_hash) {
        var $anchor = $(location_hash);
        if ($anchor.size() > 0) {
            $('html, body').animate({
                    scrollTop: $anchor.offset().top - 100
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
});
</script>