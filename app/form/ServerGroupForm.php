<?php
namespace SupBoard\Form;

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
            $this->add($id);
        }

        // name
        $name = new Text('name', [
            'class' => 'form-control'
        ]);

        $name->addValidators([
            new PresenceOf([
                'message' => '分组名称不能为空'
            ])
        ]);

        $this->add($name);

        // description
        $description = new Text('description', [
            'class' => 'form-control'
        ]);

        $this->add($description);

        // sort
        $sort = new Text('sort', [
            'class' => 'form-control',
            'value' => 0
        ]);

        $this->add($sort);
    }
}