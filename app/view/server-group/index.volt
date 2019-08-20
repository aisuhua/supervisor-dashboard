{{ content() }}

<ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li><a href="/server-group">服务器组管理</a></li>
    <li class="active">服务器组列表</li>
</ol>

{{ flashSession.output() }}

<table id="server-group-list" class="table table-bordered table-hover">
    <thead>
    <tr>
        <th></th>
        <th>服务器组名称</th>
        <th>描述</th>
        <th>排序值</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
    </thead>
</table>

<script>

    $(document).ready(function() {

        var dataTable = $('#server-group-list').DataTable({
            processing: true,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            serverSide: true,
            stateSave: true,
            ajax: '/server-group/list',
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            order: [
                [3, 'desc']
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
                    data: 'name',
                    targets: 1,
                    orderable: false
                },
                {
                    data: 'description',
                    targets: 2,
                    orderable: false
                },
                {
                    data: 'sort',
                    targets: 3,
                    orderable: false
                },
                {
                    data: 'create_time',
                    targets: 4,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var myDate = new Date(data * 1000);
                        return myDate.format('Y-m-d');
                    }
                },
                {
                    targets: 5,
                    data: 'id',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        var html = '<a href="/server-group/edit/'+ data +'">修改</a> | ';
                        html += '<a href="javascript: void(0);" class="delete">删除</a> | ';
                        html += '<a href="javascript: void(0);" class="delete">服务器管理</a>'

                        return html;
                    }
                }
            ],
            buttons: [
                {
                    text: '添加服务器组',
                    titleAttr: 'Add a new record',
                    className: 'btn btn-default',
                    action: function (e, dt, node, config) {
                        var url = "/server-group/create";
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
                dataTable.buttons().container().appendTo('#server-group-list_wrapper .col-sm-6:eq(0)');
            }
        });

        $('#server-group-list tbody').on('click', 'td a.delete', function () {
            var row = dataTable.row($(this).closest('tr'));
            var data = row.data();

            if (!confirm('真的要删除“'+ data.name +'”分组吗？')) {
                return false;
            }

            var url = '/server-group/delete';
            $.pjax({
                url: url,
                container: '#pjax-container',
                type: 'POST',
                data: {ids: data.id}
            });
        });

    });
</script>