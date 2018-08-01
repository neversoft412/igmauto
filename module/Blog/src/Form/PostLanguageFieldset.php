<?php

namespace Blog\Form;

use Blog\Entity\PostLanguage;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StringLength;

class PostLanguageFieldset extends Fieldset implements InputFilterProviderInterface
{
    public const POST_LANGUAGE = 'postLanguages';
    public const ID = 'id';
    public const TITLE = 'title';
    public const TEXT = 'text';
    public const LANGUAGE = 'language';

    public function __construct(DoctrineObject $doctrineObjectHydrator)
    {
        parent::__construct(self::POST_LANGUAGE);

        $this->setHydrator($doctrineObjectHydrator);
        $this->setObject(new PostLanguage());

        $this->add(
            [
                'name' => self::ID,
                'type' => 'hidden',
            ]
        );

        $this->add(
            [
                'name' => self::TITLE,
                'type' => Text::class,
                'options' => [
                    'label' => 'Post Title',
                ],
            ]
        );

        $this->add(
            [
                'name' => self::TEXT,
                'type' => Textarea::class,
                'options' => [
                    'label' => 'Post Text',
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name' => self::ID,
                'required' => false,
                'filters' => [
                    [
                        'name' => ToInt::class,
                    ],
                ],
            ],
            [
                'name' => self::TITLE,
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                    [
                        'name' => StripTags::class,
                    ],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            [
                'name' => self::TEXT,
                'required' => true,
                'filters' => [
                    [
                        'name' => StripTags::class,
                    ],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'max' => 4096,
                        ],
                    ],
                ],
            ],
        ];
    }
}
