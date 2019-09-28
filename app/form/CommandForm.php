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
use SupBoard\Tool\Tool;

class CommandForm extends Form
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
            'autocomplete' => 'off',
            'placeholder' => ''
        ]);

        $command->setFilters([
            'string',
            'trim'
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
    }
}