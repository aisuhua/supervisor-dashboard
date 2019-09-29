<?php
namespace SupBoard\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Numericality;
use SupBoard\Model\ServerGroup;

class ServerForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        // id

        if (isset($options['edit']) && $options['edit'])
        {
            $id = new Hidden('id');
            $this->add($id);
        }

        // server_group_id
        $server_group = ServerGroup::find([
            'order' => 'sort desc'
        ]);

        $server_group_id = new Select('server_group_id', $server_group, [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => '请选择分组',
            'emptyValue' => '',
            'class' => 'form-control'
        ]);

        $server_group_id->addValidators([
            new PresenceOf([
                'message' => '请选择分组'
            ])
        ]);

        $this->add($server_group_id);

        // ip
        $ip = new Text('ip', [
            'class' => 'form-control',
            'autocomplete' => 'on'
        ]);

        $ip->addValidators([
            new PresenceOf([
                'message' => 'IP 地址不能为空'
            ]),
            new Regex(
                [
                    "pattern" => "/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/",
                    "message" => "IP 地址格式不正确",
                ]
            )
        ]);

        $this->add($ip);

        // port
        $port = new Text('port', [
            'class' => 'form-control',
            'autocomplete' => 'on',
            'value' => '9001'
        ]);

        $port->addValidators([
            new PresenceOf([
                'message' => 'Supervisor 端口不能为空'
            ]),
            new Numericality(
                [
                    "message" => "Supervisor 端口必须是数字",
                ]
            ),
            new Between(
                [
                    "minimum" => 1,
                    "maximum" => 65535,
                    "message" => "请填写正确的 Supervisor 端口",
                ]
            )
        ]);

        $this->add($port);

        // username
        $username = new Text('username', [
            'class' => 'form-control',
            'autocomplete' => 'on',
            'placeholder' => "留空则使用系统用户名",
            'value' => $GLOBALS['supervisor']['username']
        ]);

        $username->addValidators([
            new PresenceOf([
                'message' => '用户名不能为空'
            ])
        ]);

        $this->add($username);

        // password
        $password = new Text('password', [
            'class' => 'form-control',
            'autocomplete' => 'on',
            'value' => $GLOBALS['supervisor']['password']
        ]);

        $password->addValidators([
            new PresenceOf([
                'message' => '密码不能为空'
            ])
        ]);

        $this->add($password);

        // agent port
        $agent_port = new Text('agent_port', [
            'class' => 'form-control',
            'autocomplete' => 'on',
            'value' => '9002'
        ]);

        $agent_port->addValidators([
            new Numericality(
                [
                    "message" => "sync_conf 端口必须是数字",
                ]
            ),
            new Between(
                [
                    "minimum" => 1,
                    "maximum" => 65535,
                    "message" => "请填写正确的 agent port",
                ]
            )
        ]);

        $this->add($agent_port);

        // agent root
        $agent_root = new Text('agent_root', [
            'class' => 'form-control',
            'autocomplete' => 'on',
            'value' => '/www/web/supervisor-agent'
        ]);

        $agent_root->addValidators([
            new PresenceOf([
                'message' => '请填写正确的 agent root'
            ])
        ]);
        $this->add($agent_root);
    }
}