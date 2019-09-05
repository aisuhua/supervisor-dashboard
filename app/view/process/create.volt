{{ content() }}
{{ flashSession.output() }}
{% include 'partials/processNav.volt' %}

{% if reload_config is not empty %}
<script>
    $.ajax({
        url: '/process/reloadConfig?server_id={{ server.id }}',
        success: function(data) {},
        timeout: 10000
    });
</script>
{% endif %}

{% set simple_class = '' %}
{% set advanced_class = '' %}

{% if mode is not empty %}
    {% set advanced_class = 'active' %}
{% else %}
    {% set simple_class = 'active' %}
{% endif %}

<!-- Nav tabs -->
<ul class="nav nav-pills" role="tablist" style="margin-bottom: 20px;">
    <li role="presentation" class="{{ simple_class }}"><a href="#simple" aria-controls="simple" role="tab" data-toggle="tab">简单</a></li>
    <li role="presentation" class="{{ advanced_class }}"><a href="#advanced" aria-controls="advanced" role="tab" data-toggle="tab">高级</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane {{ simple_class }}" id="simple">
        <form method="post" action="/process/create?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" data-pjax>
            {{ form.render('server_id', ['value': server.id]) }}
            <div class="form-group">
                <label for="program">程序名</label>
                {{ form.render('program') }}
            </div>
            <div class="form-group">
                <label for="command">命令</label>
                {{ form.render('command') }}
            </div>
            <div class="form-group">
                <label for="process_name">进程名</label>
                {{ form.render('process_name') }}
            </div>
            <div class="form-group">
                <label for="numprocs">进程数</label>
                {{ form.render('numprocs') }}
            </div>
            <div class="form-group">
                <label for="numprocs_start">进程下标起始值</label>
                {{ form.render('numprocs_start') }}
            </div>
            <div class="form-group">
                <label for="user">执行进程的用户</label>
                {{ form.render('user') }}
            </div>
            <div class="form-group">
                <label for="directory">目录</label>
                {{ form.render('directory') }}
            </div>
            <div class="form-group">
                <label for="autostart">自动启动</label>
                {{ form.render('autostart') }}
            </div>
            <div class="form-group">
                <label for="startretries">启动重试次数</label>
                {{ form.render('startretries') }}
            </div>
            <div class="form-group">
                <label for="autorestart">自动重启</label>
                {{ form.render('autorestart') }}
            </div>
            <div class="form-group">
                <label for="redirect_stderr">错误重定向</label>
                {{ form.render('redirect_stderr') }}
            </div>
            <div class="form-group">
                <label for="stdout_logfile">标准输出日志文件</label>
                {{ form.render('stdout_logfile') }}
            </div>
            <div class="form-group">
                <label for="stdout_logfile_backups">标准输出日志备份</label>
                {{ form.render('stdout_logfile_backups') }}
            </div>
            <div class="form-group">
                <label for="stdout_logfile_maxbytes">标准输出日志的最大字节数</label>
                {{ form.render('stdout_logfile_maxbytes') }}
            </div>
            <button type="submit" class="btn btn-primary">确认添加</button>
        </form>
    </div>

    <div role="tabpanel" class="tab-pane {{ advanced_class }}" id="advanced">
        <form method="post" action="/process/create?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" data-pjax>
            {{ form.render('server_id', ['value': server.id]) }}
            <input type="hidden" name="mode" value="ini" />

            <div class="form-group">
                <textarea id="ini" name="ini">{{ ini }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">确认添加</button>
        </form>
    </div>
</div>

<script>
$(function() {

    // code editor
    var editor = CodeMirror.fromTextArea(document.getElementById('ini'), {
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

    editor.setSize('100%', '100%');

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        editor.refresh();
    });
});



</script>