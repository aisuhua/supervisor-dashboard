<?php
use Phalcon\Mvc\View;

class SupervisorController extends ControllerSupervisorBase
{
    public function readLogAction()
    {
        $callback = function ()
        {
            // 只看前面 1M 的日志
            $log = $this->supervisor->readLog(0, 1024 * 1024);
            $this->view->log = $log;
        };
        $this->setCallback($callback);
        $this->invoke();

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->setTemplateBefore('readLog');

        if ($this->isPjax())
        {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }
    }

    public function clearLogAction()
    {
        $result = [];
        $callback = function()
        {
            $this->supervisor->clearLog();
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = "日志清理完成";

        return $this->response->setJsonContent($result);
    }

    public function restartAction()
    {
        $result = [];
        $result['state'] = 1;
        $result['message'] = self::formatMessage("Supervisor 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function ()
        {
            $this->supervisor->restart();
        };
        $this->setCallback($callback);
        $this->invoke();
    }
}

