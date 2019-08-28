{{ content() }}
{{ flashSession.output() }}

<div class="modal" id="load-config-modal" tabindex="-1" role="dialog" aria-labelledby="load-config-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="load-config-label">INI 编辑模式</h4>
            </div>
            <div class="modal-body">
                <textarea id="code" class="code">
[program:cat1]
command:/bin/cat
directory:%(here)s
process_name:%(program_name)s_%(process_num)s
numprocs:1
numprocs_start:0
startretries:20
redirect_stderr:true
stdout_logfile:AUTO
stdout_logfile_backups:0
stdout_logfile_maxbytes:1MB
autostart:true
autorestart:true
user:root
[program:cat2]
command:/bin/bash
directory:%(here)s
process_name:%(program_name)s_%(process_num)s
numprocs:1
numprocs_start:0
startretries:20
redirect_stderr:true
stdout_logfile:AUTO
stdout_logfile_backups:0
stdout_logfile_maxbytes:1MB
autostart:false
autorestart:true
user:root</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary">确定修改</button>
            </div>
        </div>
    </div>
</div>