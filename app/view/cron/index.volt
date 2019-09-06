{{ content() }}
{{ flashSession.output() }}
{% include 'process/nav.volt' %}

<table id="cron-list" class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th></th>
        <th>ID</th>
        <th>时间</th>
        <th>命令</th>
        <th>状态</th>
        <th>说明</th>
        <th>下次执行时间</th>
        <th>上次执行时间</th>
        <th></th>
    </tr>
    </thead>
</table>

<script>

    $(document).ready(function() {

        var dataTable = $('#cron-list').DataTable({
            processing: true,
            pageLength: 100,
            lengthChange: true,
            searching: true,
            serverSide: false,
            stateSave: true,
            ajax: '/cron/list',
            searchHighlight: true,
            order: [
                [1, 'asc']
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
                    data: 'time',
                    targets: 2,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return data;
                    }
                },
                {
                    data: 'command',
                    targets: 3,
                    orderable: false
                },
                {
                    data: 'status',
                    targets: 4,
                    orderable: false
                },
                {
                    data: 'description',
                    targets: 5,
                    orderable: false
                },
                {
                    data: 'last_time',
                    targets: 6,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        if (data > 0) {
                            return new Date(data * 1000).format('Y-m-d H:i');
                        }

                        return '-';
                    }
                },
                {
                    data: 'last_time',
                    targets: 7,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        if (data > 0) {
                            return new Date(data * 1000).format('Y-m-d H:i');
                        }

                        return '-';
                    }
                },
                {
                    targets: 8,
                    data: 'id',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var html = '<a href="/server-group/edit/'+ data +'">修改</a> | ';
                        html += '<a href="javascript: void(0);">查看日志</a> | ';
                        html += '<a href="/cron/active/'+ data +'" class="active">激活</a> | ';
                        html += '<a href="/cron/delete/'+ data +'" class="delete">删除</a>';

                        return html;
                    }
                }
            ]
        });

        $('#cron-list tbody').on('click', 'td a.delete', function () {
            event.stopPropagation();

            var row = dataTable.row($(this).closest('tr'));
            var data = row.data();

            if (!confirm('真的要删除 ID 为 '+ data.id +' 的记录吗？')) {
                return false;
            }

            $.pjax({
                url: $(this).attr('href'),
                container: '#pjax-container',
                type: 'POST',
                push: false
            });

            return false;
        });

    });
</script>