<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/25/17
 * Time: 9:46 AM
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\GoogleController;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use CreativeDelta\User\Google\GoogleMethod;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class GoogleControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService     = $container->get(AuthenticationService::class);
        $identityService = $container->get(UserIdentityServiceInterface::class);
        $googleMethod    = $container->get(GoogleMethod::class);
        $controller      = new GoogleController($authService, $identityService, $googleMethod, $googleMethod);
        return $controller;
    }
}