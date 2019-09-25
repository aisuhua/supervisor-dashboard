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
use SupBoard\Model\Cron;
use SupBoard\Tool\Tool;

class CronForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        // id
        if (isset($options['edit']) && $options['edit'])
        {
            $id = new Hidden('id');
            $this->add($id);
        }

        $server_id = new Hidden('server_id', [
            'class' => 'form-control',
            'autocomplete' => 'off'
        ]);

        $this->add($server_id);

        $user = new Text('user', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 'www-data'
        ]);

        $user->addValidators([
            new PresenceOf([
                'message' => '用户不能为空'
            ])
        ]);

        $this->add($user);

        // command
        $command = new Text('command', [
            'class' => 'form-control',
            'autocomplete' => 'off'
        ]);

        $command->addValidators([
            new PresenceOf([
                'message' => '命令不能为空'
            ]),
            new Regex(
                [
                    "pattern" => Tool::commandPattern(),
                    "message" => "命令格式不正确"
                ]
            )
        ]);

        $this->add($command);

        $time = new Text('time', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'placeholder' => '* * * * *',
            'style' => "width: 300px;"
        ]);

        $time->addValidators([
            new PresenceOf([
                'message' => '时间不能为空'
            ])
        ]);

        $this->add($time);

        $status = new Select(
            'status',
            [
                Cron::STATUS_ACTIVE => '启用',
                Cron::STATE_INACTIVE => '停用',
            ],
            [
                'class' => 'form-control'
            ]
        );

        $this->add($status);

        // port
        $description = new Text('description', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => ''
        ]);

        $this->add($description);

        $times = new Select(
            'times',
            [
                '' => '',
                '* * * * *' => '每分钟',
                '0 * * * *' => '每小时',
                '0 0 * * *' => '每天',
                '0 0 * * 0' => '每周',
                '0 0 1 * *' => '每月',
                '0 0 1 1 *' => '每年',
            ],
            [
                'class' => 'form-control',
                'style' => "width: 300px;"
            ]
        );

        $this->add($times);
    }
}