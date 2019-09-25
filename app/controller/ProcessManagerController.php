<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;
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
            if (!$show_sys && strpos($process['name'], 'sys_') !== false)
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

        $processLock = new ProcessLock();
        if (!$processLock->lock())
        {
            $result['state'] = 0;
            $result['message'] = '更新失败，无法获得锁';

            return $this->response->setJsonContent($result);
        }

        // 读取数据库配置
        $processes = Process::find([
            'server_id = :server_id:',
            'bind' => [
                'server_id' => $this->server->id
            ],
            'order' => 'program asc, id asc'
        ]);

        $process_arr = $processes->toArray();

        // 读取线上配置
        $uri = $this->server->getSupervisorUri();

        $read = SupervisorSyncConf::read($uri, $this->server->process_conf);
        $is_empty_file = strpos($read['message'], 'no such file or directory');

        if (!$read['state'] && $is_empty_file === false)
        {
            // 配置读取失败
            $result['state'] = 0;
            $result['message'] = "无法读取配置，{$read['message']}";

            return $this->response->setJsonContent($result);
        }

        $ini_arr = [];
        foreach ($processes as $process)
        {
            /** @var Process $process */
            $ini_arr[] = $process->getIni();
        }

        $ini = implode(PHP_EOL, $ini_arr) . PHP_EOL;
        $ret = SupervisorSyncConf::write($uri, $this->server->process_conf, $ini);

        if (!$ret['state'])
        {
            $result['state'] = 0;
            $result['message'] = $ret['message'];

            return $this->response->setJsonContent($result);
        }

        // 对比数据库与线上进程配置，统计出待新增、删除和修改的进程
        $added = [];
        $changed = [];
        $removed = [];

        // 以下逻辑是为了模拟 reloadConfig 方法统计出进程的增删改
        // 因为存在并发调用 reloadConfig，因此它的结果并不可靠
        // list($added, $changed, $removed) = $this->supervisor->reloadConfig()[0];
        $this->supervisor->reloadConfig();

        if (empty($read['content']))
        {
            // 配置内容为空，则全部进程都是新增
            if (count($process_arr) > 0)
            {
                $added = array_map(function($process) {
                    return $process['program'];
                }, $process_arr);
            }
        }
        else
        {
            $parsed = parse_ini_string($read['content'], true, INI_SCANNER_RAW);
            if ($parsed === false)
            {
                $result['state'] = 0;
                $result['message'] = "配置解析失败，请重试";

                return $this->response->setJsonContent($result);
            }

            $db_programs = array_map(function($process) {
                return $process['program'];
            }, $process_arr);

            $parsed_programs = array_map(function($key) {
                return explode(':', $key)[1];
            }, array_keys($parsed));

            // 待新增的进程
            $added = array_diff($db_programs, $parsed_programs);
            // 待删除的进程
            $removed = array_diff($parsed_programs, $db_programs);

            // 需要判断是否发生修改的进程
            $key_parts = [];
            foreach ($processes as $process)
            {
                $key_parts[$process->program] = $process;
            }

            $intersect = array_intersect($db_programs, $parsed_programs);
            if (!empty($intersect))
            {
                foreach ($intersect as $program)
                {
                    /** @var Process $process */
                    $process = $key_parts[$program];
                    $process->assign($parsed["program:{$program}"]);

                    if ($process->hasChanged())
                    {
                        $changed[] = $program;
                    }
                }
            }
        }

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

        // 解锁
        $processLock->unlock();

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }


    public function logAction()
    {
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');

        $process_name = $group . ':' . $name;
        $running = false;

        $process_info = $this->supervisor->getProcessInfo($process_name);
        if (in_array($process_info['statename'], ['STARTING', 'RUNNING', 'STOPPING']))
        {
            $running = true;
        }

        // 只看前面 1M 的日志
        // 注意这里应开启 strip_ansi = true，否则当日志含有 ansi 字符时讲无法查看日志
        $log_info = $this->supervisor->tailProcessStdoutLog($process_name, 0, 1024 * 1024);

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

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

        $process_name = $group . ':' . $name;
        $timeout = 10;
        $start_time = time();
        $log = [];

        while (true)
        {
            try
            {
                // 先查看是否有新日志产生
                $log = $this->supervisor->tailProcessStdoutLog($process_name, 0, 0);

                $offset = $log[1] < $offset ? 0 : $offset;
                if ($log[1] > $offset)
                {
                    // 有新日志产生
                    $log = $this->supervisor->tailProcessStdoutLog($process_name, 0, $log[1] - $offset);
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

        $process_name = $group . ':' . $name;
        $filename = $name . '_' . $this->server_id .'_' . date('YmdHis') . '.log';
        $log_info = $this->supervisor->tailProcessStdoutLog($process_name, 0, 1 * 1024 * 1024);

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

