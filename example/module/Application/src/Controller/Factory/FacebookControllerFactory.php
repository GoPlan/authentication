<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 11:57 AM
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\FacebookController;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Facebook\FacebookMethod;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class FacebookControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService     = $container->get(AuthenticationService::class);
        $identityService = $container->get(UserIdentityServiceInterface::class);
        $facebookMethod  = $container->get(FacebookMethod::class);
        $controller      = new FacebookController($authService, $identityService, $facebookMethod);
        return $controller;
    }
}