<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class ServerGroup extends Model
{
    public $id;
    public $name;
    public $description;
    public $sort;
    public $create_time;
    public $update_time;

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new Uniqueness(
                [
                    'field'   => 'name',
                    'message' => '该分组名称已存在',
                ]
            )
        );

        return $this->validate($validator);
    }
}