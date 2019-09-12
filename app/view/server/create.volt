{{ content() }}

<div class="page-header">
    <h1>添加服务器</h1>
</div>

<ol class="breadcrumb">
    {% if serverGroup is empty %}
        <li><a href="/server">服务器列表</a></li>
        {% set action = '/server/create' %}
        {% set style = '' %}
    {% else %}
        <li><a href="/server-group/{{ serverGroup.id }}/server">{{ serverGroup.name }}的服务器列表</a></li>
        {% set action = '/server-group/' ~ serverGroup.id ~ '/server/create' %}
        {% set style = 'display: none;' %}
    {% endif %}
    <li class="active">添加服务器</li>
</ol>

<form class="form-horizontal" method="post" action="{{ action }}" data-pjax>
    <div class="form-group" style="{{ style }}">
        <label for="server_group_id" class="col-sm-2 control-label">所属分组</label>
        <div class="col-sm-10">
            {% if serverGroup is empty %}
                {{ form.render('server_group_id') }}
            {% else %}
                {#{{ form.render('server_group_id', ['value': serverGroup.id, 'type': 'hidden', 'readonly': 'readonly']) }}#}
                <p class="form-control-static">{{ serverGroup.name }}</p>
                <input type="hidden" value="{{ serverGroup.id }}" id="server_group_id" name="server_group_id" />
            {% endif %}
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
        <label for="sync_conf_port" class="col-sm-2 control-label">同步配置端口</label>
        <div class="col-sm-10">
            {{ form.render('sync_conf_port') }}
            <span id="helpBlock" class="help-block">请填写启动 <a href="http://gitlab.115rc.com/suzhihua/supervisor-sync-config" target="_blank">supervisor-sync-conf</a> 时所使用的端口。</span>
        </div>
    </div>
    <div class="form-group">
        <label for="conf_path" class="col-sm-2 control-label">进程配置</label>
        <div class="col-sm-10">
            {{ form.render('process_conf') }}
        </div>
    </div>
    <div class="form-group">
        <label for="conf_path" class="col-sm-2 control-label">定时任务配置</label>
        <div class="col-sm-10">
            {{ form.render('cron_conf') }}
        </div>
    </div>
    <div class="form-group" style="display: none;">
        <label for="sort" class="col-sm-2 control-label">排序字段</label>
        <div class="col-sm-10">
            {{ form.render('sort') }}
            <span id="helpBlock" class="help-block">值越大排得越靠前，有效值范围 0 ～ 999。</span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>