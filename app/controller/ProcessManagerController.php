<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;
use SupBoard\Model\Server;
use SupBoard\Model\ServerGroup;
use SupBoard\Supervisor\StatusCode;
use Zend\XmlRpc\Client\Exception\FaultException;
use SupBoard\Model\Process;
use SupBoard\Form\ProcessForm;

class ProcessManagerController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $show_sys = $this->request->get('show_sys', 'int', 0);

        $process_groups = [];
        $process_warnings = [];

        $processes = $this->supervisor->getAllProcessInfo();
        foreach ($processes as $process)
        {
            // 不显示系统进程
            if (!DEBUG && !$show_sys && Process::isSystemProcess($process['group']))
            {
                continue;
            }

            $process_groups[] = $process['group'];

            if ($process['statename'] != 'RUNNING')
            {
                $process_warnings[] = $process;
            }
        }

        // 所有进程组
        $process_groups = array_unique($process_groups);

        // 数据库的进程信息
        $local_processes = Process::find([
            "server_id = {$this->server->id}"
        ])->toArray();

        $key_parts = array_column($local_processes, null, 'program');

        $process_group_merge = [];
        foreach ($process_groups as $key => $process_group)
        {
            $process_group_merge[$key] = ['program' => $process_group];
            if (isset($key_parts[$process_group]))
            {
                $process_group_merge[$key] += $key_parts[$process_group];
            }
        }

        $this->view->processes = $processes;
        $this->view->process_groups = $process_group_merge;
        $this->view->process_warnings = $process_warnings;
    }

    public function stopAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $process = $this->supervisor->getProcessInfo($process_name);
        if ($process['statename'] == 'RUNNING')
        {
            try
            {
                $this->supervisor->stopProcess($process_name, false);
            }
            catch (FaultException $e)
            {
                $this->handleStopException($e);
            }
        }

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在停止");

        return $this->response->setJsonContent($result);
    }

    public function startAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $process = $this->supervisor->getProcessInfo($process_name);
        if ($process['statename'] != 'RUNNING')
        {
            try
            {
                $this->supervisor->startProcess($process_name, false);
            }
            catch (FaultException $e)
            {
                $this->handleStartException($e);
            }
        }

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在启动");

        return $this->response->setJsonContent($result);
    }

    public function restartAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $process = $this->supervisor->getProcessInfo($process_name);
        if ($process['statename'] == 'RUNNING')
        {
            try
            {
                $this->supervisor->stopProcess($process_name, true);
            }
            catch (FaultException $e)
            {
                $this->handleStopException($e);
            }
        }

        try
        {
            $this->supervisor->startProcess($process_name, false);
        }
        catch (FaultException $e)
        {
            $this->handleStartException($e);
        }

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    public function stopGroupAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');

        try
        {
            $this->supervisor->stopProcessGroup($group, false);
        }
        catch (FaultException $e)
        {
            $this->handleStopException($e);
        }

        $result['state'] = 1;
        $result['message'] = self::formatMessage($group . " 正在停止");

        return $this->response->setJsonContent($result);
    }

    public function startGroupAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');

        try
        {
            $this->supervisor->startProcessGroup($group, false);
        }
        catch (FaultException $e)
        {
            $this->handleStartException($e);
        }

        $result['state'] = 1;
        $result['message'] = self::formatMessage($group . " 正在启动");

        return $this->response->setJsonContent($result);
    }

    public function restartGroupAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');

        $result['state'] = 1;
        $result['message'] = self::formatMessage($group . " 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        try
        {
            $this->supervisor->stopProcessGroup($group, true);
        }
        catch (FaultException $e)
        {
            $this->handleStopException($e);
        }

        try
        {
            $this->supervisor->startProcessGroup($group, false);
        }
        catch (FaultException $e)
        {
            $this->handleStartException($e);
        }

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    public function clearLogAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $this->supervisor->clearProcessLogs($process_name);

        $result['state'] = 1;
        $result['message'] = "{$name} 日志清理完成";

        return $this->response->setJsonContent($result);
    }

    /**
     * 只停止非系统进程
     *
     * @TODO
     */
    public function stopAllAction()
    {
        $result = [];
        $result['state'] = 1;
        $result['message'] = self::formatMessage("正在停止所有进程");
        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $this->supervisor->stopAllProcesses(false);

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    /**
     * 只重启非系统进程
     *
     * @TODO
     */
    public function restartAllAction()
    {
        $result = [];
        $result['state'] = 1;
        $result['message'] = self::formatMessage("正在重启所有进程");
        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        try
        {
            $this->supervisor->stopAllProcesses(true);
        }
        catch (FaultException $e)
        {
            $this->handleStopException($e);
        }

        try
        {
            $this->supervisor->startAllProcesses(false);
        }
        catch (FaultException $e)
        {
            $this->handleStartException($e);
        }

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    public function reloadConfigAction()
    {
        $result = [];

        // 更新服务器配置
        $supAgent = $this->server->getSupAgent();
        if (!$supAgent->ping())
        {
            $result['state'] = 0;
            $result['message'] = "同步失败，无法连接 <a href='#'>SupAgent</a> 服务";
            return $this->response->setJsonContent($result);
        }

        $reload = $supAgent->processReload();
        if (!$reload['state'])
        {
            $result['state'] = 0;
            $result['message'] = $reload['message'];
            return $this->response->setJsonContent($result);
        }

        // 以下逻辑是为了模拟 reloadConfig 方法统计出进程的增删改
        // 因为存在并发调用 reloadConfig，因此它的结果并不可靠
        list($added, $changed, $removed) = $this->supervisor->reloadConfig()[0];

        $messages = [];
        if (!empty($added))
        {
            foreach ($added as $key => $item)
            {
                $messages[] = "{$item} 正在添加";
            }
        }

        if (!empty($removed))
        {
            foreach ($removed as $key => $item)
            {
                $messages[] = "{$item} 正在删除";
            }
        }

        if (!empty($changed))
        {
            foreach ($changed as $key => $item)
            {
                $messages[] = "{$item} 正在更新";
            }
        }

        if (empty($messages))
        {
            $result['state'] = 2;
            $result['message'] = "没有配置需要更新";
        }
        else
        {
            $result['state'] = 1;
            $result['message'] = $this->formatMessage(implode("\n", $messages));
        }

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        foreach ($added as $group)
        {
            try
            {
                $this->supervisor->addProcessGroup($group);
            }
            catch (FaultException $e)
            {
                $this->handleAddException($e);
            }
        }

        foreach ($changed as $group)
        {
            try
            {
                $this->supervisor->stopProcessGroup($group);
            }
            catch (FaultException $e)
            {
                $this->handleStopException($e);
            }

            try
            {
                $this->supervisor->removeProcessGroup($group);
            }
            catch (FaultException $e)
            {
                $this->handleRemoveException($e);
            }

            try
            {
                $this->supervisor->addProcessGroup($group);
            }
            catch (FaultException $e)
            {
                $this->handleAddException($e);
            }
        }

        foreach ($removed as $group)
        {
            try
            {
                $this->supervisor->stopProcessGroup($group);
            }
            catch (FaultException $e)
            {
                $this->handleStopException($e);
            }

            try
            {
                $this->supervisor->removeProcessGroup($group);
            }
            catch (FaultException $e)
            {
                $this->handleRemoveException($e);
            }
        }

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }


    public function logAction()
    {
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $stderr = $this->request->get('stderr', 'int', 0);

        $process_name = $group . ':' . $name;
        $running = false;

        $process_info = $this->supervisor->getProcessInfo($process_name);
        if (in_array($process_info['statename'], ['STARTING', 'RUNNING', 'STOPPING']))
        {
            $running = true;
        }

        // 只看前面 1M 的日志
        // 注意这里应开启 strip_ansi = true，否则当日志含有 ansi 字符时讲无法查看日志
        $log_function = $stderr ? 'tailProcessStderrLog' : 'tailProcessStdoutLog';
        $log_info = $this->supervisor->{$log_function}($process_name, 0, 1024 * 1024);

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->stderr = $stderr;
        $this->view->running = $running;
        $this->view->group = $group;
        $this->view->name = $name;
        $this->view->log = $log_info[0];
        $this->view->offset = $log_info[1];
    }

    public function tailAction()
    {
        $result = [];

        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $offset = $this->request->get('offset', 'int', 0);
        $stderr = $this->request->get('stderr', 'int', 0);

        $process_name = $group . ':' . $name;
        $log_function = $stderr ? 'tailProcessStderrLog' : 'tailProcessStdoutLog';
        $timeout = 10;
        $start_time = time();
        $log = [];

        while (true)
        {
            try
            {
                // 先查看是否有新日志产生
                $log = $this->supervisor->{$log_function}($process_name, 0, 0);

                $offset = $log[1] < $offset ? 0 : $offset;
                if ($log[1] > $offset)
                {
                    // 有新日志产生
                    $log = $this->supervisor->{$log_function}($process_name, 0, $log[1] - $offset);
                    break;
                }
            }
            catch (FaultException $e)
            {
                if ($e->getCode() == StatusCode::BAD_NAME)
                {
                    $result['state'] = 0;
                    $result['message'] = '已执行完毕，页面停止刷新';

                    return $this->response->setJsonContent($result);
                }

                throw $e;
            }

            if (time() - $start_time >= $timeout)
            {
                break;
            }

            sleep(1);
            continue;
        }

        $result['state'] = 1;
        $result['log'] = $log[0];
        $result['offset'] = $log[1];

        return $this->response->setJsonContent($result);
    }

    public function downloadAction()
    {
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $stderr = $this->request->get('stderr', 'int', 0);

        $process_name = $group . ':' . $name;
        $log_function = $stderr ? 'tailProcessStderrLog' : 'tailProcessStdoutLog';
        $filename = $name . '_' . $this->server_id .'_' . date('YmdHis') . '.log';
        $log_info = $this->supervisor->{$log_function}($process_name, 0, 50 * 1024 * 1024);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header("Content-Length: {$log_info[1]}");

        echo $log_info[0];
        exit;
    }
}

