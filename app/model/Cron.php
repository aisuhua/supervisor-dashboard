<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

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

        $this->belongsTo('server_id', 'Server', 'id', [
            'alias' => 'server',
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

    /**
     * 产生定时任务对应的程序名称
     *
     * @param $datetime
     * @return string
     */
    public function makeProgramName($datetime)
    {
        return 'sys_cron_' . $this->id . '_' . $datetime;
    }
}