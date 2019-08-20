<?php

class ServerController extends ControllerBase
{
    public function indexAction()
    {
        $group_id = $this->request->get('group_id', 'int');

        $serverGroup = ServerGroup::findFirst($group_id);
        if (!$serverGroup)
        {
            $this->flashSession->error("不存在该服务器组");
            return $this->response->redirect($this->request->getHTTPReferer());
        }

        $this->view->serverGroup = $serverGroup;
    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);

        $server = Server::find([
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'sort desc'
        ]);

        $total = Server::count();

        $data = [];
        $data['draw'] = $draw + 1;
        $data['recordsTotal'] = $total;
        $data['recordsFiltered'] = $total;
        $data['data'] = $server->toArray();

        return $this->response->setJsonContent($data);
    }
}

