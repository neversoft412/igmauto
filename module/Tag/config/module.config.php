<?php

namespace Tag;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Tag\Factory\TagFormFactory;
use Tag\Form\TagForm;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'form_elements' => [
        'factories' => [
            TagForm::class => TagFormFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\TagController::class => Factory\TagControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'tag' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '[/:localization]/tag',
                    'defaults' => [
                        'controller' => Controller\TagController::class,
                        'action' => 'index',
                        'localization' => 'bg',
                    ],
                    'constraints' => [
                        'localization' => '[a-zA-Z]{2}',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/view/:id',
                            'defaults' => [
                                'controller' => Controller\TagController::class,
                                'action' => 'view',
                            ],
                        ],
                        'constraints' => [
                            'id' => '[1-9]\d*',
                        ],
                    ],
                    'addTag' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/addTag',
                            'defaults' => [
                                'controller' => Controller\TagController::class,
                                'action' => 'addTagWithAjax',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/edit/:id',
                            'defaults' => [
                                'controller' => Controller\TagController::class,
                                'action' => 'edit',
                            ],
                            'constraints' => [
                                'id' => '[1-9]\d*',
                            ],
                        ],
                    ],
                    'editTag' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/editTag',
                            'defaults' => [
                                'controller' => Controller\TagController::class,
                                'action' => 'editTagWithAjax',
                            ],
                        ],
                    ],
                    'deleteTag' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/deleteTag',
                            'defaults' => [
                                'controller' => Controller\TagController::class,
                                'action' => 'deleteTagWithAjax',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
