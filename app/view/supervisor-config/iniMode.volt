{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    <li><a href="/server/{{ server.id }}/process?ip={{ server.ip }}&port={{ server.port }}">{{ server.ip }}:{{ server.port }}</a></li>
    {#<li><a href="/server/{{ server.id }}/config">修改配置</a></li>#}
    {#<li class="active">INI 编辑模式</li>#}
</ol>

<ul class="nav nav-tabs">
    <li role="presentation" class="active"><a href="#">配置列表</a></li>
    <li role="presentation"><a href="#">INI 模式</a></li>
    <li role="presentation"><a href="#">添加/修改配置</a></li>
    <li role="presentation"><a href="#">从其他服务器克隆</a></li>
    <li role="presentation"><a href="#">克隆到其他服务器</a></li>
</ul>

<textarea id="ini-code" class="ini-code">{{ ini }}</textarea>

