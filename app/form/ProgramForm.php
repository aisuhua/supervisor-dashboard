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

class ProgramForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        // id
        if (isset($options['edit']) && $options['edit'])
        {
            $id = new Hidden('id');
            $this->add($id);
        }

        // server_id
        $server_id = new Hidden('server_id');

        $server_id->addValidators([
            new PresenceOf([
                'message' => '缺少分组 ID'
            ])
        ]);

        $this->add($server_id);

        // program
        $program = new Text('program', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => ''
        ]);

        $program->addValidators([
            new PresenceOf([
                'message' => '程序名不能为空'
            ])
        ]);

        $this->add($program);


    }
}