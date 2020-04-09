<?php

namespace Supervisor;

use Zend\XmlRpc\Client as RpcClient;
use Zend\Http\Client as HttpClient;

/**
 * Supervisor
 */
class Supervisor
{
    /**
     * @var Zend\XmlRpc\Client
     */
    protected $rpcClient;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $key;

    /**
     * The constructor
     *
     * @param string $name
     * @param string $ipAddress The server ip adress
     * @param string $username Default set to null
     * @param string $password Default set to null
     * @param integer $port Default set to null
     */
    public function __construct($name, $ipAddress, $username = null, $password = null, $port = null)
    {
        $this->name = $name;

        $this->rpcClient = new RpcClient('http://'.$ipAddress.':'.$port.'/RPC2/');

        if ($username !== null && $password !== null) {
            $this->rpcClient->getHttpClient()->setAuth($username, $password, HttpClient::AUTH_BASIC);
        }

        $this->createKey($ipAddress, $username, $password, $port);
    }

    /**
     * get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * get ipAddress
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Create a unique key. It ll be used to retrieve a supervisor object.
     *
     * @param string $ipAdress
     * @param string $username
     * @param string $password
     */
    private function createKey($ipAdress, $port, $username = null, $password = null)
    {
        $this->key = hash('md5', serialize(array(
            $ipAdress,
            $port,
            $username,
            $password,
        )));
    }

    /**
     * Get process by name
     *
     * @param string $name
     * @param string $group
     *
     * @return Process
     */
    public function getProcessByNameAndGroup($name, $group)
    {
        return new Process($name, $group, $this->rpcClient);
    }

    /**
     * getProcesses
     *
     * @param array $groups Only show processes in these process groups.
     *
     * @return Process[]
     */
    public function getProcesses($groups = array())
    {
        $processes = array();

        $result = $this->rpcClient->call('supervisor.getAllProcessInfo');
        foreach ($result as $cnt => $process) {
            // Skip process when process group not listed in $groups
            if (!empty($groups) && !in_array($process['group'], $groups)) {
                continue;
            }

            $processes[$cnt] = new Process($process['name'], $process['group'], $this->rpcClient);
        }

        return $processes;
    }

    /**
     * Check if supervisor is running
     *
     * @return boolean
     */
    public function checkConnection()
    {
        try {
            $this->getState();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return the version of the RPC API used by supervisord
     *
     * @return string version version id
     */
    public function getAPIVersion()
    {
        return $this->rpcClient->call('supervisor.getAPIVersion');
    }

    /**
     * Return the version of the supervisor package in use by supervisord
     *
     * @return string version version id
     */
    public function getSupervisorVersion()
    {
        return $this->rpcClient->call('supervisor.getSupervisorVersion');
    }

    /**
     * Return identifiying string of supervisord
     *
     * @return string identifier identifying string
     */
    public function getIdentification()
    {
        return $this->rpcClient->call('supervisor.getIdentification');
    }

    /**
     * Return current state of supervisord as a struct
     *
     * @return array An array with keys string statecode, integer statename
     */
    public function getState()
    {
        return $this->rpcClient->call('supervisor.getState');
    }

    /**
     * Return the PID of supervisord
     *
     * @return integer PID
     */
    public function getPID()
    {
        return $this->rpcClient->call('supervisor.getPID');
    }

    /**
     * Read length bytes from the main log starting at offset
     *
     * @param integer $offset offset to start reading from
     * @param integer $length length number of bytes to read from the log
     *
     * @return string result Bytes of log
     */
    public function readLog($offset, $length)
    {
        return $this->rpcClient->call('supervisor.readLog', array($offset, $length));
    }

    /**
     * Clear the main log.
     *
     * @return boolean result always returns True unless error
     */
    public function clearLog()
    {
       return $this->rpcClient->call('supervisor.clearLog');
    }

    /**
     * Start all processes listed in the configuration file
     *
     * @param boolean $wait Wait for each process to be fully started
     *
     * @return array result An array containing start statuses
     */
    public function startAllProcesses($wait = true)
    {
        return $this->rpcClient->call('supervisor.startAllProcesses', array($wait));
    }

    /**
     * Start all processes in a specific process group.
     *
     * @param string $group Process group name
     * @param boolean $wait Wait for each process to be fully started
     *
     * @return array result An array containing start statuses
     */
    public function startProcessGroup($group, $wait = true)
    {
        return $this->rpcClient->call('supervisor.startProcessGroup', array($group, $wait));
    }

    /**
     * Stop all processes listed in the configuration file
     *
     * @param boolean $wait Wait for each process to be fully stoped
     *
     * @return array result An array containing start statuses
     */
    public function stopAllProcesses($wait = true)
    {
        return $this->rpcClient->call('supervisor.stopAllProcesses', array($wait));
    }

    /**
     * Stop all processes in a specific process group.
     *
     * @param string $group Process group name
     * @param boolean $wait Wait for each process to be fully started
     *
     * @return array result An array containing start statuses
     */
    public function stopProcessGroup($group, $wait = true)
    {
        return $this->rpcClient->call('supervisor.stopProcessGroup', array($group, $wait));
    }

    /**
     * Send an event that will be received by event listener subprocesses subscribing to the RemoteCommunicationEvent.
     *
     * @param string $type String for the “type” key in the event header
     * @param string $data Data for the event body
     *
     * @return boolean Always return True
     */
    public function sendRemoteCommEvent($type, $data)
    {
        return $this->rpcClient->call('supervisor.sendRemoteCommEvent', array($type, $data));
    }

    /**
     * Clear all process log files
     *
     * @return boolean result Always return true
     */
    public function clearAllProcessLogs()
    {
        return $this->rpcClient->call('supervisor.clearAllProcessLogs');
    }
}
