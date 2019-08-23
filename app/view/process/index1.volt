<div class="alert alert-success" style="height: 40px; padding-top: 9px;">
    当前服务器：172.16.0.69:9001 &nbsp;&nbsp;
    <a href="#" style="text-decoration: underline">添加配置</a> &nbsp;
    <a href="#" target="_blank" style="text-decoration: underline;">查看配置文件</a>
</div>

<table class="table table-bordered">
    <tbody>
    <tr>
        <th style="width: 15%; vertical-align: middle;">程序名</th>
        <td><input type="text" class="form-control" name="program" value="CALENDAR_DELETE_NOTICE"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">命令</th>
        <td><input type="text" class="form-control" name="command" value="/usr/bin/php /www/web/life.115.com/App/Task/CALENDAR_DELETE_NOTICE.php"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">进程名</th>
        <td> <input type="text" class="form-control" name="process_name" value="%(program_name)s_%(process_num)s"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">进程数</th>
        <td><input type="text" class="form-control" name="numprocs" value="1"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">进程下标起始值</th>
        <td><input type="text" class="form-control" name="numprocs_start" value="0"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">目录</th>
        <td><input type="text" class="form-control" name="directory" value="%(here)s"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">自动启动</th>
        <td><input type="text" class="form-control" name="autostart" value="true"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">启动重试次数</th>
        <td><input type="text" class="form-control" name="startretries" value="20"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">自动重启</th>
        <td><input type="text" class="form-control" name="autorestart" value="true"></td>
    </tr>

    <tr>
        <th style="width: 15%; vertical-align: middle;">错误重定向(redirect_stderr)</th>
        <td><input type="text" class="form-control" name="redirect_stderr" value="true"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">标准输出日志文件(stdout_logfile)</th>
        <td><input type="text" class="form-control" name="stdout_logfile" value="AUTO"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">标准输出日志备份</th>
        <td><input type="text" class="form-control" name="stdout_logfile_backups" value="0"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;"> 标准输出日志的最大字节数</th>
        <td><input type="text" class="form-control" name="stdout_logfile_maxbytes" value="1MB"></td>
    </tr>

    <tr>
        <th style="width: 15%; vertical-align: middle;">操作</th>
        <td>
            <button class="btn btn-sm btn-success" style="width: 70px;">修改</button>&nbsp;
            <button class="btn btn-sm btn-danger" style="width: 70px;">删除</button>
        </td>
    </tr>

    </tbody>
</table>