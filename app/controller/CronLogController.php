<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;
use Phalcon\Db;
use SupBoard\Model\Cron;
use SupBoard\Model\CronLog;
use SupBoard\Supervisor\StatusCode;
use SupBoard\Supervisor\SupAgent;
use Zend\XmlRpc\Client\Exception\FaultException;

class CronLogController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $cron_id = $this->request->get('cron_id', 'int', 0);
        $this->view->cron_id = $cron_id;
    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);
        $cron_id = $this->request->get('cron_id', 'int');

        // 查看特定服务器的日志
        $bind = [];
        $where = 'server_id = :server_id:';
        $bind['server_id'] = $this->server_id;

        if ($cron_id)
        {
            $where .= ' AND cron_id = :cron_id:';
            $bind['cron_id'] = $cron_id;
        }

        $cronLogs = CronLog::find([
            $where,
            'bind' => $bind,
            'columns' => "id, server_id, cron_id, command, program, status, start_time, end_time",
            'order' => 'id desc'
        ]);

        $total = CronLog::count();
        $data = $cronLogs->toArray();

        $result = [];
        $result['draw'] = $draw + 1;
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
        $result['data'] = $data;

        return $this->response->setJsonContent($result);
    }

    public function logAction($id)
    {
        /** @var CronLog $cronLog */
        $cronLog = CronLog::findFirst($id);
        if (!$cronLog)
        {
            $this->flash->error("该日志不存在");
            $this->dispatcher->forward([
                'action' => 'index'
            ]);

            return false;
        }

        if ($cronLog->status == CronLog::STATUS_INI)
        {
            $this->flash->error("该定时任务正在启动，请稍后");
            $this->dispatcher->forward([
                'action' => 'index'
            ]);

            return false;
        }

        $running = false;
        $offset = 0;

        // 正在运行的进程直接读取 Supervisor 日志
        if ($cronLog->status == CronLog::STATUS_STARTED)
        {
            try
            {
                $process_name = $cronLog->getProcessName();
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

                $supAgent = $cronLog->getServer()->getSupAgent();
                $log = $supAgent->tailCronLog($cronLog->id);
            }
        }
        else
        {
            $supAgent = $cronLog->getServer()->getSupAgent();
            $log = $supAgent->tailCronLog($cronLog->id);
        }

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->running = $running;
        $this->view->group = $cronLog->program;
        $this->view->name = $cronLog->program . '_0';
        $this->view->cronLog = $cronLog;
        $this->view->log = $log;
        $this->view->offset = $offset;
    }

    public function downloadAction($id)
    {
        /** @var CronLog $cronLog */
        $cronLog = CronLog::findFirst($id);

        if (!$cronLog)
        {
            $this->flash->error("不存在该任务日志");

            $this->dispatcher->forward([
                'action' => 'index'
            ]);
            return false;
        }

        $filename =  $cronLog->program . '_' . date('YmdHi', $cronLog->start_time) . '.log';

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $supAgent = $cronLog->getServer()->getSupAgent();
        echo $supAgent->tailCronLog($cronLog->id, 0);
        exit;
    }

    public function stopAction($id)
    {
        /** @var CronLog $cronLog */
        $cronLog = CronLog::findFirst($id);
        if (!$cronLog)
        {
            $result['state'] = 0;
            $result['message'] = "不存在该任务，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }

        try
        {
            $this->supervisor->stopProcessGroup($cronLog->program, false);

            $result['state'] = 1;
            $result['message'] = $this->formatMessage("ID 为 {$id} 的任务正在停止");

            return $this->response->setJsonContent($result);
        }
        catch (FaultException $e)
        {
            if ($e->getCode() != StatusCode::BAD_NAME)
            {
                throw $e;
            }

            $result['state'] = 0;
            $result['message'] = "该任务已结束或不存在，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }
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
            // 只删除已经完成的任务日志
            $finished = implode(',', [
                CronLog::STATUS_FAILED,
                CronLog::STATUS_UNKNOWN,
                CronLog::STATUS_STOPPED,
                CronLog::STATUS_FINISHED
            ]);

            $phql = "DELETE FROM " . CronLog::class . " WHERE id IN ({ids:array-int}) AND status IN({$finished})";
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
           'action' => 'index',
            'params' => [
                'server_id' => $this->server_id
            ]
        ]);

        return false;
    }
}