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
    var dataTable = $('#cron-log-table').DataTable({
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
                        return '<span class="">正在执行</span>';
                    } else if (data == 1) {
                        return '<span class="">正在执行</span>';
                    } else if (data == 2) {
                        return '<span class="text-">已完成</span>';
                    } else {
                        return '<span class="text-danger">执行失败</span>';
                    }
                }
            },
            {
                data: 'end_time',
                targets: 5,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data > 0) {
                        return Math.abs((data - full.start_time)) + "秒" ;
                    }

                    return data;
                }
            },
            {
                data: 'start_time',
                targets: 6,
                orderable: false,
                render: function (data, type, full, meta) {
                    var date = new Date(data * 1000);
                    return date.format('Y-m-d H:i:s');
                }
            },
            {
                targets: 7,
                data: 'id',
                orderable: false,
                render: function (data, type, full, meta) {
                    var html = '<a href="/server-group/edit/'+ data +'">查看日志</a>';

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
                        alert('请先选择分组');
                        return false;
                    }

                    if (confirm("真的要删除这"+ count +"个分组吗？")) {
                        var url = '/server-group/delete';
                        $.pjax({
                            url: url,
                            container: '#pjax-container',
                            type: 'POST',
                            data: {ids: ids}
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

});
</script>