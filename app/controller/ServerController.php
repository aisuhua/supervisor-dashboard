<?php
namespace SupBoard\Controller;

use SupBoard\Model\ServerGroup;
use SupBoard\Model\Server;
use SupBoard\Form\ServerForm;

class ServerController extends ControllerBase
{
    public function indexAction()
    {
        $group_id = $this->request->get('group_id', 'int', 0);
        $this->view->group_id = $group_id;
    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);
        $group_id = $this->request->get('group_id', 'int');

        $builder = $this
            ->modelsManager
            ->createBuilder()
            ->from(['s' => Server::class])
            ->leftJoin(ServerGroup::class, "s.server_group_id = g.id", 'g');

        if ($group_id)
        {
            $builder->where('server_group_id = :server_group_id:', [
                'server_group_id' => $group_id
            ]);
        }

        $servers = $builder->columns([
            'g.id as server_group_id',
            'g.name as server_group_name',
            's.id as id',
            's.ip as ip',
            's.port as port',
            's.username as username',
            's.password as password',
            's.agent_port as agent_port',
            's.agent_root as agent_root',
            's.update_time as update_time',
            's.create_time as create_time',
        ])
        ->orderBy('g.sort DESC, s.ip ASC')
        ->offset($offset)
        ->limit($limit)
        ->getQuery()
        ->execute();

        $total = $servers->count();
        $result = [];
        $result['draw'] = $draw + 1;
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
        $result['data'] = $servers;

        return $this->response->setJsonContent($result);
    }

    public function createAction()
    {
        $group_id = $this->request->get('group_id', 'int', 0);
        $form = new ServerForm(null);

        if ($this->request->isPost())
        {
            if (!$form->isValid($this->request->getPost()))
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
            else
            {
                $server = new Server([
                    'server_group_id' => $this->request->getPost('server_group_id', ['trim', 'int'], 0),
                    'ip' => $this->request->getPost('ip', ['trim', 'string'], ''),
                    'port' => $this->request->getPost('port', ['trim', 'int'], 0),
                    'username' => $this->request->getPost('username', ['trim', 'string'], ''),
                    'password' => $this->request->getPost('password', ['trim']),
                    'agent_port' => $this->request->getPost('agent_port', ['trim', 'int'], 0),
                    'agent_root' => $this->request->getPost('agent_root', ['trim', 'string'], '')
                ]);

                if (!$server->create())
                {
                    $this->flash->error($server->getMessages());
                }
                else
                {
                    $this->flash->success("添加成功");
                    $form->clear();
                }
            }
        }

        $this->view->form = $form;
        $this->view->group_id = $group_id;
    }

    public function editAction($id)
    {
        $server = Server::findFirst($id);
        if (!$server)
        {
            $this->flash->error("该服务器不存在");
            $this->dispatcher->forward([
                'action' => 'index'
            ]);

            return false;
        }

        if ($this->request->isPost())
        {
            $server->assign([
                'server_group_id' => $this->request->getPost('server_group_id', ['trim', 'int'], 0),
                'ip' => $this->request->getPost('ip', ['trim', 'string'], ''),
                'port' => $this->request->getPost('port', ['trim', 'int'], 0),
                'username' => $this->request->getPost('username', ['trim', 'string'], ''),
                'password' => $this->request->getPost('password', ['trim']),
                'agent_port' => $this->request->getPost('agent_port', ['trim', 'int'], 0),
                'agent_root' => $this->request->getPost('agent_root', ['trim', 'string'], '')
            ]);

            $form = new ServerForm($server, [
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
                if (!$server->save())
                {
                    $this->flash->error($server->getMessages());
                }
                else
                {
                    $this->flash->success("保存成功");
                    $form->clear();
                }
            }
        }

        $this->view->server = $server;
        $this->view->form = new ServerForm($server, [
            'edit' => true
        ]);
    }

    public function deleteAction()
    {
        $ids = $this->request->getPost('ids', 'string', '');

        $id_arr = array_filter(explode(',', $ids), function($item) {
            return is_numeric($item);
        });

        if (empty($id_arr))
        {
            $this->flash->error("请先选择服务器");
        }
        else
        {
            $phql = "DELETE FROM " . Server::class . " WHERE id IN ({ids:array-int})";
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
                $messages = $result->getMessages();
                foreach ($messages as $message)
                {
                    $this->flash->error($message->getMessage());
                }
            }
        }

        $this->dispatcher->forward([
            'action' => 'index'
        ]);

        return false;
    }
}