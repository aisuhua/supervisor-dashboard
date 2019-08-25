<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class ControllerSupervisorBase extends ControllerBase
{
    /**
     * @var Server $server
     */
    protected $server;
    protected $callback;

    /**
     * @link https://stackoverflow.com/questions/16941456/adding-a-php-anonymous-function-to-an-object
     * @param $callback
     */
    protected function setCallback($callback)
    {
        $this->callback = $callback;
    }

    protected function invoke()
    {
        $exception = null;
        for ($i = 0; $i < 3600; $i++)
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
}
