{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<form method="post" action="/process/ini?server_id={{ server.id }}&ip={{ server.ip }}&port={{ server.port }}" data-pjax>
    <div class="form-group">
        <textarea id="ini" name="ini">{{ ini }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">确定修改</button>
</form>