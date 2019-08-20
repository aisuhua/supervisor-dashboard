<?php
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

class ServerGroupForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        // id
        if (isset($options['edit']) && $options['edit'])
        {
            $id = new Hidden('id');
        }
        else
        {
            $id = new Text('id');
        }

        $this->add($id);

        // name
        $name = new Text('name', [
            'class' => 'form-control',
            'autocomplete' => 'off'
        ]);

        $name->addValidators([
            new PresenceOf([
                'message' => '组名不能为空'
            ]),
            new Uniqueness(
                [
                    'model' => new ServerGroup(),
                    'message' => '该组名已存在',
                ]
            )
        ]);

        $this->add($name);

        // description
        $description = new Text('description', [
            'class' => 'form-control',
            'autocomplete' => 'off'
        ]);

        $this->add($description);

        // sort
        $sort = new Text('sort', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 999
        ]);

        $this->add($sort);
    }
}