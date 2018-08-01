<?php

namespace Tag\Form;

use Application\Validator\Unique;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Tag\Entity\Tag;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

class TagForm extends Form
{
    public const ID = 'id';
    public const NAME = 'name';
    public const SUBMIT = 'submit';

    /**
     * @var DoctrineObject
     */
    private $doctrineObjectHydrator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TagForm constructor.
     *
     * @param DoctrineObject $doctrineObjectHydrator
     * @param EntityManager  $entityManager
     */
    public function __construct(
        DoctrineObject $doctrineObjectHydrator,
        EntityManager $entityManager
    ) {
        parent::__construct();
        $this->doctrineObjectHydrator = $doctrineObjectHydrator;
        $this->entityManager = $entityManager;
    }

    public function init()
    {
        $this->setHydrator($this->doctrineObjectHydrator);
        $this->setObject(new Tag());
        $this->addElements();
        $this->addInputFilter();
    }

    private function addElements()
    {
        $this->add(
            [
                'name' => self::ID,
                'type' => Hidden::class,
            ]
        );

        $this->add(
            [
                'name' => self::NAME,
                'type' => Text::class,
                'options' => [
                    'label' => 'Tag name',
                ],
            ]
        );

        $this->add(
            [
                'name' => self::SUBMIT,
                'type' => Submit::class,
                'attributes' => [
                    'value' => 'Insert new tag',
                ],
            ]
        );
    }

    private function addInputFilter()
    {
        /** @var InputFilter $inputFilter */
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add(
            [
                'name' => self::ID,
                'required' => false,
                'filters' => [
                    [
                        'name' => ToInt::class,
                    ],
                ],
            ]
        );

        $inputFilter->add(
            [
                'name' => self::NAME,
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
                    [
                        'name' => Unique::class,
                        'options' => [
                            'object_manager' => $this->entityManager,
                            'target_class' => Tag::class,
                            'unique_column' => 'name',
                            'messages' => [
                                Unique::NOT_UNIQUE_VALUE => 'There is already a tag with this name',
                            ],
                        ],
                    ],
                ],
            ]
        );

    }

}
