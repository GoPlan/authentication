<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\FacebookController;
use Application\Controller\Factory\FacebookControllerFactory;
use Application\Controller\Factory\IndexControllerFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router'       => [
        'routes' => [
            'home'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'          => Segment::class,
                'options'       => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'register' => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route' => '/register',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'facebook' => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route' => '/facebook'
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'register'        => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/register',
                                            'defaults' => [
                                                'controller' => FacebookController::class,
                                                'action'     => 'register'
                                            ]
                                        ]
                                    ],
                                    'register-return' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/register-return',
                                            'defaults' => [
                                                'controller' => FacebookController::class,
                                                'action'     => 'register-return'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'sign-in'  => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route' => '/sign-in',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'facebook' => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route' => '/facebook'
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'sign-in'        => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/sign-in',
                                            'defaults' => [
                                                'controller' => FacebookController::class,
                                                'action'     => 'sign-in'
                                            ]
                                        ]
                                    ],
                                    'sign-in-return' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/sign-in-return',
                                            'defaults' => [
                                                'controller' => FacebookController::class,
                                                'action'     => 'sign-in-return'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ],
    ],
    'controllers'  => [
        'factories' => [
            Controller\IndexController::class    => IndexControllerFactory::class,
            Controller\FacebookController::class => FacebookControllerFactory::class
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];
