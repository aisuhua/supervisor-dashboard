<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class CronLog
 *
 * @method Server getServer()
 */
class CronLog extends Model
{
    public $id;
    public $cron_id;
    public $server_id;
    public $program;
    public $command;
    public $start_time;
    public $end_time;
    public $log;
    public $status;
    public $update_time;
    public $create_time;

    const STATUS_INI = 0; // 初始化状态
    const STATUS_STARTED = 1; // 已启动
    const STATUS_FINISHED = 2; // 已正常完成
    const STATUS_FAILED = -1; // 没有正常退出
    const STATUS_UNKNOWN = -2; // 无法确定进程的执行状态

    public function initialize()
    {
        $this->belongsTo('server_id', 'Server', 'id', [
            'alias' => 'server',
            'reusable' => true
        ]);
    }

    public function beforeCreate()
    {
        $this->status = self::STATUS_INI;
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->update_time = time();
    }
}