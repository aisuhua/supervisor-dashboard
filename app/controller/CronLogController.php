<?php
use Phalcon\Mvc\View;

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

        $where = '1=1';
        $bind = [];
        if ($cron_id)
        {
            $where .= ' AND cron_id = :cron_id:';
            $bind['cron_id'] = $cron_id;
        }

        $cronLogs = CronLog::find([
            $where,
            'bind' => $bind,
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


}