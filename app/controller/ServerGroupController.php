<?php

class ServerGroupController extends ControllerBase
{
    public function indexAction()
    {

    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);

        $serverGroups = ServerGroup::find([
            'limit' => $limit,
            'offset' => $offset
        ]);

        $total = ServerGroup::count();

        $data = [];
        $data['draw'] = $draw + 1;
        $data['recordsTotal'] = $total;
        $data['recordsFiltered'] = $total;
        $data['data'] = $serverGroups->toArray();

        return $this->response->setJsonContent($data);
    }

    public function createAction()
    {
        if ($this->request->isPost())
        {
            $name = $this->request->getPost('name', ['trim', 'string'], '');
            $description = $this->request->getPost('description', ['trim', 'string'], '');
            $sort = $this->request->getPost('sort', 'int', 0);

            $serverGroup = new ServerGroup();
            $serverGroup->name = $name;
            $serverGroup->description = $description;
            $serverGroup->sort = $sort;

            if ($serverGroup->create())
            {
                $this->flashSession->success('恭喜你，分组添加成功！');
                return $this->response->redirect('server-group');
            }
            else
            {
                $messages = $serverGroup->getMessages();

                foreach ($messages as $message)
                {
                    $this->flashSession->error($message->getMessage());
                }
            }
        }
    }
}

