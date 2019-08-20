<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

class ServerGroup extends Model
{
    public $id;
    public $name;
    public $description;
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
            'name',
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