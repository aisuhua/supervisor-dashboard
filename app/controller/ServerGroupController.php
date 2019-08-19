<?php

class ServerGroupController extends ControllerBase
{
    public function indexAction()
    {

    }

    public function addAction()
    {

    }

    public function add2Action()
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
            $this->flash->success('恭喜你，分组添加成功！');
        }
        else
        {
            $messages = $serverGroup->getMessages();

            foreach ($messages as $message)
            {
                $this->flash->error($message->getMessage());
            }
        }

        $this->dispatcher->forward(['action' => 'index']);
    }
}

