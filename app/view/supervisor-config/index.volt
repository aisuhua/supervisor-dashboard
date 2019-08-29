{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    <li><a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}">{{ server.ip }}:{{ server.port }}</a></li>
    <li class="active">修改配置</li>
</ol>

<div style="margin-bottom: 20px;">
    <div class="btn-group" role="group">
        <a class="btn btn-default add-process-btn">添加配置</a>
        <a class="btn btn-default expand-all-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开全部</a>
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                更多 <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="/server/{{ server.id }}/config/ini-mode" id="load-config">INI 编辑模式</a></li>
                <li><a href="#">从其他服务器克隆到本机</a></li>
                <li><a href="#">从本机克隆到其他服务器</a></li>
             </ul>
        </div>
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
                <th>执行进程的用户</th>
                <td>{{ form.render('user', ['value': program.user ]) }}</td>
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
                    <a type="button" class="btn btn-sm btn-link btn-copy"><span class="glyphicon glyphicon-copy"></span> 复制</a>
                    <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>
{% else %}
    {#<div class="panel panel-default">#}
        {#<div class="panel-body">#}
            没有任何配置信息可修改，请先 <a href="javascript:void(0);" class="add-process-btn">添加配置</a>。
        {#</div>#}
    {#</div>#}
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
                <th>执行进程的用户</th>
                <td>{{ form.render('user') }}</td>
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
                    <button type="submit" class="btn btn-success">确定添加</button>
                    <a class="btn btn-sm btn-link btn-paste"><span class="glyphicon glyphicon-paste"></span> 粘贴</a>
                    <button type="reset" class="btn btn-sm btn-link"><i class="fa fa-undo" aria-hidden="true"></i> 重置</button>
                    <a class="btn btn-sm btn-link expand-btn"><span class="glyphicon glyphicon-menu-down"></span> 展开</a>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<!-- 模态框内容 -->
<div id="load-config-modal-wrapper"></div>

<script>
$(function() {

    var editor = null;

    // INI 编辑模式
    $('#load-config').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        
        var url = $(this).attr('href');
        $('#load-config-modal-wrapper').load(url, function() {

            editor = CodeMirror.fromTextArea(document.getElementById('ini-code'),{
                mode: "properties",
                lineNumbers: true,
                lineWrapping: true,
                indentUnit: 0,
                autoRefresh: true,
                automaticLayout: true,
                extraKeys: {
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });

            $('#load-config-modal').modal({
                show: true
            });

            $('#ini-submit').click(function() {
                var url = $(this).attr('data-url');
                $.post(url, {ini: editor.getValue()}, function(data) {
                    if (!data.state) {
                        error(data.message);
                        return false;
                    }

                    $.pjax({
                        url: window.location.pathname + window.location.search,
                        container: '#pjax-container',
                        push: true
                    });

                    $('#load-config-modal').modal('hide');
                });
            });
        });

        return false;
    });

    $('#load-config-modal-wrapper').on('shown.bs.modal', function() {
        editor.refresh();
    });

//    $('#load-config').click();


    // 修改表单和添加表单
    var $formEdit = $('form.form-edit');
    var $formCreate = $('form.form-create');
    var $expandAllBtn = $('.btn-group a.expand-all-btn');

    // 处理页面加载时表格的展开配置和收起配置
    var formAll = getItem('form-all');
    if (formAll == 'open') {
        html = '<span class="glyphicon glyphicon-menu-up"></span> 收起全部';
        $expandAllBtn.html(html).addClass('expanded');
    }

    $formEdit.add($formCreate).each(function (index, value) {
        var id = $(this).attr('id');

        var item = getItem(id);
        if (item || formAll) {
            if (item == 'open' || (item == null && formAll == 'open')) {
                $(this).find('table tr.expand-tr').removeClass('invisible');

                var html = '<span class="glyphicon glyphicon-menu-up"></span> 收起';
                $(this).find('.expand-btn').html(html).addClass('expanded');
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

        for (var i = -1; i <= aLength; i++) {
            var aKeyName = localStorage.key(i);
            if (aKeyName && aKeyName.indexOf('supervisor-form-') != -1) {
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

    // 展开配置配置和收起配置配置切换
    $('table tr td .expand-btn').click(function() {
        var html;
        var $form;

        if ($(this).hasClass('expanded')) {　// 收起配置
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开';
            $(this).html(html).removeClass('expanded');

            $form = $(this).closest('form');
            $form.find('table tr.expand-tr').addClass('hidden');

            setItem($form.attr('id'), 'close');

            if ($('table tr td .expand-btn').filter('.expanded').size() == 0) {
                html = '<span class="glyphicon glyphicon-menu-down"></span> 展开全部';
                $expandAllBtn.html(html).removeClass('expanded');

                //localStorage.setItem('form-all', 'close');
            }
        } else {　// 展开配置
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起';
            $(this).html(html).addClass('expanded');

            $form = $(this).closest('form');
            $form.find('table tr.expand-tr').removeClass('hidden');

            setItem($form.attr('id'), 'open');

            if ($('table tr td .expand-btn').not('.expanded').size() == 0) {
                html = '<span class="glyphicon glyphicon-menu-up"></span> 收起全部';
                $expandAllBtn.html(html).addClass('expanded');

                // localStorage.setItem('form-all', 'open');
            }
        }
    });

    // 展开配置所有和收起配置所有切换
    $expandAllBtn.click(function() {
        var html = '';
        var $this = $(this);

        if ($this.hasClass('expanded')) {
            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开全部';
            $this.html(html).removeClass('expanded');

            html = '<span class="glyphicon glyphicon-menu-down"></span> 展开';
            $('table tr td .expand-btn').filter('.expanded')
                .html(html)
                .removeClass('expanded');

            $('table tr.expand-tr').addClass('hidden');

            clearStorage();
            clearStorage();
            setItem('form-all', 'close');
        } else {
            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起全部';
            $this.html(html).addClass('expanded');

            html = '<span class="glyphicon glyphicon-menu-up"></span> 收起';
            $('table tr td .expand-btn').not('.expanded')
                .html(html)
                .addClass('expanded');

            $('table tr.expand-tr').removeClass('hidden');

            clearStorage();
            clearStorage();
            setItem('form-all', 'open');
        }
    });

    // 滚动到页脚的新增配置表单
    function focusFormCreate() {
        $('html, body').animate({scrollTop: $formCreate.offset().top}, 'fast');
        var $input = $formCreate.find('input#program');
        $input.val($input.val()).focus();
    }

    // 添加配置事件处理
    $('a.add-process-btn').click(function() {
        focusFormCreate();
        $formCreate.find('button[type=reset]').click();
    });

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

                var $input = $anchor.closest('form').find('input#program');
                $input.val($input.val()).focus();
            }
        }
    });

    // 添加配置
    $formCreate.submit(function(event) {
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
    $formEdit.submit(function(event) {
        event.preventDefault();

        $.post($(this).attr('action'), $(this).serialize(), function(data) {
            if (data.state) {
                var tmp = data.data;
                for (var key in tmp) {
                    $formEdit.find('#' + key).val(tmp[key]);
                }

                success(data.message);
            } else {
                error(data.message);
            }
        });
    });

    // 删除配置
    $formEdit.find('.btn-delete').click(function(event) {
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


//    $formEdit.find('.btn-copy').click(function() {
//        var $form = $(this).closest('form');
//
//        $form.find('input, select').each(function(index, value) {
//            var $element = $(value);
//
//            if ($element.attr('id') == 'program') {
//                $formCreate.find('#' + $element.attr('id')).val($element.val() + '_copy');
//            } else {
//                $formCreate.find('#' + $element.attr('id')).val($element.val());
//            }
//        });
//
//        focusFormCreate();
//    });

    $formEdit.find('.btn-copy').click(function() {
        setItem('config-copy', JSON.stringify($(this).closest('form').serializeArray()));

        $(this).tooltip({
            'title': '复制成功！'
        }).tooltip('show');
    });

    $formEdit.find('.btn-copy').mouseleave(function() {
        $(this).tooltip('destroy');
    });

    $formCreate.find('.btn-paste').click(function() {
        var copy = getItem('config-copy');
        if (!copy) {
            $(this).tooltip({
                'title': '没有任何内容'
            }).tooltip('show');

            return false;
        }

        copy = JSON.parse(copy);
        if (!copy) {
            $(this).tooltip({
                'title': '没有任何内容'
            }).tooltip('show');

            return false;
        }

        for (var i = 0; i < copy.length; i++) {

            if (copy[i].name == 'id' || copy[i].name == 'server_id') {
                continue;
            }

            var $element = $formCreate.find('[name=' + copy[i].name + ']');
            if ($element) {
                $element.val(copy[i].value);
            }
        }
    });
});
</script>
