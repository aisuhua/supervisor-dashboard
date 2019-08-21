{{ content() }}

<ol class="breadcrumb">
    <li><a href="/server">服务器管理</a></li>
    <li class="active">添加服务器</li>
</ol>

<form class="form-horizontal" method="post" action="/server/create" data-pjax>
    <div class="form-group">
        <label for="server_group_id" class="col-sm-2 control-label">所属分组</label>
        <div class="col-sm-10">
            {{ form.render('server_group_id') }}
        </div>
    </div>
    <div class="form-group">
        <label for="ip" class="col-sm-2 control-label">服务器 IP</label>
        <div class="col-sm-10">
            {{ form.render('ip') }}
        </div>
    </div>
    <div class="form-group">
        <label for="port" class="col-sm-2 control-label">端口</label>
        <div class="col-sm-10">
            {{ form.render('port') }}
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">用户名</label>
        <div class="col-sm-10">
            {{ form.render('username') }}
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">密码</label>
        <div class="col-sm-10">
            {{ form.render('password') }}
        </div>
    </div>
    <div class="form-group">
        <label for="sync_conf_port" class="col-sm-2 control-label">sync_conf 端口</label>
        <div class="col-sm-10">
            {{ form.render('sync_conf_port') }}
        </div>
    </div>
    <div class="form-group">
        <label for="conf_path" class="col-sm-2 control-label">配置文件路径</label>
        <div class="col-sm-10">
            {{ form.render('conf_path') }}
        </div>
    </div>
    <div class="form-group">
        <label for="sort" class="col-sm-2 control-label">排序</label>
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