<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;
use SupBoard\Model\Command;
use SupBoard\Form\CommandForm;
use SupBoard\Model\CronLog;
use SupBoard\Supervisor\StatusCode;
use Zend\XmlRpc\Client\Exception\FaultException;

class CommandController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $form = new CommandForm(null);

        if ($this->request->isPost())
        {
            $supAgent = $this->server->getSupAgent();
            if (!$supAgent->ping())
            {
                $this->flash->error("执行失败，无法连接 <a href='#'>supervisor agent</a> 服务");
                goto end;
            }

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

            $command->refresh();
            $program = $command->getProgram();
            $command->program = $program;
            $command->save();

            // 更新配置
            $reload = $supAgent->commandAdd($command->id);
            if (!$reload['state'])
            {
                $this->flash->error($reload['message']);
                goto end;
            }

            // 启动进程
            $this->supervisor->reloadConfig();
            $this->supervisor->addProcessGroup($program);
            $this->supervisor->startProcessGroup($program);

            // $this->flash->success("命令已开始执行");
            $form->clear();
            $this->view->success = true;
            $this->view->command = $command->refresh();
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

    public function logAction($id)
    {
        /** @var Command $command */
        $command = Command::findFirst($id);
        if (!$command)
        {
            $this->flash->error("该日志不存在");
            $this->dispatcher->forward([
                'action' => 'history'
            ]);

            return false;
        }

        if ($command->status == CronLog::STATUS_INI)
        {
            $this->flash->error("该命令正在启动执行，请稍后");
            $this->dispatcher->forward([
                'action' => 'history'
            ]);

            return false;
        }

        $running = false;
        $offset = 0;

        // 正在运行的进程直接读取 Supervisor 日志
        if ($command->status == CronLog::STATUS_STARTED)
        {
            try
            {
                $process_name = $command->getProcessName();
                $info = $this->supervisor->tailProcessStdoutLog($process_name, 0, 1 * 1024 * 1024);

                $log = $info[0];
                $offset = $info[1];
                $running = true;
            }
            catch (FaultException $e)
            {
                // 进程不存在，一般来说是因为该进程已经执行完成并已经被删除
                if ($e->getCode() != StatusCode::BAD_NAME)
                {
                    throw $e;
                }

                $supAgent = $command->getServer()->getSupAgent();
                $log = $supAgent->tailCommandLog($command->id);
            }
        }
        else
        {
            $supAgent = $command->getServer()->getSupAgent();
            $log = $supAgent->tailCommandLog($command->id);
        }

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->running = $running;
        $this->view->group = $command->program;
        $this->view->name = $command->program . '_0';
        $this->view->command = $command;
        $this->view->log = $log;
        $this->view->offset = $offset;
    }

    public function downloadAction($id)
    {
        /** @var Command $command */
        $command = Command::findFirst($id);
        if (!$command)
        {
            $this->flash->error("该命令日志不存在");
            $this->dispatcher->forward([
                'action' => 'history'
            ]);

            return false;
        }

        $filename =  $command->program . '_' . date('YmdHi', $command->start_time) . '.log';

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $supAgent = $command->getServer()->getSupAgent();
        echo $supAgent->tailCommandLog($command->id, 0);
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

            $phql = "DELETE FROM " . Command::class . " WHERE id IN ({ids:array-int}) AND status IN({$finished})";
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

        $this->dispatcher->forward([
            'action' => 'history',
            'params' => [
                'server_id' => $this->server_id
            ]
        ]);

        return false;
    }

    public function stopAction($id)
    {
        /** @var Command $command */
        $command = Command::findFirst($id);
        if (!$command)
        {
            $result['state'] = 0;
            $result['message'] = "不存在该命令执行记录，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }

        try
        {
            $this->supervisor->stopProcessGroup($command->getProgram(), false);

            $result['state'] = 1;
            $result['message'] = $this->formatMessage("ID 为 {$id} 的命令正在停止");

            return $this->response->setJsonContent($result);
        }
        catch (FaultException $e)
        {
            if ($e->getCode() != StatusCode::BAD_NAME)
            {
                throw $e;
            }

            $result['state'] = 0;
            $result['message'] = "该命令进程已结束或不存在，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }
    }
}