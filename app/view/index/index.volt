{#<div class="alert alert-info alert-override" role="alert">#}

{#</div>#}

<div class="panel panel-default">
    <div class="panel-body">
        服务状态：<span class="text-success">RUNNING</span> &nbsp; | &nbsp; 地址: 172.16.210.54 &nbsp; | &nbsp; 端口: 9001 &nbsp; &nbsp;<a href="#" target="_blank">服务日志</a>
    </div>
</div>


<div style="margin: -5px 0 15px 0;">

    <div>
        <a href="#"><button class="btn btn-warning">重启服务</button></a>&nbsp;
        <a href="#"><button class="btn btn-warning">更新配置</button></a>&nbsp;
        <a href="/program"><button class="btn btn-primary">修改配置</button></a>&nbsp;
    </div>

    {#<select id="project-select" class="form-control" style="width: 200px; float:left;">#}
    {#<option value="#">筛选：全部</option>#}
    {#<option value="#">PUSHARE_RECV_FILE</option>#}
    {#</select>#}

    <div style="clear:both;"></div>
</div>


<div class="alert alert-danger">
    您有2个任务异常，请联系相关人员进行处理。
</div>

<table class="table table-bordered">
    <tr>
        <th>任务名称</th>
        <th>任务描述</th>
        <th>任务状态</th>
        <th>操作</th>
    </tr>
    <tr>
        <td>PUSHARE_RECV_FILE_0_1</td>
        <td>Aug 16 02:31 PM</td>
        <td><span class="label label-danger">STOPPED</span></td>
        <td>
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
        </td>
    </tr>
    <tr>
        <td>PUSHARE_RECV_FILE_0_2</td>
        <td>Aug 16 02:31 PM</td>
        <td><span class="label label-danger">STOPPED</span></td>
        <td>
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
        </td>
    </tr>
</table>


<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>任务名称</th>
        <th>任务描述</th>
        <th>任务状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th colspan="3">
            <span class="label label-info" style="font-size:14px">DELETE_FILE_NEW</span>&nbsp;
            {#<span class="process-group-tip">(没有负责人，请<a href="#">设置</a>)</span>#}
        </th>
        <th>
            <button class="btn btn-xs btn-warning" type="button">重启</button>&nbsp;
            <button class="btn btn-xs btn-warning" type="button">启动</button>&nbsp;
            <button class="btn btn-xs btn-warning" type="button">停止</button>
        </th>
    </tr>
    <tr>
        <td>DELETE_FILE_NEW_0</td>
        <td>pid 21059, uptime 0:02:24</td>
        <td>
            <span class="label label-success">RUNNING</span>
        </td>
        <td>
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
        </td>
    </tr>
    <tr>
        <td>DELETE_FILE_NEW_1</td>
        <td>pid 21059, uptime 0:02:24</td>
        <td>
            <span class="label label-success">RUNNING</span>
        </td>
        <td>
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
        </td>
    </tr>

    <tr>
        <th colspan="3">
            <span class="label  label-info" style="font-size:14px">DELETE_FILE_NEW</span>&nbsp;
            {#<span class="process-group-tip">(没有负责人，请<a href="#">设置</a>)</span>#}
        </th>
        <th>
            <button class="btn btn-xs btn-warning" type="button">重启</button>&nbsp;
            <button class="btn btn-xs btn-warning" type="button">启动</button>&nbsp;
            <button class="btn btn-xs btn-warning" type="button">停止</button>
        </th>
    </tr>
    <tr>
        <td>DELETE_FILE_NEW_0</td>
        <td>pid 21059, uptime 0:02:24</td>
        <td>
            <span class="label label-success">RUNNING</span>
        </td>
        <td>
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
        </td>
    </tr>
    <tr>
        <td>DELETE_FILE_NEW_1</td>
        <td>pid 21059, uptime 0:02:24</td>
        <td>
            <span class="label label-success">RUNNING</span>
        </td>
        <td>
            <a href="#">重启</a>&nbsp;&nbsp;
            <a href="#">启动</a>&nbsp;&nbsp;
            <a href="#">停止</a>&nbsp;&nbsp;
            <a href="#">清理日志</a>&nbsp;&nbsp;
            <a href="#">查看日志</a>
        </td>
    </tr>
    </tbody>
</table>