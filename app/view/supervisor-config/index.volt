{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    <li><a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}">{{ server.ip }}:{{ server.port }}</a></li>
    <li class="active">修改配置</li>
</ol>

<div style="margin-bottom: 20px; float: left;">
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
            <tr class="expand-tr invisible">
                <th>目录</th>
                <td>{{ form.render('directory', ['value': program.directory ]) }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th>自动启动</th>
                <td>
                    {{ form.render('autostart', ['value': program.autostart ]) }}
                </td>
            </tr>
            <tr class="expand-tr invisible">
                <th>启动重试次数</th>
                <td>{{ form.render('startretries', ['value': program.startretries ]) }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th>自动重启</th>
                <td>
                    {{ form.render('autorestart', ['value': program.autorestart ]) }}
                </td>
            </tr>
            <tr class="expand-tr invisible">
                <th>错误重定向(redirect_stderr)</th>
                <td>
                    {{ form.render('redirect_stderr', ['value': program.redirect_stderr ]) }}
                </td>
            </tr>
            <tr class="expand-tr invisible">
                <th>标准输出日志文件(stdout_logfile)</th>
                <td>{{ form.render('stdout_logfile', ['value': program.stdout_logfile ]) }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th>标准输出日志备份</th>
                <td>{{ form.render('stdout_logfile_backups', ['value': program.stdout_logfile_backups ]) }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th> 标准输出日志的最大字节数</th>
                <td>{{ form.render('stdout_logfile_maxbytes', ['value': program.stdout_logfile_maxbytes ]) }}</td>
            </tr>
            <tr>
                <th>操作</th>
                <td>
                    <button type="submit" class="btn btn-sm btn-success">修改</button>
                    <a href="/server/{{ server.id }}/config/delete" class="btn btn-sm btn-danger btn-delete">删除</a>
                    <button type="button" class="btn btn-sm btn-primary btn-copy">复制</button>
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

<form method="post" action="/server/{{ server.id }}/config/create" class="form-create" id="form-create">
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
            <tr class="expand-tr invisible">
                <th>目录</th>
                <td>{{ form.render('directory') }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th>自动启动</th>
                <td>
                    {{ form.render('autostart') }}
                </td>
            </tr>
            <tr class="expand-tr invisible">
                <th>启动重试次数</th>
                <td>{{ form.render('startretries') }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th>自动重启</th>
                <td>
                    {{ form.render('autorestart') }}
                </td>
            </tr>
            <tr class="expand-tr invisible">
                <th>错误重定向(redirect_stderr)</th>
                <td>
                    {{ form.render('redirect_stderr') }}
                </td>
            </tr>
            <tr class="expand-tr invisible">
                <th>标准输出日志文件(stdout_logfile)</th>
                <td>{{ form.render('stdout_logfile') }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th>标准输出日志备份</th>
                <td>{{ form.render('stdout_logfile_backups') }}</td>
            </tr>
            <tr class="expand-tr invisible">
                <th> 标准输出日志的最大字节数</th>
                <td>{{ form.render('stdout_logfile_maxbytes') }}</td>
            </tr>
            <tr>
                <th>操作</th>
                <td>
                    <button class="btn btn-success">确认添加</button>
                    <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开配置</a>
                    <button type="reset" class="btn btn-xs btn-link">重置</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<script>
$(function() {

    // 修改表单和添加表单
    var $formEdit = $('form.form-edit');
    var $formCreate = $('form.form-create');
    var $expandAllBtn = $('.btn-group a.expand-all-btn');

    // 处理页面加载时表格的展开和收起
    var formAll = localStorage.getItem('form-all');
    if (formAll == 'open') {
        html = '<span class="glyphicon glyphicon-menu-up"></span> 收起所有';
        $expandAllBtn.html(html).addClass('expanded');
    }

    $formEdit.add($formCreate).each(function (index, value) {
        var id = $(this).attr('id');

        var item = localStorage.getItem(id);
        if (item || formAll) {
            if (item == 'open' || (item == null && formAll == 'open')) {
                $(this).find('table tr.expand-tr').removeClass('invisible');

                var html = '<span class="glyphicon glyphicon-menu-up"></span> 收起配置';
                $(this).find('a.expand-btn').html(html).addClass('expanded');
            } else {
                $(this).find('table tr.expand-tr').addClass('hidden').removeClass('invisible');
            }
        } else {
            $(this).find('table tr.expand-tr').addClass('hidden').removeClass('invisible');
        }
    });

    // 清理　localStorage 缓存
    function clearStorage() {
        var aLength = localStorage.length;
        console.log(aLength);
        for (var i = -1; i <= aLength; i++) {
            var aKeyName = localStorage.key(i);
            console.log(aKeyName);
            if (aKeyName && aKeyName.indexOf('form-') != -1) {
                localStorage.removeItem(aKeyName);
            }
        }
    }

    // 回到顶部
    $.scrollUp({
        animation: 'fade',
        scrollImg: true
    });

    //$('.expand-tr').removeClass('hidden');

    // 展开配置和收起配置切换
    $('a.expand-btn').click(function() {
        var html;
        var $form;

        if ($(this).hasClass('expanded')) {　// 收起
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开配置';
            $(this).html(html).removeClass('expanded');

            $form = $(this).closest('form');
            $form.find('table tr.expand-tr').addClass('hidden');

            localStorage.setItem($form.attr('id'), 'close');

            if ($('a.expand-btn').filter('.expanded').size() == 0) {
                html = '<span class="glyphicon glyphicon-menu-down"></span> 展开所有';
                $expandAllBtn.html(html).removeClass('expanded');

                //localStorage.setItem('form-all', 'close');
            }
        } else {　// 展开
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起配置';
            $(this).html(html).addClass('expanded');

            $form = $(this).closest('form');
            $form.find('table tr.expand-tr').removeClass('hidden');

            localStorage.setItem($form.attr('id'), 'open');

            if ($('a.expand-btn').not('.expanded').size() == 0) {
                html = '<span class="glyphicon glyphicon-menu-up"></span> 收起所有';
                $expandAllBtn.html(html).addClass('expanded');

                // localStorage.setItem('form-all', 'open');
            }
        }
    });

    // 展开所有和收起所有切换
    $expandAllBtn.click(function() {
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

            clearStorage();
            clearStorage();
            localStorage.setItem('form-all', 'close');
        } else {
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起所有';
            $this.html(html).addClass('expanded');

            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起配置';
            $('table tr td a.expand-btn').not('.expanded')
                .html(html)
                .addClass('expanded');

            $('table tr.expand-tr').removeClass('hidden');

            clearStorage();
            clearStorage();
            localStorage.setItem('form-all', 'open');
        }
    });

    function focusFormCreate() {
        $('html, body').animate({scrollTop: $formCreate.offset().top}, 'fast');
        $formCreate.find('input#program').focus();
    }

    // 添加配置事件处理
    $('a.add-process-btn').click(focusFormCreate);

    // 处理添加配置后页面刷新定位到页脚
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

    // 添加配置
    $formCreate.submit(function() {
        event.preventDefault();

        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                var url = window.location.pathname + window.location.search;
                url += '#form-create';
                $.pjax({url: url, container: '#pjax-container'});
            } else {
                error(data.message);
            }
        });
    });

    // 修改配置
    $formEdit.submit(function() {
        event.preventDefault();

        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                success(data.message);
            } else {
                error(data.message);
            }
        });
    });

    // 删除配置
    $formEdit.find('.btn-delete').click(function() {
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

    $formEdit.find('.btn-copy').click(function() {
        var $form = $(this).closest('form');

        $form.find('input, select').each(function(index, value) {
            var $element = $(value);

            if ($element.attr('id') == 'program') {
                $formCreate.find('#' + $element.attr('id')).val($element.val() + '_copy');
            } else {
                $formCreate.find('#' + $element.attr('id')).val($element.val());
            }
        });

        focusFormCreate();
    });
});
</script>
