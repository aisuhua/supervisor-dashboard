{{ content() }}

<ol class="breadcrumb">
    <li><a href="/server">服务器管理</a></li>
    <li class="active">服务器列表</li>
</ol>

{{ flashSession.output() }}

<table id="server-list" class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th></th>
        <th>服务器 IP</th>
        <th>端口</th>
        <th>配置文件路径</th>
        <th>sync_conf 端口</th>
        <th>排序值</th>
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
                    if (getParam('server_group_id')) {
                        d.server_group_id = getParam('server_group_id');
                    }
                }
            },
            searchHighlight: true,
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            order: [
                [5, 'desc']
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
                    data: 'conf_path',
                    targets: 3,
                    orderable: false
                },
                {
                    data: 'sync_conf_port',
                    targets: 4,
                    orderable: false
                },
                {
                    data: 'sort',
                    targets: 5,
                    orderable: false
                },
                {
                    data: 'update_time',
                    targets: 6,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var myDate = new Date(data * 1000);
                        return timeAgo(myDate);
                    }
                },
                {
                    data: 'server_group_id',
                    targets: 7,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return '<a href="/server?server_group_id='+ full.serverGroup.id +'">' + full.serverGroup.name + '</a>';
                    }
                },
                {
                    targets: 8,
                    data: 'id',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var html = '<a href="/" target="_blank">管理</a> | ';
                        html += '<a href="/server/edit/'+ data +'?server_group_id='+ full.serverGroup.id +'">修改</a> | ';
                        html += '<a href="javascript: void(0);" class="delete">删除</a>';

                        return html;
                    }
                }
            ],
            buttons: [
                {
                    text: '添加服务器',
                    titleAttr: '添加服务器',
                    className: 'btn btn-default',
                    action: function (e, dt, node, config) {
                        var url = "/server/create";
                        $.pjax({url: url, container: '#pjax-container'})
                    }
                },
                {
                    text: '批量删除',
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

                        if (confirm("真的要删除这"+ count +"台服务器吗？")) {
                            var url = '/server/delete';
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

            if (!confirm('真的要删除“'+ data.ip + ':' + data.port +'”吗？')) {
                return false;
            }

            var url = '/server/delete';
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