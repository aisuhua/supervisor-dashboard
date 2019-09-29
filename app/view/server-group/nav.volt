{#<ol class="breadcrumb">#}
    {#<li class="active">分组管理</li>#}
{#</ol>#}

{% set group_class = '' %}
{% set group_create_class = '' %}
{% set server_class = '' %}
{% set server_create_class = '' %}

{% if dispatcher.getControllerName() == 'server-group' %}
    {% if dispatcher.getActionName() == 'index' %}
        {% set group_class = 'active' %}
    {% elseif dispatcher.getActionName() == 'create' or dispatcher.getActionName() == 'edit' %}
        {% set group_create_class = 'active' %}
    {% endif %}
{% elseif dispatcher.getControllerName() == 'server' %}
    {% if dispatcher.getActionName() == 'index' %}
        {% set server_class = 'active' %}
    {% elseif dispatcher.getActionName() == 'create' or dispatcher.getActionName() == 'edit' %}
        {% set server_create_class = 'active' %}
    {% endif %}
{% endif %}

<ul id="" class="nav nav-tabs my-tabs1" role="tablist" style="margin-bottom: 20px;">
    <li role="presentation" class="{{ group_class }}"><a href="/server-group">分组列表</a></li>
    <li role="presentation" class="{{ group_create_class }}"><a href="/server-group/create">添加/修改分组</a></li>
    <li role="presentation" class="{{ server_class }}"><a href="/server">服务器列表</a></li>
    <li role="presentation" class="{{ server_create_class }}"><a href="/server/create?group_id={{ group_id }}">添加/修改服务器</a></li>
</ul>