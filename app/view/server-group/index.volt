{{ content() }}

{{ flashSession.output() }}

<ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li><a href="/server-group">分组管理</a></li>
    <li class="active">分组列表</li>
</ol>

<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>组名</th>
        <th>描述</th>
        <th>排序值</th>
    </tr>
    </thead>
</table>

<script>
    $(document).ready(function() {
        var dataTable = $('#example').DataTable({
            processing: true,
            pageLength: 15,
            lengthChange: false,
            select: true,
            serverSide: true,
            ajax: '/server-group/list',
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' },
                { data: 'sort' },
            ],
            buttons: [
                {
                    text: '添加',
                    titleAttr: 'Add a new record',
                    className: 'btn btn-primary',
                    action: function (e, dt, node, config) {
                        var url = "/server-group/create";
                        $.pjax({url: url, container: '#pjax-container'})
                    }
                },
                {
                    text: '删除',
                    className: 'btn btn-danger',
                    action: function ( e, dt, node, config ) {
                        alert( 'Button activated' );
                    }
                }
            ],
            initComplete: function(settings, json) {
                dataTable.buttons().container().appendTo('#example_wrapper .col-sm-6:eq(0)');
            }
        });

    });
</script>