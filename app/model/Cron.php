<?php
namespace SupBoard\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\Model\Relation;

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
}