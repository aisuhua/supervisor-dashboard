<?php
use Phalcon\Mvc\View;
use Phalcon\Db;

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

    public function tailAction($id)
    {
        // 看最后的 10M 日志内容
        $sql = 'SELECT id, command, status, program, RIGHT(log, 1 * 1024 * 1024) as log FROM cron_log WHERE id = :id LIMIT 1';
        $cronLog = $this->db->fetchOne($sql, Db::FETCH_ASSOC, [
            'id' => $id
        ]);

        if (!$cronLog)
        {
            $this->flash->error("不存在该任务日志");

            $this->dispatcher->forward([
                'action' => 'index'
            ]);
            return false;
        }

        $running = false;
        // 正在运行的进程直接读取 Supervisor 日志
        if ($cronLog['status'] == CronLog::STATUS_STARTED)
        {
            try
            {
                $process_name = $cronLog['program'] . ':' . $cronLog['program'] . '_0';
                $cronLog['log'] = $this->supervisor->tailProcessStdoutLog($process_name, 0, 1 * 1024 * 1024)[0];
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
                $cronLog = $this->db->fetchOne($sql, Db::FETCH_ASSOC, [
                    'id' => $id
                ]);

                if (!$cronLog)
                {
                    $this->flash->error("不存在该任务日志");

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

        $this->view->setTemplateBefore('cronLogTail');

        if ($this->isPjax())
        {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }

        $this->view->running = $running;
        $this->view->cronLog = $cronLog;
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

        $path_parts = pathinfo($cronLog->command);
        $filename = $path_parts['filename'] . '_' . date('Ymd-Hi', $cronLog->start_time) . $cronLog->id . '.log';

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        echo $cronLog->log;
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
        catch (Zend\XmlRpc\Client\Exception\FaultException $e)
        {
            if ($e->getCode() != XmlRpc::BAD_NAME)
            {
                throw $e;
            }

            $result['state'] = 0;
            $result['message'] = "该任务已结束或不存在，无法执行停止操作";

            return $this->response->setJsonContent($result);
        }
    }
}