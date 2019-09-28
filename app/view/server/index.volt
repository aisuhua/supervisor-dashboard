{{ content() }}
{{ flashSession.output() }}

<ol class="breadcrumb">
    {% if serverGroup is empty %}
        <li class="active">服务器列表</li>
    {% else %}
        <li class="active">{{ serverGroup.name }}的服务器列表</li>
    {% endif %}
</ol>

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
        <th>IP 地址</th>
        <th>Supervisor 端口</th>
        <th>Supervisor 用户名</th>
        <th>Supervisor 密码</th>
        <th>agent port</th>
        <th>agent root</th>
        <th>更新时间</th>
        <th>所属分组</th>
        <th>操作</th>
    </tr>
    </thead>
</table>

<script>

    $(document).ready(function() {

        var dataTable = $('#server-list').DataTable({
            processing: true,
            pageLength: 25,
            lengthChange: false,
            searching: true,
            serverSide: false,
            stateSave: true,
            ajax: {
                url: '/server/list',
                data: function ( d ) {
                    d.server_group_id = server_group_id;
                }
            },
            searchHighlight: true,
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
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
                    data: 'ip',
                    targets: 1,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return data;
                    }
                },
                {
                    data: 'port',
                    targets: 2,
                    orderable: false
                },
                {
                    data: 'username',
                    targets: 3,
                    orderable: false
                },
                {
                    data: 'password',
                    targets: 4,
                    orderable: false
                },
                {
                    data: 'agent_port',
                    targets: 5,
                    orderable: false
                },
                {
                    data: 'agent_root',
                    targets: 6,
                    orderable: false
                },
                {
                    data: 'update_time',
                    targets: 7,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var myDate = new Date(data * 1000);
                        return timeAgo(myDate) == "刚刚" ? '<span class="text-success">刚刚</span>' :  timeAgo(myDate);
                    }
                },
                {
                    data: 'server_group_id',
                    targets: 8,
                    orderable: false,
                    visible: server_group_id <= 0,
                    render: function (data, type, full, meta) {
                        return full.serverGroup.name;
                    }
                },
                {
                    targets: 9,
                    data: 'id',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var html = '<a href="/process?server_id='+ full.id +'&ip='+ full.ip +'&port='+ full.port +'" target="_blank">控制台</a> | ';

                        var edit_html = '<a href="/server/edit/'+ data +'">修改</a>  | ';
                        if (server_group_id > 0)
                        {
                            edit_html = '<a href="/server-group/'+ server_group_id +'/server/edit/'+ data + '">修改</a> | ';
                        }

                        html += edit_html;
                        html += '<a href="javascript: void(0);" class="delete">删除</a>';

                        return html;
                    }
                }
            ],
            buttons: [
                {
                    text: '添加',
                    titleAttr: '添加服务器',
                    className: 'btn btn-default',
                    action: function (e, dt, node, config) {
                        var url = "/server/create";

                        if (server_group_id > 0)
                        {
                            url = '/server-group/'+ server_group_id +'/server/create';
                        }

                        $.pjax({url: url, container: '#pjax-container'})
                    }
                },
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

                        if (confirm("真的要删除这"+ count +"台服务器吗？")) {

                            var url = "/server/delete";

                            if (server_group_id > 0)
                            {
                                url = '/server-group/'+ server_group_id +'/server/delete';
                            }

                            $.pjax({
                                url: url,
                                container: '#pjax-container',
                                type: 'POST',
                                data: {ids: ids}
                            });
                        }
                    }
                }
            ],
            initComplete: function(settings, json) {
                dataTable.buttons().container().appendTo('#server-list_wrapper .col-sm-6:eq(0)');
            }
        });

        $('#server-list tbody').on('click', 'td a.delete', function () {
            var row = dataTable.row($(this).closest('tr'));
            var data = row.data();

            if (!confirm('真的要删除“'+ data.ip + ':' + data.port +'”这台服务器吗？')) {
                return false;
            }

            var url = '/server/delete?server_group_id=' + server_group_id;
            $.pjax({
                url: url,
                container: '#pjax-container',
                type: 'POST',
                data: {ids: data.id}
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