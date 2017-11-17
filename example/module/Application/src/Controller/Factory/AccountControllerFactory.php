<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 07/11/2017
 * Time: 13:24
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Account\AccountMethod;
use CreativeDelta\User\Application\Controller\AccountController;
use CreativeDelta\User\Core\Domain\UserIdentityServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;

class AccountControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // TODO: Implement __invoke() method.
        $authService = $container->get(AuthenticationService::class);
        $UserIdentityServiceInterface = $container->get(UserIdentityServiceInterface::class);
        $userRegisterMethod = new AccountMethod();
        $controller = new AccountController($authService, $UserIdentityServiceInterface, $userRegisterMethod);
        return $controller;
    }
}