{{ content() }}
{{ flashSession.output() }}
{% include 'server-group/nav.volt' %}

<script>
    var server_group_id = 0;
    {% if serverGroup is not empty %}
        server_group_id = {{ serverGroup.id }};
    {% endif %}
</script>

<table id="server-list" class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th></th>
        <th>ID</th>
        <th>所属分组</th>
        <th>IP 地址</th>
        <th>端口</th>
        <th>用户名</th>
        <th>密码</th>
        <th>agent port</th>
        <th>agent root</th>
        <th>更新时间</th>
        <th>操作</th>
    </tr>
    </thead>
</table>

<script>

$(function() {
    var $serverList = $('#server-list');
    var dataTable = $serverList.DataTable({
        processing: true,
        pageLength: 25,
        lengthChange: false,
        searching: true,
        serverSide: false,
        stateSave: true,
        ajax: {
            url: '/server/list',
            data: function ( d ) {
                d.group_id = {{ group_id }};
            }
        },
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
                data: 'server_group_id',
                targets: 2,
                orderable: false,
                render: function (data, type, full, meta) {
                    return full.server_group_name;
                }
            },
            {
                data: 'ip',
                targets: 3,
                orderable: false,
                render: function (data, type, full, meta) {
                    return data;
                }
            },
            {
                data: 'port',
                targets: 4,
                orderable: false
            },
            {
                data: 'username',
                targets: 5,
                orderable: false
            },
            {
                data: 'password',
                targets: 6,
                orderable: false
            },
            {
                data: 'agent_port',
                targets: 7,
                orderable: false
            },
            {
                data: 'agent_root',
                targets: 8,
                orderable: false
            },
            {
                data: 'update_time',
                targets: 9,
                orderable: false,
                render: function (data, type, full, meta) {
                    return timeAgo(data);
                }
            },
            {
                targets: 10,
                data: 'id',
                orderable: false,
                render: function (data, type, full, meta) {
                    var html = '<a href="/server/edit/'+ data + '">修改</a>';
                    html += '<span class="text-muted"> | </span>';
                    html += '<a href="javascript: void(0);" class="delete">删除</a>';
                    html += '<span class="text-muted"> | </span>';
                    html += '<a href="/process?server_id='+ full.id +'&ip='+ full.ip +'&port='+ full.port +'" target="_blank" data-nopjax>Supervisor 控制台</a>';

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
                        alert('请先选择服务器');
                        return false;
                    }

                    if (confirm("真的要删除这 "+ count +" 台服务器吗？")) {

                        var url = "/server/delete?group_id={{ group_id }}";

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
            dataTable.buttons().container().appendTo('#server-list_wrapper .col-sm-6:eq(0)');
        }
    });

    $serverList.on('click', 'td a.delete', function () {
        var row = dataTable.row($(this).closest('tr'));
        var data = row.data();

        if (!confirm('真的要删除 '+ data.ip + ':' + data.port +' 这台服务器吗？')) {
            return false;
        }

        var url = '/server/delete?group_id={{ group_id }}';
        $.pjax({
            url: url,
            container: '#pjax-container',
            type: 'POST',
            data: {ids: data.id},
            push: false
        });
    });

    $("#select-all").on( "click", function(e) {
        if ($(this).is( ":checked" )) {
            dataTable.rows(  ).select();
        } else {
            dataTable.rows(  ).deselect();
        }
    });
});
</script>