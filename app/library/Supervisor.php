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

    public function readProcessStdoutLog($name, $offset, $length)
    {
        return $this->rpcClient->call('supervisor.readProcessStdoutLog', array($name, $offset, $length));
    }

    public function clearProcessLogs($name)
    {
        return $this->rpcClient->call('supervisor.clearProcessLogs', array($name));
    }

    public function shutdown()
    {
        return $this->rpcClient->call('supervisor.shutdown');
    }

    public function restart()
    {
        return $this->rpcClient->call('supervisor.restart');
    }

    public function reloadConfig()
    {
        return $this->rpcClient->call('supervisor.reloadConfig');
    }

    public function addProcessGroup($name)
    {
        return $this->rpcClient->call('supervisor.addProcessGroup', array($name));
    }

    public function removeProcessGroup($name)
    {
        return $this->rpcClient->call('supervisor.removeProcessGroup', array($name));
    }
}