<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

class Server extends Model
{
    public $id;
    public $ip;
    public $port;
    public $username;
    public $password;
    public $sync_conf_port;
    public $conf_path;
    public $sort;
    public $create_time;
    public $update_time;

    public function beforeCreate()
    {
        $this->create_time = $this->update_time = time();
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'ip',
            new Uniqueness(
                [
                    'field'   => 'name',
                    'message' => '该组名已存在',
                ]
            )
        );

        return $this->validate($validator);
    }
}