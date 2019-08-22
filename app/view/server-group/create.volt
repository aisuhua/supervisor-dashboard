{{ content() }}

{#<ol class="breadcrumb">#}
    {#<li><a href="/server-group">分组管理</a></li>#}
    {#<li class="active">添加分组</li>#}
{#</ol>#}

<div class="page-header">
    <h1>添加分组</h1>
</div>

<ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li><a href="/server-group">分组列表</a></li>
    <li class="active">添加分组</li>
</ol>

<form class="form-horizontal" method="post" action="/server-group/create" data-pjax>
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">分组名称</label>
        <div class="col-sm-10">
            {{ form.render('name') }}
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-sm-2 control-label">分组描述</label>
        <div class="col-sm-10">
            {{ form.render('description') }}
        </div>
    </div>
    <div class="form-group">
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