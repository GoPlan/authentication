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
            \Zend\Session\SessionManager::class                                => \CreativeDelta\User\Core\Impl\Factory\SessionManagerFactory::class,
            \CreativeDelta\User\Core\Impl\Service\AuthenticationService::class => \CreativeDelta\User\Core\Impl\Factory\AuthenticationServiceFactory::class
        ],
        'aliases'   => [
            \Zend\Authentication\AuthenticationService::class => \CreativeDelta\User\Core\Impl\Service\AuthenticationService::class
        ]
    ],


    // User should define below configurations in local.php for security purpose.

    'authConfig' => [
        'facebook' => [
            'appId'     => "1442542719380071",
            "appSecret" => "21db879762856c2639ce0ba2a9076a9b",
            "appScope"  => ""
        ],
        'google'   => [
            "clientId"     => "524836760491-47m4ms6usfvhjd9lssqtc2p792nf3jne.apps.googleusercontent.com",
            "clientSecret" => "4iJsPeglVynfbH3xmWcUKmfX",
            "clientScope"  => "public_profile",
            "apiKey"       => "",
        ]
    ],

    'db' => [
        'driver'   => 'pdo_pgsql',
        'hostname' => 'localhost',
        'database' => 'erp-identity',
        'username' => 'admin',
        'password' => 'admin'
    ],
];
