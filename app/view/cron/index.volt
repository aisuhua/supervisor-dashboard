{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<table id="cron-list" class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>用户</th>
        <th>时间</th>
        <th>命令</th>
        <th>备注</th>
        <th>状态</th>
        <th>下次执行时间</th>
        <th>上次执行时间</th>
        {#<th>更新时间</th>#}
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        {% for cron in cron_arr %}
        <tr>
            <td>{{ cron['id'] }}</td>
            <td><code>{{ cron['user'] }}</code></td>
            <td><code>{{ cron['time'] }}</code></td>
            <td><code>{{ cron['command'] }}</code></td>
            <td>{{ cron['description'] }}</td>
            <td>
                {% if cron['status'] == 1 %}
                    启用
                {% else %}
                    <span class="text-danger">停用</span>
                {% endif %}
            </td>
            <td>
                {% if cron['status'] == 1 %}
                    {{ date ('Y-m-d H:i', cron['next_time']) }}
                {% else %}
                    无
                {% endif %}
            </td>
            <td>
                {% if cron['last_time'] %}
                    {{ date ('Y-m-d H:i', cron['last_time']) }}
                {% else %}
                    无
                {% endif %}
            </td>
            {#<td>{{ date ('Y-m-d H:i', cron['update_time']) }}</td>#}
            <td>
                <a href="/cron/edit/{{ cron['id'] }}?server_id={{ server.id }}">修改</a> <span class="text-muted">|</span>
                <a href="/cron/log?server_id={{ server.id }}&cron_id={{ cron['id'] }}">日志</a> <span class="text-muted">|</span>
                <a href="/cron/delete/{{ cron['id'] }}?server_id={{ server.id }}" onclick="return confirm('真的要删除吗？');" data-nopush class="delete">删除</a>
            </td>
        </tr>
        {% else %}
        <tr><td colspan="8" class="text-center">暂无数据</td></tr>
        {% endfor %}
    </tbody>
</table>