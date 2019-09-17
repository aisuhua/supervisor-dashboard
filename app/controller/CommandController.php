<?php
use Phalcon\Mvc\View;
use Phalcon\Db;

class CommandController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $form = new CommandForm(null);

        if ($this->request->isPost())
        {
            $command = new Command();
            $form->bind($this->request->getPost(), $command);

            if (!$form->isValid())
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message->getMessage());
                    goto end;
                }
            }

            if (!$command->create())
            {
                $this->flash->error($command->getMessages());
                goto end;
            }

            // 向 supervisor 添加进程并启动进程
            $uri = $this->server->getSupervisorUri();

            $commandLock = new CommandLock();
            if (!$commandLock->lock())
            {
                $this->flash->error('无法获得锁');
            }

            // 先读取配置
            $read = SupervisorSyncConf::read($uri, $this->server->command_conf);
            $is_empty_file = strpos($read['message'], 'no such file or directory');

            if (!$read['state'] && $is_empty_file === false)
            {
                // 配置读取失败
                $this->flash->error("配置读取失败，{$read['message']}");
                goto end;
            }

            // 将新进程配置追加到配置末尾
            $ini = trim($read['content']) . PHP_EOL . $command->getIni();
            $write = SupervisorSyncConf::write($uri, $this->server->command_conf, $ini);

            if (!$write['state'])
            {
                $this->flash->error($write['message']);
                goto end;
            }

            // 启动进程
            $this->supervisor->reloadConfig();
            $this->supervisor->addProcessGroup($command->getProgramName());
            $this->supervisor->startProcessGroup($command->getProgramName());

            // 更新命令进程状态为已启动
            /** @var Command $command */
            $command = Command::findFirst($command->id);
            $command->status = Command::STATUS_STARTED;
            $command->start_time = time();

            if (!$command->save())
            {
                $this->flash->error($command->getMessages());
                goto end;
            }

            // $form->clear();
            $this->flash->success("命令已开始执行");
            $this->view->success = true;
            $this->view->command = $command;
        }

        end:

        $this->view->form = $form;
    }

    public function historyAction()
    {

    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);

        // 查看特定服务器的日志
        $bind = [];
        $where = 'server_id = :server_id:';
        $bind['server_id'] = $this->server_id;

        $command = Command::find([
            $where,
            'bind' => $bind,
            'columns' => "id, server_id, command, status, start_time, end_time",
            'order' => 'id desc'
        ]);

        $total = Command::count();
        $data = $command->toArray();

        $result = [];
        $result['draw'] = $draw + 1;
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
        $result['data'] = $data;

        return $this->response->setJsonContent($result);
    }

    public function tailAction($id)
    {
        $sql = 'SELECT id, command, status, RIGHT(log, 1 * 1024 * 1024) as log FROM command WHERE id = :id LIMIT 1';
        $command = $this->db->fetchOne($sql, Db::FETCH_ASSOC, [
            'id' => $id
        ]);

        if (!$command)
        {
            $this->flash->error("不存在该命令日志");

            $this->dispatcher->forward([
                'action' => 'index'
            ]);
            return false;
        }

        $running = false;
        // 正在运行的进程直接读取 Supervisor 日志
        if ($command['status'] == Command::STATUS_STARTED)
        {
            try
            {
                $process_name = Command::makeProcessName($command['id']);
                $command['log'] = $this->supervisor->tailProcessStdoutLog($process_name, 0, 1 * 1024 * 1024)[0];
                $running = true;
            }
            catch (Exception $e)
            {
                // 进程不存在，一般来说是因为该进程已经执行完成并已经被删除
                if ($e->getCode() != XmlRpc::BAD_NAME)
                {
                    throw $e;
                }

                // 如果进程已经执行完成，则读取数据库的日志
                $command = $this->db->fetchOne($sql, Db::FETCH_ASSOC, [
                    'id' => $id
                ]);

                if (!$command)
                {
                    $this->flash->error("不存在该命令日志");

                    $this->dispatcher->forward([
                        'action' => 'index'
                    ]);
                    return false;
                }
            }
        }

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->setTemplateBefore('commandTail');

        if ($this->isPjax())
        {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }

        $this->view->running = $running;
        $this->view->command = $command;
    }

    public function downloadAction($id)
    {
        /** @var Command $command */
        $command = Command::findFirst($id);

        if (!$command)
        {
            $this->flash->error("不存在该命令日志");

            $this->dispatcher->forward([
                'action' => 'index'
            ]);
            return false;
        }

        $filename = 'command_' . $command->id . '_' . date('YmdHi', $command->start_time) . '.log';

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        echo $command->log;
        exit;
    }

    public function deleteAction()
    {
        $ids = $this->request->getPost('ids', 'string', '');

        $id_arr = array_filter(explode(',', $ids), function($item) {
            return is_numeric($item);
        });

        if (empty($id_arr))
        {
            $this->flash->error("请先选择要删除的日志");
        }
        else
        {
            // 只删除已经完成的命令日志
            $finished = implode(',', [
                CronLog::STATUS_FAILED,
                CronLog::STATUS_UNKNOWN,
                CronLog::STATUS_STOPPED,
                CronLog::STATUS_FINISHED
            ]);

            $phql = "DELETE FROM Command WHERE id IN ({ids:array-int}) AND status IN({$finished})";
            $result = $this->modelsManager->executeQuery(
                $phql,
                ['ids' => $id_arr]
            );

            if ($result->success())
            {
                $this->flash->success("删除成功");
            }
            else
            {
                $this->flash->error($result->getMessages());
            }
        }

        return $this->dispatcher->forward([
            'action' => 'history',
            'params' => [
                'server_id' => $this->server_id
            ]
        ]);
    }

    public function stopAction($id)
    {
        /** @var Command $command */
        $command = Command::findFirst($id);
        if (!$command)
        {
            $result['state'] = 0;
            $result['message'] = "不存在该命令记录，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }

        try
        {
            $this->supervisor->stopProcessGroup($command->getProgramName(), false);

            $result['state'] = 1;
            $result['message'] = $this->formatMessage("ID 为 {$id} 的命令正在停止");

            return $this->response->setJsonContent($result);
        }
        catch (Zend\XmlRpc\Client\Exception\FaultException $e)
        {
            if ($e->getCode() != XmlRpc::BAD_NAME)
            {
                throw $e;
            }

            $result['state'] = 0;
            $result['message'] = "该命令进程已结束或不存在，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }
    }
}