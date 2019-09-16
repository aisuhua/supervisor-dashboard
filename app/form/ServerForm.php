<?php
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Numericality;

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
            'autocomplete' => 'off',
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
            'autocomplete' => 'off',
            'value' => 'worker'
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
            'autocomplete' => 'off',
            'value' => 111111
        ]);

        $password->addValidators([
            new PresenceOf([
                'message' => '密码不能为空'
            ])
        ]);

        $this->add($password);

        // process_conf
        $process_conf = new Text('process_conf', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '/etc/supervisor/conf.d/process.conf'
        ]);

        $process_conf->addValidators([
            new Regex(
                [
                    "pattern" => "/^\/etc\/supervisor\/conf\.d\/[a-zA-Z0-9]+\.conf$/",
                    "message" => "进程配置不正确，格式：/etc/supervisor/conf.d/YOUR_CONF_NAME.conf",
                ]
            )
        ]);

        $this->add($process_conf);

        // cron_conf
        $cron_conf = new Text('cron_conf', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '/etc/supervisor/conf.d/cron.conf'
        ]);

        $cron_conf->addValidators([
            new Regex(
                [
                    "pattern" => "/^\/etc\/supervisor\/conf\.d\/[a-zA-Z0-9]+\.conf$/",
                    "message" => "定时任务配置不正确，格式：/etc/supervisor/conf.d/YOUR_CONF_NAME.conf",
                ]
            )
        ]);

        $this->add($cron_conf);

        // command_conf
        $command_conf = new Text('command_conf', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '/etc/supervisor/conf.d/command.conf'
        ]);

        $command_conf->addValidators([
            new Regex(
                [
                    "pattern" => "/^\/etc\/supervisor\/conf\.d\/[a-zA-Z0-9]+\.conf$/",
                    "message" => "命令配置不正确，格式：/etc/supervisor/conf.d/YOUR_CONF_NAME.conf",
                ]
            )
        ]);

        $this->add($command_conf);

        // sync_conf_port
        $sync_conf_port = new Text('sync_conf_port', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '8089'
        ]);

        $sync_conf_port->addValidators([
            new Numericality(
                [
                    "message" => "sync_conf 端口必须是数字",
                ]
            ),
            new Between(
                [
                    "minimum" => 1,
                    "maximum" => 65535,
                    "message" => "请填写正确的 sync_conf 端口",
                ]
            )
        ]);

        $this->add($sync_conf_port);

        // sort
        $sort = new Text('sort', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 0
        ]);

        $this->add($sort);
    }
}