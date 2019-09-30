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
        <th>进程名称</th>
        <th>更新时间</th>
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
        lengthChange: true,
        searching: true,
        serverSide: false,
        stateSave: true,
        ajax: '/process/list',
        searchHighlight: true,
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
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
                data: 'program',
                targets: 4,
                orderable: false,
                visible: true
            },
            {
                data: 'update_time',
                targets: 5,
                orderable: false,
                render: function (data, type, full, meta) {
                    return timeAgo(data);
                }
            },
            {
                targets: 6,
                data: 'id',
                orderable: false,
                render: function (data, type, full, meta) {
                    var html = '<a href="/process?server_id='+ full.server_id +'&ip='+ full.server_ip +'&port='+ full.server_port +'#'+ full.program +'" target="_blank" data-nopjax>查看</a>';
                    html += '<span class="text-muted"> | </span>';
                    html += '<a href="/process/edit/'+ data +'?server_id='+ full.server_id + '" target="_blank" data-nopjax>修改</a>';
                    return html;
                }
            }
        ]
    });
});
</script>