{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<table id="cron-log-table" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th></th>
        <th>ID</th>
        <th>任务 ID</th>
        <th>命令</th>
        <th>状态</th>
        <th>耗时</th>
        <th>启动时间</th>
        <th>操作</th>
    </tr>
    </thead>
</table>

<script>
$(function() {
    var $cronLogTable = $('#cron-log-table');

    var dataTable = $cronLogTable.DataTable({
        processing: true,
        pageLength: 10,
        lengthChange: false,
        searching: true,
        serverSide: false,
        stateSave: true,
        ajax: {
            url: '/cron-log/list',
            data: function ( d ) {
                d.cron_id = {{ cron_id }};
                d.server_id = {{ server_id }}
            }
        },
        searchHighlight: true,
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        order: [
            [1, 'desc']
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: '',
                targets: 0,
                orderable: false,
                className: 'select-checkbox'
            },
            {
                data: 'id',
                targets: 1,
                orderable: false
            },
            {
                data: 'cron_id',
                targets: 2,
                orderable: false
            },
            {
                data: 'command',
                targets: 3,
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'status',
                targets: 4,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data == 0) {
                        return '<span class="">正在启动</span>';
                    } else if (data == 1) {
                        return '<span class="">正在执行</span>';
                    } else if (data == 2) {
                        return '<span class="text-success">已完成</span>';
                    } else if (data == -2) {
                        return '<span class="text-warning">无法确定</span>';
                    } else if (data == -3) {
                        return '<span class="text-warning">被中断执行</span>';
                    } else {
                        return '<span class="text-danger">失败</span>';
                    }
                }
            },
            {
                data: 'end_time',
                targets: 5,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data > 0 && full.start_time > 0) {
                        return fancyTimeFormat(data - full.start_time);
                    }

                    return '';
                }
            },
            {
                data: 'start_time',
                targets: 6,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data > 0) {
                        var date = new Date(data * 1000);
                        return date.format('Y-m-d H:i:s');
                    }
                    return '';
                }
            },
            {
                targets: 7,
                data: 'id',
                orderable: false,
                render: function (data, type, full, meta) {
                    var html = '';

                    if (full.status == 1) {
                        html += '<a href="/cron-log/log/'+ data +'?server_id='+ full.server_id + '&ip={{ server.ip }}&port={{ server.port }}" target="_blank" data-nopjax>预览日志</a>';
                        html += ' <span class="text-muted">|</span> ';
                        html += '<a href="/cron-log/stop/'+ data +'?server_id='+ full.server_id +'" class="stop" data-nopjax>立即停止</a>';
                    } else if (full.status != 0) {
                        html += '<a href="/cron-log/log/'+ data +'?server_id='+ full.server_id + '&ip={{ server.ip }}&port={{ server.port }}" target="_blank" data-nopjax>预览日志</a>';
                        html += ' <span class="text-muted">|</span> ';
                        html += '<a href="/cron-log/download/'+ data +'?server_id='+ full.server_id + '" data-nopjax>下载日志</a>';
                    }

                    return html;
                }
            }
        ],
        buttons: [
            {
                text: '删除',
                className: 'btn btn-default',
                action: function () {
                    var ids = '';
                    var count = 0;

                    dataTable.rows({selected: true}).every( function () {
                        var d = this.data();
                        ids += d.id + ',';
                        count++;
                    });

                    if (count <= 0)
                    {
                        alert('请选择要删除的日志');
                        return false;
                    }

                    if (confirm("真的要删除这 "+ count +" 条日志吗？")) {
                        var url = '/cron-log/delete?server_id={{ server.id }}';
                        $.pjax({
                            url: url,
                            container: '#pjax-container',
                            type: 'POST',
                            data: {ids: ids},
                            push: false
                        });
                    }
                    // $('#server-group-list').DataTable().ajax.reload();
                }
            }
        ],
        initComplete: function(settings, json) {
            dataTable.buttons().container().appendTo('#cron-log-table_wrapper .col-sm-6:eq(0)');
        }
    });

    $cronLogTable.on('click', 'tbody td a.stop', function () {
        var row = dataTable.row($(this).closest('tr'));
        var data = row.data();

        // 停止正在执行的任务
        if ($(this).hasClass('stop'))
        {
            if (!confirm('真的要停止 ID 为 ' + data.id + " 的任务吗？")) {
                return false;
            }

            $.post($(this).attr('href'), function(data) {
                if (data.state) {
                    success(data.message);
                } else {
                    error(data.message);
                }
            });

            return false;
        }
    });

});
</script>