{{ content() }}
{{ flashSession.output() }}
{% include 'server-group/nav.volt' %}

<table id="process-list" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th></th>
        <th>ID</th>
        <th>所在分组</th>
        <th>所在服务器</th>
        <th>用户</th>
        <th>时间</th>
        <th>命令</th>
        <th>状态</th>
        <th>下次执行时间</th>
        <th>上次执行时间</th>
        <th>更新时间</th>
        <th>备注</th>
        <th>操作</th>
    </tr>
    </thead>
</table>

<script>
$(function() {
    var $processList = $('#process-list');

    var dataTable = $processList.DataTable({
        processing: true,
        pageLength: 10,
        lengthChange: false,
        searching: true,
        serverSide: false,
        stateSave: true,
        ajax: '/cron/list',
        searchHighlight: true,
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        order: [
            [2, 'desc']
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
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'group_name',
                targets: 2,
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'server_ip',
                targets: 3,
                orderable: false
            },
            {
                data: 'user',
                targets: 4,
                orderable: false,
                visible: true
            },
            {
                data: 'time',
                targets: 5,
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'command',
                targets: 6,
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'status',
                targets: 7,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data) {
                        return '<span class="text-success">启用</span>';
                    } else {
                        return '<span class="text-danger">停用</span>';
                    }
                }
            },
            {
                data: 'next_time',
                targets: 8,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data > 0) {
                        var date = new Date(data * 1000);
                        return date.format('Y-m-d H:i');
                    }
                    return '';
                }
            },
            {
                data: 'last_time',
                targets: 9,
                orderable: false,
                render: function (data, type, full, meta) {
                    if (data > 0) {
                        var date = new Date(data * 1000);
                        return date.format('Y-m-d H:i');
                    }
                    return '';
                }
            },
            {
                data: 'update_time',
                targets: 10,
                orderable: false,
                render: function (data, type, full, meta) {
                    return timeAgo(data);
                }
            },
            {
                data: 'description',
                targets: 11,
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                targets: 12,
                data: 'id',
                orderable: false,
                render: function (data, type, full, meta) {
                    var html = '<a href="/cron?server_id='+ full.server_id +'#'+ data +'" target="_blank" data-nopjax>查看</a>';
                    html += '<span class="text-muted"> | </span>';
                    html += '<a href="/cron/edit/'+ data +'?server_id='+ full.server_id + '" target="_blank" data-nopjax>修改</a>';

                    return html;
                }
            }
        ],
        buttons: [
            {
                text: '删除',
                className: 'btn btn-danger',
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

                    if (confirm("真的要删除这 "+ count +" 个分组吗？")) {
                        var url = '/server-group/delete';
                        $.pjax({
                            url: url,
                            container: '#pjax-container',
                            type: 'POST',
                            data: {ids: ids},
                            push: false
                        });
                    }
                }
            }
        ],
        initComplete: function(settings, json) {
            dataTable.buttons().container().appendTo('#server-group-list_wrapper .col-sm-6:eq(0)');
        }
    });

    $processList.on('click', 'td a.delete', function () {
        var row = dataTable.row($(this).closest('tr'));
        var data = row.data();

        if (!confirm('真的要删除 '+ data.name +' 吗？')) {
            return false;
        }

        var url = '/server-group/delete';
        $.pjax({
            url: url,
            container: '#pjax-container',
            type: 'POST',
            data: {ids: data.id},
            push: false
        });
    });
});
</script>