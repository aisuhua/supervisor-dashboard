<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class ControllerSupervisorBase extends ControllerBase
{
    protected $server_id;

    /**
     * @var Server $server
     */
    protected $server;

    /**
     * @var Supervisor $supervisor;
     */
    protected $supervisor;

    /**
     * @var Callback $callback
     */
    protected $callback;

    public function initialize()
    {
        $server_id = $this->request->get('server_id', 'int', 0);

        if ($server_id)
        {
            /** @var Server $server */
            $server = Server::findFirst($server_id);
            if (!$server)
            {
                $this->flashSession->error("不存在该服务器");
                return $this->response->redirect($this->request->getHTTPReferer());
            }

            $supervisor = new Supervisor(
                $server->id,
                $server->ip,
                $server->username,
                $server->password,
                $server->port
            );

            $this->server_id = $server_id;
            $this->server = $server;
            $this->supervisor = $supervisor;

            $this->view->server_id = $server_id;
            $this->view->server = $server;
        }
    }

    protected function formatMessage($message)
    {
        return "<strong>{$message}</strong>\n请刷新页面查看进度";
    }

    protected function handleStopException(Exception $e)
    {
        if ($e->getCode() != XmlRpc::NOT_RUNNING)
        {
            throw $e;
        }
    }

    protected function handleStartException(Exception $e)
    {
        if ($e->getCode() != XmlRpc::ALREADY_STARTED)
        {
            throw $e;
        }
    }

    protected function handleAddException(Exception $e)
    {
        if ($e->getCode() != XmlRpc::ALREADY_ADDED)
        {
            throw $e;
        }
    }

    protected function handleRemoveException(Exception $e)
    {
        if ($e->getCode() != XmlRpc::BAD_NAME)
        {
            throw $e;
        }
    }
}
