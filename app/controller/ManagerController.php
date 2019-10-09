<?php
namespace SupBoard\Controller;


use Cron\CronExpression;
use SupBoard\Model\Cron;
use SupBoard\Model\Process;
use SupBoard\Model\Server;
use SupBoard\Model\ServerGroup;

class ManagerController extends ControllerBase
{
    public function processAllAction()
    {

    }

    public function processListAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 10000);

        $where = "1 = 1";
        if (!DEBUG_MODE)
        {
            $where .= ' AND is_sys = 0';
        }

        $processes = Process::find([
            'conditions' => $where,
            'columns' => "id, server_id, program, update_time",
            'order' => 'id desc',
            'offset' => $offset,
            'limit' => $limit
        ])->toArray();

        $servers = Server::find();
        $serverGroups = ServerGroup::find();

        $server_columns = array_column($servers->toArray(), null, 'id');
        $group_columns = array_column($serverGroups->toArray(), null, 'id');

        foreach ($processes as &$process)
        {
            $server = $server_columns[$process['server_id']];
            $group = $group_columns[$server['server_group_id']];

            $process['group_id'] = $group['id'];
            $process['group_name'] = $group['name'];
            $process['server_id'] = $server['id'];
            $process['server_ip'] = $server['ip'];
            $process['server_port'] = $server['port'];
        }

        $total = count($processes);
        $result = [];
        $result['draw'] = $draw + 1;
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
        $result['data'] = $processes;

        return $this->response->setJsonContent($result);
    }


    public function cronAllAction()
    {

    }

    public function cronListAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 10000);

        $crones = $this
            ->modelsManager
            ->createBuilder()
            ->from(['c' => Cron::class])
            ->leftJoin(Server::class, "c.server_id = s.id", 's')
            ->leftJoin(ServerGroup::class, 's.server_group_id = g.id', 'g')
            ->columns([
                'g.id as group_id',
                'g.name as group_name',
                's.id as server_id',
                's.ip as server_ip',
                's.port as server_port',
                'c.id as id',
                'c.user as user',
                'c.time as time',
                'c.command as command',
                'c.status as status',
                'c.last_time as last_time',
                'c.update_time as update_time',
                'c.description as description',
            ])
            ->orderBy('g.sort desc, c.id asc')
            ->offset($offset)
            ->limit($limit)
            ->getQuery()
            ->execute();

        $total = $crones->count();
        $crones = $crones->toArray();

        foreach ($crones as &$cron)
        {
            $cronExpress = CronExpression::factory($cron['time']);
            $cron['next_time'] = $cronExpress->getNextRunDate()->format('U');
        }

        $result = [];
        $result['draw'] = $draw + 1;
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
        $result['data'] = $crones;

        return $this->response->setJsonContent($result);
    }
}