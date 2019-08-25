<?php
use Phalcon\Mvc\View;

class ProcessController extends ControllerSupervisorBase
{
    /**
     * @var Server $server
     */
    protected $server;

    /**
     * @var Supervisor $supervisor;
     */
    protected $supervisor;

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
                return $this->response->redirect('/');
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

    public function indexAction()
    {
        $callback = function ()
        {
            $processes = $this->supervisor->getAllProcessInfo();
            $processGroups = array_unique(array_column($processes, 'group'));
            $process_warnings = array_filter($processes, function($process) {
                return $process['statename'] != 'RUNNING';
            });

            $this->view->processes = $processes;
            $this->view->processGroups = $processGroups;
            $this->view->process_warnings = $process_warnings;
        };

        $this->setCallback($callback);
        $this->invoke();
    }

    public function createAction()
    {

    }

    public function stopAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function () use ($name) {
            $process = $this->supervisor->getProcessInfo($name);
            if ($process['statename'] == 'RUNNING')
            {
                $this->supervisor->stopProcess($name);
            }
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = Tool::shortName($name) . " 已停止";

        return $this->response->setJsonContent($result);
    }

    public function startAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            $process = $this->supervisor->getProcessInfo($name);
            if ($process['statename'] != 'RUNNING')
            {
                $this->supervisor->startProcess($name);
            }
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = Tool::shortName($name) . " 已启动";

        return $this->response->setJsonContent($result);
    }

    public function restartAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            $process = $this->supervisor->getProcessInfo($name);
            if ($process['statename'] == 'RUNNING')
            {
                $this->supervisor->stopProcess($name);
            }

            $this->supervisor->startProcess($name);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = Tool::shortName($name) . " 已重启";

        return $this->response->setJsonContent($result);
    }

    public function stopGroupAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use($name)
        {
            $this->supervisor->stopProcessGroup($name);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = "{$name} 进程组已停止";

        return $this->response->setJsonContent($result);
    }

    public function startGroupAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            $this->supervisor->startProcessGroup($name);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = "{$name} 进程组已启动";

        return $this->response->setJsonContent($result);
    }

    public function restartGroupAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            $this->supervisor->stopProcessGroup($name);
            $this->supervisor->startProcessGroup($name);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = "{$name} 进程组已重启";

        return $this->response->setJsonContent($result);
    }

    public function tailLogAction()
    {
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            // 只看前面 1M 的日志
            $log = $this->supervisor->tailProcessStdoutLog($name, 0, 1 * 1024 * 1024);
            $this->view->log = $log;
        };
        $this->setCallback($callback);
        $this->invoke();

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->setTemplateBefore('tailLog');

        if ($this->isPjax())
        {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }

        $exploded = explode(':', $name);

        $this->view->group = $exploded[0];
        $this->view->name = $exploded[1];
    }

    public function clearLogAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            $this->supervisor->clearProcessLogs($name);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = "{$name} 日志清理完成";

        return $this->response->setJsonContent($result);
    }

    public function stopAllAction()
    {
        $callback = function()
        {
            $this->supervisor->stopAllProcesses();
        };
        $this->setCallback($callback);
        $this->invoke();

        $this->flashSession->success("已停止所有任务");

        return $this->redirectToIndex();
    }

    public function restartAllAction()
    {
        $callback = function()
        {
            $this->supervisor->stopAllProcesses();
            $this->supervisor->startAllProcesses();
        };
        $this->setCallback($callback);
        $this->invoke();

        $this->flashSession->success("已重启所有任务");

        return $this->redirectToIndex();
    }
}

