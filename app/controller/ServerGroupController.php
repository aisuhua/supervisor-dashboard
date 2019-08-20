<?php
use Phalcon\Tag;

class ServerGroupController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('分组管理');
    }

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
        $form = new ServerGroupForm(null);

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
                $serverGroup = new ServerGroup([
                    'name' => $this->request->getPost('name', ['trim', 'string'], ''),
                    'description' => $this->request->getPost('description', ['trim', 'string'], ''),
                    'sort' => $this->request->getPost('sort', 'int', 0)
                ]);

                if (!$serverGroup->create())
                {
                    $this->flash->error($serverGroup->getMessages());
                }
                else
                {
                    $this->flash->success("分组添加成功！");
                    $form->clear();
                }
            }
        }

        $this->view->form = $form;
    }
}

