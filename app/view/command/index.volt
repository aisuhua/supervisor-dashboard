{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

{% if success is not empty %}
<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>命令已开始执行</p>
                <p>
                    <a href="/command/log/{{ command.id }}?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" data-nopjax target="_blank" class="btn btn-primary">查看日志</a>
                    或前往<a href="/command/history?server_id={{ server.id }}" id="link" data-nopjax>命令执行历史</a>页面执行更多操作。
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script>
$('#myModal').modal({
    backdrop: 'static',
    show: true
});
</script>
{% endif %}

<form method="post" action="/command?server_id={{ server.id }}" data-pjax>
    {{ form.render('server_id', ['value': server.id]) }}
    <div class="form-group">
        <label for="command">命令</label>
        {{ form.render('command') }}
    </div>
    <div class="form-group">
        <label for="user">用户</label>
        {{ form.render('user') }}
    </div>
    <button type="submit" class="btn btn-primary">执行</button>
</form>

