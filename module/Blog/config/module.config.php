<?php

namespace Blog;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    // 'service_manager' => [
    //     'aliases' => [
    //         Model\PostRepositoryInterface::class => Model\ZendDbSqlRepository::class,
    //         Model\PostCommandInterface::class => Model\ZendDbSqlCommand::class,
    //     ],
    //     'factories' => [
    //         Model\ZendDbSqlRepository::class => Factory\ZendDbSqlRepositoryFactory::class,
    //         Model\ZendDbSqlCommand::class => Factory\ZendDbSqlCommandFactory::class,
    //     ],
    // ],
    'service_manager' => [
        'factories' => [
            Entity\LanguageRepository::class => InvokableFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\PostForm::class => Factory\PostFormFactory::class,
            Form\PostLanguageFieldset::class => Factory\PostLanguageFieldsetFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\ListController::class => Factory\ListControllerFactory::class,
            Controller\WriteController::class => Factory\WriteControllerFactory::class,
            Controller\DeleteController::class => Factory\DeleteControllerFactory::class,
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
            'blog' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '[/:localization]/blog',
                    'defaults' => [
                        'controller' => Controller\ListController::class,
                        'action' => 'index',
                        'localization' => 'bg',
                    ],
                    'constraints' => [
                        'localization' => '[a-zA-Z]{2}',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:id',
                            'defaults' => [
                                'action' => 'detail',
                            ],
                            'constraints' => [
                                'id' => '[1-9]\d*',
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'addPost' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/addPost',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'addPostWithAjax',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/edit/:id',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'edit',
                            ],
                            'constraints' => [
                                'id' => '[1-9]\d*',
                            ],
                        ],
                    ],
                    'editPost' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/editPost',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action' => 'editPostWithAjax',
                            ],
                        ],
                    ],
                    'deletePost' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/deletePost',
                            'defaults' => [
                                'controller' => Controller\DeleteController::class,
                                'action' => 'deletePostWithAjax',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
