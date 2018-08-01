<?php

namespace Blog\Form;

use Application\Validator\MultipleInArray;
use Blog\Entity\LanguageRepository;
use Blog\Entity\Post;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Form\Element\EntitySelect;
use Tag\Entity\Tag;
use Tag\Entity\TagRepository;
use Zend\Filter\ToInt;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class PostForm extends Form
{

    public const ID = 'id';
    public const TAGS = 'tags';
    public const SUBMIT = 'submit';
    public const CAPTCHA = 'captcha';
    /**
     * @var DoctrineObject
     */
    private $doctrineObjectHydrator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * PostForm constructor.
     *
     * @param DoctrineObject     $doctrineObjectHydrator
     * @param EntityManager      $entityManager
     * @param LanguageRepository $languageRepository
     * @param TagRepository      $tagRepository
     */
    public function __construct(
        DoctrineObject $doctrineObjectHydrator,
        EntityManager $entityManager,
        LanguageRepository $languageRepository,
        TagRepository $tagRepository
    ) {
        parent::__construct();
        $this->doctrineObjectHydrator = $doctrineObjectHydrator;
        $this->entityManager = $entityManager;
        $this->languageRepository = $languageRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function init()
    {
        $this->setHydrator($this->doctrineObjectHydrator);
        $this->setObject(new Post($this->languageRepository->findAll()));
        $this->addElements();
        $this->addCollection();
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
                'name' => self::TAGS,
                'type' => EntitySelect::class,
                'attributes' => [
                    'multiple' => 'multiple',
                ],
                'options' => [
                    'label' => 'Tags',
                    'disable_inarray_validator' => true,
                    'object_manager' => $this->entityManager,
                    'target_class' => Tag::class,
                    'property' => 'name',
                ],
            ]
        );

        $this->add(
            [
                'name' => self::SUBMIT,
                'type' => Submit::class,
                'attributes' => [
                    'value' => 'Insert new Post',
                ],
            ]
        );

        $this->add(
            [
                'name' => self::CAPTCHA,
                'type' => Captcha::class,
                'attributes' => [
                    'class' => 'form-control captcha-input',
                ],
                'options' => [
                    'label' => 'Human check',
                    'captcha' => [
                        'class' => 'Image',
                        'imgDir' => 'public/img/captcha',
                        'suffix' => '.png',
                        'imgUrl' => '/img/captcha/',
                        'imgAlt' => 'CAPTCHA Image',
                        'font' => './data/font/thorne_shaded.ttf',
                        'fsize' => 24,
                        'width' => 350,
                        'height' => 100,
                        'expiration' => 600,
                        'dotNoiseLevel' => 40,
                        'lineNoiseLevel' => 3,
                    ],
                ],
            ]
        );
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function addCollection()
    {
        $this->add(
            [
                'type' => Collection::class,
                'name' => PostLanguageFieldset::POST_LANGUAGE,
                'options' => [
                    'label' => 'Post languages',
                    'allow_add' => true,
                    'count' => $this->languageRepository->getCount(),
                    'target_element' => [
                        'type' => PostLanguageFieldset::class,
                    ],
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
                'name' => self::TAGS,
                'required' => true,
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'You have not selected tag/s',
                            ],
                        ],
                    ],
                    [
                        'name' => MultipleInArray::class,
                        'options' => [
                            'haystack' => $this->tagRepository->getIds(),
                        ],
                    ],
                ],
            ]
        );

        // $inputFilter->add(
        //     [
        //         'name' => self::TITLE,
        //         'required' => true,
        //         'filters' => [
        //             [
        //                 'name' => StringTrim::class,
        //             ],
        //             [
        //                 'name' => StripTags::class,
        //             ],
        //         ],
        //         'validators' => [
        //             // [
        //             //     'name' => NotEmpty::class,
        //             //     'options' => [
        //             //         'messages' => [
        //             //             NotEmpty::IS_EMPTY => 'Не сте въвели заглавие на поста',
        //             //         ],
        //             //     ],
        //             // ],
        //             [
        //                 'name' => StringLength::class,
        //                 'options' => [
        //                     'encoding' => 'UTF-8',
        //                     'max' => 100,
        //                     'messages' => [
        //                         StringLength::TOO_LONG => 'Заглавието е прекалено дълго',
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ]
        // );

        // $inputFilter->add(
        //     [
        //         'name' => self::TEXT,
        //         'required' => true,
        //         'filters' => [
        //             [
        //                 'name' => StripTags::class,
        //             ],
        //         ],
        //         'validators' => [
        //             [
        //                 'name' => NotEmpty::class,
        //                 'options' => [
        //                     'messages' => [
        //                         NotEmpty::IS_EMPTY => 'Не сте въвели съдържание на поста',
        //                     ],
        //                 ],
        //             ],
        //             [
        //                 'name' => StringLength::class,
        //                 'options' => [
        //                     'encoding' => 'UTF-8',
        //                     'max' => 4096,
        //                     'messages' => [
        //                         StringLength::TOO_LONG => 'Съдържанието е прекалено дълго',
        //                     ],
        //                 ],
        //             ],
        //         ],
        //     ]
        // );
    }
}
