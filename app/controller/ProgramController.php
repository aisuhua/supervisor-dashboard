<?php

class ProgramController extends ControllerBase
{
    public function initialize()
    {
        $server_id = $this->dispatcher->getParam('server_id');

        if ($server_id)
        {
            $server = Server::findFirst($server_id);
            if (!$server)
            {
                $this->flashSession->error("不存在该服务器");
                return $this->response->redirect($this->request->getHTTPReferer());
            }

            $this->view->server = $server;
            $this->server = $server;
        }
    }

    public function indexAction()
    {
        /**
         * 当前所选中的服务器模型实例
         * @var Server $server
         */
        $server = $this->server;

        $supervisor = new Supervisor(
            $server->id,
            $server->ip,
            'worker',
            '111111',
            $server->port
        );

        $processes = $supervisor->getAllProcessInfo();
        $processGroups = array_unique(array_column($processes, 'group'));

        $this->view->processes = $processes;
        $this->view->processGroups = $processGroups;
    }

    public function createAction()
    {

    }
}

