<?php
namespace SupBoard\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class Command
 *
 * @method Server getServer()
 */
class Command extends Model
{
    public $id;
    public $server_id;
    public $program;
    public $user;
    public $command;
    public $status;
    public $start_time;
    public $end_time;
    public $log;
    public $update_time;
    public $create_time;

    const STATUS_INI = 0; // 初始化状态
    const STATUS_STARTED = 1; // 已启动
    const STATUS_FINISHED = 2; // 已正常完成
    const STATUS_FAILED = -1; // 没有正常退出
    const STATUS_UNKNOWN = -2; // 无法确定进程的执行状态
    const STATUS_STOPPED = -3; // 被中断

    const PROGRAM_PREFIX = '_supervisor_command_';

    public function initialize()
    {
        $this->belongsTo('server_id', Server::class, 'id', [
            'alias' => 'Server',
            'reusable' => false
        ]);
    }

    public function beforeCreate()
    {
        $this->create_time = time();
        $this->status = self::STATUS_INI;
    }

    public function beforeSave()
    {
        $this->update_time = time();
    }

    public function getProgram()
    {
        return self::PROGRAM_PREFIX . $this->id;
    }

    public function getProcessName()
    {
        return $this->program . ':' . $this->program . '_0';
    }
}