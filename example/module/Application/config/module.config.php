<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CreativeDelta\User\Application;

use CreativeDelta\User\Application\Controller\FacebookController;
use CreativeDelta\User\Application\Controller\Factory\AccountControllerFactory;
use CreativeDelta\User\Application\Controller\Factory\FacebookControllerFactory;
use CreativeDelta\User\Application\Controller\Factory\GoogleControllerFactory;
use CreativeDelta\User\Application\Controller\Factory\IndexControllerFactory;
use CreativeDelta\User\Application\Controller\Factory\UserControllerFactory;
use CreativeDelta\User\Application\Controller\Factory\UserIdentityServiceInterfaceFactory;
use CreativeDelta\User\Application\Controller\GoogleController;
use CreativeDelta\User\Application\Controller\UserController;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
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
            'phpinfo'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/phpinfo',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'phpinfo',
                    ],
                ],
            ],
            'account'        => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/account[/:action]',
                    'defaults' => [
                        'controller' => Controller\AccountController::class,
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
                            ],
                            'google'   => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route' => '/google'
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'register'        => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/register',
                                            'defaults' => [
                                                'controller' => GoogleController::class,
                                                'action'     => 'register'
                                            ]
                                        ]
                                    ],
                                    'register-return' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/register-return',
                                            'defaults' => [
                                                'controller' => GoogleController::class,
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
                            ],
                            'google'   => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route' => '/google'
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'sign-in'        => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/sign-in',
                                            'defaults' => [
                                                'controller' => GoogleController::class,
                                                'action'     => 'sign-in'
                                            ]
                                        ]
                                    ],
                                    'sign-in-return' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/sign-in-return',
                                            'defaults' => [
                                                'controller' => GoogleController::class,
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
            'user'        => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/user',
                    'defaults' => [
                        'controller' => UserController::class,
                        'action'     => 'index'
                    ]
                ]
            ]
        ],
    ],
    'controllers'  => [
        'factories' => [
            Controller\UserController::class     => UserControllerFactory::class,
            Controller\IndexController::class    => IndexControllerFactory::class,
            Controller\FacebookController::class => FacebookControllerFactory::class,
            Controller\GoogleController::class   => GoogleControllerFactory::class,
            Controller\AccountController::class => AccountControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            UserIdentityServiceInterface::class => UserIdentityServiceInterfaceFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__ . '/../view/creative-delta/user/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/creative-delta/user/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/creative-delta/user/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/creative-delta/user/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
];
