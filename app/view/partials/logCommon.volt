<samp id="log">{{ log | escape | default("没有任何日志记录") }}</samp>

<script>
$(function() {
    $(document).off('pjax:send');
    $(document).off('pjax:complete');
    $(document).off('pjax:end');
    $(document).off('ajaxStart');
    $(document).off('ajaxStop');

    var offset = {{ offset }};
    var log = document.querySelector('#log');
    var xhr = null;

    function scrollToButton() {
        $('html, body').scrollTop(function() {
            return $(this).height();
        });
    }

    $('#refresh').click(function() {
        var url = $(this).attr('href');

        if (log.innerHTML == '没有任何日志记录') {
            log.innerHTML = '';
        }

        if ($(this).hasClass('refreshing')) {
            if (xhr) {
                xhr.abort();
            }
            $(this).removeClass('refreshing').html('自动刷新');

            return false;
        }

        $(this).addClass('refreshing').html('停止刷新 <i class="fa fa-spinner fa-pulse fa-fw"></i>');
        // 滚动到页底
        scrollToButton();

        function refresh() {
            url = removeURLParameter(removeURLParameter(url, 'r'), 'offset');
            url = url + '&offset=' + offset + '&r=' + Math.random();

            xhr = $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.state <= 0) {
                        info(data.message);

                        $('#refresh').removeClass('refreshing').html('自动刷新');
                        return false;
                    }

                    if (data.log.length > 0) {
                        // https://stackoverflow.com/questions/18393981/append-vs-html-vs-innerhtml-performance
                        var c = document.createDocumentFragment();
                        var e = document.createElement("span");
                        e.textContent = data.log;
                        c.appendChild(e);
                        log.appendChild(c);
                    }

                    // 修改 offset
                    offset = data.offset;
                    scrollToButton();

                    refresh();
                }
            });
        }

        refresh();

        return false;
    });
});
</script>