<?php
namespace SupBoard\Controller;

use Phalcon\Tag;

class ServerGroupController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('分组管理');
        //$this->view->setTemplateBefore('container');
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
                    $this->flashSession->success("添加成功");
                    $form->clear();

                    return $this->response->redirect('server-group');
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $ids = $this->request->getPost('ids', 'string', '');

        $id_arr = array_filter(explode(',', $ids), function($item) {
            return is_numeric($item);
        });

        if (empty($id_arr))
        {
            $this->flashSession->error("请选择分组");
        }
        else
        {
            $phql = "DELETE FROM ServerGroup WHERE id IN ({ids:array-int})";
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

        return $this->response->redirect('server-group');
    }

    public function editAction($id)
    {
        $serverGroup = ServerGroup::findFirst($id);
        if (!$serverGroup)
        {
            $this->flashSession->error("不存在该分组");

            return $this->response->redirect("server-group");
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
                    $this->flashSession->success("修改成功");
                    $form->clear();

                    return $this->response->redirect('server-group');
                }
            }
        }

        $this->view->serverGroup = $serverGroup;
        $this->view->form = new ServerGroupForm($serverGroup, [
            'edit' => true
        ]);
    }
}