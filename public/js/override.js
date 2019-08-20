$.extend( true, $.fn.dataTable.defaults, {
    language: {
        "sProcessing": "处理中...",
        "sLengthMenu": "显示 _MENU_ 项结果",
        "sZeroRecords": "没有匹配结果",
        "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
        "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
        "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
        "sInfoPostFix": "",
        "sSearch": "搜索:",
        "sUrl": "",
        "sEmptyTable": "表中数据为空",
        "sLoadingRecords": "载入中...",
        "sInfoThousands": ",",
        "oPaginate": {
            "sFirst": "首页",
            "sPrevious": "上页",
            "sNext": "下页",
            "sLast": "末页"
        },
        "oAria": {
            "sSortAscending": ": 以升序排列此列",
            "sSortDescending": ": 以降序排列此列"
        },
        select: {
            rows: {
                _: "选中 %d 项",
                1: "选中 1 项",
                0: ""
            }
        }
    }
});

function initPNotify() {

    PNotify.prototype.options.styling = 'bootstrap3';

    var $pnotify = $('.pnotify');

    if($pnotify.size() <= 0) {
        return false;
    }

    $pnotify.each(function() {

        var $that = $(this);

        if($that.hasClass('alert-success')) {
            new PNotify({
                title: '成功提示',
                text: $that.html(),
                type: 'success',
                delay: 3000,
                desktop: {
                    desktop: false
                }
            });
        } else if($that.hasClass('alert-danger')) {
            new PNotify({
                title: '错误提示',
                text: $that.html(),
                type: 'error',
                desktop: {
                    desktop: false
                }
            });
        } else if($that.hasClass('alert-info')) {
            new PNotify({
                title: '温馨提示',
                text: $that.html(),
                type: 'info',
                desktop: {
                    desktop: true
                }
            });
        } else if($that.hasClass('alert-warning')) {
            new PNotify({
                title: '警告',
                text: $that.html(),
                type: 'notice',
                desktop: {
                    desktop: false
                }
            });
        }

        $that.remove();
    });
}


$(function () {

    /**
     * Pjax
     */
    //maximum cache size for previous container contents
    //https://github.com/defunkt/jquery-pjax
    $.pjax.defaults.maxCacheLength = 0;

    $(document).pjax('a', '#pjax-container');

    $(document).on('submit', 'form[data-pjax]', function(event) {
        $.pjax.submit(event, '#pjax-container');
    });

    /**
     * NProgress
     */
    $(document).on('pjax:send', function() {
        NProgress.start();
    });

    $(document).on('pjax:complete', function() {
        NProgress.done();
    });

    $(document).on("pjax:end", function() {
        initPNotify();
    });

    initPNotify();
});


