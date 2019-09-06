<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

class Cron extends Model
{
    public $id;
    public $server_id;
    public $command;
    public $time;
    public $description;
    public $status;
    public $last_time;
    public $update_time;
    public $create_time;

    const STATUS_ACTIVE = 1;
    const STATE_INACTIVE = -1;

    public function beforeCreate()
    {
        $this->create_time = time();
        $this->status = self::STATUS_ACTIVE;
    }

    public function beforeSave()
    {
        $this->update_time = time();
    }
}