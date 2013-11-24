<?php

namespace RelyAuth\Form;

use Zend\Form\Form;

class RegistrationForm extends Form {

    public function __construct(){
        parent::__construct('register');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Name'
            )
        ));

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Email'
            )
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password'
            ),
            'options' => array(
                'label' => 'Password'
            )
        ));

                $this->add(array(
                    'name' => 'passwordCheck',
                    'attributes' => array(
                        'type' => 'password'
                    ),
                    'options' => array(
                        'label' => 'Retype Password'
                    )
                ));



        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton'
            )
        ));
    }
}

