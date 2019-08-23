<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ name }} - 进程日志</title>
    <link rel="shortcut icon" type="image/png" href="/favicons/favicon.ico?v=2" />
    <script src='/js/jquery.min.js'></script>
    <style>
        * {
            padding: 0px;
            margin: 0px;
        }
        .head {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 40px;
            background-color: #f8f8f8;
            border-bottom: 1px solid rgb(231, 231, 231);
        }
        .head-blank {
            width: 100%;
            height: 41px;
        }
        .head .info {
            overflow: hidden;
            position: absolute;
            top: 0px;
            left: 0px;
            height: 40px;
            line-height: 40px;
            padding-left: 15px;
        }
        .head .info .tip {
            margin-left: 20px;
            color: #666;
        }
        .head a {
            cursor: pointer;
            position: absolute;
            display: block;
            top: 5px;
            right: 15px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            padding: 0px 13px;
            background-color: #337ab7;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            border: 1px solid #2e6da4;
        }
        .head a:hover {
            background: #286090;
        }
        .log {
            font-size: 14px;
            line-height: 1.5em;
            padding: 15px;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<div class='head'>
    <div class='info'>
        {{ name }} - 进程日志
        <span class='tip'></span>
    </div>
    <a class='btn'>自动刷新</a>
</div>
<div class='head-blank'></div>

<div class='log-wrap'>
    {#<pre class='log'>暂无日志: SNAPSHOT_CREATE_0_9</pre>#}
    <pre class='log'>{{ log[0] }}</pre>
</div>

<script>
    $(function() {


        // 滚动条跳到最后
        log.scrollBottom();

        // 点击按钮
        $('.btn').click(function() {

            // 开始自动刷新
            if ($(this).text() == '自动刷新') {

                // 开始自动刷新
                log.startAutoRefresh();
                $(this).text('停止自动刷新');
            } else {
                // 停止自动刷新
                log.stopAutoRefresh();
                $(this).text('自动刷新');
            }
        });

        // 用户支持Enter或空格键操作刷新
        $(document).keypress(function (event) {
            // Enter或空格键盘
            if (event.keyCode == 13 || event.keyCode == 32 || event.charCode == 32) {
                $('.btn').click();
            }
        });
    });


    /**
     * 日志操作对象
     */
    var log = {
        // 30秒
        time: 30,
        // 停止标志
        isAutoRefreshStop: false,
        // 开始自动刷新
        startAutoRefresh: function() {
            // 滚动条跳到最后
            this.scrollBottom();

            // 初始化
            this.isAutoRefreshStop = false;
            log.time = 30;

            // 倒计时
            $('.tip').html('(30秒内每秒自动刷新)');

            // 30秒内每秒自动刷新
            this.intervalId = setInterval(function() {

                // 停止
                if (log.isAutoRefreshStop) {
                    return;
                }

                // 刷新日志
                log.refresh();
                // 滚动条跳到最后
                log.scrollBottom();
                // 减一秒
                log.time--;
                // 倒计时
                $('.tip').html('(' + log.time + '秒内每秒自动刷新)');
                // 停止自动刷新
                if (log.time <= 0) {
                    log.stopAutoRefresh();
                }

            }, 1000);
        },
        // 刷新日志
        refresh: function() {
            // URL
            var url = location.href;
            // AJAX获取最新的日志
            $.get(url, {}, function (data) {
                $('.log').html(data);
                // 滚动条跳到最后
                log.scrollBottom();
            });
        },
        // 滚动条跳到最后
        scrollBottom: function() {
            $('html, body').scrollTop(function() {
                return $(this).height();
            });
        },
        // 停止自动刷新
        stopAutoRefresh: function() {
            // 停止标志
            this.isAutoRefreshStop = true;

            // 清除自动刷新
            clearInterval(this.intervalId);

            // 修改提示
            $('.tip').html('(自动刷新已停止)');


            $('.btn').text('自动刷新');

        }
    };

</script>
</body>
</html>


