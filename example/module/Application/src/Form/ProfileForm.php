<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 13/11/2017
 * Time: 12:03
 */

namespace CreativeDelta\User\Application\Form;


use Zend\Form\Form;

class ProfileForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->add(
            [
                'name' => 'Identity',
                'type' => 'Hidden',
            ]);

        $this->add(
            [
                'name' => 'txtCurrentPassword',
                'type' => 'Password',
                'options' => [
                    'label' => 'Current password:'
                ]
            ]);


        $this->add(
            [
                'name' => 'txtPassword',
                'type' => 'Password',
                'options' => [
                    'label' => 'New password:'
                ]
            ]);

        $this->add(
            [
                'name' => 'txtConfirmPassword',
                'type' => 'Password',
                'options' => [
                    'label' => 'Confirm password:'
                ]
            ]);


        $this->add(
            [
                'name' => 'ResultMessages',
                'type' => 'Hidden',
            ]);

        $this->add(
            [
                'name' => 'submit',
                'type' => 'submit',
                'options' => [
                    'value' => 'Update',
                    'id' => 'btnsubmit',
                ]
            ]
        );

    }

}