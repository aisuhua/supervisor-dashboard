{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<table id="cron-list" class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>定时任务 ID</th>
        <th>命令</th>
        <th>执行状态</th>
        <th>执行耗时</th>
        <th>启动时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>