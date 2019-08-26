{{ content() }}
{{ flashSession.output() }}

<div id="myModal"></div>

<script>
// $(document).ready(function(){
//     console.log('111');
//     $('#myModal').modal('show')
// });
// $(window).load(function(){
//     //$('#myModal').modal({ show: false});
// });

</script>

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
<form method="post" action="/server/{{ server.id }}/config/create" class="form-update" id="{{ program.program }}">
    {{ form.render('server_id', ['value': program.server_id]) }}
    <table class="table table-bordered table-form">
        <tbody>
            <tr>
                <th>程序名</th>
                <td>{{ form.render('program', ['value': program.program ]) }}</td>
            </tr>
            <tr>
                <th>命令</th>
                <td>{{ form.render('command', ['value': program.command ]) }}</td>
            </tr>
            <tr>
                <th>进程名</th>
                <td>{{ form.render('process_name', ['value': program.process_name ]) }}</td>
            </tr>
            <tr>
                <th>进程数</th>
                <td>{{ form.render('numprocs', ['value': program.numprocs ]) }}</td>
            </tr>
            <tr>
                <th>进程下标起始值</th>
                <td>{{ form.render('numprocs_start', ['value': program.numprocs_start ]) }}</td>
            </tr>
            <tr class="expand-tr hidden">
                <th>目录</th>
                <td>{{ form.render('directory', ['value': program.directory ]) }}</td>
            </tr>
            <tr class="expand-tr hidden">
                <th>自动启动</th>
                <td>
                    {{ form.render('autostart', ['value': program.autostart ]) }}
                </td>
            </tr>
            <tr class="expand-tr hidden">
                <th>启动重试次数</th>
                <td>{{ form.render('startretries', ['value': program.startretries ]) }}</td>
            </tr>
            <tr class="expand-tr hidden">
                <th>自动重启</th>
                <td>
                    {{ form.render('autorestart', ['value': program.autorestart ]) }}
                </td>
            </tr>
            <tr class="expand-tr hidden">
                <th>错误重定向(redirect_stderr)</th>
                <td>
                    {{ form.render('redirect_stderr', ['value': program.redirect_stderr ]) }}
                </td>
            </tr>
            <tr class="expand-tr hidden">
                <th>标准输出日志文件(stdout_logfile)</th>
                <td>{{ form.render('stdout_logfile', ['value': program.stdout_logfile ]) }}</td>
            </tr>
            <tr class="expand-tr hidden">
                <th>标准输出日志备份</th>
                <td>{{ form.render('stdout_logfile_backups', ['value': program.stdout_logfile_backups ]) }}</td>
            </tr>
            <tr class="expand-tr hidden">
                <th> 标准输出日志的最大字节数</th>
                <td>{{ form.render('stdout_logfile_maxbytes', ['value': program.stdout_logfile_maxbytes ]) }}</td>
            </tr>
            <tr>
                <th>操作</th>
                <td>
                    <button class="btn btn-sm btn-success">修改</button>
                    <button class="btn btn-sm btn-danger">删除</button>
                    <button class="btn btn-sm btn-primary">复制</button>
                    <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开配置</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>
{% endfor %}

<h3 id="add-config" name="add-config">添加配置</h3>

<form method="post" action="/server/{{ server.id }}/config/create" class="form-create">
    {{ form.render('server_id', ['value': server.id]) }}
    <table class="table table-bordered table-form">
    <tbody>
    <tr>
        <th>程序名</th>
        <td>{{ form.render('program') }}</td>
    </tr>
    <tr>
        <th>命令</th>
        <td>{{ form.render('command') }}</td>
    </tr>
    <tr>
        <th>进程名</th>
        <td>{{ form.render('process_name') }}</td>
    </tr>
    <tr>
        <th>进程数</th>
        <td>{{ form.render('numprocs') }}</td>
    </tr>
    <tr>
        <th>进程下标起始值</th>
        <td>{{ form.render('numprocs_start') }}</td>
    </tr>
    <tr class="expand-tr hidden">
        <th>目录</th>
        <td>{{ form.render('directory') }}</td>
    </tr>
    <tr class="expand-tr hidden">
        <th>自动启动</th>
        <td>
            {{ form.render('autostart') }}
        </td>
    </tr>
    <tr class="expand-tr hidden">
        <th>启动重试次数</th>
        <td>{{ form.render('startretries') }}</td>
    </tr>
    <tr class="expand-tr hidden">
        <th>自动重启</th>
        <td>
            {{ form.render('autorestart') }}
        </td>
    </tr>
    <tr class="expand-tr hidden">
        <th>错误重定向(redirect_stderr)</th>
        <td>
            {{ form.render('redirect_stderr') }}
        </td>
    </tr>
    <tr class="expand-tr hidden">
        <th>标准输出日志文件(stdout_logfile)</th>
        <td>{{ form.render('stdout_logfile') }}</td>
    </tr>
    <tr class="expand-tr hidden">
        <th>标准输出日志备份</th>
        <td>{{ form.render('stdout_logfile_backups') }}</td>
    </tr>
    <tr class="expand-tr hidden">
        <th> 标准输出日志的最大字节数</th>
        <td>{{ form.render('stdout_logfile_maxbytes') }}</td>
    </tr>
    <tr>
        <th>操作</th>
        <td>
            <button class="btn btn-sm btn-success">确认添加</button>
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

    if (window.location.hash) {
        //$('html, body').animate({scrollTop: $(window.location.hash).offset().top - 70}, 'fast');
    }

    //$('.expand-tr').removeClass('hidden');

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
        $('html, body').animate({scrollTop: $("h3").offset().top}, 'fast');
        $('.form-create input#program').focus();
    });

    $('.form-create').submit(function() {
        event.preventDefault();

        // https://stackoverflow.com/questions/2573979/force-page-reload-with-html-anchors-html-js
        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                var url = window.location.pathname + window.location.search;
                url = removeURLParameter(url, '_t');
                url += '&_t=' + Date.now() + '#add-config';
                window.location.href = url;
                // window.location.reload(true);
            } else {
                error(data.message);
            }
        });
    });
});
</script>
