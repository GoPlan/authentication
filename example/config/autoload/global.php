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
            \Zend\Db\Adapter\Adapter::class                                    => \Zend\Db\Adapter\AdapterServiceFactory::class,
            \CreativeDelta\User\Core\Impl\Service\AuthenticationService::class => \CreativeDelta\User\Core\Impl\Factory\AuthenticationServiceFactory::class
        ],
        'aliases'   => [
            \Zend\Authentication\AuthenticationService::class => \CreativeDelta\User\Core\Impl\Service\AuthenticationService::class
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
        'driver'   => '',
        'hostname' => '',
        'database' => '',
        'username' => '',
        'password' => ''
    ],
];
