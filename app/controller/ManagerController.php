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

        $builder = $this
            ->modelsManager
            ->createBuilder()
            ->from(['p' => Process::class])
            ->leftJoin(Server::class, "p.server_id = s.id", 's')
            ->leftJoin(ServerGroup::class, 's.server_group_id = g.id', 'g')
            ->columns([
                'g.id as group_id',
                'g.name as group_name',
                's.id as server_id',
                's.ip as server_ip',
                's.port as server_port',
                'p.program as program',
                'p.id as id',
                'p.update_time as update_time',
            ]);

        if (!DEBUG_MODE)
        {
            $builder->where('is_sys = 0');
        }

        $processes = $builder
            ->orderBy('g.sort desc, s.ip asc, p.program asc')
            ->offset($offset)
            ->limit($limit)
            ->getQuery()
            ->execute();

        $total = $processes->count();
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