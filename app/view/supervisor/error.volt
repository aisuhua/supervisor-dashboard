{% include 'process/breadcrumb.volt' %}

<div class="alert alert-danger" role="alert">
    <h4>Supervisor 连接失败</h4>
    <p>{{ message }}</p>
    <p>可能是以下原因引起：</p>
    <ul>
        {% if message == 'Unauthorized' %}
            <li>Supervisor 的帐号或密码不正确，<a href="###">修改配置</a>。</li>
        {% else %}
            <li>Supervisor 服务没有启动。</li>
            <li>Supervisor 的 XML-RPC 端口不正确，<a href="###">修改配置</a>。</li>
        {% endif %}
    </ul>
    <div style="padding-bottom: 10px;"></div>
    <p>
        <a class="btn btn-danger" href="javascript:void(0);" onclick="window.location.reload(true);">重试</a>
    </p>
</div>