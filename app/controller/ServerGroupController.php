<?php
namespace SupBoard\Controller;

use SupBoard\Model\ServerGroup;
use SupBoard\Form\ServerGroupForm;

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
            'offset' => $offset,
            'order' => 'sort desc'
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
                    $this->flash->success("添加成功");
                    $form->clear();
                }
            }
        }

        $this->view->form = $form;
    }

    public function editAction($id)
    {
        $serverGroup = ServerGroup::findFirst($id);
        if (!$serverGroup)
        {
            $this->flash->error("不存在该分组");
            $this->dispatcher->forward([
                'action' => 'index'
            ]);

            return false;
        }

        if ($this->request->isPost())
        {
            $serverGroup->assign([
                'name' => $this->request->getPost('name', ['trim', 'string'], ''),
                'description' => $this->request->getPost('description', ['trim', 'string'], ''),
                'sort' => $this->request->getPost('sort', 'int', 0)
            ]);

            $form = new ServerGroupForm($serverGroup, [
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
                if (!$serverGroup->save())
                {
                    $this->flash->error($serverGroup->getMessages());
                }
                else
                {
                    $this->flash->success("保存成功");
                    $form->clear();
                }
            }
        }

        $this->view->serverGroup = $serverGroup;
        $this->view->form = new ServerGroupForm($serverGroup, [
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
            $this->flash->error("请选择分组");
        }
        else
        {
            $phql = "DELETE FROM " . ServerGroup::class . " WHERE id IN ({ids:array-int})";
            $result = $this->modelsManager->executeQuery($phql, [
                'ids' => $id_arr
            ]);

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