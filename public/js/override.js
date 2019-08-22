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

    PNotify.defaults.styling = 'bootstrap3';
    PNotify.defaults.icons = 'bootstrap3';
    PNotify.closeAll();

    var $pnotify = $('.pnotify');

    if($pnotify.size() <= 0) {
        return false;
    }

    if (typeof window.stackTopCenter === 'undefined') {
        window.stackTopCenter = {
            'dir1': 'down',
            'firstpos1': 25
        };
    }

    var opts = {
        // stack: window.stackTopCenter
    };

    $pnotify.each(function() {
        var $that = $(this);
        opts.text = $that.html();

        if($that.hasClass('alert-success')) {
            opts.type = 'success';
            // opts.delay = 5000;
        } else if($that.hasClass('alert-danger')) {
            opts.type = 'error';
        } else if($that.hasClass('alert-info')) {
            opts.type = 'info';
        } else if($that.hasClass('alert-warning')) {
            opts.type = 'warning';
        }

        PNotify.alert(opts);
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


