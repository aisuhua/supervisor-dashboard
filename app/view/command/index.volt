{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<div class="alert alert-info" role="alert">
    该功能暂未开发完成。
</div>

<form method="post" action="" data-pjax>
    <div class="form-group">
        <label for="command">命令</label>
        <input class="form-control" type="text">
    </div>
    <div class="form-group">
        <label for="command">用户</label>
        <input class="form-control" type="text" value="www-data">
    </div>
    <button type="button" class="btn btn-primary" onclick="alert('该功能暂未开发完成。');">执行</button>
</form>

<div id="output_section" style="margin-top: 20px; display: none;">
    <h4>日志输出</h4>
    <pre id="task_output_container">暂无任何内容</pre>
</div>
