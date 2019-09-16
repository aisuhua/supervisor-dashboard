{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<form method="post" action="/process/ini?server_id={{ server.id }}" data-pjax>
    <div class="form-group">
        <textarea id="ini" name="ini" style="height: auto;" class="form-control">{{ ini }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">保存</button>
</form>

<script>
$(function() {
    autosize(document.querySelector('textarea'));
});
</script>