<div class="alert alert-success alert-override">
    添加服务器
</div>

<table class="table table-bordered">
    <tbody>
    <tr>
        <th>服务器 IP</th>
        <th>XML-RPC 端口</th>
        <th>XML-RPC 用户名</th>
        <th>XML-RPC 密码</th>
        <th style="width: 20%;">配置文件写入的路径</th>
        <th>更新配置服务所监听的端口</th>
        <th>排序字段</th>
        <th>操作</th>
    </tr>
    <tr>
        <td class="form-group">
            <input type="text" class="form-control" name="ip">
        </td>
        <td class="form-group">
            <input type="text" class="form-control" name="port" value="9001">
        </td>
        <td class="form-group">
            <input type="text" class="form-control" name="user_name" placeholder="留空则使用默认用户名">
        </td>
        <td class="form-group">
            <input type="text" class="form-control" name="password" placeholder="留空则使用默认密码">
        </td>
        <td class="form-group">
            <input type="text" class="form-control" name="conf_path" value="/etc/supervisor/conf.d/program.conf">
        </td>
        <td class="form-group">
            <input type="text" class="form-control" name="sync_conf_port" value="8089">
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

<div class="alert alert-success alert-override">
    服务器列表
</div>

<table class="table table-bordered">
    <tbody>
        <tr>
            <th>ID</th>
            <th>服务器 IP</th>
            <th>XML-RPC 端口</th>
            <th>XML-RPC 用户名</th>
            <th>XML-RPC 密码</th>
            <th style="width: 20%;">配置文件写入的路径</th>
            <th>更新配置服务所监听的端口</th>
            <th>排序字段</th>
            <th>操作</th>
        </tr>
        <tr>
            <td>1</td>
            <td><input class="form-control" type="text" name="ip" value="192.168.1.229"></td>
            <td><input type="text" class="form-control" name="port" value="9001"></td>
            <td><input class="form-control" type="text" name="user_name" value="" placeholder="留空则不修改"></td>
            <td><input class="form-control" type="text" name="password" value="" placeholder="留空则不修改"></td>
            <td><input type="text" class="form-control" name="conf_path" value="/etc/supervisor/conf.d/program.conf"></td>
            <td><input type="text" class="form-control" name="sync_conf_port" value="8089"></td>
            <td><input type="text" class="form-control" name="sort" value="0"></td>
            <td>
                <button class="btn btn-sm btn-success">修改</button>
                <button class="btn btn-sm btn-danger">删除</button>
            </td>
        </tr>
    </tbody>
</table>