{{ content() }}
{{ flashSession.output() }}

<div class="modal fade" id="load-config-modal" tabindex="-1" role="dialog" aria-labelledby="load-config-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="load-config-label">INI 编辑模式</h4>
            </div>
            <div class="modal-body">
                <textarea id="ini-code" class="ini-code">{{ ini }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="ini-submit" data-url="/server/{{ server.id }}/config/ini-mode">确定修改</button>
            </div>
        </div>
    </div>
</div>