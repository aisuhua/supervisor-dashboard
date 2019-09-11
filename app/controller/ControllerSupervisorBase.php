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

    /**
     * @link https://stackoverflow.com/questions/16941456/adding-a-php-anonymous-function-to-an-object
     * @param $callback
     */
    protected function setCallback($callback)
    {
        $this->callback = $callback;
    }

    protected function invoke($timeout = 10)
    {
        $exception = null;
        for ($i = 1; $i <= $timeout * 2; $i++)
        {
            try
            {
                call_user_func($this->callback);
                $exception = null;
                break;
            }
            catch (Exception $e)
            {
                $exception = $e;
                usleep(500000);
            }
        }

        if ($exception)
        {
            throw $exception;
        }
    }

    protected function redirectToIndex()
    {
        return $this->response->redirect(self::getIndexUrl());
    }

    protected function getIndexUrl()
    {
        return "/server/{$this->server->id}/process?ip={$this->server->ip}&port={$this->server->port}";
    }

    protected function formatMessage($message)
    {
        return "<strong>{$message}</strong>\n请刷新页面查看进度";
    }
}
