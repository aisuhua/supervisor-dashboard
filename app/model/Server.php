<?php
namespace SupBoard\Model;

use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use SupBoard\Supervisor\SupAgent;
use SupBoard\Supervisor\Supervisor;

class Server extends Model
{
    public $id;
    public $server_group_id;
    public $ip;
    public $port;
    public $username;
    public $password;
    public $sync_conf_port;
    public $process_conf;
    public $cron_conf;
    public $command_conf;
    public $sort;
    public $create_time;
    public $update_time;

    protected $supervisor;
    protected $supAgent;

    public function initialize()
    {
        $this->belongsTo('server_group_id', ServerGroup::class, 'id', [
            'alias' => 'ServerGroup',
            'reusable' => true
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

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            ['ip', 'port'],
            new Uniqueness(
                [
                    'message' => '该服务器已存在，不需要重复添加',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * 处理服务器添加后的初始化工作
     */
    public function afterCreate()
    {
        // 添加一个　cron 进程
        $cronProcess = new Process();
        $cronProcess->server_id = $this->id;
        $cronProcess->program = '_supervisor_cron';
        $cronProcess->command = "/usr/bin/php /www/web/supervisor-agent/app/cli.php cron start {$this->id}";
        $cronProcess->user = 'root';
        $cronProcess->save();
    }

    /**
     * 获取 Supervisor RPC 通许地址
     *
     * @return string
     */
    public function getSupervisorUri()
    {
        return "http://{$this->ip}:{$this->sync_conf_port}";
    }

    /**
     * @param bool $reusable
     *
     * @return Supervisor
     */
    public function getSupervisor($reusable = true)
    {
        if ($reusable && $this->supervisor)
        {
            return $this->supervisor;
        }

        $this->supervisor = Di::getDefault()->get('supervisor', [
            $this->id, $this->ip, $this->port, $this->username, $this->password
        ]);

        return $this->supervisor;
    }

    /**
     * @param bool $reusable
     * @return SupAgent
     */
    public function getSupAgent($reusable = true)
    {
        if ($reusable && $this->supAgent)
        {
            return $this->supAgent;
        }

        $this->supAgent = Di::getDefault()->get('supAgent', [$this]);

        return $this->supAgent;
    }
}