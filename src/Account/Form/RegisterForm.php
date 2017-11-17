<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 10/11/2017
 * Time: 14:27
 */

namespace CreativeDelta\User\Account\Form;


use Zend\Form\Element\Hidden;
use Zend\Form\Element\Password;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class RegisterForm extends Form
{
    const ELEMENT_TXT_USERNAME         = 'txtUsername';
    const ELEMENT_TXT_PASSWORD         = 'txtPassword';
    const ELEMENT_TXT_CONFIRM_PASSWORD = 'txtConfirmPassword';
    const ELEMENT_RESULT_MESSAGES      = 'resultMessages';

    public function getInputFilter()
    {
        if (!$this->filter) {

            $filter = new InputFilter();

            $filter->add([
                'name'       => self::ELEMENT_TXT_USERNAME,
                'filters'    => [],
                'validators' => []
            ]);

            $filter->add([
                'name'       => self::ELEMENT_TXT_PASSWORD,
                'filters'    => [],
                'validators' => []
            ]);

            $filter->add([
                'name'       => self::ELEMENT_TXT_CONFIRM_PASSWORD,
                'filters'    => [],
                'validators' => []
            ]);

            $this->filter = $filter;
        }

        return $this->filter;
    }

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->add([
            'name'    => self::ELEMENT_TXT_USERNAME,
            'type'    => Text::class,
            'options' => [
                'label' => 'Username'
            ]
        ]);

        $this->add([
            'name'    => self::ELEMENT_TXT_PASSWORD,
            'type'    => Password::class,
            'options' => [
                'label' => 'Password'
            ]
        ]);

        $this->add([
            'name'    => self::ELEMENT_TXT_CONFIRM_PASSWORD,
            'type'    => Password::class,
            'options' => [
                'label' => 'Confirm Password'
            ]
        ]);

        $this->add([
            'name' => self::ELEMENT_RESULT_MESSAGES,
            'type' => Hidden::class
        ]);
    }
}