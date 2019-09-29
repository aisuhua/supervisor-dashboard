{{ content() }}
{{ flashSession.output() }}
{% include 'server-group/nav.volt' %}

<form method="post" action="/server-group/create" data-pjax>
    <div class="form-group">
        <label for="name">分组名称</label>
        {{ form.render('name') }}
    </div>
    <div class="form-group">
        <label for="sort">排序字段</label>
        {{ form.render('sort') }}
        <span id="helpBlock" class="help-block">值越大排得越靠前，有效值范围 0 ～ 999。</span>
    </div>
    <div class="form-group">
        <label for="description">备注</label>
        {{ form.render('description') }}
    </div>
    <button type="submit" class="btn btn-primary">提交</button>
</form>
