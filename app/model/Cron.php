<?php
namespace SupBoard\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\Model\Relation;
use SupBoard\Exception\Exception;
use SupBoard\Supervisor\SupervisorCron;
use Zend\XmlRpc\Client\Exception\FaultException;
use SupBoard\Supervisor\StatusCode;

/**
 * Class Cron
 *
 * @method Server getServer()
 */
class Cron extends Model
{
    public $id;
    public $server_id;
    public $user;
    public $command;
    public $time;
    public $description;
    public $status;
    public $last_time;
    public $prev_time;
    public $update_time;
    public $create_time;

    const STATUS_ACTIVE = 1;
    const STATE_INACTIVE = -1;

    public function initialize()
    {
        $this->keepSnapshots(true);

        $this->belongsTo('server_id', Server::class, 'id', [
            'alias' => 'Server',
            'reusable' => false
        ]);

        $this->hasMany('id', CronLog::class, 'cron_id', [
            'alias' => 'cronLog',
            'reusable' => true,
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ]
        ]);
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->update_time = time();
    }

    public function afterCreate()
    {
        // 每次添加定时任务都检查主进程是否存在
        $process = Process::findFirst([
            "server_id = :server_id: AND program = :program:",
            'bind' => [
                'server_id' => $this->server_id,
                'program' => SupervisorCron::NAME
            ]
        ]);

        if (!$process)
        {
            $server = $this->getServer();

            // 添加一个　cron 进程
            $cronProcess = new Process();
            $cronProcess->server_id = $server->id;
            $cronProcess->program = SupervisorCron::NAME;
            $cronProcess->command = "/usr/bin/php " . rtrim($server->agent_root, '/') . "/app/cli.php cron start {$server->id}";
            $cronProcess->user = 'root';
            $cronProcess->is_sys = 1;
            $cronProcess->save();

            $supAgent = $server->getSupAgent();
            $supAgent->processReload();

            $supervisor = $server->getSupervisor();
            $supervisor->reloadConfig();
            $supervisor->addProcessGroup(SupervisorCron::NAME);
            $supervisor->startProcessGroup(SupervisorCron::NAME, false);
        }
    }
}