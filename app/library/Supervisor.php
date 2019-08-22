<?php
use Supervisor\Supervisor as SupervisorBase;

class Supervisor extends SupervisorBase
{
    public function getAllProcessInfo()
    {
        return $this->rpcClient->call('supervisor.getAllProcessInfo');
    }
}