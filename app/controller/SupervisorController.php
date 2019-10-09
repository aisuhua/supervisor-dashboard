<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;

class SupervisorController extends ControllerSupervisor
{
    public function readLogAction()
    {
        $log = $this->supervisor->readLog(-1024 * 1024, 0);
        $this->view->log = $log;

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);
    }

    public function clearLogAction()
    {
        $this->supervisor->clearLog();

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

        $this->supervisor->restart();

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }
}

