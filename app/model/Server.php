<?php
namespace SupBoard\Model;

use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use SupBoard\Supervisor\SupAgent;

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
     * @return SupAgent
     */
    public function getSupAgent($reusable = true)
    {
        if ($reusable && $this->supAgent)
        {
            return $this->supAgent;
        }

        $this->supAgent = Di::getDefault()->get('supAgent', [$this->ip, $this->port]);

        return $this->supAgent;
    }
}