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
<form method="post" action="/server/{{ server.id }}/config/edit/{{ program.id }}" class="form-edit" id="form-{{ program.id }}">
    <!-- anchor -->
    <a id="{{ program.program }}" style="position: relative; top: -65px;" class="invisible"></a>

    {{ form.render('id', ['value': program.id]) }}
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
                    <button type="submit" class="btn btn-sm btn-success">修改</button>
                    <a href="/server/{{ server.id }}/config/delete" class="btn btn-sm btn-danger btn-delete">删除</a>
                    <button class="btn btn-sm btn-primary">复制</button>
                    <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开配置</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>
{% else %}
    <div class="panel panel-default">
        <div class="panel-body">
            没有任何配置信息可修改，请先 <a href="javascript:void(0);" class="add-process-btn">添加配置</a>。
        </div>
    </div>
{% endfor %}

<hr>
{#<h3 id="add-config">添加配置</h3>#}

<form method="post" action="/server/{{ server.id }}/config/create#form-create" class="form-create" id="form-create">
    {{ form.render('server_id', ['value': server.id]) }}
    <table class="table table-bordered table-form">
    <caption id="add-config">添加配置</caption>
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

    $(document).on('pjax:complete', function() {
        var location_hash = window.location.hash;
        if (location_hash) {
            var $anchor = $(location_hash);
            if ($anchor.size() > 0) {
                $('html, body').animate({
                        scrollTop: $anchor.offset().top
                    }, 'fast'
                );

                $anchor.closest('form').find('input#program').focus();
            }
        }
    });

    //$('.expand-tr').removeClass('hidden');

    $('a.expand-btn').click(function() {
        var html = '';

        if ($(this).hasClass('expanded')) {
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开配置';
            $(this).html(html);
            $(this).removeClass('expanded');
            $(this).closest('table').find('tr.expand-tr').addClass('hidden');

            sessionStorage.setItem('name', 'suhua');
        } else {
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起配置';
            $(this).html(html);
            $(this).addClass('expanded');
            $(this).closest('table').find('tr.expand-tr').removeClass('hidden');
        }
    });

    $('a.expand-all-btn').click(function() {
        var html = '';
        var $this = $(this);

        if ($this.hasClass('expanded')) {
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开所有';
            $this.html(html).removeClass('expanded');

            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开配置';
            $('table tr td a.expand-btn').filter('.expanded')
                .html(html)
                .removeClass('expanded');

            $('table tr.expand-tr').addClass('hidden');
        } else {
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起所有';
            $this.html(html).addClass('expanded');

            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起配置';
            $('table tr td a.expand-btn').not('.expanded')
                .html(html)
                .addClass('expanded');

            $('table tr.expand-tr').removeClass('hidden');
        }
    });

    $('a.add-process-btn').click(function() {
        $('html, body').animate({scrollTop: $("#add-config").offset().top}, 'fast');
        $('.form-create input#program').focus();
    });

    $('form.form-create').submit(function() {
        event.preventDefault();

        // https://stackoverflow.com/questions/2573979/force-page-reload-with-html-anchors-html-js
        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                var url = window.location.pathname + window.location.search;
                // url = removeURLParameter(url, '_t');
                // url += '&_t=' + Date.now();
                url += '#form-create';
                $.pjax({url: url, container: '#pjax-container'});
            } else {
                error(data.message);
            }
        });
    });

    $('form.form-edit').submit(function() {
        event.preventDefault();

        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                success(data.message);
            } else {
                error(data.message);
            }
        });
    });

    $('form.form-edit .btn-delete').click(function() {
        event.stopPropagation();

        var $form = $(this).closest('form');

        if (!confirm("真的要删除 " + $form.find('input#program').val() + ' 吗？')) {
            return false;
        }

        var id = $form.find('input#id').val();
        var url = $(this).attr('href');

        $.post(url, {ids: id}, function(data) {
            if (data.state) {
                success(data.message);
                $form.remove();
            } else {
                error(data.message);
            }
        });

        return false;
    });
});
</script>
