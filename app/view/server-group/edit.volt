{{ content() }}

<ol class="breadcrumb">
    <li><a href="/server-group">分组管理</a></li>
    <li class="active">修改分组</li>
</ol>

<form class="form-horizontal" method="post" action="/server-group/edit/{{ serverGroup.id }}" data-pjax>

    {{ form.render('id') }}

    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">分组名称</label>
        <div class="col-sm-10">
            {{ form.render('name') }}
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-sm-2 control-label">描述</label>
        <div class="col-sm-10">
            {{ form.render('description') }}
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
            <button type="submit" class="btn btn-primary">提交修改</button>
            <a class="btn btn-default btn-link" href="/server-group">返回</a>
        </div>
    </div>
</form>