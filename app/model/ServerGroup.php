<?php
namespace SupBoard\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\Model\Relation;

class ServerGroup extends Model
{
    public $id;
    public $name;
    public $description;
    public $sort;
    public $create_time;
    public $update_time;

    public function initialize()
    {
        $this->hasMany('id', Server::class, 'server_group_id', [
            'alias' => 'servers',
            'reusable' => true,
            'foreignKey' => [
                'action' => Relation::ACTION_RESTRICT,
                'message' => "该分组下还有服务器，请先删除服务器"
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