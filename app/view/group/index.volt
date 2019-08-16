<ol class="breadcrumb">
    <li><a href="#">服务器分组管理</a></li>
    <li class="active">添加分组</li>
</ol>

<table class="table table-bordered">
    <tbody>
    <tr>
        <th>组名</th>
        <th>组描述</th>
        <th>排序字段</th>
        <th>操作</th>
    </tr>
    <tr>
        <td class="form-group">
            <input type="text" class="form-control" name="name">
        </td>
        <td class="form-group">
            <input type="text" class="form-control" name="description">
        </td>

        <td class="form-group">
            <input type="text" class="form-control" name="sort" value="0">
        </td>
        <td>
            <button class="btn btn-sm btn-success">添加</button>
        </td>
    </tr>
    </tbody>
</table>

<ol class="breadcrumb">
    <li><a href="#">服务器分组管理</a></li>
    <li class="active">分组列表</li>
</ol>

<table class="table table-bordered">
    <tbody>
        <tr>
            <th>ID</th>
            <th>组名</th>
            <th>组描述</th>
            <th>排序字段</th>
            <th>操作</th>
        </tr>
        <tr>
            <td>1</td>
            <td><input class="form-control" type="text" name="name" value="默认分组"></td>
            <td><input class="form-control" type="text" name="desc" value="系统内置分组"></td>
            <td><input class="form-control" type="text" name="sort" value="1"></td>
            <td>
                <button class="btn btn-sm btn-success">修改</button>
                <button class="btn btn-sm btn-danger">删除</button>
            </td>
        </tr>
    </tbody>
</table>