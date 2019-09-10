{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<form method="post" action="/cron/create?server_id={{ server.id }}" data-pjax>
    {{ form.render('server_id', ['value': server.id]) }}
    <div class="col-lg-6">
        <div class="form-group">
            <label for="command">命令</label>
            {{ form.render('command') }}
        </div>
        <div class="form-group">
            <label for="description">备注</label>
            {{ form.render('description') }}
        </div>
        <div class="form-group">
            <label for="user">用户</label>
            {{ form.render('user') }}
        </div>
        <div class="form-group">
            <label for="status">状态</label>
            {{ form.render('status') }}
        </div>
        <div class="form-group">
            <label for="description">持续运行至少秒数</label>
            <input type="text" class="form-control" value="0"/>
        </div>
        <div class="form-group">
            <label for="description">保留日志数量</label>
            <input type="text" class="form-control" value="60"/>
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="times">预定义执行周期</label>
            {{ form.render('times') }}
        </div>
        <div class="form-group">
            <label for="time">时间</label>
            {{ form.render('time') }}
        </div>
        <pre>
*    *    *    *    *
-    -    -    -    -
|    |    |    |    |
|    |    |    |    |
|    |    |    |    +----- 星期 (0 - 7) (星期日 = 0 或 7)
|    |    |    +---------- 月 (1 - 12)
|    |    +--------------- 日 (1 - 31)
|    +-------------------- 小时 (0 - 23)
+------------------------- 分钟 (0 - 59)
        </pre>
        <h4>下次运行时间</h4>
        <div id="date-list" data-url="/public/getRunDates"></div>
    </div>
</form>

<script>
$(function() {
    function getRunDates() {
        var $dateList = $('#date-list');

        $.get($dateList.attr('data-url'), {time: $('#time').val()}, function (data) {
            $dateList.html(data);
        });
    }

    $('#times').change(function() {
        $('#time').val($(this).val());
        getRunDates();
    });

    var $time = $('#time');

    $time.change(function() {
        getRunDates();
    });

    if($time.length) {
        getRunDates();
    }
});
</script>