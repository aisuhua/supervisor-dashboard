{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<form method="post" action="/command?server_id={{ server.id }}" id="form-command">
    {{ form.render('server_id', ['value': server.id]) }}
    <div class="form-group">
        <label for="command">命令</label>
        {{ form.render('command') }}
    </div>
    <div class="form-group">
        <label for="user">用户</label>
        {{ form.render('user') }}
    </div>
    <button type="submit" class="btn btn-primary">执行</button>
    <button type="button" class="btn btn-warning" style="display: none;">停止</button>
</form>

<div id="output" style="margin-top: 20px;display: none;">
    <p>日志输出
        <span class="text-success" style="display: none;" id="output-info">已执行完毕</span>
        <span class="fa fa-spinner fa-pulse fa-fw" style="display: none;" id="output-icon"></span>
    </p>
    <pre id="output-log" style="max-height: 500px;"></pre>
</div>

<script>
$(function() {

    $('#form-command').submit(function() {

        $('#output-log').html('');
        $('#output-info').hide();
        $('#output-icon').hide();

        $.post($(this).attr('action'), $(this).serialize(), function(data) {

            if (!data.state) {
                error(data.message);
                return false;
            }

            success(data.message);
            var command = data.command;
            var timeoutId = null;
            var stop = false;

            function tailLog() {
                $.ajax({
                    url : '/command/tailLog/' + command.id + '?server_id=' + {{ server.id }},
                    async: false,
                    success : function(data) {
                        console.log(data);

                        if (data.state == 1) {
                            // 执行完成
                            if (!data.log) {
                                data.log = '无任何日志'
                            }

                            if (timeoutId) {
                                clearTimeout(timeoutId);
                            }

                            stop = true;

                            $('#output-icon').hide();
                            $('#output-info').show();

                            $('#output-log').html(data.log);
                            $('#output').show();
                        } else if (data.state == 2) {
                            // 正在执行
                            if (!data.log) {
                                data.log = '无任何日志'
                            }

                            $('#output-info').hide();
                            $('#output-icon').show();

                            // 执行完成
                            $('#output-log').html(data.log);
                            $('#output').show();
                        } else {
                            // 执行失败
                            error(data.message);
                            stop = true;

                            if (timeoutId) {
                                clearTimeout(timeoutId);
                            }
                        }
                    }
                });
            }

//            tailLog();
//            timeoutId = setTimeout(function run() {
//                if (stop) {
//                    clearTimeout(timeoutId);
//                    return false;
//                }
//                tailLog();
//                timeoutId = setTimeout(run, 1000);
//            }, 1000);
        });

        return false;
    });
});
</script>