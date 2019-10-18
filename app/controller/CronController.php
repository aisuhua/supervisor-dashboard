<?php
namespace SupBoard\Controller;

use Cron\CronExpression;
use SupBoard\Form\CronForm;
use SupBoard\Model\Cron;
use SupBoard\Model\Server;
use SupBoard\Model\ServerGroup;
use SupBoard\Supervisor\SupervisorCron;
use Zend\XmlRpc\Client\Exception\FaultException;
use SupBoard\Supervisor\StatusCode;

class CronController extends ControllerSupervisor
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
                    $this->view->restart_cron_task = true;
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
            // 是否需要重启定时任务守护进程
            $restart_cron_task = false;

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
                    $restart_cron_task = true;
                }

                if (!$cron->save())
                {
                    $this->flash->error($cron->getMessages());
                }
                else
                {
                    $this->flash->success("保存成功");
                    $form->clear();
                    $this->view->restart_cron_task = $restart_cron_task;
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
        /** @var Cron $cron */
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

                if ($cron->status == Cron::STATUS_ACTIVE)
                {
                    $this->view->restart_cron_task = true;
                }
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
}