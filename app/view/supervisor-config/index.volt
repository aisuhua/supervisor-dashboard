{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    <li><a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}">{{ server.ip }}:{{ server.port }}</a></li>
    <li class="active">修改配置</li>
</ol>

<div style="margin-bottom: 20px;">
    <div class="btn-group" role="group">
        <a class="btn btn-default add-process-btn">添加配置</a>
        <a class="btn btn-default expand-all-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开所有</a>
   </div>
</div>

{% for index, program in programs %}
<table class="table table-bordered">
    <input type="hidden" value="{{ program.server_id }}" name="server_id" id="server_id">
    <tbody>
    <tr>
        <th style="width: 15%; vertical-align: middle;">程序名</th>
        <td><input type="text" class="form-control" name="program" value="{{ program.program }}"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">命令</th>
        <td><input type="text" class="form-control" name="command" value="{{ program.command }}"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">进程名</th>
        <td> <input type="text" class="form-control" name="process_name" value="{{ program.process_name }}"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">进程数</th>
        <td><input type="text" class="form-control" name="numprocs" value="{{ program.numprocs }}"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">进程下标起始值</th>
        <td><input type="text" class="form-control" name="numprocs_start" value="{{ program.numprocs_start }}"></td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">目录</th>
        <td><input type="text" class="form-control" name="directory" value="{{ program.directory }}"></td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">自动启动</th>
        <td>
            <select class="form-control" name="autostart">
                <option value="true">true</option>
                <option value="false">false</option>
            </select>
        </td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">启动重试次数</th>
        <td><input type="text" class="form-control" name="startretries" value="{{ program.startretries }}"></td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">自动重启</th>
        <td>
            <select class="form-control" name="autorestart">
                <option value="true">true</option>
                <option value="false">false</option>
            </select>
        </td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">错误重定向(redirect_stderr)</th>
        <td>
            <select class="form-control" name="redirect_stderr">
                <option value="true">true</option>
                <option value="false">false</option>
            </select>
        </td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">标准输出日志文件(stdout_logfile)</th>
        <td><input type="text" class="form-control" name="stdout_logfile" value="{{ program.stdout_logfile }}"></td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;">标准输出日志备份</th>
        <td><input type="text" class="form-control" name="stdout_logfile_backups" value="{{ program.stdout_logfile_backups }}"></td>
    </tr>
    <tr class="expand-tr hidden">
        <th style="width: 15%; vertical-align: middle;"> 标准输出日志的最大字节数</th>
        <td><input type="text" class="form-control" name="stdout_logfile_maxbytes" value="{{ program.stdout_logfile_maxbytes }}"></td>
    </tr>
    <tr>
        <th style="width: 15%; vertical-align: middle;">操作</th>
        <td>
            <button class="btn btn-sm btn-success">修改</button>
            <button class="btn btn-sm btn-danger">删除</button>
            <button class="btn btn-sm btn-primary">复制</button>
            <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开配置</a>
        </td>
    </tr>
    </tbody>
</table>
{% endfor %}

<h3>添加配置</h3>

<form method="post" action="/server/{{ server.id }}/config/create" class="add-form">
    <input type="hidden" value="{{ server.id }}" name="server_id" id="server_id">
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th style="width: 15%; vertical-align: middle;">程序名</th>
            <td><input type="text" class="form-control" name="program" value=""></td>
        </tr>
        <tr>
            <th style="width: 15%; vertical-align: middle;">命令</th>
            <td><input type="text" class="form-control" name="command" value=""></td>
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
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">目录</th>
            <td><input type="text" class="form-control" name="directory" value="%(here)s"></td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">自动启动</th>
            <td>
                <select class="form-control" name="autostart">
                    <option value="true">true</option>
                    <option value="false">false</option>
                </select>
            </td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">启动重试次数</th>
            <td><input type="text" class="form-control" name="startretries" value="20"></td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">自动重启</th>
            <td>
                <select class="form-control" name="autorestart">
                    <option value="true">true</option>
                    <option value="false">false</option>
                </select>
            </td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">错误重定向(redirect_stderr)</th>
            <td>
                <select class="form-control" name="redirect_stderr">
                    <option value="true">true</option>
                    <option value="false">false</option>
                </select>
            </td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">标准输出日志文件(stdout_logfile)</th>
            <td><input type="text" class="form-control" name="stdout_logfile" value="AUTO"></td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;">标准输出日志备份</th>
            <td><input type="text" class="form-control" name="stdout_logfile_backups" value="0"></td>
        </tr>
        <tr class="expand-tr hidden">
            <th style="width: 15%; vertical-align: middle;"> 标准输出日志的最大字节数</th>
            <td><input type="text" class="form-control" name="stdout_logfile_maxbytes" value="1MB"></td>
        </tr>
        <tr>
            <th style="width: 15%; vertical-align: middle;">操作</th>
            <td>
                <button type="submit" class="btn btn-sm btn-success">确定添加</button>
                <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开配置</a>
            </td>
        </tr>
        </tbody>
    </table>
</form>

<script>
$(function() {
    // 回到顶部
    $.scrollUp({
        animation: 'fade',
        scrollImg: true
    });

    $('.expand-tr').removeClass('hidden');

    $('.expand-btn').click(function() {
        var html = '';

        if ($(this).hasClass('expanded')) {
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开配置';
            $(this).html(html);
            $(this).removeClass('expanded');
            $(this).closest('table').find('tr.expand-tr').addClass('hidden');
        } else {
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起配置';
            $(this).html(html);
            $(this).addClass('expanded');
            $(this).closest('table').find('tr.expand-tr').removeClass('hidden');
        }
    });

    $('.expand-all-btn').click(function() {
        var html = '';

        if ($(this).hasClass('expanded')) {
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开所有';
            $(this).html(html);
            $(this).removeClass('expanded');

            $('.expand-btn').filter('.expanded').click();
        } else {
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起所有';
            $(this).html(html);
            $(this).addClass('expanded');

            $('.expand-btn').not('.expanded').click();
        }
    });

    $('.add-process-btn').click(function() {
        $('html, body').animate({scrollTop: $("h3").offset().top});
    });

    $('.add-form').submit(function() {
        event.preventDefault();

        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                window.location.reload();
            } else {
                error(data.message);
            }
        });
    });
});
</script>
