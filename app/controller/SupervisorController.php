<?php
use Phalcon\Mvc\View;

class SupervisorController extends ControllerSupervisorBase
{


    public function initialize()
    {
        $server_id = $this->dispatcher->getParam('server_id', 'int');

        if ($server_id)
        {
            /**
             * @var Server $server
             */
            $server = Server::findFirst($server_id);
            if (!$server)
            {
                $this->flashSession->error("不存在该服务器");
                return $this->response->redirect($this->request->getHTTPReferer());
            }

            $supervisor = new Supervisor(
                $server->id,
                $server->ip,
                $server->username ? $server->username : 'worker',
                $server->password ? $server->password : '111111',
                $server->port
            );

            $this->server = $server;
            $this->supervisor = $supervisor;
            $this->view->server = $server;
        }
    }

    public function statusAction()
    {
        $callback = function ()
        {
          $status = $this->supervisor->getState();
        };
        $this->setCallback($callback);
        $this->invoke();
        exit;
    }

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
        $wait = (bool)$this->request->get('wait', 'int', 1);

        $callback1 = function ()
        {
            $this->supervisor->restart();
        };
        $this->setCallback($callback1);
        $this->invoke();

        if ($wait)
        {
            $timeout = 10;
            $start_time = time();
            $has_starting = true;

            // 10 秒内如果还有启动中的脚本则不再检测
            while (time() - $start_time < $timeout && $has_starting)
            {
                $has_starting = false;

                $callback2 = function () use (&$has_starting)
                {
                    $allProcessInfo = $this->supervisor->getAllProcessInfo();
                    foreach ($allProcessInfo as $processInfo)
                    {
                        if ($processInfo['statename'] == 'STARTING')
                        {
                            $has_starting = true;
                            break;
                        }
                    }
                };
                $this->setCallback($callback2);
                $this->invoke();
            }

            $this->flashSession->success("Supervisor 重启完成");
            return $this->redirectToIndex();
        }

        $result = [];
        $result['state'] = 1;
        $result['message'] = "Supervisor 正在重启，刷新页面查看进度";
        return $this->response->setJsonContent($result);
    }

    public function shutdownAction()
    {
        $callback = function()
        {
            $this->supervisor->shutdown();
        };
        $this->setCallback($callback);
        $this->invoke();

        return $this->redirectToIndex();
    }

}

