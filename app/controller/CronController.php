<?php
namespace SupBoard\Controller;

use Cron\CronExpression;
use SupBoard\Form\CronForm;
use SupBoard\Model\Cron;
use SupBoard\Model\Server;
use SupBoard\Model\ServerGroup;

class CronController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $cron_arr = Cron::find([
            'server_id = :server_id:',
            'bind' => [
                'server_id' => $this->server_id
            ],
            'order' => 'status desc, id asc'
        ])->toArray();

        $total = Cron::count();

        foreach ($cron_arr as &$cron)
        {
            $cronExpress = CronExpression::factory($cron['time']);
            $cron['next_time'] = $cronExpress->getNextRunDate()->format('U');
        }

        $this->view->cron_arr = $cron_arr;
        $this->view->total = $total;
    }

    public function createAction()
    {
        $form = new CronForm(null);

        if ($this->request->isPost())
        {
            $cron = new Cron();
            $form->bind($this->request->getPost(), $cron);

            if (!$form->isValid())
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message->getMessage());
                }
            }
            else
            {
                if (!$cron->create())
                {
                    $this->flash->error($cron->getMessages());
                }
                else
                {
                    $form->clear();
                    $this->flash->success("添加成功");
                    $this->view->reload_config = true;
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction($id)
    {
        /** @var Cron $cron */
        $cron = Cron::findFirst($id);
        if (!$cron)
        {
            $this->flash->error("不存在该定时任务");

            $this->dispatcher->forward([
                'action' => 'index',
                'params' => [
                    'server_id' => $this->server_id
                ]
            ]);
            return false;
        }

        if ($this->request->isPost())
        {
            $form = new CronForm($cron, [
                'edit' => true
            ]);

            if (!$form->isValid($this->request->getPost()))
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
            else
            {
                if ($cron->hasChanged('command') ||
                    $cron->hasChanged('user') ||
                    $cron->hasChanged('status') ||
                    $cron->hasChanged('time')
                )
                {
                    $cron->last_time = 0;
                }

                if (!$cron->save())
                {
                    $this->flash->error($cron->getMessages());
                }
                else
                {
                    $this->flash->success("修改成功");
                    $form->clear();
                }
            }
        }

        $this->view->cron = $cron;
        $this->view->form = new CronForm($cron, [
            'edit' => true
        ]);
    }

    public function deleteAction($id)
    {
        $cron = Cron::findFirst($id);

        if (!$cron)
        {
            $this->flash->error('不存在该定时任务');
        }
        else
        {
            if(!$cron->delete())
            {
                $this->flash->error($cron->getMessages());
            }
            else
            {
                $this->flash->success('删除成功');
            }
        }

        $this->dispatcher->forward([
            'action' => 'index',
            'params' => [
                'server_id' => $this->server_id
            ]
        ]);
        return true;
    }


    public function allAction()
    {

    }

    public function listAction()
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
            ->orderBy('c.id desc')
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