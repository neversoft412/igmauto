<?php

namespace Application;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/[:localization]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                        'localization' => 'bg',
                    ],
                    'constraints' => [
                        'localization' => '[a-zA-Z]{2}',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '[/:localization]/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            \Zend\I18n\Translator\TranslatorInterface::class => \Zend\I18n\Translator\TranslatorServiceFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'translate' => \Zend\I18n\View\Helper\Translate::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
            [
                'label' => 'Blog',
                'route' => 'blog',
                'pages' => [
                    [
                        'label' => 'View post',
                        'route' => 'blog/detail',
                        'action' => 'detail',
                        'resource' => 'post.view',
                    ],
                    [
                        'label' => 'New post',
                        'route' => 'blog/add',
                        'action' => 'add',
                        'resource' => 'post.add',

                    ],
                    [
                        'label' => 'Update post',
                        'route' => 'blog/edit',
                        'action' => 'edit',
                        'resource' => 'post.edit',
                    ],
                ],
            ],
            [
                'label' => 'Tag',
                'route' => 'tag',
                'pages' => [
                    [
                        'label' => 'View tag',
                        'route' => 'tag/view',
                        'action' => 'view',
                        'resource' => 'tag.view',
                    ],
                    [
                        'label' => 'Edit tag',
                        'route' => 'tag/edit',
                        'action' => 'edit',
                        'resource' => 'tag.edit',
                    ],
                ],
            ],
            [
                'label' => 'Log in',
                'route' => 'login',
                'resource' => 'user.login',
                'pages' => [
                    [
                        'label' => 'Log in',
                        'action' => 'index',
                    ],
                ],
            ],
            [
                'label' => 'Logout',
                'route' => 'logout',
                'action' => 'index',
                'resource' => 'user.logout',
            ],
        ],
    ],
];
