{{ content() }}
{{ flashSession.output() }}
{% include 'server-group/nav.volt' %}

<form method="post" action="/server/edit/{{ server.id }}" data-pjax>
    <div class="form-group">
        <label for="server_group_id">所属分组</label>
        {{ form.render('server_group_id') }}
    </div>
    <div class="form-group">
        <label for="ip">IP</label>
        {{ form.render('ip') }}
    </div>
    <div class="form-group">
        <label for="port">端口</label>
        {{ form.render('port') }}
    </div>
    <div class="form-group">
        <label for="username">用户名</label>
        {{ form.render('username') }}
    </div>
    <div class="form-group">
        <label for="password">密码</label>
        {{ form.render('password') }}
    </div>
    <div class="form-group">
        <label for="agent_port">agent port</label>
        {{ form.render('agent_port') }}
    </div>
    <div class="form-group">
        <label for="agent_root">agent root</label>
        {{ form.render('agent_root') }}
    </div>

    <button type="submit" class="btn btn-primary">保存</button>
</form>