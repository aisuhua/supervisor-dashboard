{{ content() }}

<div class="page-header">
    <h1>修改服务器</h1>
</div>

<ol class="breadcrumb">
    {% if serverGroup is empty %}
        <li><a href="/server">服务器列表</a></li>
        {% set action = '/server/edit/' ~ server.id %}
        {% set style = '' %}
    {% else %}
        <li><a href="/server-group/{{ serverGroup.id }}/server">{{ serverGroup.name }}的服务器列表</a></li>
        {% set action = '/server-group/' ~ serverGroup.id ~ '/server/edit/' ~ server.id %}
        {% set style = 'display: none;' %}
    {% endif %}
    <li class="active">修改服务器</li>
</ol>

<form class="form-horizontal" method="post" action="{{ action }}" data-pjax>
    <div class="form-group" style="{{ style }}">
        <label for="server_group_id" class="col-sm-2 control-label">所属分组</label>
        <div class="col-sm-10">
            {{ form.render('server_group_id') }}
        </div>
    </div>
    <div class="form-group">
        <label for="ip" class="col-sm-2 control-label">IP 地址</label>
        <div class="col-sm-10">
            {{ form.render('ip') }}
        </div>
    </div>
    <div class="form-group">
        <label for="port" class="col-sm-2 control-label">Supervisor 端口</label>
        <div class="col-sm-10">
            {{ form.render('port') }}
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">Supervisor 用户名</label>
        <div class="col-sm-10">
            {{ form.render('username') }}
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">Supervisor 密码</label>
        <div class="col-sm-10">
            {{ form.render('password') }}
        </div>
    </div>
    <div class="form-group">
        <label for="agent_port" class="col-sm-2 control-label">agent port</label>
        <div class="col-sm-10">
            {{ form.render('agent_port') }}
        </div>
    </div>
    <div class="form-group">
        <label for="agent_root" class="col-sm-2 control-label">agent root</label>
        <div class="col-sm-10">
            {{ form.render('agent_root') }}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>