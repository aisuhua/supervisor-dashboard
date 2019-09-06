<?php
use Phalcon\Mvc\View;

class CronController extends ControllerSupervisorBase
{
    public function indexAction()
    {

    }

    public function listAction()
    {
        $draw = $this->request->get('draw', 'int', 0);
        $offset = $this->request->get('start', 'int', 0);
        $limit = $this->request->get('length', 'int', 25);

        $cron = Cron::find([
            'limit' => $limit,
            'offset' => $offset,
            'order' => 'id asc'
        ]);

        $total = Cron::count();

        $data = [];
        $data['draw'] = $draw + 1;
        $data['recordsTotal'] = $total;
        $data['recordsFiltered'] = $total;
        $data['data'] = $cron->toArray();

        return $this->response->setJsonContent($data);
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
}