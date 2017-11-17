<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [

    'service_manager' => [
        'factories' => [
            \Zend\Db\Adapter\Adapter::class                                  => \Zend\Db\Adapter\AdapterServiceFactory::class,
            \Zend\Session\SessionManager::class                              => \CreativeDelta\User\Core\Impl\Factory\SessionManagerFactory::class,
            \Zend\Authentication\AuthenticationService::class                => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \CreativeDelta\User\Google\GoogleMethod::class                   => \CreativeDelta\User\Core\Impl\Factory\GoogleMethodFactory::class,
            \CreativeDelta\User\Facebook\FacebookMethod::class               => \CreativeDelta\User\Core\Impl\Factory\FacebookMethodFactory::class,
            \CreativeDelta\User\Core\Impl\Service\UserIdentityService::class => \CreativeDelta\User\Core\Impl\Factory\UserIdentityServiceFactory::class,
        ],
        'aliases'   => [
            \Zend\Authentication\AuthenticationServiceInterface::class          => \Zend\Authentication\AuthenticationService::class,
            \CreativeDelta\User\Core\Domain\UserIdentityServiceInterface::class => \CreativeDelta\User\Core\Impl\Service\UserIdentityService::class
        ]
    ],


    // User should define below configurations in local.php for security purpose.

    'authConfig' => [
        'facebook' => [
            'appId'     => "",
            "appSecret" => "",
            "appScope"  => ""
        ],
        'google'   => [
            "clientId"     => "",
            "clientSecret" => "",
            "clientScope"  => "",
            "apiKey"       => "",
        ]
    ],

    'db' => [
        'driver'   => "",
        'hostname' => "",
        'database' => "",
        'username' => "",
        'password' => ""
    ],
];
