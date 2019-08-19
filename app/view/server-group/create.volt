{{ content() }}

{{ flashSession.output() }}

<ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li><a href="/server-group">分组管理</a></li>
    <li class="active">添加分组</li>
</ol>

<form class="form-horizontal" method="post" action="/server-group/create" data-pjax>
    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">组名</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="name" name="name" value="{{ name }}" placeholder="" autocomplete="off">
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-sm-2 control-label">描述</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="description" name="description" value="{{ description }}" placeholder="" autocomplete="off">
        </div>
    </div>
    <div class="form-group">
        <label for="sort" class="col-sm-2 control-label">排序值</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="sort" name="sort" value="{{ sort | default(999) }}" placeholder="" autocomplete="off">
            <span id="helpBlock" class="help-block">值越小排得越靠前，有效值范围 0 ～ 999。</span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </div>
</form>