<?php

namespace User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Segment;

return [
    'service_manager' => [
        'factories' => [
            Service\UserService::class => Service\Factory\UserServiceFactory::class,
        ],
    ],
    'session_containers' => [
        'UserSession',
    ],
    'controllers' => [
        'factories' => [
            Controller\LoginController::class => Factory\LoginControllerFactory::class,
            Controller\LogoutController::class => Factory\LogoutControllerFactory::class,
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
            'login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '[/:localization]/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action' => 'index',
                        'localization' => 'bg',
                    ],
                    'constraints' => [
                        'localization' => '[a-zA-Z]{2}',
                    ],
                ],
            ],
            'logout' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '[/:localization]/logout',
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                        'action' => 'index',
                        'localization' => 'bg',
                    ],
                    'constraints' => [
                        'localization' => '[a-zA-Z]{2}',
                    ],
                ],
            ],
        ],
    ],
];
