<?php
use Phalcon\Mvc\View;

class ProcessController extends ControllerSupervisorBase
{
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

    public function stopAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function () use ($name) {
            $process = $this->supervisor->getProcessInfo($name);
            if ($process['statename'] == 'RUNNING')
            {
                $this->supervisor->stopProcess($name, false);
            }
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage(Tool::shortName($name) . " 正在停止");

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
                $this->supervisor->startProcess($name, false);
            }
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage(Tool::shortName($name) . " 正在启动");

        return $this->response->setJsonContent($result);
    }

    public function restartAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $result['state'] = 1;
        $result['message'] = self::formatMessage(Tool::shortName($name) . " 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function() use ($name)
        {
            $process = $this->supervisor->getProcessInfo($name);
            if ($process['statename'] == 'RUNNING')
            {
                $this->supervisor->stopProcess($name, true);
            }

            $this->supervisor->startProcess($name, false);
        };
        $this->setCallback($callback);
        $this->invoke();
    }

    public function stopGroupAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use($name)
        {
            $this->supervisor->stopProcessGroup($name, false);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在停止");

        return $this->response->setJsonContent($result);
    }

    public function startGroupAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $callback = function() use ($name)
        {
            $this->supervisor->startProcessGroup($name, false);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在启动");

        return $this->response->setJsonContent($result);
    }

    public function restartGroupAction()
    {
        $result = [];
        $name = $this->dispatcher->getParam('name', 'string');

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function() use ($name)
        {
            $this->supervisor->stopProcessGroup($name, true);
            $this->supervisor->startProcessGroup($name, false);
        };
        $this->setCallback($callback);
        $this->invoke();
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
        $result['message'] = Tool::shortName($name) . " 日志清理完成";

        return $this->response->setJsonContent($result);
    }

    public function stopAllAction()
    {
        $wait = (bool) $this->request->get('wait', 'int', 1);

        if ($wait)
        {
            $callback = function()
            {
                $this->supervisor->stopAllProcesses(true);
            };
            $this->setCallback($callback);
            $this->invoke();

            $this->flashSession->success("已停止所有进程");

            return $this->redirectToIndex();
        }
        else
        {
            $result = [];
            $result['state'] = 1;
            $result['message'] = self::formatMessage("正在停止所有进程");
            $this->response->setJsonContent($result)->send();

            fastcgi_finish_request();

            $callback = function()
            {
                $this->supervisor->stopAllProcesses(false);
            };
            $this->setCallback($callback);
            $this->invoke();

            $this->flashSession->success("已停止所有进程");

            $this->view->setRenderLevel(
                View::LEVEL_NO_RENDER
            );
        }
    }

    public function restartAllAction()
    {
        $wait = (bool) $this->request->get('wait', 'int', 1);

        if ($wait)
        {
            $callback = function() use ($wait)
            {
                $this->supervisor->stopAllProcesses(true);
                $this->supervisor->startAllProcesses(true);
            };
            $this->setCallback($callback);
            $this->invoke();

            $this->flashSession->success("已重启所有进程");
            return $this->redirectToIndex();
        }
        else
        {
            $result = [];
            $result['state'] = 1;
            $result['message'] = self::formatMessage("正在重启所有进程");
            $this->response->setJsonContent($result)->send();

            fastcgi_finish_request();

            $callback = function() use ($wait)
            {
                $this->supervisor->stopAllProcesses(true);
                $this->supervisor->startAllProcesses(false);
            };
            $this->setCallback($callback);
            $this->invoke();

            $this->flashSession->success("已重启所有进程");

            $this->view->setRenderLevel(
                View::LEVEL_NO_RENDER
            );
        }
    }
}

