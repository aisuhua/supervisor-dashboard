<?php

class ServerController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('服务器管理');
    }

    public function indexAction()
    {

    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);
        $server_group_id = $this->request->get('server_group_id', 'int');

        $where = '1=1';
        $bind = [];
        if ($server_group_id)
        {
            $where .= ' AND server_group_id = :server_group_id:';
            $bind['server_group_id'] = $server_group_id;
        }

        $servers = Server::find([
            $where,
            'bind' => $bind,
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'sort desc'
        ]);

        $total = Server::count();

        $data = $servers->toArray();
        foreach ($servers as $key => $server)
        {
            $data[$key]['serverGroup'] = $server->serverGroup->toArray();
        }

        $result = [];
        $result['draw'] = $draw + 1;
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
        $result['data'] = $data;

        return $this->response->setJsonContent($result);
    }

    public function createAction()
    {
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
                    'sync_conf_port' => $this->request->getPost('sync_conf_port', ['trim', 'int'], 0),
                    'conf_path' => $this->request->getPost('conf_path', ['trim', 'string'], ''),
                    'sort' => $this->request->getPost('sort', 'int', 0)
                ]);

                if (!$server->create())
                {
                    $this->flash->error($server->getMessages());
                }
                else
                {
                    $this->flashSession->success("添加成功");
                    $form->clear();

                    return $this->response->redirect('server');
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction($id)
    {
        $server = Server::findFirst($id);

        if (!$server)
        {
            $this->flashSession->error("不存在该服务器");

            return $this->response->redirect('server');
        }

        if ($this->request->isPost())
        {
            $server->assign([
                'name' => $this->request->getPost('name', ['trim', 'string'], ''),
                'description' => $this->request->getPost('description', ['trim', 'string'], ''),
                'sort' => $this->request->getPost('sort', 'int', 0)
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
                    $this->flashSession->success("修改成功");
                    $form->clear();

                    return $this->response->redirect('server');
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
            $this->flashSession->error("请先选择服务器");
        }
        else
        {
            $phql = "DELETE FROM Server WHERE id IN ({ids:array-int})";
            $result = $this->modelsManager->executeQuery(
                $phql,
                ['ids' => $id_arr]
            );

            if ($result->success())
            {
                $this->flashSession->success("删除成功");
            }
            else
            {
                $messages = $result->getMessages();
                foreach ($messages as $message)
                {
                    $this->flashSession->error($message->getMessage());
                }
            }
        }

        return $this->response->redirect('server');
    }
}

