<?php

namespace User\Form;

use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    public const USERNAME = 'username';
    public const PASSWORD = 'password';
    public const REMEMBER_ME = 'remember_me';
    public const SUBMIT = 'submit';

    public function init()
    {
        $this->addElements();
        $this->addInputFilter();
    }

    private function addElements()
    {
        $this->add(
            [
                'name' => self::USERNAME,
                'type' => Text::class,
                'options' => [
                    'label' => 'Username',
                ],
            ]
        );

        $this->add(
            [
                'name' => self::PASSWORD,
                'type' => Password::class,
                'options' => [
                    'label' => 'Password',
                ],
            ]
        );

        $this->add(
            [
                'name' => self::REMEMBER_ME,
                'type' => Checkbox::class,
                'options' => [
                    'label' => 'Remember me',
                ],
            ]
        );

        // /** @var Checkbox $rememberMe */
        // $rememberMe = $this->get(self::REMEMBER_ME);
        // $rememberMe->setChecked(true);

        $this->add(
            [
                'name' => self::SUBMIT,
                'type' => Submit::class,
                'attributes' => [
                    'value' => 'Log in',
                ],
            ]
        );
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add(
            [
                'name' => self::USERNAME,
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                    [
                        'name' => StripTags::class,
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name' => self::PASSWORD,
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                    [
                        'name' => StripTags::class,
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name' => self::REMEMBER_ME,
                'required' => false,
            ]
        );
    }
}
