{{ content() }}


<ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <li><a href="/server-group">分组管理</a></li>
    <li class="active">分组列表</li>
</ol>

<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
    <tr>
        <th>Name</th>
        <th>Position</th>
        <th>Office</th>
        <th>Age</th>
        <th>Start date</th>
        <th>Salary</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Shad Decker</td>
        <td>Regional Director</td>
        <td>Edinburgh</td>
        <td>51</td>
        <td>2008/11/13</td>
        <td>$183,000</td>
    </tr>
    <tr>
        <td>Michael Bruce</td>
        <td>Javascript Developer</td>
        <td>Singapore</td>
        <td>29</td>
        <td>2011/06/27</td>
        <td>$183,000</td>
    </tr>
    <tr>
        <td>Donna Snider</td>
        <td>Customer Support</td>
        <td>New York</td>
        <td>27</td>
        <td>2011/01/25</td>
        <td>$112,000</td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th>Name</th>
        <th>Position</th>
        <th>Office</th>
        <th>Age</th>
        <th>Start date</th>
        <th>Salary</th>
    </tr>
    </tfoot>
</table>

<script>
    $(document).ready(function() {
        var dataTable = $('#example').DataTable({
            processing: true,
            pageLength: 3,
            lengthChange: false,
            select: true,
            buttons: [
                {
                    text: '添加',
                    className: 'btn btn-primary',
                    action: function (e, dt, node, config) {
                        alert( 'Button activated' );
                    }
                },
                {
                    text: '删除',
                    className: 'btn btn-danger',
                    action: function ( e, dt, node, config ) {
                        alert( 'Button activated' );
                    }
                }
            ]
        });

        dataTable.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)');
    });
</script>