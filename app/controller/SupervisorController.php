<?php
use Phalcon\Mvc\View;

class SupervisorController extends ControllerBase
{
    private $server_id;
    private $server;

    /**
     * @var Supervisor $supervisor;
     */
    private $supervisor;

    public function initialize()
    {
        $server_id = $this->dispatcher->getParam('server_id', 'int');

        if ($server_id)
        {
            /**
             * @var Server $server
             */
            $server = Server::findFirst($server_id);
            if (!$server)
            {
                $this->flashSession->error("不存在该服务器");
                return $this->response->redirect($this->request->getHTTPReferer());
            }

            $supervisor = new Supervisor(
                $server->id,
                $server->ip,
                $server->username ? $server->username : 'worker',
                $server->password ? $server->password : '111111',
                $server->port
            );

            $this->server_id = $server_id;
            $this->server = $server;
            $this->supervisor = $supervisor;
            $this->view->server = $server;
        }
    }

    public function errorAction()
    {
        try
        {
            $this->supervisor->getState();

            return $this->dispatcher->forward([
                'controller' => 'process',
                'action' => 'index',
                'params' => [
                    'server_id' => $this->server_id
                ]
            ]);
        }
        catch (Exception $e)
        {
            if ($e instanceof Zend\XmlRpc\Client\Exception\HttpException &&
                $e->getMessage() == 'Unauthorized')
            {

            }

            $this->view->message = $e->getMessage();;
        }
    }

    public function restartAction()
    {
        $this->supervisor->restart();

        $timeout = 10;
        $start_time = time();
        $has_starting = true;

        // 10 秒内如果还有启动中的脚本则不再检测
        while (time() - $start_time < $timeout && $has_starting)
        {
            $has_starting = false;
            $allProcessInfo = $this->supervisor->getAllProcessInfo();
            foreach ($allProcessInfo as $processInfo)
            {
                if ($processInfo['statename'] == 'STARTING')
                {
                    $has_starting = true;
                    break;
                }
            }
        }

        $this->flashSession->success("Supervisor 服务重启完成");
        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function shutdownAction()
    {
        $this->supervisor->shutdown();

        return $this->response->redirect($this->request->getHTTPReferer());
    }

}

