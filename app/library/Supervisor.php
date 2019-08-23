<?php
use Supervisor\Supervisor as SupervisorBase;

class Supervisor extends SupervisorBase
{
    public function getAllProcessInfo()
    {
        return $this->rpcClient->call('supervisor.getAllProcessInfo');
    }

    public function stopProcess($name, $wait = true)
    {
        return $this->rpcClient->call('supervisor.stopProcess', array($name, $wait));
    }

    public function startProcess($name, $wait = true)
    {
        return $this->rpcClient->call('supervisor.startProcess', array($name, $wait));
    }

    public function getProcessInfo($name)
    {
        return $this->rpcClient->call('supervisor.getProcessInfo', array($name));
    }

    public function tailProcessStdoutLog($name, $offset, $length)
    {
        return $this->rpcClient->call('supervisor.tailProcessStdoutLog', array($name, $offset, $length));
    }
}