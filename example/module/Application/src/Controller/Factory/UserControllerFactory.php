<?php
/**
 * Created by PhpStorm.
 * User: ducanhle
 * Date: 8/24/17
 * Time: 3:41 PM
 */

namespace CreativeDelta\User\Application\Controller\Factory;


use CreativeDelta\User\Application\Controller\UserController;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(AuthenticationServiceInterface::class);
        $controller  = new UserController($authService);
        return $controller;
    }
}