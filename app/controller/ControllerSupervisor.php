<?php
namespace SupBoard\Controller;

use SupBoard\Exception\Exception;
use SupBoard\Model\Server;
use SupBoard\Supervisor\StatusCode;
use SupBoard\Supervisor\Supervisor;
use Zend\XmlRpc\Client\Exception\FaultException;

class ControllerSupervisor extends ControllerBase
{
    protected $server_id;
    /** @var Server $server */
    protected $server;
    /** @var Supervisor $supervisor */
    protected $supervisor;

    public function initialize()
    {
        $server_id = $this->request->get('server_id', 'int', 0);

        if (!$server_id)
        {
            throw new Exception("缺少 server_id 参数");
        }

        /** @var Server $server */
        $server = Server::findFirst($server_id);
        if (!$server)
        {
            throw new Exception("该服务器不存在");
        }

        $this->server_id = $server_id;
        $this->server = $server;
        $this->supervisor = $server->getSupervisor();

        $this->view->server_id = $server_id;
        $this->view->server = $server;
    }

    protected function formatMessage($message, $only = false)
    {
        if ($only)
        {
           return "<strong>{$message}</strong>";
        }
        return "<strong>{$message}</strong>\n请刷新页面查看进度";
    }

    protected function handleStopException(FaultException $e)
    {
        if ($e->getCode() != StatusCode::NOT_RUNNING)
        {
            throw $e;
        }
    }

    protected function handleStartException(FaultException $e)
    {
        if ($e->getCode() != StatusCode::ALREADY_STARTED)
        {
            throw $e;
        }
    }

    protected function handleAddException(FaultException $e)
    {
        if ($e->getCode() != StatusCode::ALREADY_ADDED)
        {
            throw $e;
        }
    }

    protected function handleRemoveException(FaultException $e)
    {
        if ($e->getCode() != StatusCode::BAD_NAME)
        {
            throw $e;
        }
    }
}
